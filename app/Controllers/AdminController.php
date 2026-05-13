<?php

namespace App\Controllers;

use App\Models\Employe;
use App\Models\Departement;
use App\Models\TypeConge;
use App\Models\Solde;
use \CodeIgniter\Exceptions\PageNotFoundException;

class AdminController extends BaseController
{
    protected $employeModel;
    protected $departementModel;
    protected $typeCongeModel;
    protected $soldeModel;

    public function __construct()
    {
        $this->employeModel = new Employe();
        $this->departementModel = new Departement();
        $this->typeCongeModel = new TypeConge();
        $this->soldeModel = new Solde();
    }

    /**
     * Dashboard admin : stats globales
     */
    public function dashboard()
    {
        $nbEmployes = $this->employeModel->countAll();
        $nbDepartements = $this->departementModel->countAll();
        $nbTypesCong = $this->typeCongeModel->countAll();

        return view('admin/dashboard', [
            'nbEmployes' => $nbEmployes,
            'nbDepartements' => $nbDepartements,
            'nbTypesCong' => $nbTypesCong,
        ]);
    }

    // ===== CRUD EMPLOYES =====

    /**
     * Liste tous les employés
     */
    public function listEmployes()
    {
        $employes = $this->employeModel->findAll();

        return view('admin/employes/list', [
            'employes' => $employes,
        ]);
    }

    /**
     * Affiche le formulaire de création/édition d'employé
     */
    public function formEmploye($id = null)
    {
        $employe = null;
        if ($id) {
            $employe = $this->employeModel->find($id);
            if (!$employe) {
                throw new PageNotFoundException("Employé non trouvé");
            }
        }

        $departements = $this->departementModel->findAll();

        return view('admin/employes/form', [
            'employe' => $employe,
            'departements' => $departements,
        ]);
    }

