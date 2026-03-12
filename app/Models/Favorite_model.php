<?php

namespace App\Models;

use CodeIgniter\Model;

class FavoriteModel extends Model
{
    protected $table = 'favorites';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id',
        'product_id',
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
        'user_id' => 'required|integer|is_not_unique[users.id]',
        'product_id' => 'required|integer|is_not_unique[products.id]'
    ];
    
    protected $validationMessages = [
        'user_id' => [
            'required' => 'User ID is required',
            'is_not_unique' => 'User does not exist'
        ],
        'product_id' => [
            'required' => 'Product ID is required',
            'is_not_unique' => 'Product does not exist'
        ]
    ];
    
    protected $skipValidation = false;

    // Custom validation to prevent duplicate favorites
    protected $validationRulesUnique = [
        'user_id' => 'required',
        'product_id' => 'required'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo('App\Models\UserModel', 'user_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\ProductModel', 'product_id', 'id');
    }

    // Custom Methods
    public function getFavoritesWithProducts($userId)
    {
        return $this->select('favorites.*, 
                             products.id as product_id,
                             products.name, 
                             products.price, 
                             products.image,
                             products.description,
                             products.stock,
                             products.status')
                    ->join('products', 'products.id = favorites.product_id')
                    ->where('favorites.user_id', $userId)
                    ->where('products.status', 'active')
                    ->orderBy('favorites.created_at', 'DESC')
                    ->findAll();
    }

    public function getUserFavoriteIds($userId)
    {
        return $this->select('product_id')
                    ->where('user_id', $userId)
                    ->findAll();
    }

    public function isFavorite($userId, $productId)
    {
        return $this->where('user_id', $userId)
                    ->where('product_id', $productId)
                    ->first() !== null;
    }

    public function addFavorite($userId, $productId)
    {
        // Check if already exists
        if ($this->isFavorite($userId, $productId)) {
            return false;
        }
        
        return $this->insert([
            'user_id' => $userId,
            'product_id' => $productId,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function removeFavorite($userId, $productId)
    {
        return $this->where('user_id', $userId)
                    ->where('product_id', $productId)
                    ->delete();
    }

    public function toggleFavorite($userId, $productId)
    {
        if ($this->isFavorite($userId, $productId)) {
            $this->removeFavorite($userId, $productId);
            return ['status' => 'removed', 'message' => 'Removed from favorites'];
        } else {
            $this->addFavorite($userId, $productId);
            return ['status' => 'added', 'message' => 'Added to favorites'];
        }
    }

    public function getUserFavoriteCount($userId)
    {
        return $this->where('user_id', $userId)->countAllResults();
    }

    public function getProductFavoriteCount($productId)
    {
        return $this->where('product_id', $productId)->countAllResults();
    }

    public function getMostFavoritedProducts($limit = 10)
    {
        return $this->select('products.id, products.name, products.image, 
                             COUNT(favorites.id) as favorite_count')
                    ->join('products', 'products.id = favorites.product_id')
                    ->where('products.status', 'active')
                    ->groupBy('favorites.product_id')
                    ->orderBy('favorite_count', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    public function getFavoritesByUser($userId, $limit = null)
    {
        $query = $this->where('user_id', $userId)
                      ->orderBy('created_at', 'DESC');
        
        if ($limit) {
            $query->limit($limit);
        }
        
        return $query->findAll();
    }

    public function getUserFavoritesPaginated($userId, $perPage = 10)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->paginate($perPage);
    }

    public function deleteUserFavorites($userId)
    {
        return $this->where('user_id', $userId)->delete();
    }

    public function deleteProductFavorites($productId)
    {
        return $this->where('product_id', $productId)->delete();
    }
}