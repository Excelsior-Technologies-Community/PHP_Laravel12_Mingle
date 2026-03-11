<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;

use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $user = auth()->user()->load('following'); // load following users
        $posts = Post::with('user')->latest()->get();
        return view('posts.index', compact('posts', 'user')); // pass $user to blade
    }

    public function store(Request $request)
    {
        $request->validate(['content' => 'required|string|max:500']);
        auth()->user()->posts()->create($request->all());
        return redirect()->back();
    }
}
