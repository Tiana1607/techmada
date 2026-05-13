<?php

namespace App\Controllers;

use App\Models\Conge;
use App\Models\Solde;
use App\Models\Employe;
use \CodeIgniter\Exceptions\PageNotFoundException;

class RHController extends BaseController
{
    protected $congeModel;
    protected $soldeModel;
    protected $employeModel;

    public function __construct()
    {
        $this->congeModel = new Conge();
        $this->soldeModel = new Solde();
        $this->employeModel = new Employe();
    }

    /**
     * Dashboard RH : aperçu des demandes
     */
    public function dashboard()
    {
        $demandEnAttente = $this->countCongesByStatut('en_attente');
        $demandApprouvee = $this->countCongesByStatut('approuvee');
        $demandRefusee = $this->countCongesByStatut('refusee');

        return view('rh/dashboard', [
            'enAttente' => $demandEnAttente,
            'approuvee' => $demandApprouvee,
            'refusee' => $demandRefusee,
        ]);
    }

    /**
     * Liste les demandes en attente avec filtres optionnels
     */
    public function index()
    {
        $statut = $this->request->getGet('statut') ?? 'en_attente';
        $departementId = $this->request->getGet('departement_id');

        $demandes = $this->getDemandesByFilters($statut, $departementId);

        return view('rh/list_demandes', [
            'demandes' => $demandes,
            'statutActuel' => $statut,
        ]);
    }

    /**
     * Affiche le détail d'une demande
     */
    public function detail($id)
    {
        $conge = $this->congeModel->find($id);

        if (!$conge) {
            throw new PageNotFoundException("Demande non trouvée");
        }

        $employe = $this->employeModel->find($conge['employe_id']);

        return view('rh/detail_demande', [
            'conge' => $conge,
            'employe' => $employe,
        ]);
    }

    /**
     * Approuve une demande et met à jour le solde
     */
    public function approuver($id)
    {
        $conge = $this->congeModel->find($id);

        if (!$conge) {
            return redirect()->back()->with('error', 'Demande non trouvée');
        }

        if ($conge['statut'] !== 'en_attente') {
            return redirect()->back()->with('error', 'Seules les demandes en attente peuvent être approuvées');
        }

        $annee = date('Y');
        $employeId = (int) $conge['employe_id'];
        $typeCongeId = (int) $conge['type_conge_id'];
        $solde = $this->soldeModel->getSolde($employeId, $typeCongeId, $annee);

        // Vérifier le solde avant approbation
        if (!$solde || ($solde['jours_pris'] + $conge['nb_jours']) > $solde['jours_attribues']) {
            return redirect()->back()->with('error', 'Solde insuffisant pour approuver cette demande');
        }

        // Mettre à jour le statut et le traité_par
        $this->congeModel->update($id, [
            'statut' => 'approuvee',
            'traite_par' => session()->get('user_id'),
        ]);

        // Mettre à jour le solde (incrémenter jours_pris)
        $this->soldeModel->updatePrise(
            $employeId,
            $typeCongeId,
            $annee,
            (float) $conge['nb_jours']
        );

        return redirect()->back()->with('success', 'Demande approuvée avec succès');
    }

    /**
     * Refuse une demande
     */
    public function refuser($id)
    {
        $conge = $this->congeModel->find($id);

        if (!$conge) {
            return redirect()->back()->with('error', 'Demande non trouvée');
        }

        if ($conge['statut'] !== 'en_attente') {
            return redirect()->back()->with('error', 'Seules les demandes en attente peuvent être refusées');
        }

        $commentaire = $this->request->getPost('commentaire_rh');

        // Mettre à jour le statut
        $this->congeModel->update($id, [
            'statut' => 'refusee',
            'commentaire_rh' => $commentaire,
            'traite_par' => session()->get('user_id'),
        ]);

        return redirect()->back()->with('success', 'Demande refusée avec succès');
    }

    /**
     * Affiche l'historique de toutes les demandes
     */
    public function historique()
    {
        $statut = $this->request->getGet('statut');
        $employeId = $this->request->getGet('employe_id');

        if ($statut) {
            $demandes = $this->getDemandesByFilters($statut);
        } elseif ($employeId) {
            $demandes = $this->congeModel->listByEmploye($employeId);
        } else {
            $demandes = $this->congeModel->findAll();
        }

        return view('rh/historique_demandes', [
            'demandes' => $demandes,
        ]);
    }

    /**
     * Compte les congés selon un statut via le builder du modèle.
     */
    private function countCongesByStatut(string $statut): int
    {
        return $this->congeModel
            ->builder()
            ->where('statut', $statut)
            ->countAllResults();
    }

    /**
     * Récupère les demandes avec filtre statut et/ou département sans dépendre de méthodes manquantes.
     */
    private function getDemandesByFilters(?string $statut = null, ?string $departementId = null): array
    {
        $builder = $this->congeModel->builder();
        $builder->select('conges.*')
            ->join('employes', 'employes.id = conges.employe_id', 'left');

        if ($statut) {
            $builder->where('conges.statut', $statut);
        }

        if ($departementId !== null && $departementId !== '') {
            $builder->where('employes.departement_id', $departementId);
        }

        return $builder->orderBy('conges.created_at', 'DESC')->get()->getResultArray();
    }
}
