<?php

namespace App\Controllers;

class Api extends BaseController
{
    private function json(array $data, int $code = 200): \CodeIgniter\HTTP\ResponseInterface
    {
        return $this->response->setStatusCode($code)->setJSON($data);
    }

    private function body(): array
    {
        $b = $this->request->getJSON(true);
        return is_array($b) ? $b : $this->request->getPost();
    }

    public function overview(): \CodeIgniter\HTTP\ResponseInterface
    {
        $db = $this->db;
        $recent = $db->table('quiz_sessions')
            ->select('quiz_sessions.*, subjects.name AS subject_name')
            ->join('subjects', 'subjects.id = quiz_sessions.subject_id', 'left')
            ->orderBy('quiz_sessions.id', 'DESC')->limit(30)->get()->getResultArray();
        $totalXp = (int) $db->table('xp_logs')->selectSum('amount', 't')->get()->getRowArray()['t'];
        $totalMin = (int) $db->table('study_sessions')->selectSum('minutes', 't')->get()->getRowArray()['t'];
        $subjects = $db->table('subjects')->orderBy('priority', 'ASC')->get()->getResultArray();
        return $this->json([
            'recent' => $recent,
            'xp' => $totalXp,
            'streak' => $this->streak(),
            'minutes_total' => $totalMin,
            'subjects' => $subjects,
        ]);
    }

    public function subjects(): \CodeIgniter\HTTP\ResponseInterface
    {
        return $this->json($this->db->table('subjects')->orderBy('priority', 'ASC')->get()->getResultArray());
    }

    public function phases(): \CodeIgniter\HTTP\ResponseInterface
    {
        return $this->json($this->db->table('phases')->orderBy('start_date', 'ASC')->get()->getResultArray());
    }

    public function topics(): \CodeIgniter\HTTP\ResponseInterface
    {
        $rows = $this->db->table('topics')
            ->select('topics.*, subjects.name AS subject_name, subjects.color, subjects.icon')
            ->join('subjects', 'subjects.id = topics.subject_id', 'left')
            ->orderBy('topics.id', 'ASC')->get()->getResultArray();
        return $this->json($rows);
    }

    public function mcqs(): \CodeIgniter\HTTP\ResponseInterface
    {
        $subject = (int) $this->request->getGet('subject');
        $phase = (int) $this->request->getGet('phase');
        $count = (int) $this->request->getGet('count');
        $builder = $this->db->table('mcqs')
            ->select('mcqs.*, subjects.name AS subject_name')
            ->join('subjects', 'subjects.id = mcqs.subject_id', 'left')
            ->where('mcqs.status', 'active');
        if ($subject) {
            $builder->where('mcqs.subject_id', $subject);
        }
        if ($phase) {
            $builder->where('mcqs.phase_id', $phase);
        }
        $rows = $builder->orderBy('RAND()', '', false)->limit(min(500, max(1, $count > 0 ? $count : 200)))->get()->getResultArray();
        return $this->json($rows);
    }

    public function saveMcq(string $id = ''): \CodeIgniter\HTTP\ResponseInterface
    {
        $b = $this->body();
        $required = ['subject_id', 'question', 'opt_a', 'opt_b', 'opt_c', 'opt_d', 'answer'];
        foreach ($required as $k) {
            if (!isset($b[$k]) || trim((string) $b[$k]) === '') {
                return $this->json(['message' => "Missing field: $k"], 422);
            }
        }
        $subjectId = (int) ($b['subject_id'] ?? 0);
        if ($subjectId <= 0 || !$this->db->table('subjects')->where('id', $subjectId)->countAllResults()) {
            return $this->json(['message' => 'Unknown subject.'], 422);
        }
        $answer = strtoupper(substr(trim((string) ($b['answer'] ?? '')), 0, 1));
        if (!in_array($answer, ['A', 'B', 'C', 'D'], true)) {
            return $this->json(['message' => 'Answer must be one of A, B, C, D.'], 422);
        }
        $difficulty = (string) ($b['difficulty'] ?? 'M');
        $data = [
            'subject_id' => $subjectId,
            'question' => trim((string) $b['question']),
            'opt_a' => (string) $b['opt_a'],
            'opt_b' => (string) $b['opt_b'],
            'opt_c' => (string) $b['opt_c'],
            'opt_d' => (string) $b['opt_d'],
            'answer' => $answer,
            'explanation' => (string) ($b['explanation'] ?? ''),
            'difficulty' => in_array($difficulty, ['E', 'M', 'H'], true) ? $difficulty : 'M',
        ];
        if ($id !== '') {
            $row = $this->db->table('mcqs')->where('id', (int) $id)->get()->getRowArray();
            if (!$row) {
                return $this->json(['message' => 'MCQ not found.'], 404);
            }
            $this->db->table('mcqs')->where('id', (int) $id)->update($data);
            return $this->json(['id' => (int) $id, 'message' => 'Updated']);
        }
        $this->db->table('mcqs')->insert($data);
        return $this->json(['id' => (int) $this->db->insertID(), 'message' => 'Created']);
    }

