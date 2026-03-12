<?php

namespace App\Models;

use CodeIgniter\Model;

class CartItemModel extends Model
{
    protected $table = 'cart_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'cart_id',
        'product_id',
        'quantity'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'cart_id' => 'required|integer|is_not_unique[carts.id]',
        'product_id' => 'required|integer|is_not_unique[products.id]',
        'quantity' => 'permit_empty|integer|greater_than[0]'
    ];
    
    protected $validationMessages = [
        'cart_id' => [
            'required' => 'Cart ID is required',
            'is_not_unique' => 'Cart does not exist'
        ],
        'product_id' => [
            'required' => 'Product ID is required',
            'is_not_unique' => 'Product does not exist'
        ]
    ];
    
    protected $skipValidation = false;

    // Relationships
    public function cart()
    {
        return $this->belongsTo('App\Models\CartModel', 'cart_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\ProductModel', 'product_id', 'id');
    }

    // Custom Methods
    public function getCartItemsWithProducts($cartId)
    {
        return $this->select('cart_items.*, 
                             products.name, 
                             products.price, 
                             products.image,
                             products.stock,
                             products.status as product_status')
                    ->join('products', 'products.id = cart_items.product_id')
                    ->where('cart_items.cart_id', $cartId)
                    ->where('products.status', 'active')
                    ->findAll();
    }

    public function getCartItemWithProduct($id)
    {
        return $this->select('cart_items.*, 
                             products.name, 
                             products.price, 
                             products.image,
                             products.stock,
                             products.status as product_status')
                    ->join('products', 'products.id = cart_items.product_id')
                    ->where('cart_items.id', $id)
                    ->first();
    }

    public function getCartTotal($cartId)
    {
        $result = $this->select('SUM(products.price * cart_items.quantity) as total')
                      ->join('products', 'products.id = cart_items.product_id')
                      ->where('cart_items.cart_id', $cartId)
                      ->first();
        
        return $result['total'] ?? 0;
    }

    public function updateQuantity($id, $quantity)
    {
        return $this->update($id, ['quantity' => $quantity]);
    }

    public function incrementQuantity($id, $increment = 1)
    {
        $item = $this->find($id);
        if ($item) {
            $newQuantity = $item['quantity'] + $increment;
            return $this->update($id, ['quantity' => $newQuantity]);
        }
        return false;
    }

    public function decrementQuantity($id, $decrement = 1)
    {
        $item = $this->find($id);
        if ($item) {
            $newQuantity = max(1, $item['quantity'] - $decrement);
            return $this->update($id, ['quantity' => $newQuantity]);
        }
        return false;
    }

    public function isProductInCart($cartId, $productId)
    {
        return $this->where('cart_id', $cartId)
                    ->where('product_id', $productId)
                    ->first() !== null;
    }

    public function getItemCount($cartId)
    {
        return $this->where('cart_id', $cartId)
                    ->selectSum('quantity')
                    ->first()['quantity'] ?? 0;
    }

    public function removeByProduct($cartId, $productId)
    {
        return $this->where('cart_id', $cartId)
                    ->where('product_id', $productId)
                    ->delete();
    }

    public function validateStock($cartId)
    {
        $items = $this->getCartItemsWithProducts($cartId);
        $errors = [];
        
        foreach ($items as $item) {
            if ($item['quantity'] > $item['stock']) {
                $errors[] = "{$item['name']} has only {$item['stock']} items in stock.";
            }
        }
        
        return $errors;
    }
}