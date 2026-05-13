<?php

namespace App\Models;

use CodeIgniter\Model;

class TypeConge extends Model
{
    protected $table            = 'types_conge';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'libelle',
        'jours_annuels',
        'deductible',
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
        'libelle' => 'required|max_length[150]',
        'jours_annuels' => 'required|is_natural',
        'deductible' => 'required|in_list[0,1]',
    ];
    protected $validationMessages   = [
        'libelle' => [
            'required' => 'Le libelle du type de conge est obligatoire.',
            'max_length' => 'Le libelle est trop long.',
        ],
        'jours_annuels' => [
            'required' => 'Le nombre de jours annuels est obligatoire.',
            'is_natural' => 'Le nombre de jours annuels est invalide.',
        ],
        'deductible' => [
            'required' => 'Le champ deductible est obligatoire.',
            'in_list' => 'Le champ deductible doit valoir 0 ou 1.',
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

    public function findDeductible(): array
    {
        return $this->where('deductible', 1)->findAll();
    }

    public function findNonDeductible(): array
    {
        return $this->where('deductible', 0)->findAll();
    }

    public function isDeductible(int $typeCongeId): bool
    {
        $typeConge = $this->select('deductible')->find($typeCongeId);

        return $typeConge !== null && (int) $typeConge['deductible'] === 1;
    }

    public function getTypeConges(): array
    {
        return $this->orderBy('libelle', 'ASC')->findAll();
    }

    public function getTypeCongeById(int $id): ?array
    {
        return $this->find($id);
    }

    public function createTypeConge(array $data): int|false
    {
        return $this->insert($data, true);
    }

    public function updateTypeConge(int $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    public function deleteTypeConge(int $id): bool
    {
        return $this->delete($id);
    }
}
