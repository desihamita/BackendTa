<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Models\Address;
use App\Models\User;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'status',
        'details',
        'logo',
        'user_id'
    ];

    public const STATUS_ACTIVE = 1;
    public const STATUS_ACTIVE_TEXT = 'Active';

    public const STATUS_INACTIVE = 0;
    public const STATUS_INACTIVE_TEXT = 'Inactive';

    public const LOGO_WIDTH = 800;
    public const LOGO_HEIGHT = 800;
    public const LOGO_THUMB_WIDTH = 200;
    public const LOGO_THUMB_HEIGHT = 200;

    public const IMAGE_UPLOAD_PATH = 'images/uploads/shop/';
    public const THUMB_IMAGE_UPLOAD_PATH = 'images/uploads/shop_thumb/';

    final public function prepareData(array $input, $auth): array
    {
        $shop['details'] = $input['details'] ?? '';
        $shop['email'] = $input['email'] ?? '';
        $shop['name'] = $input['name'] ?? '';
        $shop['phone'] = $input['phone'] ?? '';
        $shop['status'] = isset($input['status']) && $input['status'] !== '' ? (int)$input['status'] : self::STATUS_INACTIVE;
        $shop['user_id'] = $auth->id();
        return $shop;
    }

    final public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    final public function getShopList($input): LengthAwarePaginator
    {
        $per_page = $input['per_page'] ?? 10;
        $query = self::query()->with(
            'address',
            'address.division:id,name',
            'address.district:id,name',
            'address.subDistrict:id,name',
            'address.area:id,name',
            'user:id,name'
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

    final public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    final public function getSupplierSelectList(): Builder|Collection
    {
        return self::query()
            ->select('id','name', 'phone')
            ->where('status', self::STATUS_ACTIVE)
            ->get();
    }

    public function getShopListIdName()
    {
        return self::query()->select(['id', 'name'])->where('status', 1)->get();
    }
}
