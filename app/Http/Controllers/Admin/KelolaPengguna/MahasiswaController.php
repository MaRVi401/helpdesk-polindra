<?php

namespace App\Http\Controllers\Admin\KelolaPengguna;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\User;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\KelolaPengguna\MahasiswaExport;
use Maatwebsite\Excel\Facades\Excel;

class MahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $data_mahasiswa = Mahasiswa::with(['user', 'programStudi'])
            ->orderBy('id', 'asc');
        return view('admin.kelolapengguna.mahasiswa.index', compact('data_mahasiswa'));
    }

    public function create()
    {
        $users = User::where('role', 'mahasiswa')->doesntHave('mahasiswa')->get();
        $programStudis = ProgramStudi::all();
        return view('admin.kelolapengguna.mahasiswa.create', compact('users', 'programStudis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id|unique:mahasiswa,user_id',
            'program_studi_id' => 'required|exists:program_studi,id',
            'nim' => 'required|string|unique:mahasiswa,nim',
            'tahun_masuk' => 'required|digits:4|integer|min:1900',
        ]);

        Mahasiswa::create($request->all());
        return redirect()->route('admin.mahasiswa.index')->with('success', 'Data mahasiswa berhasil ditambahkan.');
    }

    public function edit(Mahasiswa $mahasiswa)
    {
        $mahasiswa->load(['user', 'programStudi']);

        $users = User::where('role', 'mahasiswa')->get();
        $programStudis = ProgramStudi::all();

        // Buat daftar tahun secara dinamis (contoh: 15 tahun ke belakang)
        $years = [];
        $currentYear = date('Y');
        for ($year = $currentYear; $year >= $currentYear - 5; $year--) {
            $years[] = $year;
        }

        return view('admin.kelolapengguna.mahasiswa.edit', compact('mahasiswa', 'users', 'programStudis', 'years'));
    }

    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        // 1. Validasi data yang masuk
        $request->validate([
            // Aturan validasi untuk data user
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $mahasiswa->user_id,

            // Aturan validasi untuk data mahasiswa
            'program_studi_id' => 'required|exists:program_studi,id',
            'nim' => 'required|string|unique:mahasiswa,nim,' . $mahasiswa->id,
            'tahun_masuk' => 'required|digits:4|integer|min:1900',
        ]);

        try {
            // 2. Gunakan DB Transaction untuk keamanan
            DB::transaction(function () use ($request, $mahasiswa) {
                // Update data di tabel users
                $mahasiswa->user()->update([
                    'name' => $request->name,
                    'email' => $request->email,
                ]);

                // Update data di tabel mahasiswa
                $mahasiswa->update([
                    'program_studi_id' => $request->program_studi_id,
                    'nim' => $request->nim,
                    'tahun_masuk' => $request->tahun_masuk,
                ]);
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memperbarui data: ' . $e->getMessage()]);
        }

        // 3. Redirect kembali dengan pesan sukses
        return redirect()->route('admin.mahasiswa.index')->with('success', 'Data mahasiswa berhasil diperbarui.');
    }

    public function destroy(Mahasiswa $mahasiswa)
    {
        $mahasiswa->delete();
        return redirect()->route('admin.mahasiswa.index')->with('success', 'Data mahasiswa berhasil dihapus.');
    }

    // public function exportExcel(Request $request)
    // {
    //     $mahasiswaIds = $request->input('selected_mahasiswa', []);
    //     $fileName = 'mahasiswa_data_' . date('Y-m-d_H-i-s') . '.xlsx';
    //     return Excel::download(new MahasiswaExport($mahasiswaIds), $fileName);
    // }
}