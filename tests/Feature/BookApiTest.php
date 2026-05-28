<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test browsing books with pagination and search criteria.
     */
    public function test_anyone_can_list_books_paginated_and_searched(): void
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        Book::factory()->create(['title' => 'Learn Laravel in 30 Days', 'author' => 'Taylor']);
        Book::factory()->create(['title' => 'VueJS Fundamentals', 'author' => 'Evan']);

        // Check general search
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/books?search=Laravel');

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Learn Laravel in 30 Days'])
            ->assertJsonMissing(['title' => 'VueJS Fundamentals']);

        // Check pagination structure
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'author', 'cover_image', 'price', 'published_date', 'created_at', 'updated_at']
            ],
            'current_page',
            'per_page',
            'total'
        ]);
    }

    /**
     * Test viewing a specific book details.
     */
    public function test_anyone_can_view_single_book(): void
    {
        $book = Book::factory()->create();
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/books/' . $book->id);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', $book->title);
    }

    /**
     * Test user cannot create a book without JWT authentication.
     */
    public function test_unauthenticated_user_cannot_create_book(): void
    {
        $payload = [
            'title' => 'Refactoring',
            'author' => 'Martin Fowler',
            'price' => 49.99,
            'published_date' => '1999-03-09',
        ];

        $response = $this->postJson('/api/books', $payload);

        $response->assertStatus(401);
    }

    /**
     * Test authenticated user can successfully store a book.
     */
    public function test_authenticated_user_can_create_book(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $user = User::factory()->create();
        $token = auth('api')->login($user);

        $payload = [
            'title' => 'Refactoring',
            'author' => 'Martin Fowler',
            'cover_image' => \Illuminate\Http\UploadedFile::fake()->image('cover.jpg'),
            'price' => 49.99,
            'published_date' => '1999-03-09',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/books', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'Refactoring');

        $this->assertDatabaseHas('books', [
            'title' => 'Refactoring',
        ]);

        $coverImage = $response->json('data.cover_image');
        $relativePath = str_replace(url('storage') . '/', '', $coverImage);

        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($relativePath);
    }

    /**
     * Test updating a book.
     */
    public function test_authenticated_user_can_update_book(): void
    {
        $book = Book::factory()->create(['title' => 'Old Book Title']);
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        $payload = [
            'title' => 'New Awesome Title',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/books/' . $book->id, $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'New Awesome Title');

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => 'New Awesome Title',
        ]);
    }

    /**
     * Test soft deleting a book.
     */
    public function test_authenticated_user_can_soft_delete_book(): void
    {
        $book = Book::factory()->create();
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/books/' . $book->id);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Book successfully deleted.'
            ]);

        $this->assertSoftDeleted('books', [
            'id' => $book->id,
        ]);
    }
}