    public function deleteMcq(string $id): \CodeIgniter\HTTP\ResponseInterface
    {
        $this->db->table('mcqs')->where('id', (int) $id)->delete();
        if ($this->db->affectedRows() === 0) {
            return $this->json(['message' => 'MCQ not found.'], 404);
        }
        return $this->json(['message' => 'Deleted']);
    }

    public function quizResult(): \CodeIgniter\HTTP\ResponseInterface
    {
        $b = $this->body();
        $type = (string) ($b['type'] ?? 'quick');
        if (!in_array($type, ['quick', 'timed', 'random10', 'random25', 'fullmock', 'weak', 'challenge'], true)) {
            $type = 'quick';
        }
        $subjectId = (int) ($b['subject_id'] ?? 0);
        $total = (int) ($b['total'] ?? 0);
        $correct = (int) ($b['correct'] ?? 0);
        $attempted = (int) ($b['attempted'] ?? 0);
        $seconds = (int) ($b['seconds'] ?? 0);
        if ($total < 0 || $correct < 0 || $attempted < 0 || $seconds < 0) {
            return $this->json(['message' => 'Negative values are not allowed.'], 422);
        }
        if ($correct > $total || $attempted > $total) {
            return $this->json(['message' => 'correct/attempted cannot exceed total.'], 422);
        }
        if ($subjectId !== 0 && !$this->db->table('subjects')->where('id', $subjectId)->countAllResults()) {
            return $this->json(['message' => 'Unknown subject.'], 422);
        }

        $xp = $correct * 10;
        $accuracy = $total > 0 ? $correct / $total : 0;
        $xp += $accuracy >= 0.8 ? 30 : ($accuracy >= 0.5 ? 15 : 0);
        if ($type === 'challenge') {
            $xp += 100;
        }

        $this->db->transStart();
        $this->db->table('quiz_sessions')->insert([
            'session_type' => $type,
            'subject_id' => $subjectId ?: null,
            'total' => $total,
            'correct' => $correct,
            'attempted' => $attempted,
            'seconds' => $seconds,
            'xp_earned' => $xp,
        ]);
        $this->db->table('xp_logs')->insert([
            'amount' => $xp,
            'reason' => "$type quiz: $correct/$total",
        ]);
        $this->db->transComplete();
        if ($this->db->transStatus() === false) {
            return $this->json(['message' => 'Could not save quiz result.'], 500);
        }

        return $this->json([
            'message' => 'Saved',
            'xp' => $xp,
            'total_xp' => (int) $this->db->table('xp_logs')->selectSum('amount', 't')->get()->getRowArray()['t'],
        ]);
    }

