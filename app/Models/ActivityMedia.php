<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ActivityMedia extends Model
{
    protected $table = 'activity_medias';

    protected $fillable = [
        'activity_id',
        'type',
        'url',
        'titre',
        'description',
        'ordre',
    ];

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function getFullUrlAttribute(): string
    {
        if (str_starts_with($this->url, 'http')) {
            return $this->url;
        }
        return Storage::url($this->url);
    }

    public function isPhoto(): bool
    {
        return $this->type === 'photo';
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    public function getEmbedUrlAttribute(): ?string
    {
        if (!$this->isVideo()) {
            return null;
        }

        $url = $this->url;

        if (str_contains($url, 'youtube.com/watch')) {
            preg_match('/v=([^&]+)/', $url, $matches);
            return isset($matches[1]) ? "https://www.youtube.com/embed/{$matches[1]}" : $url;
        }

        if (str_contains($url, 'youtu.be/')) {
            $videoId = substr(parse_url($url, PHP_URL_PATH), 1);
            return "https://www.youtube.com/embed/{$videoId}";
        }

        if (str_contains($url, 'vimeo.com/')) {
            $videoId = substr(parse_url($url, PHP_URL_PATH), 1);
            return "https://player.vimeo.com/video/{$videoId}";
        }

        return $url;
    }
}
