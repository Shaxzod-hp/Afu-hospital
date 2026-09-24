<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\Specialization;
use App\Models\TreatmentLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TreatmentLogTest extends TestCase
{
    use RefreshDatabase;

    private Doctor $doctor;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $spec = Specialization::create(['name' => 'Kardiologiya', 'slug' => 'kardiologiya']);
        $this->doctor = Doctor::create(['full_name' => 'Ali Valiyev', 'slug' => 'ali-valiyev', 'specialization_id' => $spec->id]);
    }

    private function actingAsAdmin(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']), ['admin']);
    }

    private function photos(int $count): array
    {
        return array_map(fn ($i) => UploadedFile::fake()->image("p{$i}.jpg", 800, 600), range(1, $count));
    }

    public function test_guest_cannot_create_log(): void
    {
        $this->postJson('/api/admin/treatment-logs', [])->assertUnauthorized();
    }

    public function test_admin_creates_log_and_it_is_visible_publicly(): void
    {
        $this->actingAsAdmin();

        $res = $this->post('/api/admin/treatment-logs', [
            'doctor_id' => $this->doctor->id,
            'description' => 'Bugun 3 ta operatsiya o\'tkazildi',
            'photos' => $this->photos(2),
        ], ['Accept' => 'application/json'])->assertCreated();

        $photos = $res->json('data.photos');
        $this->assertCount(2, $photos);
        foreach ($photos as $photo) {
            $this->assertStringStartsWith('/storage/uploads/treatment-logs/', $photo);
            Storage::disk('public')->assertExists(substr($photo, strlen('/storage/')));
        }

        $this->getJson('/api/doctors/ali-valiyev/treatment-logs')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.photos', $photos);
    }

    public function test_validation_messages_are_in_uzbek(): void
    {
        $this->actingAsAdmin();

        $this->post('/api/admin/treatment-logs', [
            'doctor_id' => $this->doctor->id,
            'description' => 'abc',
            'photos' => $this->photos(1),
        ], ['Accept' => 'application/json'])
            ->assertUnprocessable()
            ->assertJsonPath('errors.photos.0', 'Kamida 2 ta rasm yuklang.')
            ->assertJsonPath('errors.description.0', 'Tavsif kamida 5 ta belgidan iborat bo\'lishi kerak.');

        $this->post('/api/admin/treatment-logs', [
            'doctor_id' => $this->doctor->id,
            'description' => 'Yetarli tavsif',
            'photos' => $this->photos(TreatmentLog::MAX_PHOTOS + 1),
        ], ['Accept' => 'application/json'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('photos');

        $this->assertDatabaseCount('treatment_logs', 0);
    }

    public function test_logs_older_than_24_hours_are_hidden_publicly_but_visible_to_admin(): void
    {
        $this->doctor->treatmentLogs()->create(['description' => 'Yangi', 'photos' => []]);
        $old = $this->doctor->treatmentLogs()->create(['description' => 'Eski', 'photos' => []]);
        $old->forceFill(['created_at' => now()->subHours(25)])->save();

        $this->getJson('/api/doctors/ali-valiyev/treatment-logs')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.description', 'Yangi');

        $this->actingAsAdmin();
        $this->getJson("/api/admin/treatment-logs?doctor_id={$this->doctor->id}")
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_admin_delete_removes_photos(): void
    {
        Storage::disk('public')->put('uploads/treatment-logs/a.jpg', 'x');
        $log = $this->doctor->treatmentLogs()->create([
            'description' => 'Lavha',
            'photos' => ['/storage/uploads/treatment-logs/a.jpg'],
        ]);

        $this->actingAsAdmin();
        $this->deleteJson("/api/admin/treatment-logs/{$log->id}")->assertOk();

        $this->assertDatabaseMissing('treatment_logs', ['id' => $log->id]);
        Storage::disk('public')->assertMissing('uploads/treatment-logs/a.jpg');
    }

    public function test_cleanup_command_deletes_only_expired_logs_and_their_photos(): void
    {
        Storage::disk('public')->put('uploads/treatment-logs/old.jpg', 'x');
        Storage::disk('public')->put('uploads/treatment-logs/new.jpg', 'x');

        $old = $this->doctor->treatmentLogs()->create([
            'description' => 'Eski',
            'photos' => ['/storage/uploads/treatment-logs/old.jpg'],
        ]);
        $old->forceFill(['created_at' => now()->subHours(25)])->save();

        $fresh = $this->doctor->treatmentLogs()->create([
            'description' => 'Yangi',
            'photos' => ['/storage/uploads/treatment-logs/new.jpg'],
        ]);

        $this->artisan('treatment-logs:cleanup')->assertSuccessful();

        $this->assertDatabaseMissing('treatment_logs', ['id' => $old->id]);
        $this->assertDatabaseHas('treatment_logs', ['id' => $fresh->id]);
        Storage::disk('public')->assertMissing('uploads/treatment-logs/old.jpg');
        Storage::disk('public')->assertExists('uploads/treatment-logs/new.jpg');
    }
}
