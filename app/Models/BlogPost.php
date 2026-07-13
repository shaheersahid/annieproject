<?php

namespace App\Models;

use App\Traits\AutoGeneratesSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    use HasFactory, AutoGeneratesSlug;

    protected $fillable = [
        'author_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'toc',
        'featured_image',
        'status',
        'published_at',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'toc'          => 'array',
        'published_at' => 'datetime',
    ];

    public function getSlugSourceColumn(): string
    {
        return 'title';
    }

    protected static function booted(): void
    {
        static::saving(function (BlogPost $post) {
            if ($post->isDirty('content') || $post->toc === null) {
                $post->toc = static::parseToc($post->content ?? '');
            }
        });
    }

    public static function parseToc(string $html): array
    {
        $toc = [];
        preg_match_all('/<h([23])(?:[^>]*)>(.*?)<\/h[23]>/is', $html, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $text = strip_tags($match[2]);
            $toc[] = [
                'level'  => (int) $match[1],
                'text'   => $text,
                'anchor' => Str::slug($text),
            ];
        }

        return $toc;
    }

    public function getRenderedContentAttribute(): string
    {
        return preg_replace_callback(
            '/<h([23])([^>]*)>(.*?)<\/h[23]>/is',
            function ($m) {
                $anchor = Str::slug(strip_tags($m[3]));
                return "<h{$m[1]}{$m[2]} id=\"{$anchor}\">{$m[3]}</h{$m[1]}>";
            },
            $this->content ?? ''
        );
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopePublished($query)
    {
        return $query
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }
}
