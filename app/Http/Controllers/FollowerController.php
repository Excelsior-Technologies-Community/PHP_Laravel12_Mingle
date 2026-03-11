<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class FollowerController extends Controller
{
    public function follow(User $user) {
        auth()->user()->following()->attach($user->id);
        return redirect()->back();
    }

    public function unfollow(User $user) {
        auth()->user()->following()->detach($user->id);
        return redirect()->back();
    }
}