<?php

namespace App\Controllers;

class Login extends BaseController
{
    public function showLogin(): string
    {
        return view('login');
    }

    public function doLogin()
    {
        $data = $this->request->getJSON(true);

        $email = $data['email'] ?? '';
        $pwd   = $data['password'] ?? '';

        if (!$email || !$pwd) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Veuillez remplir tous les champs.'
            ]);
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();

        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Email ou mot de passe incorrect.'
            ]);
        }

        if (!password_verify($pwd, $user['password'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Email ou mot de passe incorrect.'
            ]);
        }

        session()->set([
            'user_id' => $user['id'],
            'user_name' => $user['name'],
            'user_email' => $user['email'],
            'logged_in' => true
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Connexion réussie.'
        ]);
    }
}
