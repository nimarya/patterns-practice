<?php

namespace Tests\Feature\Commands;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetUsersListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_get_users_list_command_works(): void
    {
        $this->artisan('app:get-users-list')->assertSuccessful();
    }

    public function test_get_users_list_command_outputs_expected_table(): void
    {
        User::factory()->create(['name' => 'Taylor', 'email' => 'taylor@example.com']);
        User::factory()->create(['name' => 'Abigail', 'email' => 'abigail@example.com']);

        $this->artisan('app:get-users-list')
            ->expectsTable(
                ['Name', 'Email'],
                [
                    ['Taylor', 'taylor@example.com'],
                    ['Abigail', 'abigail@example.com'],
                ]
            );
    }
}
