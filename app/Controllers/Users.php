<?php

namespace App\Controllers;

class Users extends BaseController
{
    public function index() //view all users on admin dashboard
    {   
        // $session = session();
        // $checkses = $session->get('isLoggedIn');
        // if (!$checkses) {
        // return redirect()->to('/login')->with('error', 'Please login first.');
        // }

        $usermodel = model('Users_model');

        $data = array(
            'title' => 'TW32 App - View User Record',
            'users' => $usermodel->findAll()
        );

        return view('view_admin_user', $data);
    }

    public function profile() {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Please login first.');
        }

        $user_id     = $session->get('user_id');
        $ordersModel = model('Orders_model');
        $orders      = $ordersModel->getOrdersByUserWithItems($user_id);

        $data = $session->get();
        $data['orders'] = $orders;

        return view('view_user_profile', ['user' => $data, 'orders' => $orders]);
    }

    public function view($id) {  //single user view from admin dashboard
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
        'city'        => $this->request->getPost('city'),
        'postal_code' => $this->request->getPost('postal_code'),
        'address'     => $this->request->getPost('address'),
        'phone'       => $this->request->getPost('phone'),
        'role'        => $this->request->getPost('role'),
        'status'      => $this->request->getPost('status'),
        ];

        // Update session if the user is editing their own profile
        if ($session->get('user_id') == $id) {
            $session->set($data);
        }

        $redirectTo = $this->request->getPost('redirect_to') ?? 'user/profile';
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
        return redirect()->to($redirectTo);
    }

    public function delete($id) { //modal for sureness
        $usermodel = model('Users_model');
        $session = session();
        try {
        $usermodel->delete($id);

        $session->setFlashdata('success', 'User deleted successfully.');
        return redirect()->to('admin/users');

    } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
        $session->setFlashdata('error', 'Cannot delete this user because they have existing orders.');
        return redirect()->to('admin/users');
        }
    }

    public function deactview(){
        return view('deact_view');
    }

    public function statuschangeview(){
        $usermodel = model('Users_model');


        $data['users'] = $usermodel->findAll();

        return view('user_status', $data);
    }

}
