<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Item;
use App\Models\Loan;
use App\Models\ReturnLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_provides_the_required_sample_records(): void
    {
        $this->seed();

        $this->assertSame(1, User::where('role', 'admin')->count());
        $this->assertSame(2, User::where('role', 'user')->count());
        $this->assertSame(3, Category::count());
        $this->assertGreaterThanOrEqual(10, Item::count());
        $this->assertSame(5, Loan::count());
        $this->assertSame(3, ReturnLog::count());
        $this->assertGreaterThanOrEqual(5, ActivityLog::count());
    }
}
