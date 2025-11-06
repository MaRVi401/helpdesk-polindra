<?php

namespace App\Http\Controllers\AdminUnit;

use App\Models\Layanan;
use App\Models\Unit; 
use App\Models\Staff;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LayananController extends Controller
{

    /**
     * Tampilkan daftar SEMUA layanan, dengan filter.
     */
    public function index(Request $request)
    {
        
        $query = Layanan::with('unit')->latest();

        // Terapkan filter 
        if ($request->filled('id_unit')) {
            $query->where('id_unit', $request->id_unit);
        }

        $layanan = $query->paginate(10)->withQueryString();
        $units = Unit::orderBy('nama_unit', 'asc')->get();
        
        return view('admin_unit.layanan.index', compact('layanan', 'units'));
    }

    /**
     * Tampilkan form untuk membuat layanan baru.
     */
    public function create()
    {
        $units = Unit::orderBy('nama_unit', 'asc')->get();
        return view('admin_unit.layanan.create', compact('units'));
    }

    /**
     * Simpan layanan baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_unit' => 'required|exists:units,id', 
            'nama_layanan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:Aktif,Tidak Aktif',
        ]);

        Layanan::create($request->all()); 

        return redirect()->route('admin_unit.layanan.index')->with('success', 'Layanan berhasil ditambahkan.');
    }

    /**
     * Tampilkan form untuk mengedit layanan.
     */
    public function edit($id)
    {
        $layanan = Layanan::findOrFail($id); 
        $units = Unit::orderBy('nama_unit', 'asc')->get();
        
        return view('admin_unit.layanan.edit', compact('layanan', 'units'));
    }

    /**
     * Update layanan di database.
     */
    public function update(Request $request, $id)
    {
        $layanan = Layanan::findOrFail($id); 

        $request->validate([
            'id_unit' => 'required|exists:units,id', 
            'nama_layanan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:Aktif,Tidak Aktif',
        ]);

        $layanan->update($request->all());

        return redirect()->route('admin_unit.layanan.index')->with('success', 'Layanan berhasil diperbarui.');
    }

    /**
     * Hapus layanan dari database.
     */
    public function destroy($id)
    {
        $layanan = Layanan::findOrFail($id); 
        $layanan->delete();

        return redirect()->route('admin_unit.layanan.index')->with('success', 'Layanan berhasil dihapus.');
    }
}