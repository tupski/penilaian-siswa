<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Kredensial valid → login berhasil (Auth::attempt mengembalikan true).
     */
    #[Test]
    public function login_succeeds_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $credentials = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $this->assertTrue(
            auth()->attempt($credentials)
        );
    }

    /**
     * Format email tidak valid → validasi gagal.
     */
    #[Test]
    public function login_validation_fails_with_invalid_email_format(): void
    {
        $data = [
            'email' => 'not-an-email',
            'password' => 'password123',
        ];

        $validator = Validator::make($data, [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    /**
     * Email kosong → validasi gagal.
     */
    #[Test]
    public function login_validation_fails_with_empty_email(): void
    {
        $data = [
            'email' => '',
            'password' => 'password123',
        ];

        $validator = Validator::make($data, [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    /**
     * Password kosong → validasi gagal.
     */
    #[Test]
    public function login_validation_fails_with_empty_password(): void
    {
        $data = [
            'email' => 'test@example.com',
            'password' => '',
        ];

        $validator = Validator::make($data, [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    /**
     * Email dan password kosong → validasi gagal di kedua field.
     */
    #[Test]
    public function login_validation_fails_with_both_fields_empty(): void
    {
        $data = [
            'email' => '',
            'password' => '',
        ];

        $validator = Validator::make($data, [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $this->assertTrue($validator->fails());
        $errors = $validator->errors()->toArray();
        $this->assertArrayHasKey('email', $errors);
        $this->assertArrayHasKey('password', $errors);
    }

    /**
     * Password salah → autentikasi gagal.
     */
    #[Test]
    public function login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('correct-password'),
        ]);

        $credentials = [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ];

        $this->assertFalse(
            auth()->attempt($credentials)
        );
    }

    /**
     * User tidak ada → autentikasi gagal.
     */
    #[Test]
    public function login_fails_with_non_existent_email(): void
    {
        $credentials = [
            'email' => 'nonexistent@example.com',
            'password' => 'somepassword',
        ];

        $this->assertFalse(
            auth()->attempt($credentials)
        );
    }

    /**
     * Kredensial valid dengan opsi 'remember' → login berhasil.
     */
    #[Test]
    public function login_succeeds_with_remember_me(): void
    {
        $user = User::factory()->create([
            'email' => 'remember@example.com',
            'password' => Hash::make('password123'),
        ]);

        $credentials = [
            'email' => 'remember@example.com',
            'password' => 'password123',
        ];

        $this->assertTrue(
            auth()->attempt($credentials, true)
        );
    }
}
