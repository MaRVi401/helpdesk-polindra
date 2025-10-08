<?php

namespace App\Http\Controllers\Admin\KelolaPengguna;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\User;
use App\Models\Unit;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Exports\KelolaPengguna\StaffExport;
use Maatwebsite\Excel\Facades\Excel;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        // Ambil input dari request, berikan nilai default jika tidak ada
        $searchQuery = $request->input('q');
        $perPage = $request->input('per_page', 10);

        // Mulai query ke model Staff dengan relasinya
        $staffQuery = Staff::with(['user', 'unit', 'jabatan']);

        // Terapkan filter pencarian jika ada input 'q'
        if ($searchQuery) {
            $staffQuery->where(function ($query) use ($searchQuery) {
                // Cari di tabel 'staff' pada kolom 'nik'
                $query->where('nik', 'like', "%{$searchQuery}%")
                    // Atau cari di relasi 'user' pada kolom 'name' atau 'email'
                    ->orWhereHas('user', function ($userQuery) use ($searchQuery) {
                        $userQuery->where('name', 'like', "%{$searchQuery}%")
                            ->orWhere('email', 'like', "%{$searchQuery}%");
                    });
            });
        }

        // Lakukan paginasi setelah semua filter diterapkan
        $staffs = $staffQuery->orderBy('id', 'asc')->paginate($perPage);

        // Tambahkan parameter query string ke link paginasi
        $staffs->appends($request->except('page'));

        // Kirim data ke view
        return view('admin.kelolapengguna.staff.index', compact('staffs', 'searchQuery', 'perPage'));
    }

    public function create()
    {
        $users = User::whereIn('role', ['admin_unit', 'kepala_unit'])->doesntHave('staff')->get();
        $units = Unit::all();
        $jabatans = Jabatan::all();
        return view('admin.kelolapengguna.staff.create', compact('users', 'units', 'jabatans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id|unique:staff,user_id',
            'unit_id' => 'required|exists:units,id',
            'jabatan_id' => 'required|exists:jabatan,id',
            'nik' => 'required|string|unique:staff,nik',
        ]);

        Staff::create($request->all());
        return redirect()->route('admin.staff.index')->with('success', 'Data staff berhasil ditambahkan.');
    }

    public function edit(Staff $staff)
    {
        $staff->load(['user', 'unit', 'jabatan']);
        $users = User::whereIn('role', ['admin_unit', 'kepala_unit'])->get();
        $units = Unit::all();
        $jabatans = Jabatan::all();

        return view('admin.kelolapengguna.staff.edit', compact('staff', 'users', 'units', 'jabatans'));
    }

    public function update(Request $request, Staff $staff)
    {
        // 1. Validasi data yang masuk
        $request->validate([
            // Aturan validasi untuk data user
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $staff->user_id,
            'role' => [
                'required',
                Rule::in(['super_admin', 'kepala_unit', 'admin_unit'])
            ],

            // Aturan validasi untuk data staff
            'unit_id' => 'required|exists:units,id',
            'jabatan_id' => 'required|exists:jabatan,id',
            'nik' => 'required|string|unique:staff,nik,' . $staff->id,
        ]);

        try {
            // 2. Gunakan DB Transaction untuk keamanan
            DB::transaction(function () use ($request, $staff) {
                // Update data di tabel users, termasuk role
                $staff->user()->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'role' => $request->role,
                ]);

                // Update data di tabel staff
                $staff->update([
                    'unit_id' => $request->unit_id,
                    'jabatan_id' => $request->jabatan_id,
                    'nik' => $request->nik,
                ]);
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memperbarui data: ' . $e->getMessage()]);
        }

        // 3. Redirect kembali dengan pesan sukses
        return redirect()->route('admin.staff.index')->with('success', 'Data staff berhasil diperbarui.');
    }

    public function destroy(Staff $staff)
    {
        $staff->delete();
        return redirect()->route('admin.staff.index')->with('success', 'Data staff berhasil dihapus.');
    }

    public function exportExcel(Request $request)
    {
        $staffIds = $request->input('selected_staff', []);
        $fileName = 'staff_data_' . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new StaffExport($staffIds), $fileName);
    }
}
