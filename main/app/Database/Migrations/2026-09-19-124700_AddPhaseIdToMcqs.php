<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPhaseIdToMcqs extends Migration
{
    public function up(): void
    {
        $fields = [
            'phase_id' => ['type' => 'INT', 'null' => true, 'default' => null, 'after' => 'topic_id'],
        ];
        $this->forge->addColumn('mcqs', $fields);
        $this->db->query('CREATE INDEX idx_mcq_phase ON mcqs (phase_id)');
    }

    public function down(): void
    {
        $this->db->query('DROP INDEX idx_mcq_phase ON mcqs');
        $this->forge->dropColumn('mcqs', 'phase_id');
    }
}
