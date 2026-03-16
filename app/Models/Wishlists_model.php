<?php
namespace App\Models;
use CodeIgniter\Model;

class Wishlists_model extends Model
{
    protected $table            = 'wishlists';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false; // ← turn this off
    protected $createdField     = 'created_at';

    protected $allowedFields = [
        'user_id',
        'product_id',
        'created_at', // ← add this
    ];

    public function getWishlistByUser($user_id)
    {
        return $this->select('wishlists.*, products.name, products.variant, products.price, products.image, products.stock, products.category, products.status, products.description')
                    ->join('products', 'products.id = wishlists.product_id')
                    ->where('wishlists.user_id', $user_id)
                    ->findAll();
    }

    public function isWishlisted($user_id, $product_id)
    {
        return $this->where('user_id', $user_id)
                    ->where('product_id', $product_id)
                    ->first() !== null;
    }

    public function toggle($user_id, $product_id)
    {
        $existing = $this->where('user_id', $user_id)
                         ->where('product_id', $product_id)
                         ->first();

        if ($existing) {
            $this->delete($existing['id']);
            return false;
        }

        $this->insert([
            'user_id'    => $user_id,
            'product_id' => $product_id,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        return true;
    }

    public function removeItem($user_id, $product_id)
    {
        return $this->where('user_id', $user_id)
                    ->where('product_id', $product_id)
                    ->delete();
    }

    public function getWishlistCount($user_id)
    {
        return $this->where('user_id', $user_id)->countAllResults();
    }

    public function moveToCart($user_id, $product_id)
    {
        $cartModel = model('CartItemModel');
        $cartModel->addOrUpdate($user_id, $product_id, 1);
        return $this->removeItem($user_id, $product_id);
    }
}