    /**
     * Crée un nouvel employé
     */
    public function createEmploye()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'email' => 'required|valid_email|is_unique[employes.email]',
            'password' => 'required|min_length[6]',
            'role' => 'required|in_list[admin,rh,employe]',
            'departement_id' => 'permit_empty|numeric',
        ]);

        if (!$validation->run($this->request->getPost())) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'nom' => $this->request->getPost('nom'),
            'prenom' => $this->request->getPost('prenom'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => $this->request->getPost('role'),
            'departement_id' => $this->request->getPost('departement_id') ?: null,
            'actif' => 1,
        ];

        if ($this->employeModel->insert($data)) {
            return redirect()->to('/admin/employes')->with('success', 'Employé créé avec succès');
        } else {
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la création');
        }
    }

    /**
     * Met à jour un employé
     */
    public function updateEmploye($id)
    {
        $employe = $this->employeModel->find($id);
        if (!$employe) {
            throw new PageNotFoundException("Employé non trouvé");
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'email' => 'required|valid_email',
            'role' => 'required|in_list[admin,rh,employe]',
            'departement_id' => 'permit_empty|numeric',
        ]);

        if (!$validation->run($this->request->getPost())) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'nom' => $this->request->getPost('nom'),
            'prenom' => $this->request->getPost('prenom'),
            'email' => $this->request->getPost('email'),
            'role' => $this->request->getPost('role'),
            'departement_id' => $this->request->getPost('departement_id') ?: null,
        ];

        // Si un nouveau mot de passe est fourni
        $newPassword = $this->request->getPost('password');
        if ($newPassword) {
            $data['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        } else {
            $data['password'] = $employe['password'];
        }

        if ($this->employeModel->update($id, $data)) {
            return redirect()->to('/admin/employes')->with('success', 'Employé mis à jour avec succès');
        } else {
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la mise à jour');
        }
    }

    /**
     * Désactive un employé
     */
    public function disableEmploye($id)
    {
        $employe = $this->employeModel->find($id);
        if (!$employe) {
            throw new PageNotFoundException("Employé non trouvé");
        }

        if ($this->employeModel->update($id, ['actif' => 0])) {
            return redirect()->back()->with('success', 'Employé désactivé avec succès');
        } else {
            return redirect()->back()->with('error', 'Erreur lors de la désactivation');
        }
    }

    // ===== CRUD DEPARTEMENTS =====

    /**
     * Liste tous les départements
     */
    public function listDepartements()
    {
        $departements = $this->departementModel->findAll();

        return view('admin/departements/list', [
            'departements' => $departements,
        ]);
    }

    /**
     * Affiche le formulaire de création/édition d'un département
     */
    public function formDepartement($id = null)
    {
        $departement = null;
        if ($id) {
            $departement = $this->departementModel->find($id);
            if (!$departement) {
                throw new PageNotFoundException("Département non trouvé");
            }
        }

        return view('admin/departements/form', [
            'departement' => $departement,
        ]);
    }

    /**
     * Crée un nouveau département
     */
    public function createDepartement()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'nom' => 'required|string',
            'description' => 'permit_empty|string',
        ]);

        if (!$validation->run($this->request->getPost())) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'nom' => $this->request->getPost('nom'),
            'description' => $this->request->getPost('description'),
        ];

        if ($this->departementModel->insert($data)) {
            return redirect()->to('/admin/departements')->with('success', 'Département créé avec succès');
        } else {
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la création');
        }
    }

    /**"
     * Met à jour un département
     */
    public function updateDepartement($id)
    {
        $departement = $this->departementModel->find($id);
        if (!$departement) {
            throw new PageNotFoundException("Département non trouvé");
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'nom' => 'required|string',
            'description' => 'permit_empty|string',
        ]);

        if (!$validation->run($this->request->getPost())) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'nom' => $this->request->getPost('nom'),
            'description' => $this->request->getPost('description'),
        ];

        if ($this->departementModel->update($id, $data)) {
            return redirect()->to('/admin/departements')->with('success', 'Département mis à jour avec succès');
        } else {
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la mise à jour');
        }
    }

    // ===== CRUD TYPES DE CONGE =====

    /**
     * Liste tous les types de congé
     */
    public function listTypes()
    {
        $types = $this->typeCongeModel->findAll();

        return view('admin/types/list', [
            'types' => $types,
        ]);
    }

    /**
     * Affiche le formulaire de création/édition d'un type
     */
    public function formType($id = null)
    {
        $type = null;
        if ($id) {
            $type = $this->typeCongeModel->find($id);
            if (!$type) {
                throw new PageNotFoundException("Type de congé non trouvé");
            }
        }

        return view('admin/types/form', [
            'type' => $type,
        ]);
    }

    /**
     * Crée un nouveau type
     */
    public function createType()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'libelle' => 'required|string',
            'jours_annuels' => 'required|numeric',
            'deductible' => 'permit_empty|numeric',
        ]);

        if (!$validation->run($this->request->getPost())) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'libelle' => $this->request->getPost('libelle'),
            'jours_annuels' => $this->request->getPost('jours_annuels'),
            'deductible' => $this->request->getPost('deductible') ? 1 : 0,
        ];

        if ($this->typeCongeModel->insert($data)) {
            return redirect()->to('/admin/types')->with('success', 'Type de congé créé avec succès');
        } else {
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la création');
        }
    }

    /**
     * Met à jour un type de congé
     */
    public function updateType($id)
    {
        $type = $this->typeCongeModel->find($id);
        if (!$type) {
            throw new PageNotFoundException("Type de congé non trouvé");
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'libelle' => 'required|string',
            'jours_annuels' => 'required|numeric',
            'deductible' => 'permit_empty|numeric',
        ]);

        if (!$validation->run($this->request->getPost())) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'libelle' => $this->request->getPost('libelle'),
            'jours_annuels' => $this->request->getPost('jours_annuels'),
            'deductible' => $this->request->getPost('deductible') ? 1 : 0,
        ];

        if ($this->typeCongeModel->update($id, $data)) {
            return redirect()->to('/admin/types')->with('success', 'Type de congé mis à jour avec succès');
        } else {
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la mise à jour');
        }
    }

    // ===== GESTION SOLDES =====

    /**
     * Liste les soldes
     */
    public function listSoldes()
    {
        $annee = $this->request->getGet('annee') ?? date('Y');
        $soldes = $this->getSoldesByAnnee((int) $annee);

        return view('admin/soldes/list', [
            'soldes' => $soldes,
            'annee' => $annee,
        ]);
    }

    /**
     * Initialise les soldes pour l'année courante
     */
    public function initSoldes()
    {
        $annee = date('Y');
        
        // Récupérer tous les employés
        $employes = $this->employeModel->findAll();
        
        // Récupérer tous les types de congé
        $types = $this->typeCongeModel->findAll();

        foreach ($employes as $employe) {
            foreach ($types as $type) {
                // Vérifier si le solde n'existe pas déjà
                $existing = $this->soldeModel->getSolde($employe['id'], $type['id'], $annee);
                
                if (!$existing) {
                    $this->soldeModel->insert([
                        'employe_id' => $employe['id'],
                        'type_conge_id' => $type['id'],
                        'annee' => $annee,
                        'jours_attribues' => $type['jours_annuels'],
                        'jours_pris' => 0,
                        'jours_restants' => $type['jours_annuels'],
                    ]);
                }
            }
        }

        return redirect()->to('/admin/soldes')->with('success', 'Soldes initialisés avec succès pour l\'année ' . $annee);
    }

    /**
     * Récupère tous les soldes d'une année donnée.
     */
    private function getSoldesByAnnee(int $annee): array
    {
        $soldes = $this->soldeModel
            ->where('annee', $annee)
            ->findAll();

        foreach ($soldes as &$solde) {
            $solde['jours_restants'] = (float) $solde['jours_attribues'] - (float) $solde['jours_pris'];
        }

        return $soldes;
    }
}
