<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Doctor;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Specialization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): User
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin, ['admin']);

        return $admin;
    }

    private function specialization(): Specialization
    {
        return Specialization::create(['name' => 'Kardiologiya', 'slug' => 'kardiologiya']);
    }

    // ---------- Aloqa formasi ----------

    public function test_contact_is_saved_and_sent_to_telegram(): void
    {
        Http::fake(['api.telegram.org/*' => Http::response(['ok' => true])]);

        $this->postJson('/api/contacts', [
            'name' => 'Ali <b>Valiyev</b>',
            'phone' => '+998 90 123 45 67',
            'message' => 'Qabulga yozilmoqchiman',
        ])->assertCreated()->assertJsonMissingPath('data');

        $this->assertDatabaseHas('contacts', ['phone' => '+998 90 123 45 67']);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/bottest-token/sendMessage')
                && $request['chat_id'] === '-100123'
                && str_contains($request['text'], 'Ali &lt;b&gt;Valiyev&lt;/b&gt;');
        });
    }

    public function test_contact_is_saved_even_if_telegram_is_down(): void
    {
        Http::fake(['api.telegram.org/*' => Http::response(['ok' => false], 500)]);

        $this->postJson('/api/contacts', ['name' => 'Ali', 'phone' => '901234567'])
            ->assertCreated();

        $this->assertSame(1, Contact::count());
    }

    public function test_contact_rejects_invalid_phone_with_json_even_without_accept_header(): void
    {
        Http::fake();

        $this->post('/api/contacts', ['name' => 'Ali', 'phone' => 'abc'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('phone');

        Http::assertNothingSent();
    }

    public function test_contact_form_is_rate_limited(): void
    {
        Http::fake();

        for ($i = 0; $i < 3; $i++) {
            $this->postJson('/api/contacts', ['name' => 'Ali', 'phone' => '901234567'])->assertCreated();
        }

        $this->postJson('/api/contacts', ['name' => 'Ali', 'phone' => '901234567'])->assertStatus(429);
    }

    public function test_admin_routes_require_admin_token(): void
    {
        $this->getJson('/api/admin/contacts')->assertUnauthorized();

        Sanctum::actingAs(User::factory()->create(['role' => 'user']), ['admin']);
        $this->getJson('/api/admin/contacts')->assertForbidden();
    }

    // ---------- Yangiliklar ----------

    public function test_unpublished_news_is_hidden_from_public_but_visible_to_admin(): void
    {
        $category = NewsCategory::create(['name' => 'Yangilik', 'slug' => 'yangilik']);
        $news = News::create([
            'title' => 'Qoralama', 'slug' => 'qoralama', 'category_id' => $category->id,
            'content' => 'Matn', 'is_published' => false,
        ]);

        $this->getJson('/api/news')->assertOk()->assertJsonCount(0, 'data');
        $this->getJson('/api/news/qoralama')->assertNotFound();
        $this->getJson("/api/news/{$news->id}")->assertNotFound();

        $this->actingAsAdmin();
        $this->getJson("/api/admin/news/{$news->id}")->assertOk()->assertJsonPath('title', 'Qoralama');
        $this->assertSame(0, $news->fresh()->views, 'Admin ko\'rishi views ni oshirmasligi kerak');
    }

    // ---------- Slug ----------

    public function test_duplicate_names_get_unique_slugs_instead_of_500(): void
    {
        $this->actingAsAdmin();

        $first = $this->postJson('/api/admin/services', ['name' => 'UZI tekshiruvi'])->assertCreated();
        $second = $this->postJson('/api/admin/services', ['name' => 'UZI tekshiruvi'])->assertCreated();

        $this->assertSame('uzi-tekshiruvi', $first->json('data.slug'));
        $this->assertSame('uzi-tekshiruvi-1', $second->json('data.slug'));
    }

    // ---------- Operatsiyalar ----------

    public function test_surgery_included_items_are_saved_and_can_be_cleared(): void
    {
        $this->actingAsAdmin();

        $res = $this->postJson('/api/admin/surgeries', [
            'name' => 'Appendektomiya',
            'included_items' => [['name' => 'Narkoz', 'price' => 500000]],
        ])->assertCreated();

        $id = $res->json('data.id');
        $this->getJson("/api/surgeries/{$id}")
            ->assertJsonPath('data.included_items.0.name', 'Narkoz');

        // Frontend bo'sh ro'yxatni included_items="" qilib yuboradi
        $this->post("/api/admin/surgeries/{$id}", ['_method' => 'PUT', 'name' => 'Appendektomiya', 'included_items' => ''])
            ->assertOk();
        $this->getJson("/api/surgeries/{$id}")->assertJsonPath('data.included_items', null);
    }

    // ---------- Shifokorlar ----------

    public function test_doctor_schedule_is_saved_validated_and_kept_on_update(): void
    {
        $this->actingAsAdmin();
        $spec = $this->specialization();

        $schedule = json_encode([
            'monday' => ['active' => true, 'start' => '08:00', 'end' => '14:00'],
            'tuesday' => ['active' => false, 'start' => '09:00', 'end' => '18:00'],
            'funday' => ['active' => true, 'start' => '09:00', 'end' => '18:00'],
        ]);

        $res = $this->post('/api/admin/doctors', [
            'full_name' => 'Aliyev Vali', 'specialization_id' => $spec->id,
            'experience_years' => 2, 'schedule' => $schedule,
        ])->assertCreated();

        $res->assertJsonCount(1, 'data.schedules')
            ->assertJsonPath('data.schedules.0.day', 'monday');

        $id = $res->json('data.id');

        // Jadvalsiz yangilash eski jadvalni o'chirmasligi kerak
        $this->post("/api/admin/doctors/{$id}", ['_method' => 'PUT', 'full_name' => 'Aliyev Vali', 'bio' => ''])
            ->assertOk()
            ->assertJsonCount(1, 'data.schedules');

        // Tugash vaqti boshlanishdan oldin — xato
        $bad = json_encode(['monday' => ['active' => true, 'start' => '18:00', 'end' => '09:00']]);
        $this->post("/api/admin/doctors/{$id}", ['_method' => 'PUT', 'schedule' => $bad])
            ->assertStatus(422)
            ->assertJsonValidationErrors('schedule');
    }

    public function test_treatment_logs_do_not_match_doctor_by_slug_prefix_number(): void
    {
        $spec = $this->specialization();
        $doctor5 = Doctor::create(['full_name' => 'A', 'slug' => 'a', 'specialization_id' => $spec->id]);
        $doctor5->treatmentLogs()->create(['description' => 'Maxfiy lavha', 'photos' => []]);

        $this->getJson("/api/doctors/{$doctor5->id}-boshqa/treatment-logs")->assertNotFound();
        $this->getJson("/api/doctors/{$doctor5->id}/treatment-logs")->assertOk()->assertJsonCount(1, 'data');
        $this->getJson('/api/doctors/a/treatment-logs')->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_specialization_with_doctors_cannot_be_deleted(): void
    {
        $this->actingAsAdmin();
        $spec = $this->specialization();
        Doctor::create(['full_name' => 'A', 'slug' => 'a', 'specialization_id' => $spec->id]);

        $this->deleteJson("/api/admin/specializations/{$spec->id}")->assertStatus(422);
        $this->assertDatabaseHas('specializations', ['id' => $spec->id]);
    }
}
