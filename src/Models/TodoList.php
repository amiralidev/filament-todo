<?php

namespace Amiralidev\Filament\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TodoList extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'sort_order',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(TodoItem::class)->orderBy('sort_order');
    }
}
