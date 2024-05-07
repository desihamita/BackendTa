<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'serial',
        'status',
        'description',
        'photo',
        'user_id'
    ];

    final public function storeCategory(array $input):Builder|Model
    {
        return self::query()->create($input);
    }
}