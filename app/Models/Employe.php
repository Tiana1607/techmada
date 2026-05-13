<?php

namespace App\Models;

use CodeIgniter\Model;

class Employe extends Model
{
    protected $table            = 'employes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nom',
        'prenom',
        'email',
        'password',
        'role',
        'departement_id',
        'date_embauche',
        'actif',
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
        'nom' => 'required|max_length[120]',
        'prenom' => 'required|max_length[120]',
        'email' => 'required|valid_email|max_length[255]',
        'password' => 'required|max_length[255]',
        'role' => 'permit_empty|max_length[50]',
        'departement_id' => 'permit_empty|is_natural_no_zero',
        'date_embauche' => 'permit_empty|valid_date',
        'actif' => 'permit_empty|in_list[0,1]',
    ];
    protected $validationMessages   = [
        'nom' => [
            'required' => 'Le nom est obligatoire.',
            'max_length' => 'Le nom est trop long.',
        ],
        'prenom' => [
            'required' => 'Le prenom est obligatoire.',
            'max_length' => 'Le prenom est trop long.',
        ],
        'email' => [
            'required' => 'L email est obligatoire.',
            'valid_email' => 'L email est invalide.',
            'max_length' => 'L email est trop long.',
        ],
        'password' => [
            'required' => 'Le mot de passe est obligatoire.',
            'max_length' => 'Le mot de passe est trop long.',
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

    public function findByEmail(string $email): ?array
    {
        return $this->where('email', $email)->first();
    }

    public function isActive(int $id): bool
    {
        $employe = $this->select('actif')->find($id);

        return $employe !== null && (int) $employe['actif'] === 1;
    }
}
