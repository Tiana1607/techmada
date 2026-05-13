<?php

namespace App\Controllers;

use App\Models\Employe;
use App\Models\Departement;
use App\Models\Conge;
use App\Models\Solde;
use App\Models\TypeConge;

class EmployeController extends BaseController
{
    protected $employeModel;
    protected $departementModel;
    protected $congeModel;
    protected $soldeModel;
    protected $typeCongeModel;
    protected $userId;

    public function __construct()
    {
        $this->employeModel = new Employe();
        $this->departementModel = new Departement();
        $this->congeModel = new Conge();
        $this->soldeModel = new Solde();
        $this->typeCongeModel = new TypeConge();
        $this->userId = session()->get('user_id');
    }

    /**
     * Dashboard : stats + soldes + dernières demandes
     */
    public function dashboard()
    {
        $data = $this->buildEmployeContext();

        return view('employe/dashboard', $data);
    }

    /**
     * Affiche le formulaire de nouvelle demande
     */
    public function formulaire()
    {
        $data = $this->buildEmployeContext();
        $data['typesCong'] = $this->typeCongeModel->findAll();

        return view('employe/formulaire', [
            'typesCong' => $data['typesCong'],
            'soldes' => $data['soldes'],
            'metrics' => $data['metrics'],
            'userNom' => $data['userNom'],
            'userPrenom' => $data['userPrenom'],
            'user' => $data['user'],
            'departement' => $data['departement'],
            'annee' => $data['annee'],
        ]);
    }

