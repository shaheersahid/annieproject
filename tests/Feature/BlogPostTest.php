<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BlogPostTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_scheduled_blog_post_with_date_and_time(): void
    {
        $admin = User::factory()->create();
        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $admin->assignRole($role);

        $response = $this->actingAs($admin)->post(route('admin.blog.store'), [
            'title' => 'Better Desk Comfort',
            'slug' => '',
            'excerpt' => 'Practical advice for a more comfortable desk.',
            'content' => '<h2>Start with posture</h2><p>Useful article content.</p>',
            'status' => 'published',
            'publish_date' => '2030-08-20',
            'publish_time' => '14:35',
            'meta_title' => 'Better Desk Comfort Guide',
        ]);

        $response->assertRedirect(route('admin.blog.index'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('blog_posts', [
            'title' => 'Better Desk Comfort',
            'slug' => 'better-desk-comfort',
            'status' => 'published',
            'published_at' => '2030-08-20 14:35:00',
        ]);

        $post = BlogPost::query()->where('title', 'Better Desk Comfort')->firstOrFail();
        $this->assertSame('Start with posture', $post->toc[0]['text']);
    }

    public function test_future_scheduled_post_is_hidden_from_public_blog(): void
    {
        $author = User::factory()->create();
        $post = BlogPost::create([
            'author_id' => $author->id,
            'title' => 'Future Guide',
            'content' => '<p>Not public yet.</p>',
            'status' => 'published',
            'published_at' => now()->addDay(),
        ]);

        $this->get(route('blog.index'))
            ->assertOk()
            ->assertDontSee('Future Guide');

        $this->get(route('blog.show', $post->slug))
            ->assertNotFound();
    }

    public function test_blog_editor_uses_local_rich_text_editor_and_separate_time_control(): void
    {
        $admin = User::factory()->create();
        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $admin->assignRole($role);

        $this->actingAs($admin)
            ->get(route('admin.blog.create'))
            ->assertOk()
            ->assertSee('admin/assets/libs/tinymce/tinymce.min.js', false)
            ->assertSee('name="publish_date"', false)
            ->assertSee('name="publish_time"', false)
            ->assertSee('name="content"', false);
    }
}
