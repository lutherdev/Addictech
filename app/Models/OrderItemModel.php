<?php
// App/Models/Order_items_model.php
namespace App\Models;
use CodeIgniter\Model;

class OrderItemModel extends Model
{
    protected $table            = 'order_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false; // no timestamps on order_items

    protected $allowedFields = [
        'order_id',
        'product_id',
        'product_name',
        'variant',
        'price',
        'quantity',
        'subtotal',
    ];

    // Get all items belonging to an order
    public function getItemsByOrder($order_id)
    {
        return $this->select('order_items.*, products.image, products.category')
                    ->join('products', 'products.id = order_items.product_id', 'left')
                    ->where('order_items.order_id', $order_id)
                    ->findAll();
    }

    // Insert all items from a cart into an order (called during checkout)
    public function insertFromCart($order_id, $cart_items)
    {
        $rows = array_map(function ($item) use ($order_id) {
            return [
                'order_id'     => $order_id,
                'product_id'   => $item['product_id'],
                'product_name' => $item['name'],
                'variant'      => $item['variant'],
                'price'        => $item['price'],
                'quantity'     => $item['quantity'],
                'subtotal'     => $item['price'] * $item['quantity'],
            ];
        }, $cart_items);

        return $this->insertBatch($rows);
    }
}