    public function studyLog(): \CodeIgniter\HTTP\ResponseInterface
    {
        $b = $this->body();
        $minutes = (int) ($b['minutes'] ?? 0);
        $subjectId = (int) ($b['subject_id'] ?? 0);
        if ($minutes <= 0) {
            return $this->json(['message' => 'minutes required'], 422);
        }
        if ($minutes > 1440) {
            return $this->json(['message' => 'minutes cannot exceed 1440 (24h).'], 422);
        }
        if ($subjectId !== 0 && !$this->db->table('subjects')->where('id', $subjectId)->countAllResults()) {
            return $this->json(['message' => 'Unknown subject.'], 422);
        }
        $this->db->table('study_sessions')->insert([
            'session_date' => date('Y-m-d'),
            'minutes' => $minutes,
            'subject_id' => $subjectId ?: null,
            'source' => 'app',
        ]);
        $xp = min($minutes, 120); // 1 XP per minute, capped
        $this->db->table('xp_logs')->insert(['amount' => $xp, 'reason' => "studied $minutes minutes"]);
        return $this->json(['message' => 'Logged', 'xp' => $xp]);
    }

    public function heatmap(): \CodeIgniter\HTTP\ResponseInterface
    {
        $start = date('Y-m-d', strtotime('-119 days'));
        $rows = $this->db->table('study_sessions')
            ->select('session_date, SUM(minutes) AS minutes')
            ->where('session_date >=', $start)
            ->groupBy('session_date')
            ->orderBy('session_date', 'ASC')->get()->getResultArray();
        foreach ($rows as &$r) {
            $r['minutes'] = (int) $r['minutes'];
        }
        return $this->json($rows);
    }

    public function tagDocument(string $id): \CodeIgniter\HTTP\ResponseInterface
    {
        $b = $this->body();
        $sid = (int) ($b['subject_id'] ?? 0);
        if (!$this->db->table('documents')->where('id', (int) $id)->get()->getRowArray()) {
            return $this->json(['message' => 'Document not found.'], 404);
        }
        if ($sid !== 0 && !$this->db->table('subjects')->where('id', $sid)->countAllResults()) {
            return $this->json(['message' => 'Unknown subject.'], 422);
        }
        $this->db->table('documents')->where('id', (int) $id)->update([
            'subject_id' => $sid ?: null,
        ]);
        return $this->json(['message' => 'Tagged']);
    }

    public function renameDocument(): \CodeIgniter\HTTP\ResponseInterface
    {
        $b = $this->body();
        $id = (int) ($b['id'] ?? 0);
        $title = trim((string) ($b['title'] ?? ''));
        if ($id <= 0) {
            return $this->json(['message' => 'Valid id required.'], 422);
        }
        if ($title === '') {
            return $this->json(['message' => 'Title required.'], 422);
        }
        if (!$this->db->table('documents')->where('id', $id)->get()->getRowArray()) {
            return $this->json(['message' => 'Document not found.'], 404);
        }
        $this->db->table('documents')->where('id', $id)->update(['title' => $title]);
        return $this->json(['message' => 'Renamed']);
    }

    public function calendar(): \CodeIgniter\HTTP\ResponseInterface
    {
        $rows = $this->db->table('daily_plan')->orderBy('task_date', 'ASC')->get()->getResultArray();
        return $this->json($rows);
    }

    public function setDayStatus(string $date): \CodeIgniter\HTTP\ResponseInterface
    {
        $dt = \DateTime::createFromFormat('Y-m-d', $date);
        if (!$dt || $dt->format('Y-m-d') !== $date) {
            return $this->json(['message' => 'bad date, use YYYY-MM-DD (display is DD-MM-YYYY)'], 400);
        }
        $b = $this->body();
        $status = (string) ($b['status'] ?? 'pending');
        if (!in_array($status, ['pending', 'done', 'skipped'], true)) {
            return $this->json(['message' => 'bad status'], 422);
        }
        $existing = $this->db->table('daily_plan')->where('task_date', $date)->get()->getRowArray();
        if (!$existing) {
            return $this->json(['message' => 'day not found'], 404);
        }
        if ($status === 'done') {
            // Atomic pending->done transition: concurrent requests can't double-award XP.
            $this->db->table('daily_plan')->where('task_date', $date)->where('status !=', 'done')->update(['status' => 'done']);
            if ($this->db->affectedRows() > 0) {
                $this->db->table('xp_logs')->insert(['amount' => 20, 'reason' => "daily mission $date"]);
            }
        } else {
            $this->db->table('daily_plan')->where('task_date', $date)->update(['status' => $status]);
        }

        // 2-Way Sync with Notion
        if ($existing && !empty($existing['notion_id'])) {
            try {
                $notion = new \App\Services\NotionService();
                $notion->pushDayStatus($existing['notion_id'], $status);
            } catch (\Throwable $e) {
                // Ignore push failures
            }
        }

        return $this->json(['message' => 'Updated', 'status' => $status]);
    }

