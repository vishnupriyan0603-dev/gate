<?php

namespace App\Controllers;

class Performance extends BaseController
{
    public function index(): string
    {
        $data = $this->pageData('performance', 'Performance', 'performance');
        $data['subjects'] = $this->db->table('subjects')->orderBy('priority', 'ASC')->get()->getResultArray();
        return view('partials/layout', array_merge($data, ['content' => view('pages/performance', $data)]));
    }
}