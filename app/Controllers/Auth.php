<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function index()
    {
        // If already logged in, redirect to appropriate dashboard
        if (session()->get('logged_in')) {
            return $this->redirectToDashboard();
        }

        return view('auth/login');
    }

    public function login()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $remember = $this->request->getPost('remember');

        $userModel = new UserModel();

        // Use the verifyPassword method from UserModel
        $user = $userModel->verifyPassword($username, $password);

        if (!$user) {
            return redirect()->back()->with('error', 'Username atau password salah');
        }

        // Update last login
        $userModel->updateLastLogin($user['id']);

        // Set session
        $sessionData = [
            'user_id' => $user['id'],
            'username' => $user['username'],
            'name' => $user['full_name'],
            'role' => $user['role'],
            'logged_in' => true
        ];

        session()->set($sessionData);

        // Set remember me cookie if checked
        if ($remember) {
            $this->response->setCookie('remember_token', $user['id'], 2592000); // 30 days
        }

        return $this->redirectToDashboard();
    }

    public function logout()
    {
        session()->destroy();
        $this->response->deleteCookie('remember_token');

        return redirect()->to('/')->with('success', 'Anda telah keluar');
    }

    private function redirectToDashboard()
    {
        $role = session()->get('role');

        switch ($role) {
            case 'admin':
                return redirect()->to('/admin/dashboard');
            case 'guru_piket':
                return redirect()->to('/guru-piket/dashboard');
            case 'student':
            case 'parent':
                return redirect()->to('/student/dashboard');
            default:
                return redirect()->to('/');
        }
    }
}
