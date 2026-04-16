<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Division;
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
             if (in_array($user->role, ['Dewan', 'TA', 'Persidangan'])) {
                $divisi = strtoupper($user->divisi ?? '');
                
                // Simplified 3-tier Dewan Grouping
                if (str_contains($divisi, 'KETUA DJSN')) return 'Ketua DJSN';
                if (str_contains($divisi, 'PME')) return 'Komisi PME';
                if (str_contains($divisi, 'KOMJAKUM') || str_contains($divisi, 'KEBIJAKAN')) return 'KOMJAKUM';
                
                // Fallbacks
                if ($user->role === 'Dewan') return 'Anggota Dewan Lainnya';
            }

            if ($user->role === 'admin') return 'Admin Utama';
            if ($user->role === 'DJSN') return 'Sekretariat DJSN'; // Generic Label for specific role
            if ($user->role === 'Persidangan') return 'Persidangan'; // Fallback for General Persidangan
            
            // Fallback for others by Role Name
            return $user->role;
        });

        // Define Priority Order for Groups
        $groupPriority = [
            'Admin Utama' => 0,
            'Ketua DJSN' => 1,
            'Komisi PME' => 2,
            'KOMJAKUM' => 3,
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
            
        $divisions = Division::orderBy('category')->orderBy('order')->get();
            
        return view('master-data.index', compact('groupedUsers', 'divisions'));
    }

    public function topics()
    {
        return view('master-data.topics');
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(User::ROLES)],
            'division_id' => 'nullable|exists:divisions,id',
            'prefix' => 'nullable|string|max:20',
        ]);

        User::create([
            'name' => $request->name,
            'prefix' => $request->prefix ?? 'Bapak',
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'division_id' => $request->division_id,
            // Mirror to divisi for backward compatibility if needed, 
            // but we'll transition to division_id
            'divisi' => Division::find($request->division_id)?->name,
        ]);

        return redirect()->route('master-data.index')->with('success', 'Akun berhasil dibuat.');
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(User::ROLES)],
            'division_id' => 'nullable|exists:divisions,id',
            'prefix' => 'nullable|string|max:20',
        ];
        
        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8';
        }
        
        $request->validate($rules);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'division_id' => $request->division_id,
            'divisi' => Division::find($request->division_id)?->name,
            'prefix' => $request->prefix ?? 'Bapak',
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

    // --- Division Management ---

    public function divisions()
    {
        $divisions = Division::orderBy('category')
            ->orderBy('order', 'asc')
            ->get();
            
        return view('master-data.divisions', compact('divisions'));
    }

    public function storeDivision(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:Ketua DJSN,Komisi,Sekretariat DJSN',
            'order' => 'nullable|integer'
        ]);

        Division::create($request->all());

        return redirect()->back()->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function updateDivision(Request $request, Division $division)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:Ketua DJSN,Komisi,Sekretariat DJSN',
            'order' => 'nullable|integer'
        ]);

        $division->update($request->all());

        return redirect()->back()->with('success', 'Jabatan berhasil diperbarui.');
    }

    public function destroyDivision(Division $division)
    {
        // Check if any users are using this division
        if ($division->users()->count() > 0) {
            return redirect()->back()->with('error', 'Jabatan ini tidak dapat dihapus karena masih digunakan oleh pengguna.');
        }

        $division->delete();
        return redirect()->back()->with('success', 'Jabatan berhasil dihapus.');
    }

    public function reorderDivision(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:divisions,id',
        ]);

        foreach ($request->order as $index => $id) {
            Division::where('id', $id)->update(['order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
