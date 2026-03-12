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
        $user = $usermodel->where('username', $this->request->getPost('username'))->first();  
        if (!$user){
            return redirect()->to('dashboard')->with('error', 'Invalid username');
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
            $session->set('role', $user['role']);
            $session->set('user_id', $user['id']);
            $session->set('username', $user['username']);
            $session->set('email', $user['email']);
            $session->set('created_at', $user['created_at']);
            $session->set('updated_at', $user['updated_at']);
            $session->set('name', $user['first_name']." ".$user['last_name']);
            $session->set('isLoggedIn', true);

            return redirect()->to('home')->with('success', 'HELLO '.$user['first_name'] .', SUCCESS LOGIN');
        }
        return redirect()->to('dashboard')->with('error', 'Invalid password.');
    }

    public function regview(){
        return view('register');
    }

    public function register(){
        $usermodel = model('Users_model');
        $session = session();
        //IMPLEMENT REGISTER AND FORM VALIDATION FOR IT!!
        //this is not the same as ADD USER ng personnel!
        $validation = service('validation');

        $dataToVal = array (
            'username' => $this->request->getPost('username'),
            'password' => $this->request->getPost('password'), 
            'email' => $this->request->getPost('email'),
            'firstname' => $this->request->getPost('firstname'),
            'middlename' => $this->request->getPost('middlename'),
            'lastname' => $this->request->getPost('lastname'),
            'role' => $this->request->getPost('role'),
            'status' => "Active"
        );

        $data = array (
            'username' => $this->request->getPost('username'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'email' => strtoupper($this->request->getPost('email')),
            'first_name' => strtoupper($this->request->getPost('firstname')),
            'middle_name' => strtoupper($this->request->getPost('middlename')),
            'last_name' => strtoupper($this->request->getPost('lastname')),
            'role' => strtoupper($this->request->getPost('role')),
            'status' => "PENDING"
        );

        $existuser = $usermodel->where('username', $data['username'])->first();
        if ($existuser){ //if the user exists and if that user isnt equal to the one u r editing
            $session->setFlashData('error', 'username already exists');
            return redirect()->to('register');
        }

        $existemail = $usermodel->where('email', $data['email'])->first();
        if ($existemail){ //if the user exists and if that user isnt equal to the one u r editing
            $session->setFlashData('error', 'email already used');
            return redirect()->to('register');
        }

        if(!$validation->run($dataToVal, 'signup')){
            $errors = implode('<br>', $validation->getErrors());
            $session->setFlashData('error', $errors);
            return redirect()->to('register');
        }

        $usermodel->insert($data);

        $user = $usermodel
        ->select('email, id, first_name, last_name')
        ->where('email', $data['email'])
        ->first();

        $token = $this->createResetToken($user['id']);
        $message = "<h2>Hello, ".$user['first_name'].' ' .$user['last_name'].",</h2><br>
            PLEASE CONFIRM YOUR ACCOUNT BY CLICKING THE LINK BELOW<br>. 
            ------> <a href=".base_url('auth/verify/'.$token).">!!!CLICK THIS LINK!!!</a> <------------
            <br>From ITSO TEAM";
        $email = service('email');
        $email->setFrom('lutherdeanph2@gmail.com', 'noname');
        $email->setTo($user['email']);
        $email->setSubject('VERIFY ACCOUNT');
        $email->setMessage($message);
        if(!$email->send()){
            $session->setFlashData('error', $email->printDebugger(['headers', 'subject', 'body']));
            return redirect()->to('dashboard');
        }

        $session->setFlashData('success', 'Register Success. Verify your Account.');
        return redirect()->to('dashboard');
    }

    public function verify($token){
        $session = session();

        $tokenModel = model('User_Token_model');
        $usermodel = model('Users_model');

        $tokenData = $tokenModel->where('token', $token)->first();

        if (!$tokenData) {
            $session->setFlashData('error', 'Invalid or expired token.');
            return redirect()->to('/dashboard');
        }

        $tokenTime = strtotime($tokenData['created_at']);
            if (time() - $tokenTime > 3600) {  // 1 hour
                $session->setFlashData('error', 'Token expired.');
                return redirect()->to('/dashboard');
            }

        if($tokenData['used'] == 1){
            $session->setFlashData('error', 'token already used!');
            return redirect()->to('/dashboard');
        }
        $tokenModel->update($tokenData['id'], ['used' => 1]);

        $usermodel->update($tokenData['user_id'], ['status' => 'ACTIVE']);

        $tokenModel->delete($tokenData['id']);

        $session->setFlashData('success', 'account verified!');
        return redirect()->to('dashboard');

    }

    public function logout(){
        $session = session();
        $session->destroy();
        return redirect()->to('dashboard');
    }
}
