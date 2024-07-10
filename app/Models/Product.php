<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Manager\ImageManager;
use Illuminate\Support\Str;

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
        'category_id' ,
        'sub_category_id',
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
        'photo',
        'created_by_id',
        'updated_by_id',
    ];

    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 0;

    public const PHOTO_WIDTH = 800;
    public const PHOTO_HEIGHT = 800;
    public const PHOTO_THUMB_WIDTH = 200;
    public const PHOTO_THUMB_HEIGHT = 200;

    public const PHOTO_UPLOAD_PATH = 'images/uploads/product/';
    public const THUMB_PHOTO_UPLOAD_PATH = 'images/uploads/product_thumb/';

    final public function storeProduct(array $input, int $auth_id): mixed
    {
        $productData = $this->prepareData($input, $auth_id);

        if(isset($input['photo'])) {
            $name = Str::slug($productData['name'] . now());
            $productData['photo'] = ImageManager::processImageUpload(
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

        return self::create($productData);
    }

    private function prepareData(array $input, int $auth_id): array
    {
        return [
            'category_id' => $input['category_id'] ?? null,
            'sub_category_id' => $input['sub_category_id'] ?? null,
            'created_by_id' => $auth_id,
            'updated_by_id' => $auth_id,
            'name' => $input['name'] ?? '',
            'slug' => $input['slug'] ? Str::slug($input['slug']) : '',
            'status' => $input['status'] ?? '',
            'cost' => $input['cost'] ?? '',
            'price' => $input['price'] ?? '',
            'photo' => $input['photo'] ?? '',
            'discount_end' => $input['discount_end'] ?? null,
            'discount_fixed' => $input['discount_fixed'] ?? null,
            'discount_percent' => $input['discount_percent'] ?? null,
            'discount_start' => $input['discount_start'] ?? null,
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
            'created_by:id,name',
            'updated_by:id,name',
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

    final public function getProductListWithValue()
    {
        return self::query()->select('id', 'name', 'stock')->get();
    }

    public function getProductForBarcode($input)
    {
        $query = self::query()->select(
            'id',
            'name',
            'sku',
            'price',
            'discount_end',
            'discount_fixed',
            'discount_percent',
            'discount_start',
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

    final public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    final public function sub_category(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    final public function created_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    final public function updated_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    public function getAllProduct($columns = ['*'])
    {
        $products = DB::table('products')->select($columns)->get();
        return collect($products);
    }
}