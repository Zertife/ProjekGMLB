<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ActivityLog;
use App\Models\Item;
use App\Models\Loan;
use App\Models\ReturnLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventorySystemTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $user;
    protected $category;
    protected $item;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->user = User::factory()->create([
            'role' => 'user',
        ]);

        $this->category = Category::create([
            'name' => 'Elektronik',
        ]);

        $this->item = Item::create([
            'category_id' => $this->category->id,
            'name'        => 'Proyektor Epson',
            'code'        => 'PRJ-01',
            'stock'       => 5,
            'condition'   => 'baik',
        ]);
    }

    /**
     * Step 8: Dashboard Admin & User.
     */
    public function test_admin_and_user_can_access_dashboard()
    {
        $responseAdmin = $this->actingAs($this->admin)->get('/dashboard');
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertSee('Dashboard Admin');

        $responseUser = $this->actingAs($this->user)->get('/dashboard');
        $responseUser->assertStatus(200);
        $responseUser->assertSee('Dashboard Saya');
    }

    /**
     * Step 9: Manajemen User (Admin CRUD).
     */
    public function test_admin_can_manage_users()
    {
        // Akses daftar user
        $response = $this->actingAs($this->admin)->get('/users');
        $response->assertStatus(200);

        // Tambah user baru
        $responseStore = $this->actingAs($this->admin)->post('/users', [
            'name'                  => 'Budi Siswa',
            'email'                 => 'budi@sekolah.sch.id',
            'role'                  => 'user',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $responseStore->assertRedirect('/users');
        $this->assertDatabaseHas('users', ['email' => 'budi@sekolah.sch.id']);

        // Non-admin dilarang mengakses
        $responseForbidden = $this->actingAs($this->user)->get('/users');
        $responseForbidden->assertStatus(403);
    }

    /**
     * Step 10: CRUD Kategori.
     */
    public function test_admin_can_crud_categories()
    {
        $response = $this->actingAs($this->admin)->post('/categories', [
            'name' => 'Alat Olahraga',
        ]);
        $response->assertRedirect('/categories');
        $this->assertDatabaseHas('categories', ['name' => 'Alat Olahraga']);
    }

    /**
     * Step 11: CRUD Barang & Soft Delete.
     */
    public function test_item_crud_and_soft_delete()
    {
        $response = $this->actingAs($this->admin)->post('/items', [
            'category_id' => $this->category->id,
            'name'        => 'Laptop Asus',
            'code'        => 'LPT-01',
            'stock'       => 10,
            'condition'   => 'baik',
        ]);
        $response->assertRedirect('/items');
        $this->assertDatabaseHas('items', ['code' => 'LPT-01']);

        $item = Item::where('code', 'LPT-01')->first();
        $this->actingAs($this->admin)->delete("/items/{$item->id}");

        // Cek soft delete
        $this->assertSoftDeleted('items', ['id' => $item->id]);
    }

    /**
     * Step 12: Peminjaman Barang & Auto-kurangi Stok.
     */
    public function test_borrowing_reduces_stock_automatically()
    {
        $initialStock = $this->item->stock; // 5

        $response = $this->actingAs($this->user)->post('/loans', [
            'item_id'   => $this->item->id,
            'quantity'  => 2,
            'loan_date' => now()->toDateString(),
            'due_date'  => now()->addDays(3)->toDateString(),
        ]);

        $response->assertRedirect('/loans');

        // Pastikan record loan bertambah
        $this->assertDatabaseHas('loans', [
            'user_id'  => $this->user->id,
            'item_id'  => $this->item->id,
            'quantity' => 2,
            'status'   => 'dipinjam',
        ]);

        // Pastikan stok berkurang otomatis dari 5 menjadi 3
        $this->item->refresh();
        $this->assertEquals($initialStock - 2, $this->item->stock);
    }

    public function test_admin_can_record_a_good_return_and_restore_stock()
    {
        $loan = Loan::create([
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
            'quantity' => 2,
            'loan_date' => now()->subDays(2)->toDateString(),
            'due_date' => now()->addDays(5)->toDateString(),
            'status' => 'dipinjam',
        ]);
        $this->item->decrement('stock', 2);

        $this->actingAs($this->admin)->get('/return-logs/create')->assertOk();
        $response = $this->actingAs($this->admin)->post('/return-logs', [
            'loan_id' => $loan->id,
            'returned_at' => now()->toDateString(),
            'condition_on_return' => 'baik',
            'notes' => 'Kelengkapan lengkap.',
        ]);

        $response->assertRedirect('/return-logs');
        $this->assertDatabaseHas('return_logs', [
            'loan_id' => $loan->id,
            'condition_on_return' => 'baik',
            'notes' => 'Kelengkapan lengkap.',
        ]);
        $this->assertDatabaseHas('loans', ['id' => $loan->id, 'status' => 'dikembalikan']);
        $this->assertSame(5, $this->item->fresh()->stock);
        $this->assertDatabaseHas('activity_logs', ['action' => 'return_loan', 'user_id' => $this->admin->id]);

        $this->from('/return-logs/create')->actingAs($this->admin)->post('/return-logs', [
            'loan_id' => $loan->id,
            'returned_at' => now()->toDateString(),
            'condition_on_return' => 'baik',
        ])->assertSessionHasErrors('loan_id');
    }

    public function test_damaged_return_is_logged_without_adding_damaged_units_to_available_stock()
    {
        $loan = Loan::create([
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
            'quantity' => 1,
            'loan_date' => now()->subDays(2)->toDateString(),
            'due_date' => now()->addDays(5)->toDateString(),
            'status' => 'dipinjam',
        ]);
        $this->item->decrement('stock');

        $this->actingAs($this->admin)->post('/return-logs', [
            'loan_id' => $loan->id,
            'returned_at' => now()->toDateString(),
            'condition_on_return' => 'rusak berat',
            'notes' => 'Tidak layak digunakan.',
        ])->assertRedirect('/return-logs');

        $this->assertSame(4, $this->item->fresh()->stock);
        $this->assertDatabaseHas('return_logs', ['loan_id' => $loan->id, 'condition_on_return' => 'rusak berat']);
        $this->assertDatabaseHas('loans', ['id' => $loan->id, 'status' => 'dikembalikan']);
    }

    public function test_users_cannot_open_admin_logs_and_only_see_their_own_loans()
    {
        $otherUser = User::factory()->create(['name' => 'Peminjam Rahasia']);
        Loan::create([
            'user_id' => $otherUser->id,
            'item_id' => $this->item->id,
            'quantity' => 1,
            'loan_date' => now()->toDateString(),
            'due_date' => now()->addDays(2)->toDateString(),
            'status' => 'dipinjam',
        ]);

        $this->actingAs($this->user)->get('/activity-logs')->assertForbidden();
        $this->actingAs($this->user)->get('/return-logs')->assertForbidden();
        $this->actingAs($this->user)->get('/loans')->assertOk()->assertDontSee('Peminjam Rahasia');
        $this->actingAs($this->admin)->get('/activity-logs')->assertOk();
        $this->actingAs($this->admin)->get('/return-logs')->assertOk();
    }

    public function test_admin_cannot_delete_an_item_used_by_an_overdue_loan()
    {
        $this->item->update(['stock' => 3]);
        Loan::create([
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
            'quantity' => 2,
            'loan_date' => now()->subDays(10)->toDateString(),
            'due_date' => now()->subDays(3)->toDateString(),
            'status' => 'terlambat',
        ]);

        $this->actingAs($this->admin)->delete("/items/{$this->item->id}")
            ->assertRedirect('/items')
            ->assertSessionHas('error');

        $this->assertDatabaseHas('items', ['id' => $this->item->id, 'deleted_at' => null]);
    }

    public function test_category_and_user_deletion_preserve_inventory_history()
    {
        $this->item->delete();
        $this->actingAs($this->admin)->delete("/categories/{$this->category->id}")
            ->assertRedirect('/categories')
            ->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $this->category->id]);

        Loan::create([
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
            'quantity' => 1,
            'loan_date' => now()->subDays(4)->toDateString(),
            'due_date' => now()->subDays(2)->toDateString(),
            'status' => 'dikembalikan',
        ]);

        $this->actingAs($this->admin)->delete("/users/{$this->user->id}")
            ->assertRedirect('/users')
            ->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->user->id]);
    }

    public function test_admin_can_soft_delete_an_overdue_loan_and_restore_reserved_stock()
    {
        $this->item->update(['stock' => 3]);
        $loan = Loan::create([
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
            'quantity' => 2,
            'loan_date' => now()->subDays(10)->toDateString(),
            'due_date' => now()->subDays(3)->toDateString(),
            'status' => 'terlambat',
        ]);

        $this->actingAs($this->admin)->delete("/loans/{$loan->id}")->assertRedirect('/loans');

        $this->assertSoftDeleted('loans', ['id' => $loan->id]);
        $this->assertSame(5, $this->item->fresh()->stock);
    }

    public function test_list_pages_support_search_filters_and_pagination()
    {
        foreach (range(1, 12) as $number) {
            Category::create(['name' => "Kategori {$number}"]);
        }

        $this->actingAs($this->admin)->get('/categories')->assertOk()->assertSee('page=2');
        $this->actingAs($this->admin)->get('/categories?search=Elektronik')->assertOk()->assertSee('Elektronik')->assertDontSee('Kategori 1</span>');
        $this->actingAs($this->admin)->get('/items?search=PRJ-01&condition=baik')->assertOk()->assertSee('PRJ-01');
        $this->actingAs($this->admin)->get('/users?role=user')->assertOk()->assertSee($this->user->name);
    }

    public function test_admin_can_create_a_loan_for_a_user_but_users_cannot_access_admin_write_routes()
    {
        $this->actingAs($this->admin)->post('/loans', [
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
            'quantity' => 2,
            'loan_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
        ])->assertRedirect('/loans');

        $this->assertSame(3, $this->item->fresh()->stock);
        $this->assertDatabaseHas('loans', ['user_id' => $this->user->id, 'item_id' => $this->item->id, 'quantity' => 2]);

        $this->actingAs($this->user)->get('/items/create')->assertForbidden();
        $this->actingAs($this->user)->post('/categories', ['name' => 'Tidak Diizinkan'])->assertForbidden();
        $this->actingAs($this->user)->post('/return-logs', [])->assertForbidden();
    }

    public function test_activity_and_return_log_tables_are_searchable_and_paginated()
    {
        foreach (range(1, 16) as $number) {
            ActivityLog::create([
                'user_id' => $this->admin->id,
                'action' => $number === 16 ? 'special_audit' : 'view_dashboard',
                'description' => "Aktivitas audit {$number}",
            ]);
        }

        $this->actingAs($this->admin)->get('/activity-logs')->assertOk()->assertSee('page=2');
        $this->actingAs($this->admin)->get('/activity-logs?search=special_audit')->assertOk()->assertSee('special_audit')->assertDontSee('view_dashboard');

        foreach (range(1, 11) as $number) {
            $loan = Loan::create([
                'user_id' => $this->user->id,
                'item_id' => $this->item->id,
                'quantity' => 1,
                'loan_date' => now()->subDays(2)->toDateString(),
                'due_date' => now()->subDay()->toDateString(),
                'status' => 'dikembalikan',
            ]);
            ReturnLog::create([
                'loan_id' => $loan->id,
                'returned_at' => now()->subDay()->toDateString(),
                'condition_on_return' => $number === 11 ? 'rusak ringan' : 'baik',
                'notes' => "Pemeriksaan {$number}",
            ]);
        }

        $this->actingAs($this->admin)->get('/return-logs')->assertOk()->assertSee('page=2');
        $this->actingAs($this->admin)->get('/return-logs?condition=rusak%20ringan')->assertOk()->assertSee('Pemeriksaan 11')->assertDontSee('Pemeriksaan 10');
    }

    public function test_invalid_loan_request_returns_validation_error_without_changing_stock()
    {
        $this->actingAs($this->user)->post('/loans', [
            'item_id' => $this->item->id,
            'quantity' => 6,
            'loan_date' => now()->toDateString(),
            'due_date' => now()->addDays(2)->toDateString(),
        ])->assertSessionHasErrors('quantity');

        $this->assertSame(5, $this->item->fresh()->stock);
        $this->assertDatabaseCount('loans', 0);
    }
}
