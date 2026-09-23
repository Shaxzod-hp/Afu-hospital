<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->input('per_page', 20), 1), 100);

        return response()->json([
            'success' => true,
            'data' => Contact::latest()->paginate($perPage),
            'unread' => Contact::where('read', false)->count(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => ['required', 'string', 'max:50', 'regex:/^\+?[0-9\s\-()]{7,20}$/'],
            'specialty' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:2000',
        ], [
            'phone.regex' => "Telefon raqam noto'g'ri formatda.",
        ]);

        $contact = Contact::create($validated);

        $this->notifyTelegram($contact);

        return response()->json([
            'success' => true,
            'message' => 'Murojaatingiz qabul qilindi. Tez orada siz bilan bog\'lanamiz.',
        ], 201);
    }

    private function notifyTelegram(Contact $contact): void
    {
        // env() emas, config() — `php artisan config:cache` dan keyin ham ishlaydi
        $botToken = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (!$botToken || !$chatId) {
            return;
        }

        $text = "📩 <b>Yangi murojaat!</b>\n\n";
        $text .= "👤 <b>Ism:</b> " . htmlspecialchars($contact->name) . "\n";
        $text .= "📞 <b>Telefon:</b> " . htmlspecialchars($contact->phone) . "\n";
        if ($contact->specialty) {
            $text .= "🩺 <b>Mutaxassislik:</b> " . htmlspecialchars($contact->specialty) . "\n";
        }
        if ($contact->message) {
            $text .= "💬 <b>Xabar:</b> " . htmlspecialchars($contact->message) . "\n";
        }
        $text .= "\n🕒 <b>Sana:</b> " . $contact->created_at->timezone(config('app.timezone'))->format('Y-m-d H:i');

        try {
            $response = Http::timeout(5)->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'HTML',
            ]);

            if ($response->failed()) {
                Log::warning('Telegram notification failed', ['status' => $response->status()]);
            }
        } catch (\Throwable $e) {
            // Telegram ishlamasa ham murojaat bazaga saqlangan — foydalanuvchiga xato ko'rsatmaymiz
            Log::warning('Telegram notification error: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        $contact = Contact::findOrFail($id);

        $validated = $request->validate([
            'read' => 'required|boolean',
        ]);

        $contact->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Holat yangilandi.',
            'data' => $contact,
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return response()->json([
            'success' => true,
            'message' => 'Murojaat o\'chirildi.',
        ]);
    }
}
