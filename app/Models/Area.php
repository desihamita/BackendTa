<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class Area extends Model
{
    use HasFactory;
    protected $guarded = [];

    final public function getAreaBySubDistrictId(int $id): Builder|Collection
    {
        return self::query()->select('id as value', 'name as label')->where('sub_district_id', $id)->get();
    }
}