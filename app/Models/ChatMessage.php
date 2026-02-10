<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    protected $fillable = [
        'conversation_id',
        'sender_type',
        'sender_id',
        'message',
        'is_read',
        'metadata',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'metadata' => 'array',
    ];

    const SENDER_USER = 'user';
    const SENDER_BOT = 'bot';
    const SENDER_ADMIN = 'admin';

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ChatConversation::class, 'conversation_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function markAsRead(): void
    {
        $this->update(['is_read' => true]);
    }

    public function isFromBot(): bool
    {
        return $this->sender_type === self::SENDER_BOT;
    }

    public function isFromUser(): bool
    {
        return $this->sender_type === self::SENDER_USER;
    }

    public function isFromAdmin(): bool
    {
        return $this->sender_type === self::SENDER_ADMIN;
    }
}
