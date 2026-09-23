<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class ContactController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Contact::latest()->paginate(20),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'specialty' => 'nullable|string|max:255',
            'message' => 'nullable|string',
        ]);

        $contact = Contact::create($validated);

        // Send Telegram Notification
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_GROUP_CHAT_ID');

        if ($botToken && $chatId) {
            $text = "📩 <b>Yangi murojaat!</b>\n\n";
            $text .= "👤 <b>Ism:</b> " . htmlspecialchars($contact->name) . "\n";
            $text .= "📞 <b>Telefon:</b> " . htmlspecialchars($contact->phone) . "\n";
            if ($contact->specialty) {
                $text .= "🩺 <b>Mutaxassislik:</b> " . htmlspecialchars($contact->specialty) . "\n";
            }
            if ($contact->message) {
                $text .= "💬 <b>Xabar:</b> " . htmlspecialchars($contact->message) . "\n";
            }
            $text .= "\n🕒 <b>Sana:</b> " . $contact->created_at->format('Y-m-d H:i');

            try {
                Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $text,
                    'parse_mode' => 'HTML',
                ]);
            } catch (\Exception $e) {
                // Ignore telegram errors to not break the user experience
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Murojaatingiz qabul qilindi. Tez orada siz bilan bog\'lanamiz.',
            'data' => $contact,
        ], 201);
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
