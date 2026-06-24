<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Profile Customization
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            Update your profile bio, theme colors, and cover photo.
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- Bio -->
        <div>
            <x-input-label for="bio" value="Bio" />
            <x-text-input id="bio" name="bio" type="text" class="mt-1 block w-full" :value="old('bio', $user->bio)" />
            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
        </div>

        <!-- Theme -->
        <div>
            <x-input-label value="Theme" />

            @php
                // existing saved theme (JSON) decode karine current values
                // mate use kariye chhe — jo na hoy to defaults
                $current = $user->themeColors();

                $presets = [
                    'light' => ['bg' => '#f9fafb', 'surface' => '#ffffff', 'text' => '#111827', 'accent' => '#4f46e5'],
                    'dark'  => ['bg' => '#111827', 'surface' => '#1f2937', 'text' => '#f3f4f6', 'accent' => '#6366f1'],
                    'blue'  => ['bg' => '#eff6ff', 'surface' => '#ffffff', 'text' => '#1e3a8a', 'accent' => '#2563eb'],
                ];
            @endphp

         
            <div class="mt-2 flex gap-3">
                @foreach ($presets as $name => $colors)
                    <button
                        type="button"
                        onclick='applyPreset(@json($colors))'
                        class="flex items-center gap-2 px-3 py-1.5 border rounded-md text-sm text-gray-700 hover:bg-gray-50"
                    >
                        <span class="flex">
                            <span class="h-4 w-4 rounded-full border" style="background-color: {{ $colors['bg'] }};"></span>
                            <span class="h-4 w-4 rounded-full border -ml-1" style="background-color: {{ $colors['accent'] }};"></span>
                        </span>
                        {{ ucfirst($name) }}
                    </button>
                @endforeach
            </div>

            <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div>
                    <label for="theme_bg" class="block text-xs text-gray-600 mb-1">Background</label>
                    <input
                        type="color"
                        id="theme_bg"
                        name="theme_bg"
                        value="{{ old('theme_bg', $current['bg']) }}"
                        class="h-10 w-full rounded border border-gray-300 cursor-pointer"
                    />
                </div>

                <div>
                    <label for="theme_surface" class="block text-xs text-gray-600 mb-1">Surface</label>
                    <input
                        type="color"
                        id="theme_surface"
                        name="theme_surface"
                        value="{{ old('theme_surface', $current['surface']) }}"
                        class="h-10 w-full rounded border border-gray-300 cursor-pointer"
                    />
                </div>

                <div>
                    <label for="theme_text" class="block text-xs text-gray-600 mb-1">Text</label>
                    <input
                        type="color"
                        id="theme_text"
                        name="theme_text"
                        value="{{ old('theme_text', $current['text']) }}"
                        class="h-10 w-full rounded border border-gray-300 cursor-pointer"
                    />
                </div>

                <div>
                    <label for="theme_accent" class="block text-xs text-gray-600 mb-1">Accent</label>
                    <input
                        type="color"
                        id="theme_accent"
                        name="theme_accent"
                        value="{{ old('theme_accent', $current['accent']) }}"
                        class="h-10 w-full rounded border border-gray-300 cursor-pointer"
                    />
                </div>
            </div>

            <x-input-error class="mt-2" :messages="$errors->get('theme_bg')" />
            <x-input-error class="mt-2" :messages="$errors->get('theme_surface')" />
            <x-input-error class="mt-2" :messages="$errors->get('theme_text')" />
            <x-input-error class="mt-2" :messages="$errors->get('theme_accent')" />
        </div>

        <!-- Cover Photo -->
        <div>
            <x-input-label for="cover_photo" value="Cover Photo" />

            <input
                id="cover_photo"
                name="cover_photo"
                type="file"
                accept="image/png, image/jpeg, image/jpg"
                class="mt-1 block w-full text-sm text-gray-600"
                onchange="previewCoverPhoto(event)"
            />

            <img
                id="cover-photo-preview"
                src="{{ $user->cover_photo ? asset('storage/' . $user->cover_photo) : '' }}"
                class="mt-3 h-32 w-full object-cover rounded {{ $user->cover_photo ? '' : 'hidden' }}"
                alt="Cover photo preview"
            />

            <p class="mt-1 text-xs text-gray-500">JPEG, PNG — max 2MB.</p>

            <x-input-error class="mt-2" :messages="$errors->get('cover_photo')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p class="text-sm text-gray-600">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>

<script>
 
    function previewCoverPhoto(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('cover-photo-preview');

        if (!file) {
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }

  
    function applyPreset(colors) {
        document.getElementById('theme_bg').value = colors.bg;
        document.getElementById('theme_surface').value = colors.surface;
        document.getElementById('theme_text').value = colors.text;
        document.getElementById('theme_accent').value = colors.accent;
    }
</script>