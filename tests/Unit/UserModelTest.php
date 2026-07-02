<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Menguji bahwa isAdmin() mengembalikan true jika role user adalah 'admin'.
     */
    #[Test]
    public function is_admin_returns_true_when_role_is_admin(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->assertTrue($user->isAdmin());
    }

    /**
     * Menguji bahwa isAdmin() mengembalikan false jika role user adalah 'guru'.
     */
    #[Test]
    public function is_admin_returns_false_when_role_is_guru(): void
    {
        $user = User::factory()->create(['role' => 'guru']);

        $this->assertFalse($user->isAdmin());
    }

    /**
     * Menguji bahwa isGuru() mengembalikan true jika role user adalah 'guru'.
     */
    #[Test]
    public function is_guru_returns_true_when_role_is_guru(): void
    {
        $user = User::factory()->create(['role' => 'guru']);

        $this->assertTrue($user->isGuru());
    }

    /**
     * Menguji bahwa isGuru() mengembalikan false jika role user adalah 'admin'.
     */
    #[Test]
    public function is_guru_returns_false_when_role_is_admin(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->assertFalse($user->isGuru());
    }

    /**
     * Menguji bahwa isUser() (alias dari isGuru) mengembalikan true jika role adalah 'guru'.
     */
    #[Test]
    public function is_user_returns_true_when_role_is_guru(): void
    {
        $user = User::factory()->create(['role' => 'guru']);

        $this->assertTrue($user->isUser());
    }

    /**
     * Menguji bahwa isUser() (alias dari isGuru) mengembalikan false jika role adalah 'admin'.
     */
    #[Test]
    public function is_user_returns_false_when_role_is_admin(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->assertFalse($user->isUser());
    }
}
