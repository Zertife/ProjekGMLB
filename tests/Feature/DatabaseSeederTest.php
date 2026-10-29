<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Item;
use App\Models\Loan;
use App\Models\ReturnLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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

        $this->assertSame(5, Item::where('code', 'ELK-002')->value('stock'));
        $this->assertSame(15, Item::where('code', 'LAB-002')->value('stock'));
        $this->assertSame(7, Item::where('code', 'OLR-001')->value('stock'));
    }

    public function test_user_seeder_can_be_rerun_and_keeps_demo_credentials_in_sync(): void
    {
        User::create([
            'name' => 'Old Admin Name',
            'email' => 'admin@inventaris.sch.id',
            'password' => Hash::make('old-password'),
            'role' => 'user',
        ]);

        $this->seed(\Database\Seeders\UserSeeder::class);
        $this->seed(\Database\Seeders\UserSeeder::class);

        $credentials = [
            'admin@inventaris.sch.id' => ['admin123', 'admin'],
            'user1@email.com' => ['user1234', 'user'],
            'user2@email.com' => ['user123!', 'user'],
        ];

        $this->assertSame(3, User::count());

        foreach ($credentials as $email => [$password, $role]) {
            $user = User::where('email', $email)->firstOrFail();

            $this->assertTrue(Hash::check($password, $user->password));
            $this->assertSame($role, $user->role);
        }
    }
}
