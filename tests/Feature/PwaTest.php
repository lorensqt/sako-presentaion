<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PwaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test manifest.json is present, valid JSON, and meets PWA install criteria.
     */
    public function test_manifest_json_is_valid_and_installable(): void
    {
        $manifestPath = public_path('manifest.json');
        $this->assertFileExists($manifestPath);

        $json = json_decode(file_get_contents($manifestPath), true);
        $this->assertIsArray($json);
        $this->assertEquals('ML Sako Cooperative', $json['name']);
        $this->assertEquals('ML Sako', $json['short_name']);
        $this->assertEquals('standalone', $json['display']);
        $this->assertEquals('/', $json['start_url']);
        $this->assertNotEmpty($json['icons']);

        // Check essential icon sizes exist on disk
        $this->assertFileExists(public_path('img/icons/icon-192x192.png'));
        $this->assertFileExists(public_path('img/icons/icon-512x512.png'));
        $this->assertFileExists(public_path('img/icons/apple-touch-icon.png'));
        $this->assertFileExists(public_path('img/icons/maskable-icon-512x512.png'));
    }

    /**
     * Test service worker file exists and is accessible.
     */
    public function test_service_worker_file_exists(): void
    {
        $swPath = public_path('sw.js');
        $this->assertFileExists($swPath);

        $content = file_get_contents($swPath);
        $this->assertStringContainsString('addEventListener', $content);
        $this->assertStringContainsString('install', $content);
        $this->assertStringContainsString('fetch', $content);
    }

    /**
     * Test that landing/welcome page contains PWA metadata and install prompt.
     */
    public function test_guest_pages_contain_pwa_meta_tags(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('rel="manifest"', false);
        $response->assertSee('apple-mobile-web-app-capable', false);
        $response->assertSee('pwa-install-container', false);
    }

    /**
     * Test that authenticated member pages contain PWA metadata and install prompt.
     */
    public function test_member_pages_contain_pwa_meta_tags(): void
    {
        $user = User::create([
            'name' => 'Member PWA Test',
            'email' => 'pwa@example.com',
            'password' => bcrypt('password'),
            'company_id' => '12345678',
            'role' => 'member',
        ]);

        $response = $this->actingAs($user)->get('/savings');
        $response->assertStatus(200);
        $response->assertSee('rel="manifest"', false);
        $response->assertSee('apple-touch-icon', false);
        $response->assertSee('pwa-install-container', false);
    }
}
