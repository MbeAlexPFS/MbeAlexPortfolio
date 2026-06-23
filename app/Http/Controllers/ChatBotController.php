<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatBotController extends Controller
{
    public function chat(Request $request): JsonResponse
    {
        $data = $request->validate([
            'message' => 'required|string|max:1000',
            'history' => 'array',
            'history.*.role' => 'required|in:user,model',
            'history.*.text' => 'required|string',
            'page_title' => 'nullable|string|max:200',
        ]);

        $systemPrompt = $this->buildSystemPrompt($data['page_title'] ?? null);

        $reply = $this->tryGemini($systemPrompt, $data);
        $provider = 'Gemini';

        if ($reply === null) {
            $reply = $this->tryGroq($systemPrompt, $data);
            $provider = 'Groq';
        }

        if ($reply === null) {
            $reply = $this->tryOpenRouter($systemPrompt, $data);
            $provider = 'OpenRouter';
        }

        if ($reply === null) {
            return response()->json(
                [
                    'error' => 'Tous les services IA sont indisponibles pour le moment. Réessaie plus tard.',
                ],
                502,
            );
        }

        return response()->json(['reply' => $reply, 'provider' => $provider]);
    }

    private function tryGemini(string $systemPrompt, array $data): ?string
    {
        $apiKey = config('services.gemini.api_key');

        if (! $apiKey) {
            return null;
        }

        $contents = [];
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $systemPrompt]],
        ];
        $contents[] = ['role' => 'model', 'parts' => [['text' => 'Compris.']]];

        foreach ($data['history'] ?? [] as $msg) {
            $contents[] = [
                'role' => $msg['role'],
                'parts' => [['text' => $msg['text']]],
            ];
        }

        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $data['message']]],
        ];

        $response = Http::timeout(15)->post(
            "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent?key={$apiKey}",
            ['contents' => $contents],
        );

        if ($response->failed()) {
            return null;
        }

        return $response->json('candidates.0.content.parts.0.text');
    }

    private function tryGroq(string $systemPrompt, array $data): ?string
    {
        $apiKey = config('services.groq.api_key');

        if (! $apiKey) {
            return null;
        }

        $messages = [['role' => 'system', 'content' => $systemPrompt]];

        foreach ($data['history'] ?? [] as $msg) {
            $messages[] = [
                'role' => $msg['role'] === 'model' ? 'assistant' : 'user',
                'content' => $msg['text'],
            ];
        }

        $messages[] = ['role' => 'user', 'content' => $data['message']];

        $response = Http::timeout(15)
            ->withHeaders([
                'Authorization' => 'Bearer '.$apiKey,
                'Content-Type' => 'application/json',
            ])
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => config('services.groq.model'),
                'messages' => $messages,
                'max_tokens' => 500,
            ]);

        if ($response->failed()) {
            return null;
        }

        return $response->json('choices.0.message.content');
    }

    private function tryOpenRouter(string $systemPrompt, array $data): ?string
    {
        $apiKey = config('services.openrouter.api_key');

        if (! $apiKey) {
            return null;
        }

        $messages = [['role' => 'system', 'content' => $systemPrompt]];

        foreach ($data['history'] ?? [] as $msg) {
            $messages[] = [
                'role' => $msg['role'] === 'model' ? 'assistant' : 'user',
                'content' => $msg['text'],
            ];
        }

        $messages[] = ['role' => 'user', 'content' => $data['message']];

        $response = Http::timeout(20)
            ->withHeaders([
                'Authorization' => 'Bearer '.$apiKey,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => config('app.url'),
                'X-Title' => config('app.name'),
            ])
            ->post('https://openrouter.ai/api/v1/chat/completions', [
                'model' => config('services.openrouter.model'),
                'messages' => $messages,
                'max_tokens' => 500,
            ]);

        if ($response->failed()) {
            return null;
        }

        return $response->json('choices.0.message.content');
    }

    private function buildSystemPrompt(?string $pageTitle): string
    {
        $pageContext = '';

        if ($pageTitle) {
            $pageContext = "\n\nL'utilisateur consulte actuellement la page : « {$pageTitle} ». Adapte ta réponse au contexte de cette page si pertinent.";
        }

        return <<<PROMPT
        Tu es l'assistant virtuel du portfolio de MbeAlex (Alex). Tu réponds en français uniquement, de façon concise et amicale.

        Voici les informations clés sur le site et son créateur :

        -- **Nom du site** : MbeAlex Portfolio
        -- **Créateur** : Alex Mbe (prénom : Alex, nom : Mbe). Il est développeur web créatif, spécialisé dans la conception de sites web statiques, le design graphique, la création d'affiches, de logos et le montage vidéo.
        -- **Types de projets présentés** : Sites web statiques, Designs, Affiches, Logos, Montages vidéo.
        -- **Fonctionnalités du site** : Galerie de projets, blog avec commentaires, sondages, inscription newsletter, compétences, mode sombre, profil personnalisable.
        -- **Technologies utilisées** : Laravel 13, Tailwind CSS, Alpine.js, MySQL, Vite.
        -- **Contact** : Les visiteurs peuvent envoyer un message via la page Contact.

        Règles :
        -- Réponds en français uniquement.
        -- Sois concis (2-3 phrases max si possible).
        -- Reste dans le contexte du portfolio. Si on te demande hors-sujet, réponds poliment que tu es uniquement dédié au portfolio.
        -- Si on te pose une question sur un projet, un article ou une compétence, invite à explorer la page correspondante.
        -- Ne donne jamais d'informations personnelles comme l'email ou le mot de passe.
        -- Tu peux suggérer les actions disponibles sur le site : naviguer dans les projets, lire le blog, participer aux sondages, contacter MbeAlex.{$pageContext}
        PROMPT;
    }
}
