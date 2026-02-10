<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatFaq;
use App\Services\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    protected ChatbotService $chatbotService;

    public function __construct(ChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    /**
     * Envoyer un message au chatbot
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'session_id' => 'nullable|string|max:100',
        ]);

        $user = $request->user();
        $sessionId = $request->input('session_id');

        $conversation = $this->chatbotService->getOrCreateConversation($user, $sessionId);
        $result = $this->chatbotService->processMessage($conversation, $request->input('message'), $user);

        return response()->json([
            'success' => true,
            'data' => [
                'conversation_id' => $conversation->id,
                'session_id' => $conversation->session_id,
                'user_message' => [
                    'id' => $result['user_message']->id,
                    'message' => $result['user_message']->message,
                    'created_at' => $result['user_message']->created_at->toIso8601String(),
                ],
                'bot_response' => [
                    'id' => $result['bot_message']->id,
                    'message' => $result['bot_message']->message,
                    'created_at' => $result['bot_message']->created_at->toIso8601String(),
                ],
                'escalated' => $result['escalated'],
            ],
        ]);
    }

    /**
     * Récupérer l'historique d'une conversation
     */
    public function getHistory(Request $request): JsonResponse
    {
        $request->validate([
            'conversation_id' => 'nullable|integer|exists:chat_conversations,id',
            'session_id' => 'nullable|string|max:100',
        ]);

        $user = $request->user();
        $conversationId = $request->input('conversation_id');
        $sessionId = $request->input('session_id');

        $conversation = null;

        if ($conversationId) {
            $conversation = ChatConversation::find($conversationId);
            if ($user && $conversation->user_id !== $user->id) {
                return response()->json(['error' => 'Accès non autorisé'], 403);
            }
        } elseif ($sessionId) {
            $conversation = ChatConversation::where('session_id', $sessionId)->latest()->first();
        } elseif ($user) {
            $conversation = ChatConversation::where('user_id', $user->id)
                ->where('status', '!=', ChatConversation::STATUS_CLOSED)
                ->latest()
                ->first();
        }

        if (!$conversation) {
            return response()->json([
                'success' => true,
                'data' => [
                    'messages' => [],
                    'conversation' => null,
                ],
            ]);
        }

        $messages = $this->chatbotService->getConversationHistory($conversation);

        return response()->json([
            'success' => true,
            'data' => [
                'conversation' => [
                    'id' => $conversation->id,
                    'session_id' => $conversation->session_id,
                    'status' => $conversation->status,
                    'created_at' => $conversation->created_at->toIso8601String(),
                ],
                'messages' => $messages,
            ],
        ]);
    }

    /**
     * Fermer une conversation
     */
    public function closeConversation(Request $request): JsonResponse
    {
        $request->validate([
            'conversation_id' => 'required|integer|exists:chat_conversations,id',
        ]);

        $user = $request->user();
        $conversation = ChatConversation::find($request->input('conversation_id'));

        if ($user && $conversation->user_id !== $user->id) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $conversation->close();

        return response()->json([
            'success' => true,
            'message' => 'Conversation fermée',
        ]);
    }

    /**
     * Demander à parler à un humain
     */
    public function escalateToSupport(Request $request): JsonResponse
    {
        $request->validate([
            'conversation_id' => 'required|integer|exists:chat_conversations,id',
        ]);

        $conversation = ChatConversation::find($request->input('conversation_id'));
        $conversation->escalateToSupport();

        return response()->json([
            'success' => true,
            'message' => 'Votre demande a été transmise à notre équipe.',
        ]);
    }

    /**
     * Récupérer les FAQ disponibles
     */
    public function getFaqs(): JsonResponse
    {
        $faqs = ChatFaq::actif()
            ->orderBy('category')
            ->orderBy('priority', 'desc')
            ->get()
            ->groupBy('category')
            ->map(function ($items) {
                return $items->map(function ($faq) {
                    return [
                        'id' => $faq->id,
                        'question' => $faq->question,
                        'answer' => $faq->answer,
                    ];
                });
            });

        return response()->json([
            'success' => true,
            'data' => $faqs,
        ]);
    }

    /**
     * Message de bienvenue initial
     */
    public function welcome(Request $request): JsonResponse
    {
        $user = $request->user();
        $greeting = $user 
            ? "Bonjour {$user->name} ! 👋 Je suis l'assistant virtuel de l'Olympiade de Baco-Djicoroni. Comment puis-je vous aider ?"
            : "Bonjour ! 👋 Je suis l'assistant virtuel de l'Olympiade de Baco-Djicoroni. Comment puis-je vous aider ?";

        $suggestions = [
            'Quels sont les horaires ?',
            'Quelles disciplines proposez-vous ?',
            'Comment s\'inscrire ?',
            'Quels sont les tarifs ?',
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'message' => $greeting,
                'suggestions' => $suggestions,
            ],
        ]);
    }
}
