<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class SubDistrict extends Model
{
    use HasFactory;
    protected $guarded = [];

    final public function getSubDistricsList(): Builder|Collection
    {
        return self::query()->select('id', 'name')->get();
    }
}