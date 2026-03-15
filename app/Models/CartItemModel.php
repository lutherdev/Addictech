<?php
// App/Models/Cart_items_model.php
namespace App\Models;
use CodeIgniter\Model;

class CartItemModel extends Model
{
    protected $table            = 'cart_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'user_id',
        'product_id',
        'quantity',
    ];

    // Get all cart items for a user with product details
    public function getCartByUser($user_id)
    {
        return $this->select('cart_items.*, products.name, products.variant, products.price, products.image, products.stock, products.category')
                    ->join('products', 'products.id = cart_items.product_id')
                    ->where('cart_items.user_id', $user_id)
                    ->where('products.status', 'active')
                    ->findAll();
    }

    // Add item or increment quantity if already in cart
    public function addOrUpdate($user_id, $product_id, $quantity = 1)
    {
        $existing = $this->where('user_id', $user_id)
                         ->where('product_id', $product_id)
                         ->first();

        if ($existing) {
            $newQty = min($existing['quantity'] + $quantity, 99);
            return $this->update($existing['id'], ['quantity' => $newQty]);
        }

        return $this->insert([
            'user_id'    => $user_id,
            'product_id' => $product_id,
            'quantity'   => $quantity,
        ]);
    }

    // Update quantity of a specific cart item
    public function updateQuantity($id, $user_id, $quantity)
    {
        return $this->where('id', $id)
                    ->where('user_id', $user_id)
                    ->set(['quantity' => max(1, min(99, $quantity))])
                    ->update();
    }

    // Remove a specific item
    public function removeItem($id, $user_id)
    {
        return $this->where('id', $id)
                    ->where('user_id', $user_id)
                    ->delete();
    }

    // Clear entire cart for a user (called after checkout)
    public function clearCart($user_id)
    {
        return $this->where('user_id', $user_id)->delete();
    }

    // Get total item count in cart
    public function getCartCount($user_id)
    {
        return $this->selectSum('quantity')
                    ->where('user_id', $user_id)
                    ->get()
                    ->getRow()
                    ->quantity ?? 0;
    }

    // Get cart total price
    public function getCartTotal($user_id)
    {
        $items = $this->getCartByUser($user_id);
        return array_reduce($items, function ($sum, $item) {
            return $sum + ($item['price'] * $item['quantity']);
        }, 0);
    }
}