<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class MasterStaffController extends Controller
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
        $staffSekretariat = Staff::where('type', 'sekretariat')->orderBy('name')->get();
        $staffTA = Staff::where('type', 'ta')->orderBy('name')->get();

        return view('master-data.staff', compact('staffSekretariat', 'staffTA'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => ['required', Rule::in(['sekretariat', 'ta'])],
        ]);

        Staff::create($validated);

        return redirect()->route('master-data.staff.index')
            ->with('success', 'Data Staf berhasil ditambahkan.');
    }

    public function update(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => ['required', Rule::in(['sekretariat', 'ta'])],
        ]);

        $staff->update($validated);

        return redirect()->route('master-data.staff.index')
            ->with('success', 'Data Staf berhasil diperbarui.');
    }

    public function destroy(Staff $staff)
    {
        $staff->delete();
        return redirect()->route('master-data.staff.index')
            ->with('success', 'Data Staf berhasil dihapus.');
    }
}
