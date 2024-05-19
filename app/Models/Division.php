<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

class Division extends Model
{
    use HasFactory;
    protected $guarded = [];

    final public function getDivisionList(): Builder|Collection
    {
        return self::query()->select('id', 'name')->get();
    }
}