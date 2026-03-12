<?php

namespace App\Models;

use CodeIgniter\Model;

class CartModel extends Model
{
    protected $table = 'carts';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id',
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
        'user_id' => 'required|integer|is_not_unique[users.id]'
    ];
    
    protected $validationMessages = [
        'user_id' => [
            'required' => 'User ID is required',
            'is_not_unique' => 'User does not exist'
        ]
    ];
    
    protected $skipValidation = false;

    // Relationships
    public function user()
    {
        return $this->belongsTo('App\Models\UserModel', 'user_id', 'id');
    }

    public function cartItems()
    {
        return $this->hasMany('App\Models\CartItemModel', 'cart_id', 'id');
    }

    // Custom Methods
    public function getCartWithItems($cartId)
    {
        return $this->select('carts.*, 
                             COUNT(cart_items.id) as total_items,
                             SUM(cart_items.quantity) as total_quantity')
                    ->join('cart_items', 'cart_items.cart_id = carts.id', 'left')
                    ->where('carts.id', $cartId)
                    ->groupBy('carts.id')
                    ->first();
    }

    public function getUserCart($userId)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->first();
    }

    public function getCartTotal($cartId)
    {
        $db = db_connect();
        $result = $db->table('cart_items')
                    ->select('SUM(products.price * cart_items.quantity) as total')
                    ->join('products', 'products.id = cart_items.product_id')
                    ->where('cart_items.cart_id', $cartId)
                    ->get()
                    ->getRow();
        
        return $result->total ?? 0;
    }

    public function getCartItemCount($cartId)
    {
        return $this->db->table('cart_items')
                       ->where('cart_id', $cartId)
                       ->countAllResults();
    }

    public function clearCart($cartId)
    {
        return $this->db->table('cart_items')
                       ->where('cart_id', $cartId)
                       ->delete();
    }

    public function getOrCreateCart($userId)
    {
        $cart = $this->where('user_id', $userId)->first();
        
        if (!$cart) {
            $this->insert([
                'user_id' => $userId,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $cartId = $this->insertID();
        } else {
            $cartId = $cart['id'];
        }
        
        return $cartId;
    }

    public function isCartEmpty($cartId)
    {
        $count = $this->db->table('cart_items')
                         ->where('cart_id', $cartId)
                         ->countAllResults();
        return $count === 0;
    }

    public function deleteExpiredCarts($days = 30)
    {
        $date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        // Get expired cart IDs
        $expiredCarts = $this->select('id')
                            ->where('created_at <', $date)
                            ->findAll();
        
        $cartIds = array_column($expiredCarts, 'id');
        
        if (!empty($cartIds)) {
            // Delete cart items first
            $this->db->table('cart_items')
                    ->whereIn('cart_id', $cartIds)
                    ->delete();
            
            // Delete carts
            return $this->whereIn('id', $cartIds)->delete();
        }
        
        return true;
    }
}