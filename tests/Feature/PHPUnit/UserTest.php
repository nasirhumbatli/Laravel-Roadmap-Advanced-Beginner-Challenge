<?php

namespace Tests\Feature\PHPUnit;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);

        $this->user = User::factory()->simpleUser()->create();
    }

    /**
     * A basic feature test example.
     */
    public function test_admin_can_see_user_page(): void
    {
        $response = $this->asAdmin()->get(route('users.index'));

        $response->assertStatus(200);
    }

    public function test_manager_cannot_see_user_page(): void
    {
        $response = $this->asManager()->get(route('users.index'));

        $response->assertStatus(403);
    }

    public function test_simple_user_cannot_see_user_page(): void
    {
        $response = $this->asSimpleUser()->get(route('users.index'));

        $response->assertStatus(403);
    }

    public function test_user_page_contains_user_data(): void
    {
        $response = $this->asAdmin()->get(route('users.index'));

        $response->assertStatus(200);
        $response->assertViewHas('users', function (LengthAwarePaginator $collection) {
            return $collection->contains($this->user);
        });
    }

    public function test_admin_can_see_new_user_button(): void
    {
        $response = $this->asAdmin()->get(route('users.index'));

        $response->assertStatus(200);
        $response->assertSee('New User');
    }

    public function test_admin_can_see_user_create_page(): void
    {
        $response = $this->asAdmin()->get(route('users.create'));

        $response->assertStatus(200);
    }

    public function test_admin_can_create_a_new_user(): void
    {
        $newUser = [
            'name' => 'User1',
            'email' => 'user1@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'user',
        ];

        $response = $this->asAdmin()->post(route('users.store'), $newUser);

        $response->assertStatus(302);
        $response->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'email' => $newUser['email'],
        ]);
    }

    public function test_admin_can_update_user()
    {
        $userData = [
            'name' => 'Admin UPDATED',
            'role' => 'admin'
        ];

        $response = $this->asAdmin()
            ->put(route('users.update', $this->user), $userData);

        $response->assertStatus(302);
        $response->assertRedirect(route('users.edit', $this->user));

        $updatedUser = User::find($this->user->id);
        $this->assertEquals($userData['name'], $updatedUser->name);
    }

    public function test_admin_can_delete_user()
    {
        $response = $this->asAdmin()
            ->from(route('users.index'))
            ->delete(route('users.destroy', $this->user));

        $response->assertStatus(302);
        $response->assertRedirect(route('users.index'));

        $this->assertSoftDeleted($this->user);
    }
}
