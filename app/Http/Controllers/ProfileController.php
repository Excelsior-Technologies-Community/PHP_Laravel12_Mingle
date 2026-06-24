<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validated();
        $themeKeys = ['theme_bg', 'theme_surface', 'theme_text', 'theme_accent'];

        $hasThemeInput = collect($themeKeys)->some(fn ($key) => $request->has($key));

        if ($hasThemeInput) {
            $existingTheme = $user->theme ? json_decode($user->theme, true) : [];
            $existingTheme = is_array($existingTheme) ? $existingTheme : [];

            $newTheme = [
                'bg'      => $data['theme_bg'] ?? $existingTheme['bg'] ?? '#f9fafb',
                'surface' => $data['theme_surface'] ?? $existingTheme['surface'] ?? '#ffffff',
                'text'    => $data['theme_text'] ?? $existingTheme['text'] ?? '#111827',
                'accent'  => $data['theme_accent'] ?? $existingTheme['accent'] ?? '#4f46e5',
            ];

            $user->theme = json_encode($newTheme);
        }

        $data = array_diff_key($data, array_flip($themeKeys));

        $user->fill(array_filter($data, function ($value) {
            return $value !== null;
        }));

        if ($request->hasFile('cover_photo')) {

            if ($user->cover_photo) {
                Storage::disk('public')->delete($user->cover_photo);
            }

            $user->cover_photo = $request->file('cover_photo')->store('covers', 'public');
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        if ($user->cover_photo) {
            Storage::disk('public')->delete($user->cover_photo);
        }

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}