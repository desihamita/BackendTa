<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

class District extends Model
{
    use HasFactory;
    protected $guarded = [];

    final public function getDistrictByDivisionId(string $id): Builder|Collection
    {
        return self::query()->select('id', 'name')->where('division_id', $division_id)->get();
    }
}