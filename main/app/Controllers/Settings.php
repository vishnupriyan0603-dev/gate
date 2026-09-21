<?php

namespace App\Controllers;

class Settings extends BaseController
{
    public function index(): string
    {
        $data = $this->pageData('settings', 'Settings', 'settings');
        $data['checklistCount'] = (int) $this->db->table('checklists')->countAllResults();
        $data['storedToken'] = $this->setting('notion_token');
        $data['planPage'] = $this->setting('notion_plan_page');
        $data['calPage'] = $this->setting('notion_calendar_page');
        $data['examDate'] = $this->setting('exam_date', '2027-02-05');
        $data['targetScore'] = $this->setting('target_score', '85');
        $data['weeklyHours'] = $this->setting('weekly_hour_goal', '18');
        $data['storedGroqKey'] = $this->setting('groq_api_key');
        $data['groqModel'] = $this->setting('groq_model', 'openai/gpt-oss-120b');
        $data['nSubjects'] = (int) $this->db->table('subjects')->countAllResults();
        $data['nPhases'] = (int) $this->db->table('phases')->countAllResults();
        $data['nTopics'] = (int) $this->db->table('topics')->countAllResults();
        $data['nMcqs'] = (int) $this->db->table('mcqs')->countAllResults();
        return view('partials/layout', array_merge($data, ['content' => view('pages/settings', $data)]));
    }
}