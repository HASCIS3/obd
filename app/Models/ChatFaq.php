<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ChatFaq extends Model
{
    protected $fillable = [
        'category',
        'keywords',
        'question',
        'answer',
        'priority',
        'actif',
    ];

    protected $casts = [
        'keywords' => 'array',
        'actif' => 'boolean',
    ];

    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public static function findBestMatch(string $userMessage): ?self
    {
        $userMessage = Str::lower($userMessage);
        $words = preg_split('/\s+/', $userMessage);
        
        $faqs = self::actif()->orderBy('priority', 'desc')->get();
        
        $bestMatch = null;
        $bestScore = 0;

        foreach ($faqs as $faq) {
            $score = 0;
            $keywords = $faq->keywords ?? [];
            
            foreach ($keywords as $keyword) {
                $keyword = Str::lower($keyword);
                if (Str::contains($userMessage, $keyword)) {
                    $score += 2;
                }
                foreach ($words as $word) {
                    if (strlen($word) > 2 && Str::contains($keyword, $word)) {
                        $score += 1;
                    }
                }
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $faq;
            }
        }

        return $bestScore >= 2 ? $bestMatch : null;
    }
}
