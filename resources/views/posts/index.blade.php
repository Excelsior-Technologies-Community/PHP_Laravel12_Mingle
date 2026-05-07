<x-app-layout>

    <!-- HEADER -->
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800">
            Mingle Feed
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto p-4">

        <!-- SUCCESS ALERT -->
        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- ERROR ALERT -->
        @if(session('error'))
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- SEARCH -->
        <form method="GET" class="mb-4">
            <input
                type="text"
                name="search"
                placeholder="Search posts or users..."
                class="w-full border rounded-lg p-3"
            >
        </form>

        <!-- POST FORM -->
        <form action="/posts" method="POST" class="mb-6">
            @csrf

            <textarea
                name="content"
                rows="3"
                class="w-full border rounded-lg p-3"
                placeholder="What's on your mind?"
            ></textarea>

            <button
                type="submit"
                class="bg-blue-500 hover:bg-blue-600 text-white px-5 py-2 rounded-lg mt-2"
            >
                Post
            </button>
        </form>

        <!-- POSTS -->
        <div class="space-y-4">

            @forelse($posts as $post)

                <div class="bg-white shadow rounded-xl p-4 flex justify-between">

                    <div>
                        <h3 class="font-bold text-lg">
                            {{ $post->user->name }}
                        </h3>

                        <p class="text-sm text-gray-500 mb-2">
                            {{ $post->created_at->diffForHumans() }}
                        </p>

                        <p class="text-gray-700">
                            {{ $post->content }}
                        </p>
                    </div>

                    <!-- DELETE BUTTON -->
                    @if($post->user_id == auth()->id())

                        <form action="/posts/{{ $post->id }}" method="POST">

                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="text-red-500 hover:text-red-700 text-sm"
                            >
                                Delete
                            </button>

                        </form>

                    @endif

                </div>

            @empty

                <div class="bg-gray-100 p-4 rounded-lg text-center text-gray-500">
                    No posts found.
                </div>

            @endforelse

        </div>

        <!-- PAGINATION -->
        <div class="mt-6">
            {{ $posts->links() }}
        </div>

    </div>

</x-app-layout>