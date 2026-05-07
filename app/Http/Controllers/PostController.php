<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user()->load('following');

        // SEARCH USERS (optional feature for UI)
        $search = $request->search;

        $posts = Post::with('user')
            ->when($search, function ($q) use ($search) {
                $q->where('content', 'like', "%$search%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', "%$search%")
                        ->orWhere('username', 'like', "%$search%");
                  });
            })
            ->latest()
            ->paginate(2); // ✅ pagination added

        return view('posts.index', compact('posts', 'user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:500'
        ]);

        auth()->user()->posts()->create([
            'content' => $request->content
        ]);

        return redirect()->back()->with('success', 'Post created successfully!');
    }

    // ✅ DELETE POST FUNCTION
    public function destroy(Post $post)
    {
        if ($post->user_id != auth()->id()) {
            return redirect()->back()->with('error', 'Unauthorized action!');
        }

        $post->delete();

        return redirect()->back()->with('success', 'Post deleted successfully!');
    }
}