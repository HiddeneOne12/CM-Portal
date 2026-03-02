<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interview extends Model
{
    protected $table = 'interviews';

    protected $fillable = [
        'status',
        'image',
        'title',
        'description',
        'participant_id',
        'video_link',
        'video',
        'interview_image',
        'duration',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    public function scopeEnabled($query)
    {
        return $query->where('status', 1);
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    /**
     * Get the embed URL for the video (YouTube or Vimeo) for use in iframes. Used when video is not uploaded.
     */
    public function getVideoEmbedUrlAttribute(): ?string
    {
        if (!empty($this->video)) {
            return null;
        }
        $url = $this->video_link;
        if (empty($url)) {
            return null;
        }
        if (preg_match('#(?:youtube\.com/watch\?v=|youtu\.be/)([a-zA-Z0-9_-]{11})#i', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1] . '?rel=0';
        }
        if (preg_match('#vimeo\.com/(?:video/)?(\d+)#i', $url, $m)) {
            return 'https://player.vimeo.com/video/' . $m[1];
        }
        return null;
    }
}
