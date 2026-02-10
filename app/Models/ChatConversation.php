<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatConversation extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'status',
        'assigned_admin_id',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_PENDING_SUPPORT = 'pending_support';
    const STATUS_CLOSED = 'closed';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_admin_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id');
    }

    public function lastMessage()
    {
        return $this->hasOne(ChatMessage::class, 'conversation_id')->latest();
    }

    public function unreadMessages(): HasMany
    {
        return $this->messages()->where('is_read', false);
    }

    public function escalateToSupport(): void
    {
        $this->update(['status' => self::STATUS_PENDING_SUPPORT]);
    }

    public function close(): void
    {
        $this->update(['status' => self::STATUS_CLOSED]);
    }
}
