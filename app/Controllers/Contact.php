<?php
namespace App\Controllers;

use App\Models\Contacts_model;

class Contact extends BaseController
{
    public function send()
    {
        $session      = session();
        $contactModel = model('Contacts_model');

        $full_name = trim($this->request->getPost('full_name'));
        $email     = trim($this->request->getPost('email'));
        $concern   = trim($this->request->getPost('concern'));

        // Basic validation
        if (empty($full_name) || empty($email) || empty($concern)) {
            $session->setFlashData('error', 'Please fill in all fields.');
            return redirect()->back();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $session->setFlashData('error', 'Please enter a valid email address.');
            return redirect()->back();
        }

        $contactModel->insert([
            'full_name'  => $full_name,
            'email'      => $email,
            'concern'    => $concern,
            'is_read'    => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $session->setFlashData('success', 'Your message has been sent. We will get back to you soon!');
        return redirect()->back();
    }

    // Admin view all messages
    public function adminIndex()
    {
        $contactModel = model('Contacts_model');
        return view('view_admin_contacts', [
            'contacts' => $contactModel->getAll()
        ]);
    }

    // Admin mark as read
    public function markRead($id)
    {
        $contactModel = model('Contacts_model');
        $contactModel->markAsRead($id);
        return redirect()->to('admin/contacts');
    }

    // Admin delete message
    public function delete($id)
    {
        $contactModel = model('Contacts_model');
        $contactModel->delete($id);
        session()->setFlashData('success', 'Message deleted.');
        return redirect()->to('admin/contacts');
    }
}