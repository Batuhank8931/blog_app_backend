<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller implements HasMiddleware
{

    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }

    public function index()
    {
        return Post::with('user')->latest()->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $fields = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        // Generate the slug from the title
        $fields['slug'] = $this->generateSlug($fields['title']);

        // Get the current user
        $user = $request->user();

        // Count the number of posts created by the user today
        $postCountToday = $user->posts()
            ->whereDate('created_at', now()->toDateString())
            ->count();

        // Check if the user has reached the maximum limit of 3 posts per day
        if ($postCountToday >= 3) {
            return response()->json([
                'message' => 'Max limit of posting a blog is 3 in a day.'
            ]);
        }

        // Create a new post with the validated data
        $post = $request->user()->posts()->create($fields);

        // Return the newly created post
        return response()->json(['post' => $post, 'user' => $post->user], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return ['post' => $post, 'user' => $post->user];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        Gate::authorize('modify', $post);

        // Validate the request data
        $fields = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $fields['slug'] = $this->generateSlug($fields['title']);

        $post->update($fields);

        return ['post' => $post, 'user' => $post->user];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        Gate::authorize('modify', $post);

        $post->delete();

        return ['message' => 'The post was deleted'];
    }

            // SLUG GENERATOR

    private function generateSlug($title) {
        $slug = strtolower($title);

        $slug = preg_replace('/[^\w]+/', '-', $slug);

        $slug = trim($slug, '-');

        return $slug;
    }
}
