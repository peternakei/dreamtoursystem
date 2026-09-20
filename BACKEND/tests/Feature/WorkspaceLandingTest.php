<?php

namespace Tests\Feature;

use App\Project\Workspace\WorkspaceLanding;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class WorkspaceLandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_local_browser_entry_points_open_the_vue_workspace(): void
    {
        $this->app['env'] = 'local';
        config(['workspace.frontend_url' => 'http://127.0.0.1:5174']);
        $this->get('/')->assertRedirect('http://127.0.0.1:5174/dashboard');
        $this->get('/dashboard/user')->assertRedirect('http://127.0.0.1:5174/dashboard');
        $this->get('/login')->assertRedirect('http://127.0.0.1:5174/login');
        $this->getJson('/workspace/session')->assertOk()->assertJsonStructure(['user', 'csrf_token']);
    }

    public function test_api_posts_advanced_tools_and_nonlocal_requests_are_not_redirected(): void
    {
        $this->app['env'] = 'local';
        $middleware = new WorkspaceLanding;
        $next = fn () => response('original handler');
        foreach ([Request::create('/login', 'POST'), Request::create('/quotation-versions/example/builder'), Request::create('/force-password-change'), Request::create('/login', 'GET', [], [], [], ['HTTP_ACCEPT' => 'application/json'])] as $request) {
            $this->assertSame('original handler', $middleware->handle($request, $next)->getContent());
        }
        $this->app['env'] = 'production';
        $this->assertSame('original handler', $middleware->handle(Request::create('/login'), $next)->getContent());
    }
}
