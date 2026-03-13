<?php

namespace App\Models;

use CodeIgniter\Model;

class Users_model extends Model
{
    protected $table = 'users';

    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;

    protected $returnType = 'array';

    protected $allowedFields = [
        'email',
        'password',
        'first_name',
        'middle_name',
        'last_name',
        'phone',
        'role',
        'status',

        // address fields nullable, can be input on later on the profile page
        'address_line1',
        'city',
        'postal_code',
        'country',
        'created_at',
        'updated_at'
    ];

    protected bool $allowEmptyInserts = false;

    // automatically manage timestamps
    protected $useTimestamps = true;
}