<?php

namespace App\Controllers;

class Users extends BaseController
{
    public function index()
    {   
        $session = session();
        $checkses = $session->get('isLoggedIn');
        if (!$checkses) {
        return redirect()->to('/login')->with('error', 'Please login first.');
        }

        $usermodel = model('Users_model');
        $eqpmodel = model('Equipments_model');

        $data = array(
            'title' => 'TW32 App - Dashboard',
            'users' => $usermodel->findAll(),
            'equipments' => $eqpmodel->findAll()
        );
        return view('users_home', $data);
    }

    public function profile(){
        $session = session();
        $checkses = $session->get('isLoggedIn');
        if (!$checkses) {
        return redirect()->to('/login')->with('error', 'Please login first.');
        }
        $data = $session->get();
        return view('view_user_profile', ['user' => $data]);
    }
    // public function add() {
    //     return view('users_add');
    // }

    // public function insert() {
    //     $usermodel = model('Users_model');
    //     // Creates the session object
    //     $session = session(); // $session = service('session');

    //     // Creates and loads the Validation library
    //     $validation = service('validation');

    //     $data = array ( //HASH THE PASSWORD
    //         'username' => $this->request->getPost('username'),
    //         'password' => $this->request->getPost('password'),
    //         'confirmpassword' => $this->request->getPost('confirmpassword'),
    //         'fullname' => $this->request->getPost('fullname'),
    //         //'email' => $this->request->getPost('email'),
    //     );

    //     // Runs the validation
    //     if(! $validation->run($data, 'signup')){
    //         // If validation fails, reload the form passing the error messages
    //         $data = array(
    //             'title' => 'TW32 App - Add New User',
    //             // 'errors' => $validation->getErrors()
    //         );
    //         // Set the flash data session item for the errors
    //         $session->setFlashData('errors', $validation->getErrors());
    //         return redirect()->to('users/add');
    //     }

    //     $usermodel->insert($data);
    //     $session->setFlashData('success', 'Adding new user is successful.');
    //     return redirect()->to('users');
    // }
    public function viewusers() {
        $usermodel = model('Users_model');

        $data = array(
            'title' => 'TW32 App - View User Record',
            'users' => $usermodel->findAll()
        );

        return view('view_admin_user', $data);
    }

    public function view($id) {
        $usermodel = model('Users_model');

        $data = array(
            'title' => 'TW32 App - View User Record',
            'user' => $usermodel->find($id)
        );

        return view('view_adminView_user', $data);
    }

    public function edit($id) { //FOR FORM
        $usermodel = model('Users_model');
        $session = session();

        $data = array(
            'title' => 'TW32 App - Edit User Record',
            'user' => $usermodel->find($id)
        );

        return view('view_adminEdit_user', $data);
    }

    //ACTUAL UPDATE
    public function update($id) { //TODO: validation for inputs and flashdata set
        $usermodel = model('Users_model');
        $session = session();
        $user = $usermodel->find($id);
        
        //add email, postal, other stuff
        $data = [
        'first_name'  => $this->request->getPost('first_name'),
        'last_name'   => $this->request->getPost('last_name'),
        'country'     => $this->request->getPost('country'),
        'postal_code'    => $this->request->getPost('postal_code'),
        'address'     => $this->request->getPost('address'),
        'phone'       => $this->request->getPost('phone'),
        ];
        $session->set($data);
        // $existuser = $usermodel->where('username', $data['username'])->first();
        // if ($existuser && $existuser['id'] != $id){ //if the user exists and if that user isnt equal to the one u r editing
        //     $session->setFlashData('error', 'username already exists');
        //     return redirect()->to('dashboard');
        // }

        // $existemail = $usermodel->where('email', $data['email'])->first();
        // if ($existemail && $existemail['id'] != $id){ //if the user exists and if that user isnt equal to the one u r editing
        //     $session->setFlashData('error', 'email already used');
        //     return redirect()->to('dashboard');
        // }

        $usermodel->update($id, $data);
        $session->setFlashData('success', 'User updated successfully.');
        return redirect()->to('user/profile');
    }

