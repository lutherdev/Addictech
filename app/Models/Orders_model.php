<?php
// App/Models/Orders_model.php
namespace App\Models;
use CodeIgniter\Model;

class Orders_model extends Model
{
    protected $table            = 'orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = '';

    protected $allowedFields = [
        'user_id',
        'order_number',
        'status',
        'payment_method',
        'delivery_method',
        'delivery_address',
        'subtotal',
        'shipping_fee',
        'total',
        'payment_status',
        'notes',
    ];

    // Generate a unique order number
    public function generateOrderNumber()
    {
        return 'ORD-' . strtoupper(uniqid());
    }

    // Get all orders for a user
    public function getOrdersByUser($user_id)
    {
        return $this->where('user_id', $user_id)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function getOrderWithItems($order_id)
    {
        $order = $this->select('orders.*, CONCAT(users.first_name, " ", users.last_name) AS username, users.email')
                    ->join('users', 'users.id = orders.user_id', 'left')
                    ->where('orders.id', $order_id)
                    ->first();

        if (!$order) return null;

        $orderItemsModel = new \App\Models\OrderItemModel();
        $order['items']  = $orderItemsModel->getItemsByOrder($order_id);
        return $order;
    }

public function getAllWithUser()
{
    return $this->select('
            orders.id,
            orders.total,
            orders.status,
            orders.created_at,
            CONCAT(users.first_name, " ", users.last_name) AS username,
            users.email,
            MIN(order_items.product_name) AS product_name,
            MIN(order_items.variant)      AS variant,
            MIN(products.image)           AS product_image,
            SUM(order_items.quantity)     AS total_quantity
        ')
        ->join('users',       'users.id = orders.user_id',             'left')
        ->join('order_items', 'order_items.order_id = orders.id',      'left')
        ->join('products',    'products.id = order_items.product_id',  'left')
        ->groupBy('orders.id')
        ->orderBy('orders.created_at', 'DESC')
        ->findAll();
}

    // Update order status
    public function updateStatus($order_id, $status)
    {
        return $this->update($order_id, ['status' => $status]);
    }

    // Update payment status
    public function updatePaymentStatus($order_id, $payment_status)
    {
        return $this->update($order_id, ['payment_status' => $payment_status]);
    }

    public function getOrdersByUserWithItems($user_id)
        {
            $orders = $this->where('user_id', $user_id)
                        ->orderBy('created_at', 'DESC')
                        ->findAll();

            $orderItemsModel = model('OrderItemModel');
            foreach ($orders as &$order) {
                $order['items'] = $orderItemsModel->getItemsByOrder($order['id']);
            }

            return $orders;
        }
}