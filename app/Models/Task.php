<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'todo_list_id',
        'title',
        'notes',
        'completed_at',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
        ];
    }

    public function list(): BelongsTo
    {
        return $this->belongsTo(TodoList::class, 'todo_list_id');
    }

    public function isComplete(): bool
    {
        return $this->completed_at !== null;
    }
}
