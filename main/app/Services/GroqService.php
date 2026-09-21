<?php

namespace App\Services;

class GroqService
{
    private string $api = 'https://api.groq.com/openai/v1/chat/completions';
    private string $key = '';
    private string $model;
    private \CodeIgniter\Database\BaseConnection $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $row = $this->db->table('settings')->where('skey', 'groq_api_key')->get()->getRowArray();
        if ($row) {
            $this->key = (string) $row['svalue'];
        }
        $m = $this->db->table('settings')->where('skey', 'groq_model')->get()->getRowArray();
        $this->model = $m && $m['svalue'] !== '' ? (string) $m['svalue'] : 'openai/gpt-oss-120b';
    }

    public function hasKey(): bool
    {
        return $this->key !== '';
    }

    public function model(): string
    {
        return $this->model;
    }

    /**
     * Send a chat completion to Groq.
     * @param array<int, array{role: string, content: string}> $messages
     */
    public function chat(array $messages, float $temperature = 0.7, int $maxTokens = 1024, bool $json = false): string
    {
        if (!$this->hasKey()) {
            throw new \RuntimeException('Groq API key not configured. Add it in Settings → AI.');
        }
        $payload = [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => $temperature,
            'max_tokens' => $maxTokens,
        ];
        if ($json) {
            $payload['response_format'] = ['type' => 'json_object'];
        }
        return $this->request($payload);
    }

    private function request(array $payload): string
    {
        $body = json_encode($payload);
        $headers = [
            'Authorization: Bearer ' . $this->key,
            'Content-Type: application/json',
        ];

        if (extension_loaded('curl')) {
            $ch = curl_init($this->api);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_TIMEOUT => 90,
            ]);
            $res = curl_exec($ch);
            $code = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            curl_close($ch);
        } else {
            $context = stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => implode("\r\n", $headers) . "\r\n",
                    'content' => $body,
                    'ignore_errors' => true,
                    'timeout' => 90,
                ],
                'ssl' => ['verify_peer' => true, 'verify_peer_name' => true],
            ]);
            $res = @file_get_contents($this->api, false, $context);
            $code = 200;
            foreach ($http_response_header ?? [] as $h) {
                if (preg_match('#^HTTP/\S+\s+(\d+)#', $h, $m)) {
                    $code = (int) $m[1];
                }
            }
        }

        $json = json_decode((string) $res, true);
        if ($code >= 400 || !$json) {
            $err = $json['error']['message'] ?? (string) $res;
            throw new \RuntimeException("Groq HTTP $code: $err");
        }
        $content = $json['choices'][0]['message']['content'] ?? '';
        if ($content === '') {
            throw new \RuntimeException('Groq returned empty content');
        }
        return $content;
    }

    /**
     * Defensively parse a model response into an array of MCQ items.
     */
    private function extractList(string $content): array
    {
        $cleaned = trim($content);
        if (preg_match('/```(?:json)?\s*(.*?)```/s', $content, $m)) {
            $cleaned = trim($m[1]);
        }
        $parsed = json_decode($cleaned, true);
        if (!is_array($parsed)) {
            $s = strpos($content, '{');
            $e = strrpos($content, '}');
            if ($s !== false && $e !== false && $e > $s) {
                $parsed = json_decode(substr($content, $s, $e - $s + 1), true);
            }
        }
        if (!is_array($parsed)) {
            return [];
        }
        $list = $parsed['mcqs'] ?? $parsed;
        if (!is_array($list)) {
            return [];
        }
        // Collapse a single item dict into a one-element list
        if (isset($list['question'])) {
            $list = [$list];
        }
        // Keep only entries that look like MCQs and have a question
        $list = array_values(array_filter($list, static function ($it) {
            return is_array($it) && isset($it['question']) && trim((string) $it['question']) !== '';
        }));
        return $list;
    }

    /** Accuracy 0..1 for a subject from quiz history (all time, weighted recent). */
    public function subjectLevel(int $subjectId): string
    {
        $rows = $this->db->table('quiz_sessions')
            ->where('subject_id', $subjectId)->where('total >', 0)
            ->orderBy('id', 'DESC')->limit(20)->get()->getResultArray();
        if (count($rows) === 0) {
            return 'M'; // no history → treat as medium
        }
        $w = 0;
        $acc = 0;
        foreach ($rows as $i => $r) {
            $weight = 1 / ($i + 1);
            $w += $weight;
            $acc += $weight * ($r['correct'] / $r['total']);
        }
        $avg = $acc / $w;
        if ($avg >= 0.8) {
            return 'H';
        }
        if ($avg >= 0.5) {
            return 'M';
        }
        return 'E';
    }

    /** Generate fresh GATE-style MCQs for a subject, optionally auto difficulty from study level. */
    public function generateMcqs(int $subjectId, int $count = 5, string $difficulty = 'auto', ?int $phaseId = null): array
    {
        $subj = $this->db->table('subjects')->where('id', $subjectId)->get()->getRowArray();
        if (!$subj) {
            throw new \RuntimeException('Unknown subject');
        }
        $phase = null;
        if ($phaseId) {
            $phase = $this->db->table('phases')->where('id', $phaseId)->get()->getRowArray();
            if (!$phase) {
                throw new \RuntimeException('Unknown phase');
            }
        }
        if ($difficulty === 'auto') {
            $difficulty = $this->subjectLevel($subjectId) === 'H' ? 'H' : 'M';
        }

        $topicBuilder = $this->db->table('topics')->where('subject_id', $subjectId);
        if ($phase) {
            $topicBuilder->where('phase_id', (int) $phase['id']);
        }
        $topics = [];
        foreach ($topicBuilder->limit(8)->get()->getResultArray() as $t) {
            $topics[] = $t['name'];
        }
        // Phase has no topics for this subject yet — fall back to subject topics.
        if ($phase && $topics === []) {
            foreach ($this->db->table('topics')->where('subject_id', $subjectId)->limit(8)->get()->getResultArray() as $t) {
                $topics[] = $t['name'];
            }
        }
        $topicList = $topics ? implode(', ', $topics) : 'core ' . $subj['name'] . ' concepts';

        $dmap = ['E' => 'easy (recall/definitions)', 'M' => 'medium (standard application)', 'H' => 'hard (tricky, past-paper difficulty)'];
        $system = 'You are a GATE Computer Science (2027) question paper setter. Set exactly ' . $count
            . ' single-best-answer MCQs about: ' . $subj['name'] . ' — topics: ' . $topicList
            . ($phase ? ' — study phase: ' . $phase['name'] . ' (keep questions within this phase\'s syllabus)' : '')
            . '. Difficulty: ' . ($dmap[$difficulty] ?? 'medium') . '. Questions must be standalone, exam-accurate, no markdown, no numbering. '
            . 'Reply with ONLY a JSON object: {"mcqs":[{ "question": "...", "options": ["A text","B text","C text","D text"], "answer": "B", "explanation": "..." }]}.';

        $content = $this->chat(
            [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => 'Generate the JSON now.'],
            ],
            0.7,
            2048,
            true
        );

        $list = $this->extractList($content);

        if (!$list || !is_array($list)) {
            // Retry without strict JSON mode — the model may accept plain instruction better.
            $list = $this->extractList($this->chat(
                [
                    ['role' => 'system', 'content' => $system],
                    ['role' => 'user', 'content' => 'Output JSON now, no markdown.'],
                ],
                0.8,
                2048,
                false
            ));
        }

        if (!$list || !is_array($list)) {
            throw new \RuntimeException('Groq returned no usable questions');
        }

        $inserted = [];
        foreach (array_slice($list, 0, $count) as $m) {
            $question = trim((string) ($m['question'] ?? ''));
            $opts = array_values(array_filter((array) ($m['options'] ?? [])));
            if ($question === '' || count($opts) < 2) {
                continue;
            }
            while (count($opts) < 4) {
                $opts[] = '—';
            }
            $answer = strtoupper(substr(trim((string) ($m['answer'] ?? 'A')), 0, 1));
            if ($answer === '1' || $answer === '0') {
                $answer = strtoupper(substr('ABCD', (int) $answer, 1));
            }
            if (!in_array($answer, ['A', 'B', 'C', 'D'], true)) {
                $answer = 'A';
            }
            $explanation = trim((string) ($m['explanation'] ?? ''));
            $this->db->table('mcqs')->insert([
                'subject_id' => $subjectId,
                'phase_id' => $phase ? (int) $phase['id'] : null,
                'question' => $question,
                'opt_a' => $opts[0],
                'opt_b' => $opts[1],
                'opt_c' => $opts[2],
                'opt_d' => $opts[3],
                'answer' => $answer,
                'explanation' => $explanation,
                'difficulty' => $difficulty,
                'source' => 'groq',
                'status' => 'active',
            ]);
            $inserted[] = (int) $this->db->insertID();
        }
        return ['ids' => $inserted, 'difficulty' => $difficulty, 'model' => $this->model(), 'phase' => $phase['name'] ?? null];
    }

    /** Explain arbitrary text (study notes, errors) in GATE context. */
    public function explain(string $text, string $subject = ''): string
    {
        $system = 'You are a GATE Computer Science tutor. Explain the given text concisely, '
            . 'fixing any errors, and connect it to how this concept is tested in GATE CS. '
            . 'Use clear short paragraphs, bullet points where helpful. ' . ($subject ? 'Relevant subject: ' . $subject . '.' : '');
        return $this->chat(
            [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => substr($text, 0, 4000)],
            ],
            0.4,
            700
        );
    }
}