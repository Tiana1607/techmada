<?php

namespace App\Models;

use CodeIgniter\Model;

class Conge extends Model
{
    protected $table            = 'conges';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'employe_id',
        'type_conge_id',
        'date_debut',
        'date_fin',
        'nb_jours',
        'motif',
        'statut',
        'commentaire_rh',
        'created_at',
        'traite_par',
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
        'date_debut' => 'required|valid_date',
        'date_fin' => 'required|valid_date',
        'nb_jours' => 'required|decimal',
        'statut' => 'required|max_length[50]',
        'traite_par' => 'permit_empty|is_natural_no_zero',
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
        'date_debut' => [
            'required' => 'La date de debut est obligatoire.',
            'valid_date' => 'La date de debut est invalide.',
        ],
        'date_fin' => [
            'required' => 'La date de fin est obligatoire.',
            'valid_date' => 'La date de fin est invalide.',
        ],
        'nb_jours' => [
            'required' => 'Le nombre de jours est obligatoire.',
            'decimal' => 'Le nombre de jours doit etre un nombre valide.',
        ],
        'statut' => [
            'required' => 'Le statut est obligatoire.',
            'max_length' => 'Le statut est trop long.',
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

    public function createDemande(array $data): int|false
    {
        if (! array_key_exists('statut', $data)) {
            $data['statut'] = 'en_attente';
        }

        if (! array_key_exists('created_at', $data)) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }

        return $this->insert($data, true);
    }

    public function listByEmploye(int $employeId): array
    {
        return $this->where('employe_id', $employeId)
            ->orderBy('date_debut', 'DESC')
            ->findAll();
    }

    public function listByStatut(string $statut): array
    {
        return $this->where('statut', $statut)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function updateStatut(int $congeId, string $statut, array $extraData = []): bool
    {
        $data = array_merge(['statut' => $statut], $extraData);

        return $this->update($congeId, $data);
    }
}
