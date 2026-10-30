<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    protected Category $category;
    protected User $user;
    protected Member $member;
    protected Book $book;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::create([
            'nama_kategori' => 'Teknologi',
            'deskripsi' => 'Buku-buku teknologi informasi',
        ]);

        $this->user = User::factory()->create([
            'name' => 'Petugas Perpustakaan',
            'email' => 'petugas@perpus.test',
            'role' => 'petugas',
        ]);

        $this->member = Member::create([
            'nama' => 'Budi Santoso',
            'nim' => '2024001',
            'email' => 'budi@student.test',
            'nomor_telepon' => '081234567890',
            'alamat' => 'Jl. Merdeka No. 10',
            'status' => 'aktif',
        ]);

        $this->book = Book::create([
            'judul' => 'Belajar Laravel 12',
            'penulis' => 'Taylor Otwell',
            'penerbit' => 'Laravel LLC',
            'tahun_terbit' => 2025,
            'isbn' => '978-1234567890',
            'stok' => 10,
            'category_id' => $this->category->id,
        ]);
    }

    public function test_get_books_returns_paginated_resource(): void
    {
        $response = $this->getJson('/api/books');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'judul',
                        'penulis',
                        'penerbit',
                        'tahun_terbit',
                        'isbn',
                        'stok',
                        'sampul',
                        'category' => [
                            'id',
                            'nama_kategori',
                        ],
                        'created_at',
                        'updated_at',
                    ],
                ],
                'links' => ['first', 'last', 'prev', 'next'],
                'meta' => ['current_page', 'from', 'last_page', 'links', 'path', 'per_page', 'to', 'total'],
            ]);
    }

    public function test_get_books_with_filters(): void
    {
        Book::create([
            'judul' => 'Pemrograman Python',
            'penulis' => 'Guido van Rossum',
            'penerbit' => 'Python Org',
            'tahun_terbit' => 2024,
            'isbn' => '978-0987654321',
            'stok' => 5,
            'category_id' => $this->category->id,
        ]);

        $searchResponse = $this->getJson('/api/books?judul=Laravel');
        $searchResponse->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.judul', 'Belajar Laravel 12');

        $catResponse = $this->getJson('/api/books?category_id='.$this->category->id);
        $catResponse->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_post_books_creates_book_with_status_201(): void
    {
        $payload = [
            'judul' => 'Mastering API Design',
            'penulis' => 'Arnaud Lauret',
            'penerbit' => 'Manning',
            'tahun_terbit' => 2024,
            'isbn' => '978-1617295850',
            'stok' => 7,
            'category_id' => $this->category->id,
        ];

        $response = $this->postJson('/api/books', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.judul', 'Mastering API Design')
            ->assertJsonPath('data.category.id', $this->category->id);

        $this->assertDatabaseHas('books', ['judul' => 'Mastering API Design']);
    }

    public function test_post_books_validation_fails_with_status_422(): void
    {
        $response = $this->postJson('/api/books', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['judul', 'penulis', 'penerbit', 'tahun_terbit', 'stok', 'category_id']);
    }

    public function test_get_single_book(): void
    {
        $response = $this->getJson('/api/books/'.$this->book->id);

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $this->book->id)
            ->assertJsonPath('data.judul', 'Belajar Laravel 12')
            ->assertJsonPath('data.category.nama_kategori', 'Teknologi');
    }

    public function test_get_single_book_not_found(): void
    {
        $response = $this->getJson('/api/books/99999');

        $response->assertStatus(404);
    }

    public function test_update_book(): void
    {
        $payload = [
            'judul' => 'Belajar Laravel 12 Pro Edition',
            'penulis' => 'Taylor Otwell',
            'penerbit' => 'Laravel LLC',
            'tahun_terbit' => 2025,
            'isbn' => '978-1234567890',
            'stok' => 15,
            'category_id' => $this->category->id,
        ];

        $response = $this->putJson('/api/books/'.$this->book->id, $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.judul', 'Belajar Laravel 12 Pro Edition')
            ->assertJsonPath('data.stok', 15);

        $this->assertDatabaseHas('books', [
            'id' => $this->book->id,
            'judul' => 'Belajar Laravel 12 Pro Edition',
            'stok' => 15,
        ]);
    }

    public function test_delete_book(): void
    {
        $response = $this->deleteJson('/api/books/'.$this->book->id);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Buku berhasil dihapus.']);

        $this->assertDatabaseMissing('books', ['id' => $this->book->id]);
    }

    public function test_get_members_returns_paginated_resource(): void
    {
        $response = $this->getJson('/api/members');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'nama',
                        'nim',
                        'email',
                        'nomor_telepon',
                        'alamat',
                        'status',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'links',
                'meta',
            ])
            ->assertJsonPath('data.0.nama', 'Budi Santoso');
    }

    public function test_get_single_member(): void
    {
        $response = $this->getJson('/api/members/'.$this->member->id);

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $this->member->id)
            ->assertJsonPath('data.nama', 'Budi Santoso');
    }

    public function test_post_loans_creates_loan_and_items(): void
    {
        $payload = [
            'member_id' => $this->member->id,
            'user_id' => $this->user->id,
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_kembali' => now()->addDays(7)->toDateString(),
            'book_ids' => [$this->book->id],
        ];

        $response = $this->postJson('/api/loans', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.member.id', $this->member->id)
            ->assertJsonPath('data.petugas.id', $this->user->id)
            ->assertJsonPath('data.status', 'dipinjam')
            ->assertJsonPath('data.books.0.id', $this->book->id);

        $this->assertDatabaseHas('loans', [
            'member_id' => $this->member->id,
            'user_id' => $this->user->id,
            'status' => 'dipinjam',
        ]);
    }

    public function test_post_loans_validation_fails(): void
    {
        $response = $this->postJson('/api/loans', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['member_id', 'user_id', 'tanggal_pinjam', 'tanggal_kembali', 'book_ids']);
    }

    public function test_get_loans_returns_collection_with_relationships(): void
    {
        $loan = Loan::create([
            'member_id' => $this->member->id,
            'user_id' => $this->user->id,
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_kembali' => now()->addDays(7)->toDateString(),
            'status' => 'dipinjam',
        ]);
        $loan->loanItems()->create(['book_id' => $this->book->id]);

        $response = $this->getJson('/api/loans');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'member' => ['id', 'nama'],
                        'petugas' => ['id', 'name'],
                        'tanggal_pinjam',
                        'tanggal_kembali',
                        'tanggal_dikembalikan',
                        'status',
                        'books' => [
                            '*' => ['id', 'judul'],
                        ],
                    ],
                ],
                'links',
                'meta',
            ]);
    }

    public function test_kembalikan_loan_updates_status(): void
    {
        $loan = Loan::create([
            'member_id' => $this->member->id,
            'user_id' => $this->user->id,
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_kembali' => now()->addDays(7)->toDateString(),
            'status' => 'dipinjam',
        ]);
        $loan->loanItems()->create(['book_id' => $this->book->id]);

        $response = $this->putJson('/api/loans/'.$loan->id.'/kembalikan');

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $loan->id)
            ->assertJsonPath('data.status', 'dikembalikan')
            ->assertJsonPath('data.tanggal_dikembalikan', now()->toDateString());

        $this->assertDatabaseHas('loans', [
            'id' => $loan->id,
            'status' => 'dikembalikan',
            'tanggal_dikembalikan' => now()->toDateString(),
        ]);
    }

    public function test_get_stats_returns_counts(): void
    {
        Loan::create([
            'member_id' => $this->member->id,
            'user_id' => $this->user->id,
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_kembali' => now()->addDays(7)->toDateString(),
            'status' => 'dipinjam',
        ]);

        $response = $this->getJson('/api/stats');

        $response->assertStatus(200)
            ->assertExactJson([
                'total_buku' => 1,
                'total_anggota' => 1,
                'peminjaman_aktif' => 1,
            ]);
    }
}
