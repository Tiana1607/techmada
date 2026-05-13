<?php

namespace App\Controllers;

use App\Models\Employe;

class AuthController extends BaseController
{
    protected $employeModel;

    public function __construct()
    {
        $this->employeModel = new Employe();
    }

    /**
     * Affiche le formulaire de connexion
     */
    public function loginForm()
    {
        return view('auth/login');
    }

    /**
     * Traite la connexion
     */
    public function login()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Validation
        $validation = \Config\Services::validation();
        $validation->setRules([
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ]);

        if (!$validation->run(['email' => $email, 'password' => $password])) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Récupérer l'employé par email
        $employe = $this->employeModel->findByEmail($email);

        if (!$employe) {
            return redirect()->back()->withInput()->with('error', 'Email ou mot de passe incorrect');
        }

        // Vérifier le mot de passe
        if (!password_verify($password, $employe['password'])) {
            return redirect()->back()->withInput()->with('error', 'Email ou mot de passe incorrect');
        }

        // Vérifier que l'employé est actif
        if (!$employe['actif']) {
            return redirect()->back()->with('error', 'Ce compte est désactivé');
        }

        // Créer la session
        $session = session();
        $session->set([
            'user_id' => $employe['id'],
            'email' => $employe['email'],
            'nom' => $employe['nom'],
            'prenom' => $employe['prenom'],
            'role' => $employe['role'],
            'departement_id' => $employe['departement_id'],
        ]);

        // Redirection selon le rôle
        switch ($employe['role']) {
            case 'admin':
                $redirectUrl = '/admin/dashboard';
                break;

            case 'rh':
                $redirectUrl = '/rh/dashboard';
                break;

            case 'employe':
                $redirectUrl = '/employe/dashboard';
                break;

            default:
                $redirectUrl = '/';
        }

        return redirect()->to($redirectUrl)->with('success', 'Connexion réussie');
    }

    /**
     * Déconnexion
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/')->with('success', 'Vous avez été déconnecté');
    }
}
