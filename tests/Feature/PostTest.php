<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use App\Models\Post;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class PostTest extends TestCase
{
    use RefreshDatabase, InteractsWithDatabase;

    public function test_it_can_list_posts()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        Post::factory()->count(5)->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/posts');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => [
                         'id',
                         'title',
                         'content',
                         'created_at',
                         'updated_at',
                     ],
                 ]);
    }

    public function test_it_can_show_a_post()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson("/api/posts/{$post->id}");

        $response->assertStatus(200)
             ->assertJson([
                 'post' => [
                     'id' => $post->id,
                     'user_id' => $post->user_id,
                     'title' => $post->title,
                     'slug' => $post->slug,
                     'content' => $post->content,
                     'created_at' => $post->created_at->toJSON(),
                     'updated_at' => $post->updated_at->toJSON(),
                     'user' => [
                         'id' => $post->user->id,
                         'name' => $post->user->name,
                         'email' => $post->user->email,
                         'email_verified_at' => $post->user->email_verified_at->toJSON(),
                         'created_at' => $post->user->created_at->toJSON(),
                         'updated_at' => $post->user->updated_at->toJSON(),
                     ],
                 ],
             ]);
    }

    public function test_it_can_create_a_post()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/posts', [
            'title' => 'Test Title',
            'content' => 'Test Content',
            'slug' => 'test-title',
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                    'post' => [
                        'title' => 'Test Title',
                        'content' => 'Test Content',
                        'slug' => 'test-title',
                    ]
                 ]);

        $this->assertDatabaseHas('posts', [
            'title' => 'Test Title',
            'content' => 'Test Content',
            'slug' => 'test-title',
        ]);
    }

    public function test_it_can_update_a_post()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->putJson("/api/posts/{$post->id}", [
            'title' => 'Updated Title',
            'content' => 'Updated Content',
            'slug' => 'updated-title',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'post' => [
                         'title' => 'Updated Title',
                         'content' => 'Updated Content',
                         'slug' => 'updated-title',
                     ]
                 ]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated Title',
            'content' => 'Updated Content',
            'slug' => 'updated-title',
        ]);
    }

    public function test_it_can_delete_a_post()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->deleteJson("/api/posts/{$post->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'The post was deleted',
                 ]);

        $this->assertModelMissing($post);
    }
}
