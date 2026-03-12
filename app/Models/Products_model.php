<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'category_id',
        'name',
        'description',
        'price',
        'stock',
        'image',
        'status',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[150]',
        'price' => 'required|numeric|greater_than[0]',
        'stock' => 'permit_empty|integer|greater_than_equal_to[0]',
        'category_id' => 'permit_empty|integer|is_not_null'
    ];
    
    protected $validationMessages = [
        'name' => [
            'required' => 'Product name is required',
            'min_length' => 'Product name must be at least 3 characters long'
        ],
        'price' => [
            'required' => 'Price is required',
            'numeric' => 'Price must be a number',
            'greater_than' => 'Price must be greater than 0'
        ]
    ];
    
    protected $skipValidation = false;

    // Relationships
    public function category()
    {
        return $this->belongsTo('App\Models\CategoryModel', 'category_id', 'id');
    }

    public function cartItems()
    {
        return $this->hasMany('App\Models\CartItemModel', 'product_id', 'id');
    }

    public function orderItems()
    {
        return $this->hasMany('App\Models\OrderItemModel', 'product_id', 'id');
    }

    public function favorites()
    {
        return $this->hasMany('App\Models\FavoriteModel', 'product_id', 'id');
    }

    // Custom Methods
    public function getProductsWithCategory()
    {
        return $this->select('products.*, categories.name as category_name')
                    ->join('categories', 'categories.id = products.category_id', 'left')
                    ->findAll();
    }

    public function getProductWithCategory($id)
    {
        return $this->select('products.*, categories.name as category_name, categories.description as category_description')
                    ->join('categories', 'categories.id = products.category_id', 'left')
                    ->where('products.id', $id)
                    ->first();
    }

    public function getActiveProducts()
    {
        return $this->where('status', 'active')
                    ->where('stock >', 0)
                    ->findAll();
    }

    public function getLowStockProducts($threshold = 5)
    {
        return $this->where('stock <=', $threshold)
                    ->where('stock >', 0)
                    ->findAll();
    }

    public function getOutOfStockProducts()
    {
        return $this->where('stock', 0)
                    ->orWhere('stock IS NULL')
                    ->findAll();
    }

    public function searchProducts($keyword)
    {
        return $this->like('name', $keyword)
                    ->orLike('description', $keyword)
                    ->where('status', 'active')
                    ->findAll();
    }

    public function getProductsByCategory($categoryId)
    {
        return $this->where('category_id', $categoryId)
                    ->where('status', 'active')
                    ->findAll();
    }

    public function updateStock($id, $quantity)
    {
        $product = $this->find($id);
        if ($product) {
            $newStock = $product['stock'] - $quantity;
            return $this->update($id, ['stock' => $newStock]);
        }
        return false;
    }

    public function isInStock($id, $quantity = 1)
    {
        $product = $this->find($id);
        return $product && $product['stock'] >= $quantity;
    }
}