    public function setTopicStatus(string $id): \CodeIgniter\HTTP\ResponseInterface
    {
        $b = $this->body();
        $status = (string) ($b['status'] ?? 'done');
        if (!in_array($status, ['done', 'active', 'locked'], true)) {
            return $this->json(['message' => 'bad status'], 422);
        }
        if (!$this->db->table('topics')->where('id', (int) $id)->get()->getRowArray()) {
            return $this->json(['message' => 'Topic not found.'], 404);
        }
        $this->db->table('topics')->where('id', (int) $id)->update(['status' => $status]);
        return $this->json(['message' => 'Updated']);
    }

    public function checklists(): \CodeIgniter\HTTP\ResponseInterface
    {
        $rows = $this->db->table('checklists')->orderBy('group_name', 'ASC')->get()->getResultArray();
        $grouped = [];
        foreach ($rows as $r) {
            $grouped[(string) ($r['group_name'] ?? 'general')][] = $r;
        }
        return $this->json(['count' => count($rows), 'groups' => $grouped]);
    }

    public function toggleChecklist(string $id): \CodeIgniter\HTTP\ResponseInterface
    {
        $row = $this->db->table('checklists')->where('id', (int) $id)->get()->getRowArray();
        if (!$row) {
            return $this->json(['message' => 'not found'], 404);
        }
        // Atomic flip — concurrent toggles can't lose each other.
        $this->db->query('UPDATE checklists SET checked = 1 - checked WHERE id = ?', [(int) $id]);
        $row = $this->db->table('checklists')->where('id', (int) $id)->get()->getRowArray();
        $checked = (int) ($row['checked'] ?? 0);

        if (!empty($row['notion_block_id'])) {
            try {
                $notion = new \App\Services\NotionService();
                $notion->pushToDo($row['notion_block_id'], (bool) $checked);
            } catch (\Throwable $e) {
                // Push failure shouldn't break the local toggle.
            }
        }
        return $this->json(['message' => 'Toggled', 'checked' => $checked]);
    }

    public function notionSync(): \CodeIgniter\HTTP\ResponseInterface
    {
        try {
            $notion = new \App\Services\NotionService();
            $report = $notion->syncAll();
            return $this->json(['message' => 'Sync complete', 'report' => $report]);
        } catch (\Throwable $e) {
            return $this->json(['message' => $e->getMessage()], 422);
        }
    }

    public function notionStatus(): \CodeIgniter\HTTP\ResponseInterface
    {
        $token = (string) ($this->db->table('settings')->where('skey', 'notion_token')->get()->getRowArray()['svalue'] ?? '');
        $dbId = (string) ($this->db->table('settings')->where('skey', 'notion_database_id')->get()->getRowArray()['svalue'] ?? '');
        $totalDays = (int) $this->db->table('daily_plan')->countAllResults();
        $completedDays = (int) $this->db->table('daily_plan')->where('status', 'done')->countAllResults();
        return $this->json([
            'configured' => $token !== '',
            'database_id' => $dbId,
            'total_tasks' => $totalDays,
            'completed_tasks' => $completedDays,
            'synced_at' => $this->db->table('settings')->where('skey', 'notion_synced_at')->get()->getRowArray()['svalue'] ?? null,
        ]);
    }

    public function targets(): \CodeIgniter\HTTP\ResponseInterface
    {
        return $this->json($this->db->table('mock_targets')->orderBy('sort_order', 'ASC')->get()->getResultArray());
    }

