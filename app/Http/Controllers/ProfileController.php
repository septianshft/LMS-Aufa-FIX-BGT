<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's talent scouting settings.
     */
    public function updateTalent(Request $request): RedirectResponse
    {
        $request->validate([
            'available_for_scouting' => 'boolean',
            'hourly_rate' => 'nullable|numeric|min:0',
            'talent_bio' => 'nullable|string|max:1000',
            'portfolio_url' => 'nullable|url|max:255',
            'location' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'experience_level' => 'nullable|in:beginner,intermediate,advanced,expert',
        ]);

        $user = $request->user();
        $isOptingIn = $request->boolean('available_for_scouting');

        // Update talent fields
        $user->update([
            'available_for_scouting' => $isOptingIn,
            'hourly_rate' => $isOptingIn ? $request->hourly_rate : null,
            'talent_bio' => $isOptingIn ? $request->talent_bio : null,
            'portfolio_url' => $isOptingIn ? $request->portfolio_url : null,
            'location' => $isOptingIn ? $request->location : null,
            'phone' => $isOptingIn ? $request->phone : null,
            'experience_level' => $isOptingIn ? $request->experience_level : null,
            'is_active_talent' => $isOptingIn,
        ]);

        // Handle role assignment and Talent record
        if ($isOptingIn) {
            // Assign talent role if not already assigned
            if (!$user->hasRole('talent')) {
                $user->assignRole('talent');
            }

            // Create Talent record if it doesn't exist
            if (!$user->talent) {
                \App\Models\Talent::create([
                    'user_id' => $user->id,
                    'is_active' => true,
                ]);
            } else {
                $user->talent->update(['is_active' => true]);
            }
        } else {
            // Deactivate talent but keep the role for potential future re-enabling
            if ($user->talent) {
                $user->talent->update(['is_active' => false]);
            }
        }

        return Redirect::route('profile.edit')->with('status', 'talent-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
