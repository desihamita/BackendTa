<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'addressable_type',
        'addressable_id',
        'address',
        'status',
        'type',
        'division_id',
        'district_id',
        'sub_district_id',
        'area_id'
    ];

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const SUPPLIER_ADDRESS = 1;
    const CUSTOMER_PERMANENT_ADDRESS = 2;
    const CUSTOMER_PRESENT_ADDRESS = 3;

    final public function prepareData(array $input): array
    {
        $address['address'] = $input['details'] ?? '';
        $address['status'] = self::STATUS_ACTIVE;
        $address['type'] = self::SUPPLIER_ADDRESS;

        $address['division_id'] = $input['division_id'] ?? '';
        $address['district_id'] = $input['district_id'] ?? '';
        $address['sub_district_id'] = $input['sub_district_id'] ?? '';
        $address['area_id'] = $input['area_id'] ?? '';
        return $address;
    }

    final public function addressable(): MorphTo
    {
        return $this->morphTo();
    }
}