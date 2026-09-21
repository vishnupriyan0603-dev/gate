<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        $data = $this->pageData('dashboard', 'Dashboard', 'dashboard');

        // Today's mission from daily plan
        $today = date('Y-m-d');
        $row = $this->db->table('daily_plan')->where('task_date', $today)->get()->getRowArray();
        if (!$row) {
            $row = $this->db->table('daily_plan')->where('task_date >=', $today)
                ->orderBy('task_date', 'ASC')->get()->getRowArray();
        }
        $data['mission'] = $row;

        // Exam & Start Date countdown
        $startDate = $this->setting('start_date', '2026-09-21');
        $data['startDate'] = $startDate;
        $data['daysToStart'] = (int) ceil((strtotime($startDate) - strtotime('today')) / 86400);

        $exam = strtotime($this->setting('exam_date', '2027-02-05'));
        $data['daysToExam'] = max(0, (int) ceil(($exam - strtotime('today')) / 86400));
        $data['targetScore'] = (int) $this->setting('target_score', '85');

        // GATE Registration reminder
        $regDeadline = $this->setting('gate_reg_deadline', '2026-09-30');
        $data['regDeadline'] = $regDeadline;
        $data['daysToReg'] = (int) ceil((strtotime($regDeadline) - strtotime('today')) / 86400);

        // Subject progress rings (from course topics)
        $topics = $this->db->table('topics')->select('subject_id, status')->where('subject_id IS NOT NULL')->get()->getResultArray();
        $agg = [];
        foreach ($topics as $t) {
            $sid = (int) $t['subject_id'];
            if (!isset($agg[$sid])) {
                $agg[$sid] = ['total' => 0, 'done' => 0];
            }
            $agg[$sid]['total']++;
            if ($t['status'] === 'done') {
                $agg[$sid]['done']++;
            }
        }
        $subs = $this->db->table('subjects')->orderBy('priority', 'ASC')->get()->getResultArray();
        foreach ($subs as &$s) {
            $sid = (int) $s['id'];
            $s['total'] = $agg[$sid]['total'] ?? 0;
            $s['done'] = $agg[$sid]['done'] ?? 0;
            $s['pct'] = $s['total'] > 0 ? round(($s['done'] / $s['total']) * 100) : 0;
            $s['mcqs'] = (int) $this->db->table('mcqs')->where('subject_id', $sid)->countAllResults();
        }
        $data['subjects'] = $subs;

        // Recent quiz results
        $data['recent'] = $this->db->table('quiz_sessions')
            ->select('quiz_sessions.*, subjects.name AS subject_name')
            ->join('subjects', 'subjects.id = quiz_sessions.subject_id', 'left')
            ->orderBy('quiz_sessions.id', 'DESC')
            ->limit(6)->get()->getResultArray();

        // Study minutes today
        $data['minutesToday'] = (int) $this->db->table('study_sessions')
            ->selectSum('minutes', 't')->where('session_date', $today)->get()->getRowArray()['t'];

        $data['mcqCount'] = (int) $this->db->table('mcqs')->countAllResults();

        $data['targets'] = $this->db->table('mock_targets')->orderBy('sort_order', 'ASC')->get()->getResultArray();

        return view('partials/layout', array_merge($data, ['content' => view('pages/dashboard', $data)]));
    }
}