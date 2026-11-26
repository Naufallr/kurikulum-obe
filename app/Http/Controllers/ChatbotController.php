<?php

namespace App\Http\Controllers;

use App\Models\CapaianLulusanProdi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $apiKey = config('services.gemini.key');

        if (blank($apiKey)) {
            return response()->json([
                'answer' => 'Konfigurasi Gemini belum tersedia. Silakan isi GEMINI_API_KEY di file .env.',
            ], 500);
        }
        
        $cplData = CapaianLulusanProdi::query()->orderBy('id')->get()->map(function ($item) {
            return "- {$item->id}: {$item->desk}";
        })->implode("\n");

        $systemInstruction = "Anda adalah asisten cerdas Kurikulum OBE Universitas Kebangsaan Republik Indonesia (UKRI). 
        Tugas Anda adalah membantu dosen dan admin memahami Outcome Based Education (OBE), CPL, CPMK, dan pemetaan kurikulum. 
        
        Berikut adalah daftar CPL resmi yang ada di database saat ini:
        $cplData

        Gunakan data di atas untuk menjawab jika pengguna bertanya tentang CPL. Jika data tidak ada, sarankan mereka untuk menambahkannya di menu manajemen.
        Jawablah dengan bahasa Indonesia yang profesional namun ramah.";

        try {
            foreach (['HTTP_PROXY', 'HTTPS_PROXY', 'ALL_PROXY', 'http_proxy', 'https_proxy', 'all_proxy'] as $proxyVariable) {
                putenv($proxyVariable);
                unset($_ENV[$proxyVariable], $_SERVER[$proxyVariable]);
            }

            $curlOptions = [];
            if (defined('CURLOPT_PROXY')) {
                $curlOptions[CURLOPT_PROXY] = '';
            }
            if (defined('CURLOPT_NOPROXY')) {
                $curlOptions[CURLOPT_NOPROXY] = '*';
            }

            $response = Http::timeout(30)->withOptions([
                'proxy' => [
                    'http' => null,
                    'https' => null,
                    'no' => ['*'],
                ],
                'curl' => $curlOptions,
            ])->withHeaders([
                'Content-Type' => 'application/json',
                'x-goog-api-key' => $apiKey,
            ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent', [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $systemInstruction . "\n\nPertanyaan pengguna: " . $request->message]
                        ]
                    ]
                ]
            ]);

            if ($response->failed()) {
                $message = $response->json('error.message')
                    ?? 'Gemini belum bisa memproses permintaan saat ini.';

                return response()->json([
                    'answer' => "Maaf, AI Agent mengembalikan error: {$message}",
                ], $response->status());
            }

            $result = $response->json();
            $answer = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Maaf, saya tidak dapat memproses permintaan Anda saat ini.';

            return response()->json(['answer' => $answer]);
        } catch (\Throwable $e) {
            Log::warning('Chatbot Gemini request failed.', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'answer' => 'Koneksi ke AI Agent gagal: '.$e->getMessage(),
            ], 500);
        }
    }
}
