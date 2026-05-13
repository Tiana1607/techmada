<?php

namespace App\Models;

use CodeIgniter\Model;

class Solde extends Model
{
    protected $table            = 'soldes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'employe_id',
        'type_conge_id',
        'annee',
        'jours_attribues',
        'jours_pris',
        'jours_restants',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'employe_id' => 'required|is_natural_no_zero',
        'type_conge_id' => 'required|is_natural_no_zero',
        'annee' => 'required|is_natural_no_zero',
        'jours_attribues' => 'required|decimal',
        'jours_pris' => 'permit_empty|decimal',
        'jours_restants' => 'permit_empty|decimal',
    ];
    protected $validationMessages   = [
        'employe_id' => [
            'required' => 'L employe est obligatoire.',
            'is_natural_no_zero' => 'L identifiant employe est invalide.',
        ],
        'type_conge_id' => [
            'required' => 'Le type de conge est obligatoire.',
            'is_natural_no_zero' => 'L identifiant du type de conge est invalide.',
        ],
        'annee' => [
            'required' => 'L annee est obligatoire.',
            'is_natural_no_zero' => 'L annee est invalide.',
        ],
        'jours_attribues' => [
            'required' => 'Les jours attribues sont obligatoires.',
            'decimal' => 'Les jours attribues doivent etre un nombre valide.',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function getSolde(int $employeId, int $typeCongeId, int $annee): ?array
    {
        return $this->where([
            'employe_id' => $employeId,
            'type_conge_id' => $typeCongeId,
            'annee' => $annee,
        ])->first();
    }

    public function updatePrise(int $employeId, int $typeCongeId, int $annee, float $nbJours): bool
    {
        $solde = $this->getSolde($employeId, $typeCongeId, $annee);

        if ($solde === null) {
            return false;
        }

        $joursPris = (float) $solde['jours_pris'] + $nbJours;
        $joursRestants = (float) $solde['jours_attribues'] - $joursPris;

        return $this->update((int) $solde['id'], [
            'jours_pris' => $joursPris,
            'jours_restants' => $joursRestants,
        ]);
    }
}