    /**
     * Crée une nouvelle demande
     */
    public function submitDemande()
    {
        $response = null;
        $validation = \Config\Services::validation();
        $validation->setRules([
            'type_conge_id' => 'required|numeric',
            'date_debut' => 'required|valid_date[Y-m-d]',
            'date_fin' => 'required|valid_date[Y-m-d]',
            'motif' => 'permit_empty|string',
        ]);

        if (! $validation->run($this->request->getPost())) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $dateDebut = $this->request->getPost('date_debut');
        $dateFin = $this->request->getPost('date_fin');
        $typeCongeId = $this->request->getPost('type_conge_id');
        $motif = $this->request->getPost('motif');

        // Vérifier que date_debut <= date_fin
        if ($dateDebut > $dateFin) {
            return redirect()->back()->withInput()->with('error', 'La date de début doit être avant la date de fin');
        }

        // Calculer le nombre de jours
        $nbJours = $this->calculNbJours($dateDebut, $dateFin);

        // Créer la demande
        $dataConge = [
            'employe_id' => $this->userId,
            'type_conge_id' => $typeCongeId,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'nb_jours' => $nbJours,
            'motif' => $motif,
            'statut' => 'en_attente',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        if ($this->congeModel->createDemande($dataConge)) {
            $response = redirect()->to('/employe/mes-demandes')->with('success', 'Demande de congé créée avec succès');
        } else {
            $response = redirect()->back()->withInput()->with('error', 'Erreur lors de la création de la demande');
        }

        return $response;
    }

    /**
     * Liste toutes les demandes de l'employé
     */
    public function listDemandes()
    {
        $data = $this->buildEmployeContext();
        $demandes = $this->congeModel->listByEmploye($this->userId);

        // Add type label to each demande
        foreach ($demandes as $k => $d) {
            $type = $this->typeCongeModel->find($d['type_conge_id']);
            $demandes[$k]['type_libelle'] = $type ? $type['libelle'] : '—';
        }

        return view('employe/demandes', [
            'demandes' => $demandes,
            'metrics' => $data['metrics'],
            'userNom' => $data['userNom'],
            'userPrenom' => $data['userPrenom'],
            'user' => $data['user'],
            'departement' => $data['departement'],
            'annee' => $data['annee'],
        ]);
    }

    /**
     * Affiche le profil de l'employé connecté.
     */
    public function profil()
    {
        $data = $this->buildEmployeContext();

        return view('employe/profil', $data);
    }

    /**
     * Annule une demande (seulement si en_attente)
     */
    public function cancelDemande($id)
    {
        $conge = $this->congeModel->find($id);
        $response = null;

        if (! $conge || $conge['employe_id'] != $this->userId) {
            return redirect()->back()->with('error', 'Accès refusé');
        }

        if ($conge['statut'] !== 'en_attente') {
            return redirect()->back()->with('error', 'Seules les demandes en attente peuvent être annulées');
        }

        if ($this->congeModel->update($id, ['statut' => 'annulee'])) {
            $response = redirect()->back()->with('success', 'Demande annulée avec succès');
        } else {
            $response = redirect()->back()->with('error', 'Erreur lors de l\'annulation');
        }

        return $response;
    }

    /**
     * Calcule le nombre de jours ouvrables entre deux dates
     */
    private function calculNbJours($dateDebut, $dateFin)
    {
        $debut = new \DateTime($dateDebut);
        $fin = new \DateTime($dateFin);

        $nbJours = 0;
        while ($debut <= $fin) {
            // Compter seulement les jours de semaine (1-5 = Lun-Ven)
            if ($debut->format('N') < 6) {
                $nbJours++;
            }
            $debut->modify('+1 day');
        }

        return $nbJours;
    }

    /**
     * Récupère les soldes d'un employé pour une année donnée.
     */
    private function getSoldesAnnee(int $employeId, int $annee): array
    {
        $soldes = $this->soldeModel
            ->where('employe_id', $employeId)
            ->where('annee', $annee)
            ->findAll();

        foreach ($soldes as &$solde) {
            $solde['jours_restants'] = (float) $solde['jours_attribues'] - (float) $solde['jours_pris'];
        }

        return $soldes;
    }

    /**
     * Construit les données communes pour les pages employé.
     */
    private function buildEmployeContext(): array
    {
        $annee = (int) date('Y');
        $user = $this->getEmployeCourant();
        $departement = $this->getDepartementCourant($user);
        $soldes = $this->getSoldesAvecLibelles((int) $this->userId, $annee);
        $allDemandes = $this->getDemandesAvecLibelles((int) $this->userId, null);
        $dernieresDemandes = array_slice($allDemandes, 0, 5);
        $metrics = $this->buildMetrics($soldes, $allDemandes);

        return [
            'annee' => $annee,
            'user' => $user,
            'departement' => $departement,
            'soldes' => $soldes,
            'dernieresDemandes' => $dernieresDemandes,
            'metrics' => $metrics,
            'userNom' => (string) (session()->get('nom') ?: ($user['nom'] ?? '')),
            'userPrenom' => (string) (session()->get('prenom') ?: ($user['prenom'] ?? '')),
        ];
    }

    /**
     * Retourne l'employé courant depuis la base.
     */
    private function getEmployeCourant(): array
    {
        return $this->employeModel->find($this->userId) ?: [];
    }

    /**
     * Retourne le département associé à l'employé courant.
     */
    private function getDepartementCourant(array $user): array
    {
        if (empty($user['departement_id'])) {
            return [];
        }

        return $this->departementModel->find((int) $user['departement_id']) ?: [];
    }

    /**
     * Charge les soldes avec le libellé du type de congé.
     */
    private function getSoldesAvecLibelles(int $employeId, int $annee): array
    {
        $soldes = $this->getSoldesAnnee($employeId, $annee);

        foreach ($soldes as $k => $s) {
            $type = $this->typeCongeModel->find($s['type_conge_id']);
            $soldes[$k]['type_libelle'] = $type ? $type['libelle'] : '—';
        }

        return $soldes;
    }

    /**
     * Charge les demandes avec leur libellé de type de congé.
     */
    private function getDemandesAvecLibelles(int $employeId, ?int $limit): array
    {
        $demandes = $this->congeModel->listByEmploye($employeId);

        if ($limit !== null) {
            $demandes = array_slice($demandes, 0, $limit);
        }

        foreach ($demandes as $k => $d) {
            $type = $this->typeCongeModel->find($d['type_conge_id']);
            $demandes[$k]['type_libelle'] = $type ? $type['libelle'] : '—';
        }

        return $demandes;
    }

    /**
     * Calcule les compteurs de tableau de bord.
     */
    private function buildMetrics(array $soldes, array $allDemandes): array
    {
        $metrics = [
            'en_attente' => 0,
            'approuvee' => 0,
            'refusee' => 0,
            'jours_restants_total' => 0,
        ];

        foreach ($allDemandes as $d) {
            if (isset($metrics[$d['statut']])) {
                $metrics[$d['statut']]++;
            }
        }

        foreach ($soldes as $s) {
            $metrics['jours_restants_total'] += (float) $s['jours_restants'];
        }

        return $metrics;
    }
}
