<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use App\Manager\ImageManager;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use App\Models\Brand;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Supplier;
use App\Models\User;
use App\Models\AttributeValue;

class Attribute extends Model
{
    use HasFactory;

    public const PHOTO_UPLOAD_PATH = 'images/uploads/attribute/';
    public const THUMB_PHOTO_UPLOAD_PATH = 'images/uploads/attribute_thumb/';

    public const PHOTO_WIDTH = 800;
    public const PHOTO_HEIGHT = 800;
    public const PHOTO_THUMB_WIDTH = 200;
    public const PHOTO_THUMB_HEIGHT = 200;

    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 0;

    protected $fillable = [
        'name',
        'slug',
        'status',
        'price',
        'stock',
        'sku',
        'photo',
        'description',
        'brand_id',
        'sub_category_id',
        'supplier_id',
        'category_id',
        'created_by_id',
        'updated_by_id'
    ];

    public function storeAttribute(array $input, int $auth_id): mixed
    {
        $attributeData = $this->prepareData($input, $auth_id);

        if(isset($input['photo'])) {
            $name = Str::slug($attributeData['name'] . now());
            $attributeData['photo'] = ImageManager::processImageUpload(
                $input['photo'],
                $name,
                self::PHOTO_UPLOAD_PATH,
                self::THUMB_PHOTO_UPLOAD_PATH,
                self::PHOTO_WIDTH,
                self::PHOTO_HEIGHT,
                self::PHOTO_THUMB_WIDTH,
                self::PHOTO_THUMB_HEIGHT
            );
        }

        return self::create($attributeData);
    }

    private function prepareData(array $input, int $auth_id): array
    {
        return [
            'category_id' => $input['category_id'] ?? '',
            'sub_category_id' => $input['sub_category_id'] ?? '',
            'brand_id' => $input['brand_id'] ?? '',
            'supplier_id' => $input['supplier_id'] ?? '',
            'created_by_id' => $auth_id,
            'updated_by_id' => $auth_id,
            'name' => $input['name'] ?? '',
            'slug' => $input['name'] ?? '',
            'price' => $input['price'] ?? '',
            'photo' => $input['photo'] ?? '',
            'stock' => $input['stock'] ?? '',
            'sku' => $input['sku'] ?? '',
            'description' => $input['description'] ?? '',
            'status' => $input['status'] ?? '',
        ];
    }

    final public function getAttributeList(array $input): LengthAwarePaginator
    {
        $per_page = $input['per_page'] ?? 10;
        $query = self::query()->with([
            'category:id,name',
            'sub_category:id,name',
            'brand:id,name',
            'supplier:id,name',
            'created_by:id,name',
            'updated_by:id,name',
        ]);

        if (!empty($input['search'])) {
            $query->where(function($q) use ($input) {
                $q->where('name', 'like', '%'.$input['search'].'%')
                ->orWhere('sku', 'like', '%'.$input['search'].'%');
            });
        }

        if (!empty($input['order_by'])) {
            $query->orderBy($input['order_by'], $input['direction'] ?? 'asc');
        }

        return $query->paginate($per_page);
    }

    final public function getAttributeById(int $id): Builder|Collection|Model|null
    {
        return self::query()->findOrFail($id);
    }

    final public function getAttributeListWithValue()
    {
        return self::query()->select('id', 'name', 'stock')->get();
    }

    public function getAllAttribute($columns = ['*'])
    {
        $attributes = DB::table('attributes')->select($columns)->get();
        return collect($attributes);
    }

    final public function created_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    final public function updated_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    final public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    final public function sub_category(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    final public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    final public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function getBahanBakuForBarcode($input)
    {
        $query = self::query()->select(
            'id',
            'name',
            'sku',
            'price',
        );

        if(!empty(isset($input['name']))) {
            $query->where('name', 'like', '%'.$input['name'].'%');
        }
        if(!empty(isset($input['category_id']))) {
            $query->where('category_id', $input['category_id']);
        }
        if(!empty(isset($input['sub_category_id']))) {
            $query->where('sub_category_id', $input['sub_category_id']);
        }
        return $query->get();
    }
}