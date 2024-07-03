<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Transaction;
use App\Models\Address;
use App\Models\User;

class SalesManager extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $hidden = [
        'password',
    ];

    protected $fillable = [
        'name',
        'phone',
        'email',
        'status',
        'bio',
        'photo',
        'user_id',
        'shop_id',
        'password'
    ];

    public const STATUS_ACTIVE = 1;
    public const STATUS_ACTIVE_TEXT = 'Active';

    public const STATUS_INACTIVE = 0;
    public const STATUS_INACTIVE_TEXT = 'Inactive';

    public const PHOTO_WIDTH = 800;
    public const PHOTO_HEIGHT = 800;
    public const PHOTO_THUMB_WIDTH = 200;
    public const PHOTO_THUMB_HEIGHT = 200;

    public const PHOTO_UPLOAD_PATH = 'images/uploads/sales_manager/';
    public const THUMB_PHOTO_UPLOAD_PATH = 'images/uploads/sales_manager_thumb/';

    final public function prepareData(array $input, $auth): array
    {
        $sales_manager['bio'] = $input['bio'] ?? null;
        $sales_manager['email'] = $input['email'] ?? null;
        $sales_manager['name'] = $input['name'] ?? null;
        $sales_manager['phone'] = $input['phone'] ?? null;
        $sales_manager['status'] = isset($input['status']) && $input['status'] !== 0 ? (int)$input['status'] : self::STATUS_INACTIVE;
        $sales_manager['user_id'] = $auth->id();
        $sales_manager['shop_id'] = $input['shop_id'] ?? null;

        if (isset($input['password'])) {
            $sales_manager['password'] = Hash::make($input['password']);
        }

        return $sales_manager;
    }

    final public function getSalesManagerList($input): LengthAwarePaginator
    {
        $per_page = $input['per_page'] ?? 10;
        $query = self::query()->with(
            'address',
            'address.division:id,name',
            'address.district:id,name',
            'address.subDistrict:id,name',
            'address.area:id,name',
            'user:id,name',
            'shop:id,name'
        );

        if (!empty($input['search'])) {
            $query->where('name', 'like', '%'.$input['search'].'%')
                ->orWhere('phone', 'like', '%'.$input['search'].'%')
                ->orWhere('email', 'like', '%'.$input['search'].'%');
        }

        if (!empty($input['order_by'])) {
            $query->orderBy($input['order_by'], $input['direction'] ?? 'asc');
        }

        return $query->paginate($per_page);
    }

    final public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    final public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    final public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    final public function getUserByEmailOrPhone(array $input): ?self
    {
        return self::query()->where('email', $input['email'])->orWhere('phone', $input['email'])->first();
    }

    final public function transaction(): MorphOne
    {
        return $this->morphOne(Transaction::class, 'transactionable');
    }

    public function getSalesManagerIdAndName()
    {
        return self::query()->select('id', 'name')->get();
    }
}
