<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TodoList extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'position',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class)->orderBy('position');
    }

    public function openTasks(): HasMany
    {
        return $this->tasks()->whereNull('completed_at');
    }

    public function completedTasks(): HasMany
    {
        return $this->tasks()->whereNotNull('completed_at');
    }
}
