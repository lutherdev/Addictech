<?php
// App/Models/Wishlists_model.php
namespace App\Models;
use CodeIgniter\Model;

class Wishlists_model extends Model
{
    protected $table            = 'wishlists';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = false; // wishlists only has created_at

    protected $allowedFields = [
        'user_id',
        'product_id',
    ];

    // Get all wishlist items for a user with product details
    public function getWishlistByUser($user_id)
    {
        return $this->select('wishlists.*, products.name, products.variant, products.price, products.image, products.stock, products.category, products.status')
                    ->join('products', 'products.id = wishlists.product_id')
                    ->where('wishlists.user_id', $user_id)
                    ->findAll();
    }

    // Check if a product is wishlisted by a user
    public function isWishlisted($user_id, $product_id)
    {
        return $this->where('user_id', $user_id)
                    ->where('product_id', $product_id)
                    ->first() !== null;
    }

    // Toggle wishlist — add if not present, remove if already there
    public function toggle($user_id, $product_id)
    {
        $existing = $this->where('user_id', $user_id)
                         ->where('product_id', $product_id)
                         ->first();

        if ($existing) {
            $this->delete($existing['id']);
            return false; // removed
        }

        $this->insert([
            'user_id'    => $user_id,
            'product_id' => $product_id,
        ]);
        return true; // added
    }

    // Remove a specific wishlist item
    public function removeItem($user_id, $product_id)
    {
        return $this->where('user_id', $user_id)
                    ->where('product_id', $product_id)
                    ->delete();
    }

    // Get wishlist count for a user
    public function getWishlistCount($user_id)
    {
        return $this->where('user_id', $user_id)->countAllResults();
    }

    // Move wishlist item to cart
    public function moveToCart($user_id, $product_id)
    {
        $cartModel = model('Cart_items_model');
        $cartModel->addOrUpdate($user_id, $product_id, 1);
        return $this->removeItem($user_id, $product_id);
    }
}