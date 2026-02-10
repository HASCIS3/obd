<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatFaq;
use App\Models\ChatMessage;
use App\Services\ChatbotService;
use Illuminate\Http\Request;

class ChatbotAdminController extends Controller
{
    protected ChatbotService $chatbotService;

    public function __construct(ChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    /**
     * Dashboard du chatbot
     */
    public function index()
    {
        $stats = [
            'total_conversations' => ChatConversation::count(),
            'pending_support' => ChatConversation::where('status', ChatConversation::STATUS_PENDING_SUPPORT)->count(),
            'active_conversations' => ChatConversation::where('status', ChatConversation::STATUS_ACTIVE)->count(),
            'total_messages' => ChatMessage::count(),
            'total_faqs' => ChatFaq::count(),
        ];

        $pendingConversations = ChatConversation::where('status', ChatConversation::STATUS_PENDING_SUPPORT)
            ->with(['user', 'lastMessage'])
            ->orderBy('last_message_at', 'desc')
            ->take(10)
            ->get();

        $recentConversations = ChatConversation::with(['user', 'lastMessage'])
            ->orderBy('last_message_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.chatbot.index', compact('stats', 'pendingConversations', 'recentConversations'));
    }

    /**
     * Liste des conversations
     */
    public function conversations(Request $request)
    {
        $query = ChatConversation::with(['user', 'lastMessage', 'assignedAdmin']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $conversations = $query->orderBy('last_message_at', 'desc')->paginate(20);

        return view('admin.chatbot.conversations', compact('conversations'));
    }

    /**
     * Voir une conversation
     */
    public function showConversation(ChatConversation $conversation)
    {
        $messages = $conversation->messages()->orderBy('created_at', 'asc')->get();
        $this->chatbotService->markMessagesAsRead($conversation);

        return view('admin.chatbot.conversation-show', compact('conversation', 'messages'));
    }

    /**
     * Répondre à une conversation
     */
    public function replyConversation(Request $request, ChatConversation $conversation)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $this->chatbotService->sendAdminMessage($conversation, auth()->user(), $request->message);

        return redirect()->route('admin.chatbot.conversations.show', $conversation)
            ->with('success', 'Message envoyé');
    }

    /**
     * Fermer une conversation
     */
    public function closeConversation(ChatConversation $conversation)
    {
        $conversation->close();

        return redirect()->route('admin.chatbot.conversations')
            ->with('success', 'Conversation fermée');
    }

    /**
     * Liste des FAQ
     */
    public function faqs()
    {
        $faqs = ChatFaq::orderBy('category')->orderBy('priority', 'desc')->paginate(20);

        return view('admin.chatbot.faqs', compact('faqs'));
    }

    /**
     * Formulaire de création FAQ
     */
    public function createFaq()
    {
        return view('admin.chatbot.faq-form', ['faq' => null]);
    }

    /**
     * Enregistrer une FAQ
     */
    public function storeFaq(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:100',
            'question' => 'required|string|max:500',
            'answer' => 'required|string|max:2000',
            'keywords' => 'required|string',
            'priority' => 'integer|min:0|max:100',
            'actif' => 'boolean',
        ]);

        $validated['keywords'] = array_map('trim', explode(',', $validated['keywords']));
        $validated['actif'] = $request->boolean('actif', true);

        ChatFaq::create($validated);

        return redirect()->route('admin.chatbot.faqs')
            ->with('success', 'FAQ créée avec succès');
    }

    /**
     * Formulaire d'édition FAQ
     */
    public function editFaq(ChatFaq $faq)
    {
        return view('admin.chatbot.faq-form', compact('faq'));
    }

    /**
     * Mettre à jour une FAQ
     */
    public function updateFaq(Request $request, ChatFaq $faq)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:100',
            'question' => 'required|string|max:500',
            'answer' => 'required|string|max:2000',
            'keywords' => 'required|string',
            'priority' => 'integer|min:0|max:100',
            'actif' => 'boolean',
        ]);

        $validated['keywords'] = array_map('trim', explode(',', $validated['keywords']));
        $validated['actif'] = $request->boolean('actif', true);

        $faq->update($validated);

        return redirect()->route('admin.chatbot.faqs')
            ->with('success', 'FAQ mise à jour');
    }

    /**
     * Supprimer une FAQ
     */
    public function destroyFaq(ChatFaq $faq)
    {
        $faq->delete();

        return redirect()->route('admin.chatbot.faqs')
            ->with('success', 'FAQ supprimée');
    }
}
