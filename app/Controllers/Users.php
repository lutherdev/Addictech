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

    public function view($id) {
        $usermodel = model('Users_model');

        $data = array(
            'title' => 'TW32 App - View User Record',
            'user' => $usermodel->find($id)
        );

        return view('users_view', $data);
    }

    public function edit($id) { //FOR FORM
        $usermodel = model('Users_model');
        $session = session();

        $data = array(
            'title' => 'TW32 App - Edit User Record',
            'user' => $usermodel->find($id)
        );

        return view('users_edit', $data);
    }

    //ACTUAL UPDATE
    public function update($id) { //validation for inputs and flashdata set
        $usermodel = model('Users_model');
        $session = session();
        $user = $usermodel->find($id);
        
        $data = array (
            'username' => $this->request->getPost('username'),
            'first_name' => $this->request->getPost('first_name'),
            'middle_name' => $this->request->getPost('middle_name'),
            'last_name' => $this->request->getPost('last_name'),
            'role' => $this->request->getPost('role'),
            'status' => $this->request->getPost('status'),
            'email' => $this->request->getPost('email'),
        );

        $existuser = $usermodel->where('username', $data['username'])->first();
        if ($existuser && $existuser['id'] != $id){ //if the user exists and if that user isnt equal to the one u r editing
            $session->setFlashData('error', 'username already exists');
            return redirect()->to('dashboard');
        }

        $existemail = $usermodel->where('email', $data['email'])->first();
        if ($existemail && $existemail['id'] != $id){ //if the user exists and if that user isnt equal to the one u r editing
            $session->setFlashData('error', 'email already used');
            return redirect()->to('dashboard');
        }

        $usermodel->update($id, $data);
        $session->setFlashData('success', 'User updated successfully.');
        return redirect()->to('dashboard');
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
}
