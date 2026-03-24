<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgressLog extends Model
{
    protected $fillable = [
        'user_id', 
        'lesson_id', 
        'time_spent_seconds', 
        'is_completed'
    ];

    // Using custom timestamps from schema
    const CREATED_AT = 'last_accessed';
    const UPDATED_AT = 'last_accessed';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
