<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\User;

class Category extends Model
{
    public const IMAGE_UPLOAD_PATH = 'images/uploads/category/';
    public const THUMB_IMAGE_UPLOAD_PATH = 'images/uploads/category_thumb/';

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

    final public function storeCategory(array $input): Model
    {
        return self::query()->create($input);
    }

    final public function getAllCategories(array $input): LengthAwarePaginator
    {
        $per_page = $input['per_page'] ?? 10;
        $query = self::query();
        if (!empty($input['search'])) {
            $query->where('name', 'like', '%'.$input['search'].'%');
        }
        if (!empty($input['order_by'])) {
            $query->where($input['order_by'], $input['direction'] ?? 'asc');
        }
        return $query->with('user:id,name')->paginate(10);
    }

    final public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}