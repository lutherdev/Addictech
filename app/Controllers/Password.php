<?php

namespace App\Controllers;

class Password extends BaseController{

    public function changeview(){
        $session = session();
        $checkses = $session->get('isLoggedIn');
        if (!$checkses) {
        return redirect()->to('login')->with('error', 'Please login first.');
    }
        return view('view_change_pass');
    }

    public function change(){
    $session = session();
    $userModel = model('Users_model');

    $userId = $session->get('user_id');
    $checkses = $session->get('isLoggedIn');
    if (!$checkses) {
        return redirect()->to('login')->with('error', 'Please login first.');
    }

    $currentPassword = $this->request->getPost('current_password');
    $newPassword     = $this->request->getPost('new_password');
    $confirmPassword = $this->request->getPost('confirm_password');

    // fetch user from DB
    $user = $userModel->find($userId);

    if (!$user) {
        return redirect()->back()->with('error', 'User not found.');
    }
    // check current password
    if (!password_verify($currentPassword, $user['password'])) {
        return redirect()->back()->with('error', 'Current password is incorrect.');
    }

    // check new password confirmation
    if ($newPassword !== $confirmPassword) {
        return redirect()->back()->with('error', 'New passwords do not match.');
    }

    // validate new password 
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $userModel->update($userId, ['password' => $hashedPassword]);

    return redirect()->back()->with('success', 'Password changed successfully!');
    return redirect()->to('profile')->with('success', 'Password changed!');
}


    


}