<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\Category;
use App\Models\Brand;
use App\Models\Supplier;
use App\Models\SubCategory;
use App\Models\Country;
use App\Models\User;
use App\Models\ProductPhoto;
use App\Models\ProductAttribute;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'brand_id',
        'category_id' ,
        'sub_category_id',
        'supplier_id',
        'country_id',
        'name',
        'slug',
        'status',
        'cost',
        'price',
        'discount_end',
        'discount_fixed',
        'discount_percent',
        'discount_start',
        'stock',
        'sku',
        'description',
        'created_by_id',
        'updated_by_id',
    ];

    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 0;

    final public function storeProduct(array $input, int $auth_id): mixed
    {
        return self::create($this->prepareData($input, $auth_id));
    }

    private function prepareData(array $input, int $auth_id): array
    {
        return [
            'brand_id' => $input['brand_id'] ?? '',
            'category_id' => $input['category_id'] ?? '',
            'sub_category_id' => $input['sub_category_id'] ?? '',
            'supplier_id' => $input['supplier_id'] ?? '',
            'country_id' => $input['country_id'] ?? '',
            'created_by_id' => $auth_id,
            'updated_by_id' => $auth_id,

            'name' => $input['name'] ?? '',
            'slug' => $input['slug'] ? Str::slug($input['slug']) : '',
            'status' => $input['status'] ?? '',
            'cost' => $input['cost'] ?? '',
            'price' => $input['price'] ?? '',
            'discount_end' => $input['discount_end'] ?? '',
            'discount_fixed' => $input['discount_fixed'] ?? '',
            'discount_percent' => $input['discount_percent'] ?? '',
            'discount_start' => $input['discount_start'] ?? '',
            'stock' => $input['stock'] ?? '',
            'sku' => $input['sku'] ?? '',
            'description' => $input['description'] ?? '',
        ];
    }

    final public function getProductById(int $id): Builder|Collection|Model|null
    {
        return self::query()->findOrFail($id);
    }

    final public function getProductList($input)
    {
        $per_page = $input['per_page'] ?? 10;
        $query = self::query()->with([
            'category:id,name',
            'sub_category:id,name',
            'brand:id,name',
            'supplier:id,name,phone',
            'created_by:id,name',
            'updated_by:id,name',
            'primary_photo',
            'product_attributes',
            'product_attributes.attributes',
            'product_attributes.values',
        ]);

        if (!empty($input['search'])) {
            $query->where('name', 'like', '%'.$input['search'].'%')
                ->orWhere('sku', 'like', '%'.$input['search'].'%');
        }

        if (!empty($input['order_by'])) {
            $query->orderBy($input['order_by'], $input['direction'] ?? 'asc');
        }

        return $query->paginate($per_page);
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
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    final public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    final public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    final public function created_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    final public function updated_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    final public function primary_photo(): HasOne
    {
        return $this->hasOne(ProductPhoto::class)->where('is_primary', 1);
    }

    final public function product_attributes(): HasMany
    {
        return $this->hasMany(ProductAttribute::class);
    }
}