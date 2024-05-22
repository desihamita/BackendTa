<?php

namespace App\Models;

use App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class Brand extends Model
{
    public const IMAGE_UPLOAD_PATH = 'images/uploads/brand/';
    public const THUMB_IMAGE_UPLOAD_PATH = 'images/uploads/brand_thumb/';

    public const STATUS_ACTIVE = 1;

    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'serial',
        'status',
        'description',
        'logo',
        'user_id'
    ];

    final public function storeBrand(array $input): Model
    {
        return self::query()->create($input);
    }

    final public function getAllBrands(array $input): LengthAwarePaginator
    {
        $per_page = $input['per_page'] ?? 10;
        $query = self::query();

        if (!empty($input['search'])) {
            $query->where('name', 'like', '%'.$input['search'].'%');
        }

        if (!empty($input['order_by'])) {
            $query->orderBy($input['order_by'], $input['direction'] ?? 'asc');
        }

        return $query->with('user:id,name')->paginate($per_page);
    }

    final public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    final public function getBrandIdAndName(): Builder|Collection
    {
        return self::query()->select('id', 'name')->where('status', self::STATUS_ACTIVE)->get();
    }
}