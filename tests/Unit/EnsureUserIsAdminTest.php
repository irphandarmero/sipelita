<?php

namespace Tests\Unit;

use App\Http\Middleware\EnsureUserIsAdmin;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class EnsureUserIsAdminTest extends TestCase
{
    public function test_admin_is_allowed_through_admin_middleware()
    {
        $request = Request::create('/');
        $user = new User();
        $user->role = 'admin';
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $response = (new EnsureUserIsAdmin())->handle($request, function () {
            return new Response('allowed');
        });

        $this->assertSame('allowed', $response->getContent());
    }

    public function test_non_admin_is_rejected_by_admin_middleware()
    {
        $request = Request::create('/');
        $user = new User();
        $user->role = 'petugas';
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        try {
            (new EnsureUserIsAdmin())->handle($request, function () {
                return new Response('unreachable');
            });
            $this->fail('Expected non-admin access to be rejected.');
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }
    }
}
