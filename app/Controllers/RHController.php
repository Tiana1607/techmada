<?php

namespace App\Controllers;

use App\Models\Conge;
use App\Models\Departement;
use App\Models\Solde;
use App\Models\Employe;
use \CodeIgniter\Exceptions\PageNotFoundException;

class RHController extends BaseController
{
    protected $congeModel;
    protected $departementModel;
    protected $soldeModel;
    protected $employeModel;

    public function __construct()
    {
        $this->congeModel = new Conge();
        $this->departementModel = new Departement();
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
        $recentDemandes = $this->getDemandesEnrichies(null, null, 5);

        return view('rh/dashboard', [
            'enAttente' => $demandEnAttente,
            'approuvee' => $demandApprouvee,
            'refusee' => $demandRefusee,
            'recentDemandes' => $recentDemandes,
        ]);
    }

    public function ajaxDemandes()
    {
        $statut = $this->request->getGet('statut') ?? 'en_attente';
        $departementId = $this->request->getGet('departement_id');

        $demandes = $this->getDemandesEnrichies($statut, $departementId);
        $departements = $this->departementModel->getDepartements();

        return view('rh/sections/demandes', [
            'demandes' => $demandes,
            'statutActuel' => $statut,
            'departements' => $departements,
            'totalEnAttente' => $this->countCongesByStatut('en_attente'),
            'totalApprouvee' => $this->countCongesByStatut('approuvee'),
            'totalRefusee' => $this->countCongesByStatut('refusee'),
        ]);
    }

    public function ajaxHistorique()
    {
        $statut = $this->request->getGet('statut');
        $employeId = $this->request->getGet('employe_id');

        if ($statut) {
            $demandes = $this->getDemandesEnrichies($statut);
        } elseif ($employeId) {
            $demandes = $this->getDemandesEnrichies(null, null, null, (int) $employeId);
        } else {
            $demandes = $this->getDemandesEnrichies(null, null, 50);
        }

        return view('rh/sections/historique', [
            'demandes' => $demandes,
            'statutActuel' => $statut,
        ]);
    }

    /**
     * Liste les demandes en attente avec filtres optionnels
     */
    public function index()
    {
        $statut = $this->request->getGet('statut') ?? 'en_attente';
        $departementId = $this->request->getGet('departement_id');

        $demandes = $this->getDemandesEnrichies($statut, $departementId);
        $departements = $this->departementModel->getDepartements();

        return view('rh/list_demandes', [
            'demandes' => $demandes,
            'statutActuel' => $statut,
            'departements' => $departements,
            'totalEnAttente' => $this->countCongesByStatut('en_attente'),
            'totalApprouvee' => $this->countCongesByStatut('approuvee'),
            'totalRefusee' => $this->countCongesByStatut('refusee'),
        ]);
    }

    /**
     * Affiche le détail d'une demande
     */
    public function detail($id)
    {
        $conge = $this->getDemandesDetail((int) $id);

        if (!$conge) {
            throw new PageNotFoundException("Demande non trouvée");
        }

        return view('rh/detail_demande', [
            'conge' => $conge,
            'employe' => $conge,
            'solde' => $this->soldeModel->getSolde((int) $conge['employe_id'], (int) $conge['type_conge_id'], (int) date('Y')),
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

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Demande approuvée avec succès',
            ]);
        }

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

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Demande refusée avec succès',
            ]);
        }

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
            $demandes = $this->getDemandesEnrichies($statut);
        } elseif ($employeId) {
            $demandes = $this->getDemandesEnrichies(null, null, null, (int) $employeId);
        } else {
            $demandes = $this->getDemandesEnrichies(null, null, 50);
        }

        return view('rh/historique_demandes', [
            'demandes' => $demandes,
            'statutActuel' => $statut,
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
    private function getDemandesEnrichies(?string $statut = null, ?string $departementId = null, ?int $limit = null, ?int $employeId = null): array
    {
        $builder = $this->congeModel->builder();
        $builder->select('conges.*, employes.nom, employes.prenom, employes.email, employes.role, departements.nom AS departement_nom, types_conge.libelle AS type_libelle')
            ->join('employes', 'employes.id = conges.employe_id', 'left')
            ->join('departements', 'departements.id = employes.departement_id', 'left')
            ->join('types_conge', 'types_conge.id = conges.type_conge_id', 'left');

        if ($statut) {
            $builder->where('conges.statut', $statut);
        }

        if ($departementId !== null && $departementId !== '') {
            $builder->where('employes.departement_id', $departementId);
        }

        if ($employeId !== null) {
            $builder->where('conges.employe_id', $employeId);
        }

        $builder->orderBy('conges.created_at', 'DESC');

        if ($limit !== null) {
            $builder->limit($limit);
        }

        $demandes = $builder->get()->getResultArray();

        foreach ($demandes as &$demande) {
            $demande['jours_restants'] = null;
            $solde = $this->soldeModel->getSolde((int) $demande['employe_id'], (int) $demande['type_conge_id'], (int) date('Y'));
            if ($solde !== null) {
                $demande['jours_restants'] = (float) $solde['jours_attribues'] - (float) $solde['jours_pris'];
            }
        }

        return $demandes;
    }

    private function getDemandesByFilters(?string $statut = null, ?string $departementId = null): array
    {
        return $this->getDemandesEnrichies($statut, $departementId);
    }

    private function getDemandesEnrichiesById(int $id): ?array
    {
        return $this->getDemandesEnrichies(null, null, null, $id)[0] ?? null;
    }

    private function getDemandesDetail(int $id): ?array
    {
        return $this->congeModel->builder()
            ->select('conges.*, employes.nom, employes.prenom, employes.email, employes.role, departements.nom AS departement_nom, types_conge.libelle AS type_libelle')
            ->join('employes', 'employes.id = conges.employe_id', 'left')
            ->join('departements', 'departements.id = employes.departement_id', 'left')
            ->join('types_conge', 'types_conge.id = conges.type_conge_id', 'left')
            ->where('conges.id', $id)
            ->get()
            ->getFirstRow('array');
    }
}
