<?php

namespace App\Controllers;

use App\Models\UserModel;

class Register extends BaseController
{
    public function showRegister(): string
    {
        return view('register');
    }

    public function doRegister()
    {
        $data = $this->request->getJSON(true);

        $name     = $data['name'] ?? '';
        $email    = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        $password2 = $data['password2'] ?? '';

        // Validation des champs requis
        if (!$name || !$email || !$password || !$password2) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Veuillez remplir tous les champs.'
            ]);
        }

        // Validation de la longueur du mot de passe
        if (strlen($password) < 8) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Le mot de passe doit contenir au moins 8 caractères.'
            ]);
        }

        // Validation de la correspondance des mots de passe
        if ($password !== $password2) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Les mots de passe ne correspondent pas.'
            ]);
        }

        // Validation du format email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Veuillez entrer un email valide.'
            ]);
        }

        $userModel = new UserModel();

        // Vérifier si l'email existe déjà
        $existingUser = $userModel->where('email', $email)->first();
        if ($existingUser) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cet email est déjà utilisé.'
            ]);
        }

        // Créer l'utilisateur
        $userData = [
            'name'     => $name,
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ];

        $userId = $userModel->insert($userData);

        if (!$userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la création du compte.'
            ]);
        }

        // Connecter automatiquement l'utilisateur après l'inscription
        session()->set([
            'user_id'    => $userId,
            'user_name'  => $name,
            'user_email' => $email,
            'logged_in'  => true
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Compte créé avec succès.'
        ]);
    }
}