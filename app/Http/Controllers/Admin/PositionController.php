<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource (Jabatan).
     */
    public function index()
    {
        // Ambil semua posisi (bisa diganti paginate jika needed)
        $data_positions = Jabatan::orderBy('created_at', 'asc')->get();

        // Sesuaikan ke folder view yang kamu pakai: resources/views/admin/position/index.blade.php
        return view('admin.position.index', compact('data_positions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.position.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_jabatan' => 'required|string|max:255|unique:jabatan,nama_jabatan',
        ], [
            'nama_jabatan.unique' => 'The position name already exists.',
            'nama_jabatan.required' => 'The position name field is required.',
        ]);

        // Simpan hanya field yang diperlukan
        Jabatan::create($request->only('nama_jabatan'));

        return redirect()->route('admin.position.index')
            ->with('success', 'Position successfully added.');
    }

    /**
     * Display the specified resource.
     *
     * Note: kamu tidak punya view show, jadi kita redirect ke index.
     * Jika nanti mau membuat show.blade.php, ubah return ini menjadi view('admin.position.show', ...)
     */
    public function show($id)
    {
        return redirect()->route('admin.position.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data_position = Jabatan::findOrFail($id);

        return view('admin.position.edit', compact('data_position'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data_position = Jabatan::findOrFail($id);

        $validated = $request->validate([
            'nama_jabatan' => [
                'required',
                'string',
                'max:255',
                Rule::unique('jabatan', 'nama_jabatan')->ignore($data_position->id),
            ],
        ], [
            'nama_jabatan.unique' => 'The position name already exists.',
            'nama_jabatan.required' => 'The position name field is required.',
        ]);

        $data_position->update($validated);

        return redirect()->route('admin.position.index')
            ->with('success', 'Position successfully updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data_position = Jabatan::findOrFail($id);

        // Jika model Jabatan punya relasi staff() maka pengecekan ini valid
        if (method_exists($data_position, 'staff') && $data_position->staff()->count() > 0) {
            return redirect()->route('admin.position.index')
                ->with('error', 'Jangan Dihapus! Data ini masih memiliki relasi dengan staff.');
        }

        try {
            $data_position->delete();
            return redirect()->route('admin.position.index')
                ->with('success', 'Position successfully deleted.');
        } catch (\Exception $e) {
            return redirect()->route('admin.position.index')
                ->with('error', 'An error occurred while deleting the data.');
        }
    }
}
