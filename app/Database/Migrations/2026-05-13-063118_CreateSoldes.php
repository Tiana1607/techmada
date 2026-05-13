<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSoldes extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INTEGER', 'auto_increment' => true],
            'employe_id' => ['type' => 'INTEGER'],
            'type_conge_id' => ['type' => 'INTEGER'],
            'annee' => ['type' => 'INTEGER'],
            'jours_attribues' => ['type' => 'FLOAT', 'default' => 0],
            'jours_pris' => ['type' => 'FLOAT', 'default' => 0],
            'jours_restants' => ['type' => 'FLOAT', 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('employe_id', 'employes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('type_conge_id', 'types_conge', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('soldes');
    }

    public function down()
    {
        $this->forge->dropTable('soldes');
    }
}