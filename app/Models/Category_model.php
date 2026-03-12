<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'name',
        'description',
        'created_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[100]|is_unique[categories.name]'
    ];
    
    protected $validationMessages = [
        'name' => [
            'required' => 'Category name is required',
            'min_length' => 'Category name must be at least 2 characters long',
            'is_unique' => 'Category name already exists'
        ]
    ];
    
    protected $skipValidation = false;

    // Relationships
    public function products()
    {
        return $this->hasMany('App\Models\ProductModel', 'category_id', 'id');
    }

    // Custom Methods
    public function getCategoriesWithProductCount()
    {
        return $this->select('categories.*, COUNT(products.id) as product_count')
                    ->join('products', 'products.category_id = categories.id', 'left')
                    ->groupBy('categories.id')
                    ->findAll();
    }

    public function getActiveCategories()
    {
        return $this->whereExists(function($builder) {
            return $builder->select('1')
                          ->from('products')
                          ->where('products.category_id = categories.id')
                          ->where('products.status', 'active');
        })->findAll();
    }

    public function getCategoryWithProducts($id)
    {
        return $this->select('categories.*, 
                             COUNT(products.id) as total_products,
                             SUM(CASE WHEN products.status = "active" THEN 1 ELSE 0 END) as active_products')
                    ->join('products', 'products.category_id = categories.id', 'left')
                    ->where('categories.id', $id)
                    ->groupBy('categories.id')
                   ->first();
    }

    public function searchCategories($keyword)
    {
        return $this->like('name', $keyword)
                    ->orLike('description', $keyword)
                    ->findAll();
    }

    public function hasProducts($id)
    {
        $db = db_connect();
        $result = $db->table('products')
                     ->where('category_id', $id)
                     ->countAllResults();
        return $result > 0;
    }
}