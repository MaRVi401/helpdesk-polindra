<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserProfileController extends Controller
{
    public function userProfile()
    {
        $user = Auth::user();
        return view('content.user-profile.profile', compact('user'), ['pageConfigs' => $this->pageConfigs]);
    }

    public function setProfile()
    {
        $user = Auth::user();
        return view('content.user-profile.set-profile', compact('user'), ['pageConfigs' => $this->pageConfigs]);
    }

    public function userProfileUpdate(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'email_personal' => 'nullable|email|max:50',
            'no_wa' => 'nullable|string|max:13',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:800',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Update data user
            $user->name = $request->name;
            $user->email_personal = $request->email_personal;
            $user->no_wa = $request->no_wa;

            // Upload Avatar baru
            if ($request->hasFile('avatar')) {
                // Hapus avatar lama jika ada di storage
                if ($user->avatar && Storage::disk('public')->exists('avatar/' . $user->avatar)) {
                    Storage::disk('public')->delete('avatar/' . $user->avatar);
                }

                $avatar = $request->file('avatar');
                $avatarName = time() . '_' . $user->id . '.' . $avatar->getClientOriginalExtension();

                $avatar->storeAs('avatar', $avatarName, 'public');
                $user->avatar = $avatarName;
            }
            $user->save();
            return back()->with('success', 'Profil berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function setSecurity()
    {
        $user = Auth::user();
        return view('content.user-profile.set-security', compact('user'), ['pageConfigs' => $this->pageConfigs]);
    }

    public function userPasswordUpdate(Request $request)
    {
        $user = Auth::user();

        // Cek apakah password user sudah diset atau masih null
        $hasPassword = !is_null($user->password);
        if ($hasPassword) {
            $validator = Validator::make($request->all(), [
                'currentPassword' => 'required|string|min:8',
                'newPassword' => 'required|string|min:8',
                'confirmPassword' => 'required|string|min:8|same:newPassword',
            ], [
                'currentPassword.required' => 'Password saat ini wajib diisi',
                'currentPassword.min' => 'Password minimal 8 karakter',
                'newPassword.required' => 'Password baru wajib diisi',
                'newPassword.min' => 'Password minimal 8 karakter',
                'confirmPassword.required' => 'Konfirmasi password wajib diisi',
                'confirmPassword.min' => 'Password minimal 8 karakter',
                'confirmPassword.same' => 'Konfirmasi password tidak cocok',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'newPassword' => 'required|string|min:8',
                'confirmPassword' => 'required|string|min:8|same:newPassword',
            ], [
                'newPassword.required' => 'Password baru wajib diisi',
                'newPassword.min' => 'Password minimal 8 karakter',
                'confirmPassword.required' => 'Konfirmasi password wajib diisi',
                'confirmPassword.min' => 'Password minimal 8 karakter',
                'confirmPassword.same' => 'Konfirmasi password tidak cocok',
            ]);
        }

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Jika user sudah punya password, verifikasi current password
            if ($hasPassword) {
                if (!Hash::check($request->currentPassword, $user->password)) {
                    return back()->with('error', 'Password saat ini tidak sesuai.');
                }
            }
            // Update password baru
            $user->password = Hash::make($request->newPassword);
            $user->save();
            $message = $hasPassword ? 'Password berhasil diubah.' : 'Password berhasil diatur.';
            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}