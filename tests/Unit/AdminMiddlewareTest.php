<?php

namespace Tests\Unit;

use App\Http\Middleware\AdminMiddleware;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class AdminMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    /**
     * User admin yang sudah login → lolos middleware (mengembalikan response dari next).
     */
    #[Test]
    public function admin_user_passes_through_middleware(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $request = Request::create('/admin/dashboard', 'GET');
        $middleware = new AdminMiddleware();

        $nextCalled = false;
        $next = function ($req) use (&$nextCalled) {
            $nextCalled = true;
            return response('passed');
        };

        $response = $middleware->handle($request, $next);

        $this->assertTrue($nextCalled);
        $this->assertEquals('passed', $response->getContent());
    }

    /**
     * User non-admin (guru) yang sudah login → melempar HttpException 403.
     */
    #[Test]
    public function non_admin_user_triggers_403_exception(): void
    {
        /** @var User $guru */
        $guru = User::factory()->create(['role' => 'guru']);
        $this->actingAs($guru);

        $request = Request::create('/admin/dashboard', 'GET');
        $middleware = new AdminMiddleware();

        try {
            $middleware->handle($request, function () {
                // Tidak seharusnya sampai sini
            });
            $this->fail('Expected HttpException was not thrown');
        } catch (HttpException $e) {
            $this->assertEquals(403, $e->getStatusCode());
            $this->assertStringContainsString('Unauthorized', $e->getMessage());
        }
    }

    /**
     * User belum login → melempar HttpException 403.
     */
    #[Test]
    public function unauthenticated_user_triggers_403_exception(): void
    {
        $request = Request::create('/admin/dashboard', 'GET');
        $middleware = new AdminMiddleware();

        try {
            $middleware->handle($request, function () {
                // Tidak seharusnya sampai sini
            });
            $this->fail('Expected HttpException was not thrown');
        } catch (HttpException $e) {
            $this->assertEquals(403, $e->getStatusCode());
            $this->assertStringContainsString('Unauthorized', $e->getMessage());
        }
    }

    /**
     * Verifikasi bahwa pesan exception 403 mengandung "Unauthorized".
     */
    #[Test]
    public function unauthorized_access_exception_contains_expected_message(): void
    {
        $request = Request::create('/admin/dashboard', 'GET');
        $middleware = new AdminMiddleware();

        try {
            $middleware->handle($request, function () {});
            $this->fail('Expected HttpException was not thrown');
        } catch (HttpException $e) {
            $this->assertEquals(403, $e->getStatusCode());
            $this->assertStringContainsString('Unauthorized', $e->getMessage());
        }
    }
}
