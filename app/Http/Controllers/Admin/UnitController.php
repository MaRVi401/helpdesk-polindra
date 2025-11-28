<?php

namespace App\Http\Controllers\Admin;
use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        $data_unit = Unit::with('kepalaUnit.user')->orderBy('created_at', 'asc')->get();
        return view('content.apps.admin.unit.list', compact('data_unit'));
    }

    public function create()
    {
        $data_staf = Staff::whereHas('user', function ($query) {
            $query->where('role', 'kepala_unit');
        })->with('user')->get();
        return view('content.apps.admin.unit.create', compact('data_staf'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_unit' => 'required|string|max:255|unique:units,nama_unit',
            'kepala_id' => 'nullable|exists:staff,id',
            'slug' => 'nullable|string|max:255|unique:units,slug',
        ], [
            'nama_unit.unique' => 'Nama unit ini sudah ada.',
            'slug.unique' => 'Slug ini sudah digunakan oleh unit lain.',
        ]);

        Unit::create($request->all());

        // Clear cache menu setelah unit baru dibuat
        Helpers::clearServiceMenuCache();
        return redirect()->route('unit.index')->with('success', 'Unit berhasil ditambahkan.');
    }

    public function show($id)
    {
        $data_unit = Unit::with('kepalaUnit.user')->findOrFail($id);
        return view('content.apps.admin.unit.show', compact('data_unit'));
    }

    public function edit($id)
    {
        $data_unit = Unit::findOrFail($id);
        $data_staf = Staff::whereHas('user', function ($query) {
            $query->where('role', 'kepala_unit');
        })->with('user')->get();
        return view('content.apps.admin.unit.edit', compact('data_unit', 'data_staf'));
    }

    public function update(Request $request, $id)
    {
        $data_unit = Unit::findOrFail($id);

        $validated = $request->validate([
            'nama_unit' => [
                'required',
                'string',
                'max:255',
                Rule::unique('units', 'nama_unit')->ignore($data_unit->id),
            ],
            'kepala_id' => 'nullable|exists:staff,id',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('units', 'slug')->ignore($data_unit->id),
            ],
        ], [
            'nama_unit.unique' => 'Nama unit ini sudah digunakan.',
            'slug.unique' => 'Slug ini sudah digunakan oleh unit lain.',
        ]);

        $data_unit->update($validated);

        // Clear cache menu setelah unit diupdate
        Helpers::clearServiceMenuCache();
        return redirect()->route('unit.index')->with('success', 'Unit berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $data_unit = Unit::findOrFail($id);
        if ($data_unit->staff()->count() > 0) {
            return redirect()->route('unit.index')->with('error', 'Unit ini masih memiliki staf terdaftar.');
        }

        try {
            $data_unit->delete();

            // Clear cache menu setelah unit dihapus
            Helpers::clearServiceMenuCache();
            return redirect()->route('unit.index')->with('success', 'Unit berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('unit.index')->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}