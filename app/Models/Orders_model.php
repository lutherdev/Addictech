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
    protected $updatedField     = 'updated_at';

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

    // Get a single order with its items
    public function getOrderWithItems($order_id)
    {
        $order = $this->find($order_id);
        if (!$order) return null;

        $orderItemsModel = model('OrderItemModel');
        $order['items']  = $orderItemsModel->getItemsByOrder($order_id);
        return $order;
    }

    // Get all orders (admin use)
    public function getAllWithUser()
    {
        return $this->select('orders.*, users.email, users.first_name, users.last_name')
                    ->join('users', 'users.id = orders.user_id')
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
}