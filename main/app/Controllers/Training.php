<?php

namespace App\Controllers;

class Training extends BaseController
{
    public function index(): string
    {
        $data = $this->pageData('training', 'Training Mode', 'training');
        $data['subjects'] = $this->db->table('subjects')->orderBy('priority', 'ASC')->get()->getResultArray();
        $data['phases'] = $this->db->table('phases')->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC')->get()->getResultArray();
        $data['mcqCount'] = (int) $this->db->table('mcqs')->where('status', 'active')->countAllResults();
        $data['best'] = $this->db->table('quiz_sessions')
            ->select('subject_id, MAX(CASE WHEN total > 0 THEN correct / total ELSE 0 END) AS best')
            ->groupBy('subject_id')->get()->getResultArray();
        return view('partials/layout', array_merge($data, ['content' => view('pages/training', $data)]));
    }

    public function challenge(): string
    {
        $data = $this->pageData('training', 'Daily Challenge', 'challenge');
        $data['subjects'] = $this->db->table('subjects')->orderBy('priority', 'ASC')->get()->getResultArray();
        $data['phases'] = $this->db->table('phases')->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC')->get()->getResultArray();
        return view('partials/layout', array_merge($data, ['content' => view('pages/challenge', $data)]));
    }
}