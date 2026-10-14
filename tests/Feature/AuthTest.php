<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_protected_routes(): void
    {
        $response = $this->get('/books');
        $response->assertRedirect('/login');

        $response = $this->get('/categories');
        $response->assertRedirect('/login');

        $response = $this->get('/profil');
        $response->assertRedirect('/login');
    }

    public function test_guest_can_view_login_page(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Login Perpustakaan');
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'admin@pens.ac.id',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@pens.ac.id',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_succeeds_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@pens.ac.id',
            'name' => 'Admin Perpustakaan',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@pens.ac.id',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('books.index'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/login');
        $response->assertSessionHas('success', 'Logout berhasil.');
        $this->assertGuest();
    }

    public function test_admin_can_access_categories(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/categories');
        $response->assertStatus(200);
    }

    public function test_petugas_cannot_access_categories_and_gets_403(): void
    {
        $petugas = User::factory()->create([
            'role' => 'petugas',
        ]);

        $response = $this->actingAs($petugas)->get('/categories');
        $response->assertStatus(403);
    }

    public function test_navbar_shows_categories_only_for_admin(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin Test',
            'role' => 'admin',
        ]);

        $petugas = User::factory()->create([
            'name' => 'Petugas Test',
            'role' => 'petugas',
        ]);

        $responseAdmin = $this->actingAs($admin)->get('/books');
        $responseAdmin->assertSee('Kategori');
        $responseAdmin->assertSee('Admin Test (Admin)');

        $responsePetugas = $this->actingAs($petugas)->get('/books');
        $responsePetugas->assertDontSee('<li><a href="http://localhost/categories"', false);
        $responsePetugas->assertSee('Petugas Test (Petugas)');
    }

    public function test_loan_store_automatically_assigns_authenticated_user_id(): void
    {
        $petugas = User::factory()->create([
            'name' => 'Petugas Satu',
            'role' => 'petugas',
        ]);

        $category = Category::create([
            'nama_kategori' => 'Teknologi',
            'deskripsi' => 'Buku-buku teknologi',
        ]);

        $member = Member::create([
            'nama' => 'Budi Santoso',
            'nim' => '1234567890',
            'email' => 'budi@test.local',
            'nomor_telepon' => '081234567890',
            'alamat' => 'Surabaya',
            'status' => 'aktif',
        ]);

        $book = Book::create([
            'judul' => 'Pemrograman Web Modern',
            'penulis' => 'Penulis Satu',
            'penerbit' => 'Penerbit PENS',
            'tahun_terbit' => 2024,
            'isbn' => '978-602-0000-00-1',
            'stok' => 5,
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($petugas)->get('/loans/create');
        $response->assertStatus(200);
        $response->assertSee('Petugas pencatat: Petugas Satu (otomatis dari akun yang login).');
        $response->assertDontSee('<select name="user_id"', false);

        $postResponse = $this->actingAs($petugas)->post('/loans', [
            'member_id' => $member->id,
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_kembali' => now()->addDays(7)->toDateString(),
            'book_ids' => [$book->id],
        ]);

        $postResponse->assertRedirect(route('loans.index'));
        $this->assertDatabaseHas('loans', [
            'member_id' => $member->id,
            'user_id' => $petugas->id,
        ]);
    }

    public function test_profile_page_displays_user_info(): void
    {
        $user = User::factory()->create([
            'name' => 'Budi Petugas',
            'email' => 'budi@pens.ac.id',
            'role' => 'petugas',
        ]);

        $response = $this->actingAs($user)->get('/profil');
        $response->assertStatus(200);
        $response->assertSee('Budi Petugas');
        $response->assertSee('budi@pens.ac.id');
        $response->assertSee('Petugas');
    }

    public function test_password_update_validates_old_password_and_updates(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        // Wrong current password
        $failResponse = $this->actingAs($user)->put('/profil/password', [
            'current_password' => 'wrong-old-password',
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123',
        ]);
        $failResponse->assertSessionHasErrors('current_password');

        // Mismatched confirmation
        $mismatchResponse = $this->actingAs($user)->put('/profil/password', [
            'current_password' => 'old-password',
            'password' => 'new-password123',
            'password_confirmation' => 'different-password',
        ]);
        $mismatchResponse->assertSessionHasErrors('password');

        // Too short (< 8 chars)
        $shortResponse = $this->actingAs($user)->put('/profil/password', [
            'current_password' => 'old-password',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);
        $shortResponse->assertSessionHasErrors('password');

        // Valid update
        $successResponse = $this->actingAs($user)->put('/profil/password', [
            'current_password' => 'old-password',
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123',
        ]);

        $successResponse->assertRedirect('/profil');
        $successResponse->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue(Hash::check('new-password123', $user->password));
    }
}
