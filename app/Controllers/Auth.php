<?php

namespace App\Controllers;

class Auth extends BaseController
{
    public function loginview()
    {      
        $session = session();
        $checkses = $session->get('isLoggedIn');
        if ($checkses) {
        return redirect()->to('homepage')->with('error', 'Already Loggedin');
        }
        return view('view_login');
    }

    // public function createResetToken($userId){
    //     $tokenModel = model('User_Token_model');

    //     // prepare data
    //     $data = [
    //         'user_id'    => $userId,
    //         'token'      => bin2hex(random_bytes(16)),
    //         'created_at' => date('Y-m-d H:i:s'),
    //         'expires_at' => date('Y-m-d H:i:s', strtotime('+1 hour'))
    //     ];

    //     $tokenModel->insert($data);

    //     return $data['token'];
    // }

    public function login(){
        $usermodel = model('Users_model');
        $session = session();
        //set other session details
        $user = $usermodel->where('email', $this->request->getPost('email'))->first();  
        if (!$user){
            return redirect()->to('login')->with('error', 'Invalid email bro');
        }
        $password = $this->request->getPost('password'); 
        $storedHash = $user['password'];               // hash from DB
        // if ($user['status'] == 'INACTIVE'){
        //     return redirect()->to('dashboard')->with('error', 'User deactivated');
        // } elseif ($user['status'] == 'PENDING'){
        //     return redirect()->to('dashboard')->with('error', 'Please Verify your Account First.');
        // }
        if (password_verify($password, $storedHash)) {
            //SET OTHER ROLES
            $sessionData = [
            'user_id'      => $user['id'],
            'role'         => $user['role'],
            'first_name'   => $user['first_name'],
            'last_name'    => $user['last_name'],
            'email'        => $user['email'],
            'phone'        => $user['phone'],
            'address'      => $user['address'],
            'city'         => $user['city'],
            'postal_code'  => $user['postal_code'],
            'country'      => $user['country'],
            'created_at'   => $user['created_at'],
            'updated_at'   => $user['updated_at'],
            'isLoggedIn'   => true
            ];
            $session->set($sessionData);

            return redirect()->to('home')->with('success', 'HELLO '.$user['first_name'] .', SUCCESS LOGIN');
        }
        return redirect()->to('login')->with('error', 'Invalid password.');
    }

    public function regview(){
        return view('view_register');
    }

    public function register()
{
    $usermodel = model('Users_model');
    $session = session();
    $validation = service('validation');

    $email = strtolower(trim($this->request->getPost('email')));
    $password = $this->request->getPost('password');
    $confirm = $this->request->getPost('confirm_password');
    $first = $this->request->getPost('first_name');
    $last = $this->request->getPost('last_name');

    // Validation rules
    $rules = [
        'email' => 'required|valid_email',
        'password' => 'required|min_length[6]',
        'confirm_password' => 'matches[password]'
    ];

    if (!$this->validate($rules)) {
        $session->setFlashdata('error', implode('<br>', $validation->getErrors()));
        return redirect()->back()->withInput();
    }

    // Check if email exists
    $exist = $usermodel->where('email', strtoupper($email))->first();

    if ($exist) {
        $session->setFlashdata('error', 'An account with this email already exists.');
        return redirect()->back()->withInput();
    }

    // Insert user
    $data = [
        'email' => strtoupper($email),
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'first_name' => strtoupper($first),
        'last_name' => strtoupper($last),
        'role' => 'customer',
        'status' => 'ACTIVE'
    ];

    $usermodel->insert($data);

    // Get inserted user
    $user = $usermodel->where('email', strtoupper($email))->first();

    // Login user automatically

    $sessionData = [
            'user_id'      => $user['id'],
            'role'         => $user['role'],
            'first_name'   => $user['first_name'],
            'last_name'    => $user['last_name'],
            'email'        => $user['email'],
            'phone'        => $user['phone'],
            'address'      => $user['address'],
            'city'         => $user['city'],
            'postal_code'  => $user['postal_code'],
            'country'      => $user['country'],
            'created_at'   => $user['created_at'],
            'updated_at'   => $user['updated_at'],
            'isLoggedIn'   => true
            ];
            $session->set($sessionData);

    $session->setFlashdata('success', 'Account created successfully.');

    return redirect()->to('user/profile');
}

    public function logout(){
        $session = session();
        $session->destroy();
        return redirect()->to('login');
    }
}
