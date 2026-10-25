<?php

namespace Tests\Feature;

use App\Models\Detection;
use App\Models\Device;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoadWatchDeviceApiSecurityTest extends TestCase
{
    use RefreshDatabase;

    private const TOKEN = 'roadwatch-test-token-not-for-production';

    private function createDevice(string $status = 'Active'): Device
    {
        return Device::create([
            'device_code' => 'ESP32-TEST-001',
            'name' => 'ESP32 Security Test',
            'status' => $status,
            'api_token_hash' => hash('sha256', self::TOKEN),
        ]);
    }

    private function payload(): array
    {
        return [
            'location' => 'Test Location',
            'latitude' => -6.200000,
            'longitude' => 106.816666,
            'severity' => 'High',
        ];
    }

    private function headers(string $token = self::TOKEN): array
    {
        return ['Authorization' => 'Bearer '.$token];
    }

    public function test_detection_without_token_is_rejected(): void
    {
        $this->createDevice();

        $this->postJson('/api/detections', $this->payload())
            ->assertUnauthorized();

        $this->assertDatabaseCount('detections', 0);
    }

    public function test_detection_with_wrong_token_is_rejected(): void
    {
        $this->createDevice();

        $this->postJson(
            '/api/detections',
            $this->payload(),
            $this->headers('wrong-token')
        )->assertUnauthorized();

        $this->assertDatabaseCount('detections', 0);
    }

    public function test_inactive_device_cannot_submit_detection(): void
    {
        $this->createDevice('Inactive');

        $this->postJson(
            '/api/detections',
            $this->payload(),
            $this->headers()
        )->assertUnauthorized();

        $this->assertDatabaseCount('detections', 0);
    }

    public function test_active_device_can_submit_detection(): void
    {
        $device = $this->createDevice();

        $this->postJson(
            '/api/detections',
            $this->payload(),
            $this->headers()
        )->assertCreated()
         ->assertJsonPath('success', true)
         ->assertJsonPath('device_code', $device->device_code);

        $detection = Detection::firstOrFail();

        $this->assertSame($device->id, $detection->device_id);
        $this->assertSame('Detected', $detection->status);
        $this->assertSame('High', $detection->severity);
    }

    public function test_invalid_coordinates_are_rejected(): void
    {
        $this->createDevice();

        $payload = $this->payload();
        $payload['latitude'] = 100;
        $payload['longitude'] = 200;

        $this->postJson(
            '/api/detections',
            $payload,
            $this->headers()
        )->assertUnprocessable()
         ->assertJsonValidationErrors(['latitude', 'longitude']);

        $this->assertDatabaseCount('detections', 0);
    }

    public function test_device_cannot_override_detection_status(): void
    {
        $this->createDevice();

        $payload = $this->payload();
        $payload['status'] = 'Repaired';

        $this->postJson(
            '/api/detections',
            $payload,
            $this->headers()
        )->assertCreated();

        $this->assertSame(
            'Detected',
            Detection::firstOrFail()->status
        );
    }

    public function test_valid_heartbeat_updates_device(): void
    {
        $device = $this->createDevice();

        $this->postJson('/api/devices/heartbeat', [
            'device_code' => $device->device_code,
            'gps_status' => 'Fix',
            'firmware_version' => '1.0.0',
        ], $this->headers())
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('connection_status', 'Online');

        $device->refresh();

        $this->assertNotNull($device->last_seen_at);
        $this->assertSame('Fix', $device->gps_status);
        $this->assertSame('1.0.0', $device->firmware_version);
    }

    public function test_wrong_heartbeat_token_does_not_update_device(): void
    {
        $device = $this->createDevice();

        $this->postJson('/api/devices/heartbeat', [
            'device_code' => $device->device_code,
            'gps_status' => 'Fix',
        ], $this->headers('wrong-token'))
            ->assertUnauthorized();

        $device->refresh();

        $this->assertNull($device->last_seen_at);
        $this->assertSame('Unknown', $device->gps_status);
    }
}
