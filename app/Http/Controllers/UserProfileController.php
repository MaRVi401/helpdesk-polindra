<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserProfileController extends Controller
{
    public function userProfile()
    {
        $user = Auth::user();
        return view('content.user-profile.profile', compact('user'));
    }

    public function userProfileSetting()
    {
        $user = Auth::user();
        return view('content.user-profile.setting', compact('user'));
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
            return back()->with('success', 'Profil berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}