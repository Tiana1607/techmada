<?php

namespace App\Controllers;

use App\Models\Employe;
use App\Models\Conge;
use App\Models\Solde;
use App\Models\TypeConge;

class EmployeController extends BaseController
{
    protected $employeModel;
    protected $congeModel;
    protected $soldeModel;
    protected $typeCongeModel;
    protected $userId;

    public function __construct()
    {
        $this->employeModel = new Employe();
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
        $annee = date('Y');
        
        $soldes = $this->getSoldesAnnee($this->userId, $annee);

        $dernieresDemandes = array_slice($this->congeModel->listByEmploye($this->userId), 0, 5);

        return view('employe/dashboard', [
            'soldes' => $soldes,
            'dernieresDemandes' => $dernieresDemandes,
        ]);
    }

    /**
     * Affiche le formulaire de nouvelle demande
     */
    public function formulaire()
    {
        $typesCong = $this->typeCongeModel->findAll();

        return view('employe/formulaire_demande', [
            'typesCong' => $typesCong,
        ]);
    }

    /**
     * Crée une nouvelle demande
     */
    public function submitDemande()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'type_conge_id' => 'required|numeric',
            'date_debut' => 'required|valid_date[Y-m-d]',
            'date_fin' => 'required|valid_date[Y-m-d]',
            'motif' => 'permit_empty|string',
        ]);

        if (!$validation->run($this->request->getPost())) {
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
            return redirect()->to('/employe/mes-demandes')->with('success', 'Demande de congé créée avec succès');
        } else {
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la création de la demande');
        }
    }

    /**
     * Liste toutes les demandes de l'employé
     */
    public function listDemandes()
    {
        $demandes = $this->congeModel->listByEmploye($this->userId);

        return view('employe/demandes_employe', [
            'demandes' => $demandes,
        ]);
    }

    /**
     * Annule une demande (seulement si en_attente)
     */
    public function cancelDemande($id)
    {
        $conge = $this->congeModel->find($id);

        if (!$conge || $conge['employe_id'] != $this->userId) {
            return redirect()->back()->with('error', 'Accès refusé');
        }

        if ($conge['statut'] !== 'en_attente') {
            return redirect()->back()->with('error', 'Seules les demandes en attente peuvent être annulées');
        }

        if ($this->congeModel->update($id, ['statut' => 'annulee'])) {
            return redirect()->back()->with('success', 'Demande annulée avec succès');
        } else {
            return redirect()->back()->with('error', 'Erreur lors de l\'annulation');
        }
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
}
