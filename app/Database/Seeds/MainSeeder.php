<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

// Crée : 1 admin, 2 employés, 3 types de congé, soldes initialisés
class MainSeeder extends Seeder
{
    public function run()
    {
        // 1. Département
        $this->db->table('departements')->insert(['nom' => 'Informatique', 'description' => 'Équipe tech']);

        // 2. Types de congé
        $this->db->table('types_conge')->insertBatch([
            ['libelle' => 'Congé annuel', 'jours_annuels' => 25, 'deductible' => 1],
            ['libelle' => 'Maladie', 'jours_annuels' => 15, 'deductible' => 1],
            ['libelle' => 'Sans solde', 'jours_annuels' => 10, 'deductible' => 0],
        ]);

        // 3. Employes
        $this->db->table('employes')->insertBatch([
            [
                'nom' => 'Admin',
                'prenom' => 'Super',
                'email' => 'admin@tech.mg',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role' => 'admin',
                'actif' => 1
            ],
            [
                'nom' => 'Rakoto',
                'prenom' => 'Jean',
                'email' => 'jean@tech.mg',
                'password' => password_hash('employe123', PASSWORD_DEFAULT),
                'role' => 'employe',
                'departement_id' => 1,
                'actif' => 1
            ],
            [
                'nom' => 'Rasoa',
                'prenom' => 'Marie',
                'email' => 'marie@tech.mg',
                'password' => password_hash('rh123', PASSWORD_DEFAULT),
                'role' => 'rh',
                'departement_id' => 1,
                'actif' => 1
            ],
        ]);

        // 4. Soldes initiaux pour chaque employé
        $annee = date('Y');
        // Pour employe id=2 et rh id=3, types 1 et 2
        $this->db->table('soldes')->insertBatch([
            ['employe_id' => 2, 'type_conge_id' => 1, 'annee' => $annee, 'jours_attribues' => 25, 'jours_pris' => 0],
            ['employe_id' => 2, 'type_conge_id' => 2, 'annee' => $annee, 'jours_attribues' => 15, 'jours_pris' => 0],
            ['employe_id' => 3, 'type_conge_id' => 1, 'annee' => $annee, 'jours_attribues' => 25, 'jours_pris' => 0],
        ]);
    }
}
