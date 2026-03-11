<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Posts
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto p-4">

        <!-- Post Form -->
        <form action="/posts" method="POST">
            @csrf
            <textarea name="content" placeholder="What's on your mind?" class="w-full p-2 border rounded"></textarea>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded mt-2">Post</button>
        </form>

        <!-- Posts List -->
        <div class="mt-4">
            @foreach($posts as $post)
                <div class="border p-3 rounded mb-2 bg-white shadow-sm flex justify-between items-start">
                    <div>
                        <strong>{{ $post->user->name }}</strong> 
                        - <span class="text-gray-500 text-sm">{{ $post->created_at->diffForHumans() }}</span>
                        <p class="mt-1">{{ $post->content }}</p>
                    </div>

                    <!-- Follow / Unfollow Button -->
                    @if($user->id !== $post->user->id)
                        <div>
                            @if($user->following->contains($post->user->id))
                                <form action="/unfollow/{{ $post->user->id }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded text-sm">
                                        Unfollow
                                    </button>
                                </form>
                            @else
                                <form action="/follow/{{ $post->user->id }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded text-sm">
                                        Follow
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>