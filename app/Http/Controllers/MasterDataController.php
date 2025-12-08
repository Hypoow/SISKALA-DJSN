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
        $users = User::orderByRaw("CASE 
            WHEN role = 'admin' THEN 1 
            WHEN role = 'user' THEN 2 
            WHEN role = 'Dewan' THEN 3 
            ELSE 4 END")
            ->orderBy('order', 'asc') // Sort by the assigned order
            ->orderBy('name', 'asc')  // Fallback to name if order is null
            ->get();
            
        return view('master-data.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,user,Dewan',
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
            'role' => 'required|in:admin,user,Dewan',
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

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('master-data.index')->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->delete();
        return redirect()->route('master-data.index')->with('success', 'Akun berhasil dihapus.');
    }
}
