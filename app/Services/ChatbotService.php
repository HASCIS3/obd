<?php

namespace App\Services;

use App\Models\ChatConversation;
use App\Models\ChatFaq;
use App\Models\ChatMessage;
use App\Models\User;

class ChatbotService
{
    protected array $defaultResponses = [
        'greeting' => "Bonjour ! 👋 Je suis l'assistant virtuel de l'Olympiade de Baco-Djicoroni. Comment puis-je vous aider ?",
        'fallback' => "Je n'ai pas compris votre question. Voulez-vous parler à un membre de notre équipe ?",
        'escalated' => "Votre demande a été transmise à notre équipe. Un membre vous répondra bientôt.",
        'goodbye' => "Merci de votre visite ! À bientôt au centre sportif ! 🏃‍♂️",
    ];

    protected array $greetingKeywords = ['bonjour', 'salut', 'hello', 'bonsoir', 'coucou', 'hi'];
    protected array $goodbyeKeywords = ['merci', 'au revoir', 'bye', 'à bientôt', 'aurevoir'];
    protected array $escalateKeywords = ['humain', 'personne', 'parler', 'aide', 'problème', 'urgent'];

    public function getOrCreateConversation(?User $user, ?string $sessionId = null): ChatConversation
    {
        if ($user) {
            $conversation = ChatConversation::where('user_id', $user->id)
                ->where('status', '!=', ChatConversation::STATUS_CLOSED)
                ->latest()
                ->first();

            if ($conversation) {
                return $conversation;
            }

            return ChatConversation::create([
                'user_id' => $user->id,
                'status' => ChatConversation::STATUS_ACTIVE,
                'last_message_at' => now(),
            ]);
        }

        if ($sessionId) {
            $conversation = ChatConversation::where('session_id', $sessionId)
                ->where('status', '!=', ChatConversation::STATUS_CLOSED)
                ->latest()
                ->first();

            if ($conversation) {
                return $conversation;
            }

            return ChatConversation::create([
                'session_id' => $sessionId,
                'status' => ChatConversation::STATUS_ACTIVE,
                'last_message_at' => now(),
            ]);
        }

        return ChatConversation::create([
            'session_id' => uniqid('chat_'),
            'status' => ChatConversation::STATUS_ACTIVE,
            'last_message_at' => now(),
        ]);
    }

    public function processMessage(ChatConversation $conversation, string $userMessage, ?User $user = null): array
    {
        // Sauvegarder le message utilisateur
        $userMsg = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => ChatMessage::SENDER_USER,
            'sender_id' => $user?->id,
            'message' => $userMessage,
        ]);

        $conversation->update(['last_message_at' => now()]);

        // Générer la réponse du bot
        $botResponse = $this->generateResponse($userMessage, $conversation);

        // Sauvegarder la réponse du bot
        $botMsg = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => ChatMessage::SENDER_BOT,
            'message' => $botResponse['message'],
            'metadata' => $botResponse['metadata'] ?? null,
        ]);

        return [
            'user_message' => $userMsg,
            'bot_message' => $botMsg,
            'conversation' => $conversation->fresh(),
            'escalated' => $botResponse['escalated'] ?? false,
        ];
    }

    protected function generateResponse(string $userMessage, ChatConversation $conversation): array
    {
        $lowerMessage = mb_strtolower($userMessage);

        // Vérifier les salutations
        if ($this->containsAny($lowerMessage, $this->greetingKeywords)) {
            return [
                'message' => $this->defaultResponses['greeting'],
                'metadata' => ['type' => 'greeting'],
            ];
        }

        // Vérifier les au revoir
        if ($this->containsAny($lowerMessage, $this->goodbyeKeywords)) {
            return [
                'message' => $this->defaultResponses['goodbye'],
                'metadata' => ['type' => 'goodbye'],
            ];
        }

        // Vérifier demande d'escalade
        if ($this->containsAny($lowerMessage, $this->escalateKeywords)) {
            $conversation->escalateToSupport();
            return [
                'message' => $this->defaultResponses['escalated'],
                'metadata' => ['type' => 'escalated'],
                'escalated' => true,
            ];
        }

        // Chercher dans les FAQ
        $faq = ChatFaq::findBestMatch($userMessage);
        if ($faq) {
            return [
                'message' => $faq->answer,
                'metadata' => [
                    'type' => 'faq',
                    'faq_id' => $faq->id,
                    'category' => $faq->category,
                ],
            ];
        }

        // Réponse par défaut avec suggestion d'escalade
        return [
            'message' => $this->defaultResponses['fallback'],
            'metadata' => ['type' => 'fallback'],
        ];
    }

    protected function containsAny(string $text, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (str_contains($text, $keyword)) {
                return true;
            }
        }
        return false;
    }

    public function getConversationHistory(ChatConversation $conversation, int $limit = 50): array
    {
        return $conversation->messages()
            ->orderBy('created_at', 'asc')
            ->limit($limit)
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'sender_type' => $message->sender_type,
                    'message' => $message->message,
                    'is_read' => $message->is_read,
                    'created_at' => $message->created_at->toIso8601String(),
                ];
            })
            ->toArray();
    }

    public function sendAdminMessage(ChatConversation $conversation, User $admin, string $message): ChatMessage
    {
        $msg = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => ChatMessage::SENDER_ADMIN,
            'sender_id' => $admin->id,
            'message' => $message,
        ]);

        $conversation->update([
            'status' => ChatConversation::STATUS_ACTIVE,
            'assigned_admin_id' => $admin->id,
            'last_message_at' => now(),
        ]);

        return $msg;
    }

    public function getPendingSupportConversations()
    {
        return ChatConversation::where('status', ChatConversation::STATUS_PENDING_SUPPORT)
            ->with(['user', 'lastMessage'])
            ->orderBy('last_message_at', 'desc')
            ->get();
    }

    public function markMessagesAsRead(ChatConversation $conversation): void
    {
        $conversation->messages()
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }
}
