<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Halaman index profile mengembalikan 200 untuk user yang sudah login.
     */
    public function test_index_page_returns_200(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
    }

    /**
     * Test: Update nama dan email dengan data valid.
     */
    public function test_update_name_and_email(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
            'role' => 'guru',
        ]);

        $response = $this->actingAs($user)->put('/profile', [
            'name' => 'New Name',
            'email' => 'new@example.com',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'email' => 'new@example.com',
        ]);
    }

    /**
     * Test: Update dengan email duplikat (dipakai user lain) mengembalikan error validasi.
     */
    public function test_update_with_duplicate_email_returns_error(): void
    {
        User::factory()->create([
            'email' => 'taken@example.com',
            'role' => 'guru',
        ]);

        /** @var User $user */
        $user = User::factory()->create([
            'email' => 'mine@example.com',
            'role' => 'guru',
        ]);

        $response = $this->actingAs($user)->put('/profile', [
            'name' => 'My Name',
            'email' => 'taken@example.com',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Test: Update password dengan current_password yang benar.
     */
    public function test_update_password_with_correct_current_password(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword'),
            'role' => 'guru',
        ]);

        $response = $this->actingAs($user)->put('/profile/password', [
            'current_password' => 'oldpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect();

        // Verifikasi password sudah berubah
        $this->assertTrue(
            Hash::check('newpassword123', $user->fresh()->password)
        );
    }

    /**
     * Test: Update password dengan current_password salah mengembalikan error.
     */
    public function test_update_password_with_wrong_current_password_returns_error(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'password' => Hash::make('correctpassword'),
            'role' => 'guru',
        ]);

        $response = $this->actingAs($user)->put('/profile/password', [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHasErrors(['current_password']);
    }

    /**
     * Test: Tamu tidak bisa akses rute profile.
     */
    public function test_guest_cannot_access_profile(): void
    {
        $this->get('/profile')->assertRedirect('/login');
        $this->put('/profile', [])->assertRedirect('/login');
        $this->put('/profile/password', [])->assertRedirect('/login');
    }
}
