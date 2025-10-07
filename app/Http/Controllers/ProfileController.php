<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Form to complete the profile.
     */
    public function showCompletionForm()
    {
        // Get all study program data from the database to display in the dropdown
        $programStudi = ProgramStudi::orderBy('program_studi')->get();
        return view('auth.complete-profile', compact('programStudi'));
    }

    /**
     * Save completed profile data.
     */
    public function saveCompletionForm(Request $request)
    {
        $request->validate([
            'nim' => 'required|numeric|unique:mahasiswa,nim',
            'program_studi_id' => 'required|exists:program_studi,id',
            'tahun_masuk' => 'required|numeric|digits:4|min:2010',
        ]);

        $user = Auth::user();
        Mahasiswa::create([
            'user_id' => $user->id,
            'nim' => $request->nim,
            'program_studi_id' => $request->program_studi_id,
            'tahun_masuk' => $request->tahun_masuk,
        ]);

        // Remove the marker in the session so that it will not be redirected to this page again.
        $request->session()->forget('needs_profile_completion');

        return redirect('/dashboard')->with('success', 'Profil Anda berhasil dilengkapi!');
    }
}