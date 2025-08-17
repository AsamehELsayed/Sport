<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        return Inertia::render('customer/profile/Show', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'date_of_birth' => $user->date_of_birth,
                'address_line_1' => $user->address_line_1,
                'address_line_2' => $user->address_line_2,
                'city' => $user->city,
                'state' => $user->state,
                'postal_code' => $user->postal_code,
                'country' => $user->country,
                'gender' => $user->gender,
                'bio' => $user->bio,
                'profile_photo' => $user->profile_photo,
                'marketing_emails' => $user->marketing_emails,
                'order_updates' => $user->order_updates,
                'preferred_sports' => $user->preferred_sports,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
                'last_login_at' => $user->last_login_at,
            ],
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'address_line_1' => ['nullable', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'in:male,female,other,prefer_not_to_say'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'marketing_emails' => ['boolean'],
            'order_updates' => ['boolean'],
            'preferred_sports' => ['nullable', 'string', 'max:500'],
        ]);

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => ['required', 'image', 'max:1024'], // 1MB max
        ]);

        $user = Auth::user();

        // Delete old photo if exists
        if ($user->profile_photo) {
            \Storage::disk('public')->delete($user->profile_photo);
        }

        // Store new photo
        $path = $request->file('profile_photo')->store('profile-photos', 'public');

        $user->update(['profile_photo' => $path]);

        return back()->with('success', 'Profile photo updated successfully.');
    }

    public function deletePhoto()
    {
        $user = Auth::user();

        if ($user->profile_photo) {
            \Storage::disk('public')->delete($user->profile_photo);
            $user->update(['profile_photo' => null]);
        }

        return back()->with('success', 'Profile photo removed successfully.');
    }

    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = Auth::user();

        // Delete profile photo if exists
        if ($user->profile_photo) {
            \Storage::disk('public')->delete($user->profile_photo);
        }

        // Delete user account
        $user->delete();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Your account has been deleted successfully.');
    }
}