    public function xp(): \CodeIgniter\HTTP\ResponseInterface
    {
        $total = (int) $this->db->table('xp_logs')->selectSum('amount', 't')->get()->getRowArray()['t'];
        $logs = $this->db->table('xp_logs')->orderBy('id', 'DESC')->limit(20)->get()->getResultArray();
        return $this->json(['total' => $total, 'logs' => $logs]);
    }

    public function saveSettings(): \CodeIgniter\HTTP\ResponseInterface
    {
        $b = $this->body();
        // Two forms share this endpoint (Notion + AI) — only touch keys actually sent.
        $raw = [
            'notion_token' => isset($b['notion_token']) ? trim((string) $b['notion_token']) : null,
            'notion_plan_page' => isset($b['notion_plan_page']) ? trim((string) $b['notion_plan_page']) : null,
            'notion_calendar_page' => isset($b['notion_calendar_page']) ? trim((string) $b['notion_calendar_page']) : null,
            'exam_date' => isset($b['exam_date']) ? (string) $b['exam_date'] : null,
            'target_score' => isset($b['target_score']) ? (string) (int) $b['target_score'] : null,
            'weekly_hour_goal' => isset($b['weekly_hour_goal']) ? (string) (int) $b['weekly_hour_goal'] : null,
            'groq_api_key' => isset($b['groq_api_key']) ? trim((string) $b['groq_api_key']) : null,
            'groq_model' => isset($b['groq_model']) ? trim((string) $b['groq_model']) : null,
        ];
        $updated = [];
        foreach ($raw as $k => $v) {
            if ($v === null) {
                continue;
            }
            $exists = $this->db->table('settings')->where('skey', $k)->get()->getRowArray();
            if ($exists) {
                $this->db->table('settings')->where('skey', $k)->update(['svalue' => $v]);
            } else {
                $this->db->table('settings')->insert(['skey' => $k, 'svalue' => $v]);
            }
            $updated[] = $k;
        }
        return $this->json(['message' => 'Saved', 'updated' => $updated]);
    }

    public function todayTask(): \CodeIgniter\HTTP\ResponseInterface
    {
        $row = $this->db->table('daily_plan')->where('task_date', date('Y-m-d'))->get()->getRowArray();
        return $this->json($row ?: []);
    }

    public function dailyPlan(): \CodeIgniter\HTTP\ResponseInterface
    {
        return $this->calendar();
    }

    /** Per-subject and overall study level from quiz history. */
    private function levelSummary(): array
    {
        $subjects = $this->db->table('subjects')->orderBy('priority', 'ASC')->get()->getResultArray();
        $rows = $this->db->table('quiz_sessions')
            ->select('subject_id, SUM(total) AS total, SUM(correct) AS correct')
            ->where('total >', 0)->groupBy('subject_id')->get()->getResultArray();
        $agg = [];
        foreach ($rows as $r) {
            $agg[(int) $r['subject_id']] = $r;
        }
        $out = [];
        foreach ($subjects as $s) {
            $total = (int) ($agg[$s['id']]['total'] ?? 0);
            $correct = (int) ($agg[$s['id']]['correct'] ?? 0);
            $acc = $total ? $correct / $total : null;
            $level = $acc === null ? 'M' : ($acc >= 0.8 ? 'H' : ($acc >= 0.5 ? 'M' : 'E'));
            $out[] = [
                'id' => (int) $s['id'],
                'name' => $s['name'],
                'attempts' => $total ?: 0,
                'accuracy' => $acc !== null ? round($acc * 100) : null,
                'level' => $level,
                'label' => $level === 'H' ? 'Strong' : ($level === 'M' ? 'Medium' : 'Weak'),
            ];
        }
        $weak = array_values(array_filter($out, fn ($s) => $s['level'] === 'E' || $s['attempts'] === 0));
        return ['subjects' => $out, 'weak' => $weak];
    }

    public function studyLevel(): \CodeIgniter\HTTP\ResponseInterface
    {
        return $this->json($this->levelSummary());
    }

