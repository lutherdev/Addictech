<?php
namespace App\Models;
use CodeIgniter\Model;

class Contacts_model extends Model
{
    protected $table            = 'contacts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'full_name',
        'email',
        'concern',
        'is_read',
        'created_at',
    ];

    public function getUnread()
    {
        return $this->where('is_read', 0)->orderBy('created_at', 'DESC')->findAll();
    }

    public function getAll()
    {
        return $this->orderBy('created_at', 'DESC')->findAll();
    }

    public function markAsRead($id)
    {
        return $this->update($id, ['is_read' => 1]);
    }
}