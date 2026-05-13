<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmployes extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INTEGER', 'auto_increment' => true],
            'nom' => ['type' => 'VARCHAR', 'constraint' => 120],
            'prenom' => ['type' => 'VARCHAR', 'constraint' => 120],
            'email' => ['type' => 'VARCHAR', 'constraint' => 255],
            'password' => ['type' => 'VARCHAR', 'constraint' => 255],
            'role' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'departement_id' => ['type' => 'INTEGER', 'null' => true],
            'date_embauche' => ['type' => 'DATE', 'null' => true],
            'actif' => ['type' => 'INTEGER', 'default' => 1],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('email', false, true);
        $this->forge->addForeignKey('departement_id', 'departements', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('employes');
    }

    public function down()
    {
        $this->forge->dropTable('employes');
    }
}