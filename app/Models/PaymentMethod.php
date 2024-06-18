<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class PaymentMethod extends Model
{
    use HasFactory;

    final public function getPaymentMethodList(): Builder|Collection
    {
        return self::query()->select(['id', 'name', 'account_number'])->get();
    }
}