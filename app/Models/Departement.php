<?php

namespace App\Models;

use CodeIgniter\Model;

class Departement extends Model
{
    protected $table            = 'departements';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nom',
        'description',
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
        'nom' => 'required|max_length[150]',
        'description' => 'permit_empty',
    ];
    protected $validationMessages   = [
        'nom' => [
            'required' => 'Le nom du departement est obligatoire.',
            'max_length' => 'Le nom du departement est trop long.',
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

    public function getDepartements(): array
    {
        return $this->orderBy('nom', 'ASC')->findAll();
    }

    public function createDepartement(array $data): int|false
    {
        return $this->insert($data, true);
    }

    public function updateDepartement(int $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    public function deleteDepartement(int $id): bool
    {
        return $this->delete($id);
    }
}