    public function delete($id) { //modal for sureness
        $usermodel = model('Users_model');
        $session = session();
        try {
        $usermodel->delete($id);

        // success
        $session->setFlashdata('success', 'User deleted successfully.');
        return redirect()->to('dashboard');

    } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
        $session->setFlashdata('error', 'Cannot delete this user because they have existing reservations.');
        return redirect()->to('dashboard');
        }
    }

    public function deactview(){
        return view('deact_view');
    }

    public function deact(){
        $session = session();
        $usermodel = model('Users_model');
        $user = $usermodel->find($session->get('user_id'));
        
        $data = array (
            'password' => $this->request->getPost('password')
        );

        if (!(password_verify($data['password'], $user['password']))){
            $session->setFlashdata('error', 'wrong password.');
            return redirect()->to('user/deactivate');
        }
        $usermodel->update($session->get('user_id'), ['status' => 'Inactive']);
        $session->destroy();
        return redirect()->to('login')->with('success', 'deactivated successfuly');
    }

    public function statuschangeview(){
        $usermodel = model('Users_model');


        $data['users'] = $usermodel->findAll();

        return view('user_status', $data);
    }

    public function statuschange(){
        $usermodel = model('Users_model');
    $session = session();

    // Get submitted data
    $username = $this->request->getPost('username');
    $status   = $this->request->getPost('status');

    // Validate inputs
    if (!$username || !$status) {
        return redirect()->to('users')->with('error', 'Invalid form submission.'); 
    }

    // Update the row where username matches
    $usermodel->where('username', $username)
            ->set([
                'status' => strtoupper($status),
                'updated_at' => date('Y-m-d H:i:s'),
            ])
            ->update();

    return redirect()->to('users')->with('success', 'User status updated successfully.');   // or wherever your equipment list is
    }

    // =========================================================
    // ADMIN — USER MANAGEMENT
    // =========================================================

    /**
     * Admin: List all user accounts
     * Route: GET admin/users
     */
    public function adminViewUsers()
    {
        $session = session();

        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please login first.');
        }

        if ($session->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        $usermodel = model('Users_model');

        $data = [
            'title' => 'Admin – Manage Users',
            'users' => $usermodel->orderBy('created_at', 'DESC')->findAll(),
        ];

        return view('admin_users', $data);
    }

    /**
     * Admin: View a single user's full details
     * Route: GET admin/users/view/:id
     */
    public function adminViewUser($id)
    {
        $session = session();

        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please login first.');
        }

        if ($session->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        $usermodel = model('Users_model');
        $user = $usermodel->find($id);

        if (!$user) {
            return redirect()->to('admin/users')->with('error', 'User not found.');
        }

        $data = [
            'title' => 'Admin – View User',
            'user'  => $user,
        ];

        return view('admin_user_view', $data);
    }

    /**
     * Admin: Show edit form for a user
     * Route: GET admin/users/edit/:id
     */
    public function adminEditUser($id)
    {
        $session = session();

        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please login first.');
        }

        if ($session->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        $usermodel = model('Users_model');
        $user = $usermodel->find($id);

        if (!$user) {
            return redirect()->to('admin/users')->with('error', 'User not found.');
        }

        $data = [
            'title' => 'Admin – Edit User',
            'user'  => $user,
        ];

        return view('admin_user_edit', $data);
    }

    /**
     * Admin: Process user update (all fields including role & status)
     * Route: POST admin/users/update/:id
     */
    public function adminUpdateUser($id)
    {
        $session = session();

        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please login first.');
        }

        if ($session->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        $usermodel = model('Users_model');

        if (!$usermodel->find($id)) {
            return redirect()->to('admin/users')->with('error', 'User not found.');
        }

        $data = [
            'first_name'    => $this->request->getPost('first_name'),
            'last_name'     => $this->request->getPost('last_name'),
            'middle_name'   => $this->request->getPost('middle_name'),
            'email'         => $this->request->getPost('email'),
            'phone'         => $this->request->getPost('phone'),
            'role'          => $this->request->getPost('role'),
            'status'        => strtoupper($this->request->getPost('status')),
            'address_line1' => $this->request->getPost('address_line1'),
            'city'          => $this->request->getPost('city'),
            'postal_code'   => $this->request->getPost('postal_code'),
            'country'       => $this->request->getPost('country'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ];

        $usermodel->update($id, $data);

        return redirect()->to('admin/users')->with('success', 'User updated successfully.');
    }

    /**
     * Admin: Delete a user
     * Route: GET admin/users/delete/:id
     */
    public function adminDeleteUser($id)
    {
        $session = session();

        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please login first.');
        }

        if ($session->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        $usermodel = model('Users_model');

        if (!$usermodel->find($id)) {
            return redirect()->to('admin/users')->with('error', 'User not found.');
        }

        try {
            $usermodel->delete($id);
            return redirect()->to('admin/users')->with('success', 'User deleted successfully.');
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            return redirect()->to('admin/users')->with('error', 'Cannot delete this user because they have existing records.');
        }
    }
}