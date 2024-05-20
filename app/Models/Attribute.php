<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\User;
use App\Models\AttributeValue;

class Attribute extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'status',
        'user_id'
    ];

    final public function getAttributeList(): LengthAwarePaginator
    {
        return self::query()->with(['user', 'value', 'value.user:id,name'])->orderBy('updated_at', 'desc')->paginate(10);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function value(): HasMany
    {
        return $this->hasMany(AttributeValue::class);
    }
}
