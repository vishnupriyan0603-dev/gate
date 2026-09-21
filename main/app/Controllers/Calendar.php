<?php

namespace App\Controllers;

class Calendar extends BaseController
{
    public function index(): string
    {
        $data = $this->pageData('calendar', 'Study Calendar', 'calendar');
        $data['count'] = (int) $this->db->table('daily_plan')->countAllResults();
        $data['done'] = (int) $this->db->table('daily_plan')->where('status', 'done')->countAllResults();
        return view('partials/layout', array_merge($data, ['content' => view('pages/calendar', $data)]));
    }
}