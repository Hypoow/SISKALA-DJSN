<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class MasterDataController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (Auth::user()->role !== 'admin') {
                abort(403, 'Akses ditolak.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $users = User::orderBy('order', 'asc') // Prioritize custom drag-and-drop order
            ->orderByRaw("CASE 
            WHEN role = 'admin' THEN 1 
            WHEN role = 'DJSN' THEN 2
            WHEN role = 'Tata Usaha' THEN 3
            WHEN role = 'Persidangan' THEN 4
            WHEN role = 'Bagian Umum' THEN 5
            WHEN role = 'Dewan' THEN 6
            ELSE 7 END")
            ->orderBy('name', 'asc')
            ->get();

        // Grouping Logic (Identical to ActivityController + Admin/User fallback)
        $groupedUsers = $users->groupBy(function($user) {
             if ($user->role === 'Dewan') {
                $divisi = strtolower($user->divisi ?? '');
                if (str_contains($divisi, 'ketua djsn')) return 'Ketua DJSN';
                if (str_contains($divisi, 'pme') || str_contains($divisi, 'monitoring')) return 'Komisi PME';
                if (str_contains($divisi, 'komjakum') || str_contains($divisi, 'kebijakan')) return 'Komisi Komjakum';
                
                // Dynamic Grouping for other Commissions
                if (str_contains($divisi, 'komisi')) {
                    // Return the proper case name of the division (e.g., "Komisi X")
                    return ucwords($user->divisi);
                }
                
                return 'Anggota Dewan Lainnya';
            }
            if ($user->role === 'admin') return 'Admin Utama';
            if ($user->role === 'DJSN') return 'Sekretariat DJSN'; // Generic Label for specific role
            
            // Fallback for others by Role Name
            return $user->role;
        });

        // Define Priority Order for Groups
        $groupPriority = [
            'Admin Utama' => 0,
            'Ketua DJSN' => 1,
            'Komisi PME' => 2,
            'Komisi Komjakum' => 3,
            'Anggota Dewan Lainnya' => 4,
            'Sekretariat DJSN' => 5, // Access Level 1
            'Tata Usaha' => 6,
            'Persidangan' => 7,
            'Bagian Umum' => 8,
            'User' => 9
        ];

        // Sort the Groups
        $groupedUsers = $groupedUsers->sortBy(function($items, $key) use ($groupPriority) {
            return $groupPriority[$key] ?? 99;
        });
            
        return view('master-data.index', compact('groupedUsers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(User::ROLES)],
            'divisi' => 'nullable|string|max:255',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'divisi' => $request->divisi,
        ]);

        return redirect()->route('master-data.index')->with('success', 'Akun berhasil dibuat.');
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(User::ROLES)],
            'divisi' => 'nullable|string|max:255',
        ];
        
        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8';
        }
        
        $request->validate($rules);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'divisi' => $request->divisi,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('master-data.index')->with('success', 'Akun berhasil diperbarui.');
    }

    public function reorder(Request $request) {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:users,id',
        ]);

        $ids = $request->order;
        
        // 1. Get current "order" values of these specific users
        // We want to preserve the set of values, just shuffle who has which value.
        // This ensures distinct groups don't clash or reset to 1.
        $currentOrders = User::whereIn('id', $ids)
                             ->pluck('order')
                             ->sort() // Sort values ascending (10, 20, 30)
                             ->values(); // Reset keys

        // 2. Iterate the NEW order of IDs, and assign the SORTED values
        foreach ($ids as $index => $id) {
            // Assign lowest available order value to the first person in the new list, etc.
            if (isset($currentOrders[$index])) {
                User::where('id', $id)->update(['order' => $currentOrders[$index]]);
            }
        }

        return response()->json(['success' => true]);
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('master-data.index')->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        // Prevent deleting Admin Utama
        if ($user->role === 'admin' || $user->id === 1) {
            return redirect()->route('master-data.index')->with('error', 'Akun Admin Utama tidak dapat dihapus.');
        }

        $user->delete();
        return redirect()->route('master-data.index')->with('success', 'Akun berhasil dihapus.');
    }
}
