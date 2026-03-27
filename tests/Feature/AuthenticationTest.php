<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    // ── Buyer Registration ────────────────────

    public function test_buyer_registration_page_loads(): void
    {
        $response = $this->get(route('buyer.register'));

        $response->assertStatus(200);
    }

    public function test_buyer_can_register(): void
    {
        Livewire::test(\App\Livewire\Buyer\Register::class)
            ->set('name', 'Test Buyer')
            ->set('phone', '08999888777')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register');

        $this->assertDatabaseHas('users', [
            'phone' => '08999888777',
            'role' => UserRole::Buyer->value,
        ]);
    }

    public function test_buyer_registration_requires_unique_phone(): void
    {
        User::factory()->buyer()->create(['phone' => '08999888777']);

        Livewire::test(\App\Livewire\Buyer\Register::class)
            ->set('name', 'Another Buyer')
            ->set('phone', '08999888777')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors(['phone']);

        $this->assertDatabaseCount('users', 1);
    }

    // ── Buyer Login ───────────────────────────

    public function test_buyer_login_page_loads(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
    }

    public function test_buyer_can_login_with_correct_credentials(): void
    {
        User::factory()->buyer()->create(['phone' => '08123456789']);

        Livewire::test(\App\Livewire\Buyer\Login::class)
            ->set('phone', '08123456789')
            ->set('password', 'password')
            ->call('login')
            ->assertRedirect(route('buyer.dashboard'));
    }

    public function test_buyer_cannot_login_with_wrong_password(): void
    {
        User::factory()->buyer()->create(['phone' => '08123456789']);

        Livewire::test(\App\Livewire\Buyer\Login::class)
            ->set('phone', '08123456789')
            ->set('password', 'wrong-password')
            ->call('login')
            ->assertHasErrors(['phone']);
    }

    public function test_non_buyer_cannot_login_via_buyer_login(): void
    {
        User::factory()->seller()->create(['phone' => '08123456789']);

        Livewire::test(\App\Livewire\Buyer\Login::class)
            ->set('phone', '08123456789')
            ->set('password', 'password')
            ->call('login')
            ->assertHasErrors(['phone']);
    }

    // ── Buyer Auth Middleware ──────────────────

    public function test_unauthenticated_user_redirected_from_dashboard(): void
    {
        $response = $this->get(route('buyer.dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_buyer_can_access_dashboard(): void
    {
        $user = User::factory()->buyer()->create();

        $response = $this->actingAs($user)->get(route('buyer.dashboard'));

        $response->assertStatus(200);
    }

    public function test_seller_cannot_access_buyer_dashboard(): void
    {
        $user = User::factory()->seller()->create();

        $response = $this->actingAs($user)->get(route('buyer.dashboard'));

        $response->assertRedirect(route('login'));
    }

    // ── Panel Access ──────────────────────────

    public function test_seller_can_access_seller_panel_login(): void
    {
        $response = $this->get('/seller/login');

        $response->assertStatus(200);
    }

    public function test_courier_can_access_courier_panel_login(): void
    {
        $response = $this->get('/courier/login');

        $response->assertStatus(200);
    }

    public function test_admin_can_access_admin_panel_login(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
    }

    // ── Public order tracking ─────────────────

    public function test_track_page_accessible_without_auth(): void
    {
        $response = $this->get(route('buyer.track'));

        $response->assertStatus(200);
    }
}
