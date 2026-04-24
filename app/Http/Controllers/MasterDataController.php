<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Division;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class MasterDataController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $user = Auth::user();

            if ($request->routeIs('master-data.topics')) {
                if (!$user->canManageTopics() && !$user->canAccessAdminArea()) {
                    abort(403, 'Akses ditolak.');
                }

                return $next($request);
            }

            if (!$user->canAccessAdminArea()) {
                abort(403, 'Akses ditolak.');
            }

            return $next($request);
        });
    }

    public function index()
    {
        $users = User::with(['division', 'position'])->get();

        $sortedUsers = $users->sortBy(function (User $user) {
            return sprintf(
                '%03d-%05d-%s',
                $user->management_sort_order,
                $user->order ?? 99999,
                mb_strtolower($user->name)
            );
        });

        $groupedUsers = $sortedUsers
            ->groupBy(fn (User $user) => $user->management_group_label)
            ->sortBy(function ($items, $key) {
                return optional($items->first())->management_sort_order ?? 99;
            });
            
        $divisions = Division::orderBy('category')->orderBy('order')->get();
        $positions = Position::orderBy('order')->get();
            
        return view('master-data.index', compact('groupedUsers', 'divisions', 'positions'));
    }

    public function topics()
    {
        abort_unless(Auth::user()->canManageTopics() || Auth::user()->canAccessAdminArea(), 403, 'Akses ditolak.');

        return view('master-data.topics');
    }

    public function create()
    {
        $divisions = Division::orderBy('category')->orderBy('order')->get();
        $positions = Position::orderBy('order')->get();
        $accessProfiles = User::accessProfileOptions();
        $commissionOptions = User::commissionOptions();
        $divisionStructureGroups = Division::structureGroupOptions();
        $positionStructureGroups = User::structureGroupOptions();

        return view('master-data.account-create', compact(
            'divisions',
            'positions',
            'accessProfiles',
            'commissionOptions',
            'divisionStructureGroups',
            'positionStructureGroups'
        ));
    }

    public function edit(User $user)
    {
        $divisions = Division::orderBy('category')->orderBy('order')->get();
        $positions = Position::orderBy('order')->get();
        $accessProfiles = User::accessProfileOptions();
        $commissionOptions = User::commissionOptions();
        $divisionStructureGroups = Division::structureGroupOptions();
        $positionStructureGroups = User::structureGroupOptions();

        return view('master-data.account-edit', compact(
            'user',
            'divisions',
            'positions',
            'accessProfiles',
            'commissionOptions',
            'divisionStructureGroups',
            'positionStructureGroups'
        ));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => ['nullable', Rule::in(User::ROLES)],
            'is_super_admin' => 'nullable|boolean',
            'division_id' => 'nullable|exists:divisions,id',
            'position_id' => 'nullable|exists:positions,id',
            'prefix' => 'nullable|string|max:20',
            'report_target_label' => 'nullable|string|max:255',
            'receives_disposition' => 'nullable|boolean',
            'disposition_group_label' => 'nullable|string|max:255',
        ]);

        $division = $request->filled('division_id') ? Division::find($request->division_id) : null;
        $position = $request->filled('position_id') ? Position::find($request->position_id) : null;

        User::create([
            'name' => $request->name,
            'prefix' => $request->prefix ?? 'Bapak',
            'report_target_label' => $request->filled('report_target_label') ? $request->report_target_label : null,
            'receives_disposition' => $this->normalizeNullableBoolean($request->input('receives_disposition')),
            'disposition_group_label' => $request->filled('disposition_group_label') ? $request->disposition_group_label : null,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $this->resolveRequestedUserRole($request, $division, $position),
            'division_id' => $request->division_id,
            'position_id' => $request->position_id,
            // Mirror to divisi for backward compatibility if needed, 
            // but we'll transition to division_id
            'divisi' => $division?->name,
        ]);

        return redirect()->route('master-data.index')->with('success', 'Akun berhasil dibuat.');
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['nullable', Rule::in(User::ROLES)],
            'is_super_admin' => 'nullable|boolean',
            'division_id' => 'nullable|exists:divisions,id',
            'position_id' => 'nullable|exists:positions,id',
            'prefix' => 'nullable|string|max:20',
            'report_target_label' => 'nullable|string|max:255',
            'receives_disposition' => 'nullable|boolean',
            'disposition_group_label' => 'nullable|string|max:255',
        ];
        
        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8';
        }
        
        $request->validate($rules);

        $division = $request->filled('division_id') ? Division::find($request->division_id) : null;
        $position = $request->filled('position_id') ? Position::find($request->position_id) : null;

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $this->resolveRequestedUserRole($request, $division, $position, $user),
            'division_id' => $request->division_id,
            'position_id' => $request->position_id,
            'divisi' => $division?->name,
            'prefix' => $request->prefix ?? 'Bapak',
            'report_target_label' => $request->filled('report_target_label') ? $request->report_target_label : null,
            'receives_disposition' => $this->normalizeNullableBoolean($request->input('receives_disposition')),
            'disposition_group_label' => $request->filled('disposition_group_label') ? $request->disposition_group_label : null,
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

        if ($user->isPrimarySuperAdmin()) {
            return redirect()->route('master-data.index')->with('error', 'Akun Super Admin Utama tidak dapat dihapus.');
        }

        $user->delete();
        return redirect()->route('master-data.index')->with('success', 'Akun berhasil dihapus.');
    }

    // --- Division Management ---

    public function divisions()
    {
        $divisions = Division::withCount('users')
            ->orderBy('category')
            ->orderBy('order', 'asc')
            ->get();

        $positions = Position::withCount('users')
            ->orderBy('order', 'asc')
            ->orderBy('name')
            ->get();

        $accessProfiles = User::accessProfileOptions();
        $commissionOptions = User::commissionOptions();
        $structureGroups = User::structureGroupOptions();
        $divisionStructureGroups = Division::structureGroupOptions();
        $groupedDivisions = collect($divisionStructureGroups)->mapWithKeys(fn ($label, $key) => [
            $key => $divisions->where('structure_group', $key)->values(),
        ]);
        $groupedPositions = collect($structureGroups)->mapWithKeys(fn ($label, $key) => [
            $key => $positions->where('structure_group', $key)->values(),
        ]);

        return view('master-data.builder', compact(
            'divisions',
            'positions',
            'accessProfiles',
            'commissionOptions',
            'structureGroups',
            'divisionStructureGroups',
            'groupedDivisions',
            'groupedPositions'
        ));
    }

    public function storeDivision(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'structure_group' => ['required', Rule::in(array_keys(Division::structureGroupOptions()))],
            'access_profile' => ['required', Rule::in(array_keys(User::accessProfileOptions()))],
            'commission_code' => 'nullable|string|max:255',
            'is_commission' => 'nullable|boolean',
            'description' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
        ]);

        $divisionData = $this->resolveDivisionPayload($request);

        Division::create($divisionData);

        return redirect()->back()->with('success', 'Unit kerja berhasil ditambahkan.');
    }

    public function updateDivision(Request $request, Division $division)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'structure_group' => ['required', Rule::in(array_keys(Division::structureGroupOptions()))],
            'access_profile' => ['required', Rule::in(array_keys(User::accessProfileOptions()))],
            'commission_code' => 'nullable|string|max:255',
            'is_commission' => 'nullable|boolean',
            'description' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
        ]);

        $division->update($this->resolveDivisionPayload($request, $division));

        return redirect()->back()->with('success', 'Unit kerja berhasil diperbarui.');
    }

    public function destroyDivision(Division $division)
    {
        // Check if any users are using this division
        if ($division->users()->count() > 0) {
            return redirect()->back()->with('error', 'Unit kerja ini tidak dapat dihapus karena masih digunakan oleh pengguna.');
        }

        $division->delete();
        return redirect()->back()->with('success', 'Unit kerja berhasil dihapus.');
    }

    public function reorderDivision(Request $request)
    {
        $request->validate(['order' => 'required|array']);

        foreach ($request->order as $index => $item) {
            $id = is_array($item) ? ($item['id'] ?? null) : $item;
            $structureGroup = is_array($item) ? ($item['structure_group'] ?? null) : null;

            if (!$id || !Division::whereKey($id)->exists()) {
                continue;
            }

            $payload = ['order' => $index];

            if ($structureGroup && array_key_exists($structureGroup, Division::structureGroupOptions())) {
                $payload['structure_group'] = $structureGroup;
                $payload['category'] = Division::legacyCategoryFor(
                    $structureGroup,
                    (bool) Division::whereKey($id)->value('is_commission'),
                    Division::whereKey($id)->value('name')
                );
            }

            Division::where('id', $id)->update($payload);
        }

        return response()->json(['success' => true]);
    }

    public function storePosition(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'structure_group' => ['required', Rule::in(array_keys(User::structureGroupOptions()))],
            'access_profile' => ['nullable', Rule::in(array_keys(User::accessProfileOptions()))],
            'order' => 'nullable|integer',
            'receives_disposition' => 'required|boolean',
            'disposition_group_label' => 'nullable|string|max:255',
            'report_target_label' => 'nullable|string|max:255',
        ]);

        Position::create([
            'name' => $request->name,
            'code' => $this->generateUniquePositionCode($request->name),
            'structure_group' => $request->structure_group,
            'access_profile' => $request->filled('access_profile') ? $request->access_profile : null,
            'order' => $request->input('order', 0),
            'receives_disposition' => $this->normalizeNullableBoolean($request->input('receives_disposition')),
            'disposition_group_label' => $request->filled('disposition_group_label') ? $request->disposition_group_label : null,
            'report_target_label' => $request->filled('report_target_label') ? $request->report_target_label : null,
        ]);

        return redirect()->back()->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function updatePosition(Request $request, Position $position)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'structure_group' => ['required', Rule::in(array_keys(User::structureGroupOptions()))],
            'access_profile' => ['nullable', Rule::in(array_keys(User::accessProfileOptions()))],
            'order' => 'nullable|integer',
            'receives_disposition' => 'required|boolean',
            'disposition_group_label' => 'nullable|string|max:255',
            'report_target_label' => 'nullable|string|max:255',
        ]);

        $position->update([
            'name' => $request->name,
            'structure_group' => $request->structure_group,
            'access_profile' => $request->filled('access_profile') ? $request->access_profile : null,
            'order' => $request->input('order', 0),
            'receives_disposition' => $this->normalizeNullableBoolean($request->input('receives_disposition')),
            'disposition_group_label' => $request->filled('disposition_group_label') ? $request->disposition_group_label : null,
            'report_target_label' => $request->filled('report_target_label') ? $request->report_target_label : null,
        ]);

        return redirect()->back()->with('success', 'Jabatan berhasil diperbarui.');
    }

    public function destroyPosition(Position $position)
    {
        if ($position->users()->count() > 0) {
            return redirect()->back()->with('error', 'Jabatan ini tidak dapat dihapus karena masih digunakan oleh pengguna.');
        }

        $position->delete();

        return redirect()->back()->with('success', 'Jabatan berhasil dihapus.');
    }

    public function reorderPosition(Request $request)
    {
        $request->validate(['order' => 'required|array']);

        foreach ($request->order as $index => $item) {
            $id = is_array($item) ? ($item['id'] ?? null) : $item;
            $structureGroup = is_array($item) ? ($item['structure_group'] ?? null) : null;

            if (!$id || !Position::whereKey($id)->exists()) {
                continue;
            }

            $payload = ['order' => $index];

            if ($structureGroup && array_key_exists($structureGroup, User::structureGroupOptions())) {
                $payload['structure_group'] = $structureGroup;
            }

            Position::where('id', $id)->update($payload);
        }

        return response()->json(['success' => true]);
    }

    private function normalizeNullableBoolean($value): ?bool
    {
        if ($value === null || $value === '') {
            return null;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }

    private function resolveRequestedUserRole(
        Request $request,
        ?Division $division = null,
        ?Position $position = null,
        ?User $existingUser = null
    ): string {
        if ($request->boolean('is_super_admin')) {
            return User::ROLE_SUPER_ADMIN;
        }

        if ($request->filled('role')) {
            return $request->role;
        }

        $profile = trim((string) ($position?->access_profile ?: $division?->access_profile));

        if ($profile !== '') {
            return User::legacyRoleFromAccessProfile($profile);
        }

        return $existingUser?->role ?: User::ROLE_USER;
    }

    private function resolveDivisionPayload(Request $request, ?Division $division = null): array
    {
        $structureGroup = $request->input('structure_group');
        $isCommission = $request->boolean('is_commission');
        $name = trim((string) $request->name);
        $commissionCode = $this->resolveDivisionCommissionCode(
            $request->input('commission_code'),
            $name,
            $isCommission,
            $structureGroup
        );

        if ($isCommission && $commissionCode === null) {
            throw ValidationException::withMessages([
                'commission_code' => 'Unit komisi wajib memiliki kode komisi yang valid.',
            ]);
        }

        if (
            !$isCommission
            && $commissionCode !== null
            && !array_key_exists($commissionCode, Division::commissionOptions())
        ) {
            throw ValidationException::withMessages([
                'commission_code' => 'Komisi acuan belum terdaftar di master struktur.',
            ]);
        }

        if ($isCommission) {
            $duplicateCommission = Division::query()
                ->commissionDefinitions()
                ->where('commission_code', $commissionCode)
                ->when($division, fn ($query) => $query->where('id', '!=', $division->id))
                ->exists();

            if ($duplicateCommission) {
                throw ValidationException::withMessages([
                    'commission_code' => 'Kode komisi sudah dipakai oleh komisi Dewan lain.',
                ]);
            }
        }

        return [
            'name' => $name,
            'short_label' => null,
            'category' => Division::legacyCategoryFor($structureGroup, $isCommission, $name),
            'structure_group' => $structureGroup,
            'access_profile' => $request->access_profile,
            'commission_code' => $commissionCode,
            'description' => $request->filled('description') ? $request->description : null,
            'is_commission' => $isCommission,
            'order' => $request->input('order', 0),
        ];
    }

    private function resolveDivisionCommissionCode(
        ?string $requestedCode,
        ?string $label,
        bool $isCommission,
        ?string $structureGroup
    ): ?string {
        if ($isCommission) {
            return Division::normalizeCommissionCode($requestedCode ?: $label);
        }

        if (!in_array($structureGroup, [Division::STRUCTURE_GROUP_DEWAN, Division::STRUCTURE_GROUP_SUPPORT], true)) {
            return null;
        }

        return Division::normalizeCommissionCode($requestedCode);
    }

    private function generateUniquePositionCode(string $name): string
    {
        $baseCode = Str::of($name)
            ->trim()
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/u', '_')
            ->trim('_')
            ->value();

        $baseCode = $baseCode !== '' ? $baseCode : 'jabatan';
        $code = $baseCode;
        $counter = 2;

        while (Position::where('code', $code)->exists()) {
            $code = $baseCode . '_' . $counter++;
        }

        return $code;
    }

}