    public function aiStatus(): \CodeIgniter\HTTP\ResponseInterface
    {
        $groq = new \App\Services\GroqService();
        return $this->json(['configured' => $groq->hasKey(), 'model' => $groq->model()]);
    }

    /** Adaptive MCQ generation based on study level. */
    public function aiGenerate(): \CodeIgniter\HTTP\ResponseInterface
    {
        $b = $this->body();
        $subjectId = (int) ($b['subject_id'] ?? 0);
        $phaseId = (int) ($b['phase_id'] ?? 0);
        $count = min(10, max(1, (int) ($b['count'] ?? 5)));
        $difficulty = (string) ($b['difficulty'] ?? 'auto');
        if (!in_array($difficulty, ['auto', 'E', 'M', 'H'], true)) {
            $difficulty = 'auto';
        }
        try {
            $groq = new \App\Services\GroqService();
            if (!$groq->hasKey()) {
                return $this->json(['message' => 'Add your Groq API key in Settings first.'], 422);
            }
            $level = $subjectId ? $groq->subjectLevel($subjectId) : 'M';
            $result = $groq->generateMcqs($subjectId, $count, $difficulty, $phaseId ?: null);
            $ids = $result['ids'];
            if (!$ids) {
                return $this->json(['message' => 'Groq returned no usable questions.'], 422);
            }
            $mcqs = $this->db->table('mcqs')
                ->select('mcqs.*, subjects.name AS subject_name')
                ->join('subjects', 'subjects.id = mcqs.subject_id', 'left')
                ->whereIn('mcqs.id', $ids)->get()->getResultArray();
            $this->db->table('xp_logs')->insert(['amount' => 20, 'reason' => 'AI generated ' . count($ids) . ' MCQs']);
            $scope = ($result['phase'] ?? null) ? ' for ' . $result['phase'] : '';
            return $this->json([
                'message' => 'Generated ' . count($ids) . ' MCQs' . $scope . ' (' . $result['difficulty'] . ' · ' . $result['model'] . ')',
                'mcqs' => $mcqs,
                'subject_level' => $level,
                'phase' => $result['phase'] ?? null,
                'xp' => 20,
            ]);
        } catch (\Throwable $e) {
            return $this->json(['message' => $e->getMessage()], 422);
        }
    }

    /** Latest MCQs + study-level info, adaptively tailed to the weakest subject.
     * Read-only: question generation stays on the explicit POST ai/generate. */
    public function aiLatest(): \CodeIgniter\HTTP\ResponseInterface
    {
        try {
            $levelData = $this->levelSummary();
            $weak = $levelData['weak'];
            $target = $weak[0] ?? null;
            $recent = $this->db->table('mcqs')
                ->select('mcqs.*, subjects.name AS subject_name')
                ->join('subjects', 'subjects.id = mcqs.subject_id', 'left')
                ->where('mcqs.status', 'active')->orderBy('mcqs.id', 'DESC')->limit(6)->get()->getResultArray();
            return $this->json([
                'study_level' => $levelData,
                'focus_subject' => $target,
                'generated' => ['mcqs' => [], 'difficulty' => '', 'model' => ''],
                'recent' => $recent,
                'now' => date('Y-m-d H:i'),
            ]);
        } catch (\Throwable $e) {
            return $this->json(['message' => $e->getMessage()], 422);
        }
    }

    /** Explain highlighted text via Groq (used by the Chrome extension). */
    public function aiExplain(): \CodeIgniter\HTTP\ResponseInterface
    {
        $b = $this->body();
        $text = trim((string) ($b['text'] ?? ''));
        $subject = trim((string) ($b['subject'] ?? ''));
        if ($text === '') {
            return $this->json(['message' => 'text required'], 422);
        }
        try {
            $groq = new \App\Services\GroqService();
            if (!$groq->hasKey()) {
                return $this->json(['message' => 'Add your Groq API key in Settings first.'], 422);
            }
            return $this->json(['explanation' => $groq->explain($text, $subject)]);
        } catch (\Throwable $e) {
            return $this->json(['message' => $e->getMessage()], 502);
        }
    }
}