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
        $demandEnAttente = $this->congeModel->countByStatut('en_attente');
        $demandApprouvee = $this->congeModel->countByStatut('approuvee');
        $demandRefusee = $this->congeModel->countByStatut('refusee');

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

        $demandes = $this->congeModel->listByStatut($statut, $departementId);

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
        $solde = $this->soldeModel->getSolde($conge['employe_id'], $conge['type_conge_id'], $annee);

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
            $conge['employe_id'],
            $conge['type_conge_id'],
            $annee,
            $conge['nb_jours']
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
            $demandes = $this->congeModel->listByStatut($statut);
        } elseif ($employeId) {
            $demandes = $this->congeModel->listByEmploye($employeId);
        } else {
            $demandes = $this->congeModel->findAll();
        }

        return view('rh/historique_demandes', [
            'demandes' => $demandes,
        ]);
    }
}
