<?php

namespace Tests\Feature;

use App\Models\Device;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RoadWatchApiUploadRateLimitTest extends TestCase
{
    use RefreshDatabase;

    private const TOKEN = 'roadwatch-upload-test-token';

    private function createDevice(): Device
    {
        return Device::create([
            'device_code' => 'ESP32-UPLOAD-001',
            'name' => 'Upload Security Test',
            'status' => 'Active',
            'api_token_hash' => hash('sha256', self::TOKEN),
        ]);
    }

    private function payload(): array
    {
        return [
            'location' => 'Jakarta Test',
            'latitude' => -6.2,
            'longitude' => 106.8,
        ];
    }

    private function headers(): array
    {
        return [
            'Authorization' => 'Bearer '.self::TOKEN,
            'Accept' => 'application/json',
        ];
    }

    public function test_valid_jpeg_upload_succeeds(): void
    {
        Storage::fake('public');
        $this->createDevice();

        $response = $this->post(
            '/api/detections',
            array_merge($this->payload(), [
                'image_file' => UploadedFile::fake()->image('pothole.jpg'),
            ]),
            $this->headers()
        );

        $response->assertCreated();

        $path = $response->json('data.image');

        $this->assertStringContainsString('/storage/detections/', $path);

        $filename = basename(parse_url($path, PHP_URL_PATH));

        Storage::disk('public')->assertExists('detections/'.$filename);
    }

    public function test_non_jpeg_upload_is_rejected(): void
    {
        Storage::fake('public');
        $this->createDevice();

        $this->post(
            '/api/detections',
            array_merge($this->payload(), [
                'image_file' => UploadedFile::fake()->image('invalid.png'),
            ]),
            $this->headers()
        )->assertUnprocessable()
         ->assertJsonValidationErrors('image_file');

        $this->assertDatabaseCount('detections', 0);
        $this->assertEmpty(Storage::disk('public')->allFiles('detections'));
    }

    public function test_oversized_jpeg_is_rejected(): void
    {
        Storage::fake('public');
        $this->createDevice();

        $this->post(
            '/api/detections',
            array_merge($this->payload(), [
                'image_file' => UploadedFile::fake()->image('large.jpg')->size(2048),
            ]),
            $this->headers()
        )->assertUnprocessable()
         ->assertJsonValidationErrors('image_file');

        $this->assertDatabaseCount('detections', 0);
    }

    public function test_rate_limit_blocks_request_31(): void
    {
        $this->createDevice();

        for ($i = 1; $i <= 30; $i++) {
            $this->postJson(
                '/api/detections',
                $this->payload(),
                $this->headers()
            )->assertCreated();
        }

        $this->postJson(
            '/api/detections',
            $this->payload(),
            $this->headers()
        )->assertStatus(429);

        $this->assertDatabaseCount('detections', 30);
    }

    public function test_unauthorized_upload_is_rejected(): void
    {
        Storage::fake('public');
        $this->createDevice();

        $this->post(
            '/api/detections',
            array_merge($this->payload(), [
                'image_file' => UploadedFile::fake()->image('pothole.jpg'),
            ]),
            ['Accept' => 'application/json']
        )->assertUnauthorized();

        $this->assertDatabaseCount('detections', 0);
        $this->assertEmpty(Storage::disk('public')->allFiles('detections'));
    }
}
