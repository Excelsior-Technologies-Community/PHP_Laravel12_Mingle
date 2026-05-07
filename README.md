# PHP_Laravel12_Mingle

## Introduction

This project is a Laravel 12-based social platform that demonstrates how to build a basic social networking application. Users can register, create profiles, post status updates, and interact with other users through following and unfollowing functionality. The platform provides a simple, intuitive interface using modern frontend tools, making it easy to view a feed of posts and manage user interactions.

The project is built with PHP 8.2 and MySQL, and follows best practices in Laravel development, including MVC architecture, Eloquent ORM for database management, Blade templates for the frontend, and Tailwind CSS for styling. It serves as a practical example for learning how social features can be implemented in a web application.

---

## Project Overview

The project consists of the following key features:

#### 1) User Registration and Authentication

- Users can sign up, log in, and manage their profiles. Authentication is secure and handled using Laravel’s built-in tools.

#### 2) Profile Management

- Each user can set a username and optional profile photo. This allows other users to identify them in the platform and interact through posts and follow relationships.

#### 3) Posts

- Authenticated users can create text-based posts. Posts are displayed in a feed in reverse chronological order, showing the latest updates first.

#### 4) Followers System

- Users can follow or unfollow other users. The feed can show posts from all users or be extended to show posts only from followed users.

#### 5) Follow/Unfollow Buttons

- The platform intelligently displays follow/unfollow buttons for other users while hiding them on the current user’s own posts, providing a natural social interaction workflow.

#### 6) Responsive Frontend

- Blade templates combined with Tailwind CSS provide a clean, responsive, and visually appealing interface, ensuring usability across devices.

#### 7) Database Structure

The project uses three primary tables:

- users — stores user account information, username, and profile photo

- posts — stores posts created by users

- followers — stores relationships between followers and the users they follow

---

# Project Setup

## Step 1: Create Laravel Project

Open your terminal and run:

```bash
composer create-project laravel/laravel PHP_Laravel12_Mingle "12.*"
cd PHP_Laravel12_Mingle
```

This will create a new Laravel 12 project named PHP_Laravel12_Mingle.

---

## Step 2: Set Up Database

Edit .env to configure your database:

```.env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel12_mingle
DB_USERNAME=root
DB_PASSWORD=
```

Then Run Migration Command:

```bash
php artisan migrate
```
---

## Step 3: Install Dependencies

Install Laravel Breeze for authentication scaffolding:

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install
npm run dev
```

This sets up authentication routes, controllers, and Blade templates.

---

## Step 4: Migration Table

### Add username and profile_photo_path Columns to Users Table

Create a new migration:

```bash
php artisan make:migration add_username_and_profile_to_users_table --table=users
```

In the new migration file:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('name');
            $table->string('profile_photo_path')->nullable()->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'profile_photo_path']);
        });
    }
};
```

---

### Create Posts Table

```bash
php artisan make:migration create_posts_table --create=posts
```

Add fields in database/migrations/xxxx_create_posts_table.php:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
```

---

### Create Followers Table

```bash
php artisan make:migration create_followers_table --create=followers
```
Add fields:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('followers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('follower_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('followers');
    }
};
```

Run all migrations:

```bash
php artisan migrate
```
---

## Step 5: Models

### User Model 

File: app/Models/User.php

```php
<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username', // Added for Mingle
        'email',
        'password',
        'profile_photo_path', // Added for profile photos
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relationships
     */

    // One-to-Many: User has many posts
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    // Many-to-Many: Users who follow this user
    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id');
    }

    // Many-to-Many: Users this user is following
    public function following()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id');
    }
}
```

### Post Model

File: app/Models/Post.php

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'content'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
```

---

## Step 6: Controllers

### PostController

```bash
php artisan make:controller PostController
```
In app/Http/Controllers/PostController.php:

```php
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
```

### FollowerController

```bash
php artisan make:controller FollowerController
```
In app/Http/Controllers/FollowerController.php:

```php
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
```

### RegisteredUserController.php

In app/Http/Controllers/Auth/RegisteredUserController.php:

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:'.User::class], // Added
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username, // Added
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
```

---

## Step 7: Routes 

File: routes/web.php

```php
use App\Http\Controllers\PostController;
use App\Http\Controllers\FollowerController;

Route::get('/', [PostController::class, 'index'])->middleware(['auth']);
Route::post('/posts', [PostController::class, 'store'])->middleware(['auth']);
Route::post('/follow/{user}', [FollowerController::class, 'follow'])->middleware(['auth']);
Route::post('/unfollow/{user}', [FollowerController::class, 'unfollow'])->middleware(['auth']);

require __DIR__.'/auth.php';
```

---

## Step 8: Blade Views

### index.blade.php

File: resources/views/posts/index.blade.php

```blade
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
```

### register.blade.php

File: resources/views/auth/register.blade.php

```blade
<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Username -->
        <div class="mt-4">
            <x-input-label for="username" :value="__('Username')" />
            <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
```
---

## Step 9: Test Project

### Terminal 1 — Run Laravel Development Server:

```bash
php artisan serve
```

### Terminal 2 — Run Frontend Asset Compilation (Vite/NPM):

```bash
npm run dev
```

You can now register a user, post status updates, follow/unfollow other users, and see posts in the feed.

1) Register User 1 → create some posts.

- Follow/unfollow buttons: won’t show (only their own posts).

2) Register User 2 → log in → visit /

- Follow/unfollow buttons: will show for posts by User 1

3) User 2 clicks Follow → button changes to Unfollow

4) Log back in as User 1 → visit /

- Now User 1 can see posts by User 2 → Follow/Unfollow buttons appear


## Output

<img width="1919" height="1026" alt="Screenshot 2026-03-11 170412" src="https://github.com/user-attachments/assets/625ebd93-f79c-4d1c-b51c-390dfa1699ed" />

<img width="1919" height="1031" alt="Screenshot 2026-03-11 174226" src="https://github.com/user-attachments/assets/06210c6e-0145-495f-8f6a-448d72e40707" />

<img width="1919" height="1030" alt="Screenshot 2026-03-11 180047" src="https://github.com/user-attachments/assets/cb4ccd87-aaa2-48bb-95fa-457b8e4f06f6" />

---

## Project Structure

```
PHP_Laravel12_Mingle/
├── app/
│   ├── Models/
│   │   ├── User.php
│   │   └── Post.php
│   └── Http/Controllers/
│       ├── PostController.php
│       ├── FollowerController.php
│       └── Auth/
│           └── RegisteredUserController.php
├── database/
│   ├── migrations/
│   │   ├── 2014_10_12_000000_create_users_table.php
│   │   ├── 2014_10_12_100000_create_password_resets_table.php
│   │   ├── 2026_03_11_000001_create_posts_table.php
│   │   ├── 2026_03_11_000002_create_followers_table.php
│   │   └── 2026_03_11_000003_add_username_and_profile_to_users_table.php
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php
│       ├── posts/
│       │   └── index.blade.php
│       └── auth/
│           └── register.blade.php
├── routes/
│   └── web.php
├── public/
├── .env
└── package.json
```

---

Your PHP_Laravel12_Mingle Project is now ready!
<<<<<<< HEAD

=======
>>>>>>> development
