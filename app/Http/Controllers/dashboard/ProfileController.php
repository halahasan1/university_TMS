<?php

namespace App\Http\Controllers\dashboard;

use App\Models\User;
use Illuminate\View\View;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ProfileUpdateRequest;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */

     public function show($id): View
    {
        $user =User::with(['profile.department'])->findOrFail($id);

        return view('profile.show', compact('user'));
    }

    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => Auth::user(),
            'profile' => Auth::user()->profile,
            'departments' => Department::all(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {

        $user = $request->user();
        $profile = $user->profile;

        $profile->update($request->validated());

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads/profile-images', 'public');

            if ($profile->image) {
                // delete the old image
                Storage::disk('public')->delete($profile->image->path);
                $profile->image->update(['path' => $path]);
            } else {
                $profile->image()->create(['path' => $path]);
            }
        }

        Log::info('Request Data:', $request->all());
        Log::info('Has File:', [$request->hasFile('image')]);
        return Redirect::route('profile.show', ['id' => $request->user()->id])
        ->with('status', 'profile-updated');

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
