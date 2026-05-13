<?php

namespace App\Libraries;

use App\Models\Conge;
use App\Models\Solde;
use Config\Database;

class CalculConge
{
    public function calculerNbJours(string $dateDebut, string $dateFin, bool $joursOuvres = true): float
    {
        $debutTimestamp = strtotime($dateDebut);
        $finTimestamp = strtotime($dateFin);

        if ($debutTimestamp === false || $finTimestamp === false || $finTimestamp < $debutTimestamp) {
            return 0.0;
        }

        $nbJours = 0.0;
        $courantTimestamp = $debutTimestamp;
        $finDateTimestamp = $finTimestamp;

        while ($courantTimestamp <= $finDateTimestamp) {
            $jour = getdate($courantTimestamp);
            $estWeekEnd = in_array((string) $jour['wday'], ['0', '6'], true);

            if (! $joursOuvres || ! $estWeekEnd) {
                $nbJours += 1;
            }

            $courantTimestamp = strtotime('+1 day', $courantTimestamp);
        }

        return $nbJours;
    }

    public function hasChevauchement(int $employeId, string $dateDebut, string $dateFin, ?int $congeIgnoreId = null): bool
    {
        $congeModel = new Conge();

        $builder = $congeModel->builder();
        $builder->where('employe_id', $employeId)
            ->groupStart()
                ->where('date_debut <=', $dateFin)
                ->where('date_fin >=', $dateDebut)
            ->groupEnd()
            ->whereNotIn('statut', ['annulee', 'refusee']);

        if ($congeIgnoreId !== null) {
            $builder->where('id !=', $congeIgnoreId);
        }

        return $builder->countAllResults() > 0;
    }

    public function verifierSoldeSuffisant(int $employeId, int $typeCongeId, int $annee, float $nbJours): array
    {
        $soldeModel = new Solde();
        $solde = $soldeModel->getSolde($employeId, $typeCongeId, $annee);

        if ($solde === null) {
            return [
                'ok' => false,
                'message' => 'Aucun solde trouve pour cet employe.',
                'solde' => null,
            ];
        }

        $joursRestants = (float) $solde['jours_attribues'] - (float) $solde['jours_pris'];

        return [
            'ok' => $joursRestants >= $nbJours,
            'message' => $joursRestants >= $nbJours
                ? 'Solde suffisant.'
                : 'Solde insuffisant.',
            'solde' => $solde,
        ];
    }

    public function appliquerApprobation(int $congeId, float $nbJours): bool
    {
        $congeModel = new Conge();
        $soldeModel = new Solde();
        $db = Database::connect();

        $conge = $congeModel->find($congeId);

        if ($conge === null) {
            return false;
        }

        $annee = (int) date('Y', strtotime($conge['date_debut']));

        $solde = $soldeModel->getSolde(
            (int) $conge['employe_id'],
            (int) $conge['type_conge_id'],
            $annee
        );

        if ($solde === null) {
            return false;
        }

        $joursPris = (float) $solde['jours_pris'] + $nbJours;
        $joursRestants = (float) $solde['jours_attribues'] - $joursPris;

        $db->transStart();

        $updatedConge = $congeModel->updateStatut($congeId, 'approuvee');
        $updatedSolde = $soldeModel->update((int) $solde['id'], [
            'jours_pris' => $joursPris,
            'jours_restants' => $joursRestants,
        ]);

        $db->transComplete();

        return $updatedConge && $updatedSolde && $db->transStatus();
    }
}
