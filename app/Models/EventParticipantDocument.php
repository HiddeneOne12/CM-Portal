<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class EventParticipantDocument extends Model
{
    protected $table = 'event_participant_documents';

    protected $fillable = [
        'event_participant_id',
        'title',
        'file_path',
        'file_size',
    ];

    public function eventParticipant(): BelongsTo
    {
        return $this->belongsTo(EventParticipant::class);
    }

    /** Return file size in bytes (from DB or from disk when null). */
    public function getFileSizeBytes(): ?int
    {
        if (isset($this->attributes['file_size']) && (int) $this->attributes['file_size'] > 0) {
            return (int) $this->attributes['file_size'];
        }
        if (!$this->file_path) {
            return null;
        }
        $path = str_replace('storage/', '', $this->file_path);
        if (Storage::disk('public')->exists($path)) {
            return (int) Storage::disk('public')->size($path);
        }
        return null;
    }
}
