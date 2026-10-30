<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DashboardAndReportTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'Bambang Sutrisno',
            'email' => 'admin@pens.ac.id',
            'role' => 'admin',
        ]);
    }

    public function test_dashboard_displays_stats_when_internal_api_is_available(): void
    {
        Http::fake([
            '*/api/stats' => Http::response([
                'total_buku' => 20,
                'total_anggota' => 15,
                'peminjaman_aktif' => 4,
            ], 200),
        ]);

        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
        $response->assertSee('20');
        $response->assertSee('Total Buku');
        $response->assertSee('15');
        $response->assertSee('Total Anggota');
        $response->assertSee('4');
        $response->assertSee('Peminjaman Aktif');
        $response->assertSee(route('loans.report'));
    }

    public function test_dashboard_falls_back_to_zeros_when_internal_api_fails(): void
    {
        Http::fake([
            '*/api/stats' => Http::response(null, 500),
        ]);

        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('0');
    }

    public function test_report_displays_loans_when_internal_api_is_available(): void
    {
        Http::fake([
            '*/api/loans*' => Http::response([
                'data' => [
                    [
                        'id' => 101,
                        'member' => ['id' => 1, 'nama' => 'Budi Santoso'],
                        'petugas' => ['id' => 2, 'name' => 'Siti Rahmawati'],
                        'tanggal_pinjam' => '2026-10-20',
                        'tanggal_kembali' => '2026-10-27',
                        'tanggal_dikembalikan' => null,
                        'status' => 'dipinjam',
                        'books' => [
                            ['id' => 1, 'judul' => 'Laskar Pelangi'],
                        ],
                    ],
                ],
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->user)->get(route('loans.report'));

        $response->assertStatus(200);
        $response->assertSee('Laporan Peminjaman');
        $response->assertSee('Budi Santoso');
        $response->assertSee('Siti Rahmawati');
        $response->assertSee('Laskar Pelangi');
        $response->assertSee('Dipinjam');
    }

    public function test_report_falls_back_to_empty_when_internal_api_fails(): void
    {
        Http::fake([
            '*/api/loans*' => Http::response(null, 500),
        ]);

        $response = $this->actingAs($this->user)->get(route('loans.report'));

        $response->assertStatus(200);
        $response->assertSee('Belum ada data peminjaman.');
    }
}
