<?php

namespace App\Services;

class NotionService
{
    private string $token = '';
    private string $api = 'https://api.notion.com/v1';
    private string $version = '2022-06-28';
    private string $databaseId = '7eaffc35-b21b-49d8-942d-a238d562359a';
    private \CodeIgniter\Database\BaseConnection $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $row = $this->db->table('settings')->where('skey', 'notion_token')->get()->getRowArray();
        if ($row && !empty($row['svalue'])) {
            $this->token = (string) $row['svalue'];
        }
        $dbRow = $this->db->table('settings')->where('skey', 'notion_database_id')->get()->getRowArray();
        if ($dbRow && !empty($dbRow['svalue'])) {
            $this->databaseId = (string) $dbRow['svalue'];
        }
    }

    public function hasToken(): bool
    {
        return $this->token !== '';
    }

    public function getDatabaseId(): string
    {
        return $this->databaseId;
    }

    private function request(string $method, string $url, array $body = []): array
    {
        $headers = [
            'Authorization: Bearer ' . $this->token,
            'Notion-Version: ' . $this->version,
            'Content-Type: application/json',
        ];

        if (extension_loaded('curl')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_TIMEOUT => 30,
            ]);
            if (in_array($method, ['POST', 'PATCH', 'PUT'], true) && $body) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
            }
            $res = curl_exec($ch);
            $code = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            curl_close($ch);
        } else {
            $context = stream_context_create([
                'http' => [
                    'method' => $method,
                    'header' => implode("\r\n", $headers) . "\r\n",
                    'content' => (in_array($method, ['POST', 'PATCH', 'PUT'], true) && $body) ? json_encode($body) : null,
                    'ignore_errors' => true,
                    'timeout' => 30,
                ],
                'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
            ]);
            $res = @file_get_contents($url, false, $context);
            $code = 200;
            foreach ($http_response_header ?? [] as $h) {
                if (preg_match('#^HTTP/\S+\s+(\d+)#', $h, $m)) {
                    $code = (int) $m[1];
                }
            }
        }

        $json = json_decode((string) $res, true) ?: [];
        if ($code >= 400) {
            $msg = $json['message'] ?? "Notion HTTP $code";
            throw new \RuntimeException($msg, $code);
        }
        return $json;
    }

    public function getPageBlocks(string $pageId): array
    {
        $blocks = [];
        $cursor = null;
        do {
            $url = "$this->api/blocks/$pageId/children?page_size=100" . ($cursor ? "&start_cursor=" . urlencode($cursor) : '');
            $data = $this->request('GET', $url);
            $blocks = array_merge($blocks, $data['results'] ?? []);
            $cursor = $data['next_cursor'] ?? null;
        } while ($cursor);
        return $blocks;
    }

    public function queryDatabase(string $dbId): array
    {
        $results = [];
        $cursor = null;
        do {
            $body = ['page_size' => 100];
            if ($cursor) {
                $body['start_cursor'] = $cursor;
            }
            $res = $this->request('POST', "$this->api/databases/$dbId/query", $body);
            $results = array_merge($results, $res['results'] ?? []);
            $cursor = !empty($res['has_more']) ? ($res['next_cursor'] ?? null) : null;
        } while ($cursor);
        return $results;
    }

    public function syncAll(): array
    {
        if (!$this->hasToken()) {
            throw new \RuntimeException('Notion token is not configured.');
        }

        $out = ['daily' => 0, 'subjects' => 0, 'phases' => 0, 'checklists' => 0];

        // 1. Sync from Day-wise Database
        if (!empty($this->databaseId)) {
            $out['daily'] = $this->syncDatabaseTasks($this->databaseId);
        }

        // 2. Sync Todo database if exists
        try {
            $out['checklists'] = $this->syncTodoDatabase();
        } catch (\Throwable $e) {
            // non-fatal
        }

        // 3. Sync Legacy page blocks if present
        $plan = $this->setting('notion_plan_page', '');
        if ($plan !== '') {
            try {
                $out['subjects'] = $this->syncSubjectsPriority($plan);
                $out['phases'] = $this->syncPhasesTopics($plan);
            } catch (\Throwable $e) {
                // ignore
            }
        }

        $this->db->table('settings')->upsert([
            ['skey' => 'notion_synced_at', 'svalue' => date('Y-m-d H:i:s')],
        ]);

        return $out;
    }

    public function syncDatabaseTasks(string $dbId): int
    {
        $rows = $this->queryDatabase($dbId);
        $count = 0;

        foreach ($rows as $row) {
            $props = $row['properties'] ?? [];
            $taskName = '';
            if (isset($props['Task']['title'])) {
                $taskName = implode('', array_column($props['Task']['title'], 'plain_text'));
            } elseif (isset($props['Name']['title'])) {
                $taskName = implode('', array_column($props['Name']['title'], 'plain_text'));
            }
            if ($taskName === '') continue;

            $date = $props['Date']['date']['start'] ?? null;
            if (!$date) continue;

            $day = '';
            if (isset($props['Day']['rich_text'])) {
                $day = implode('', array_column($props['Day']['rich_text'], 'plain_text'));
            }
            if ($day === '') {
                $day = date('l', strtotime($date));
            }

            $note = '';
            if (isset($props['Note']['rich_text'])) {
                $note = implode('', array_column($props['Note']['rich_text'], 'plain_text'));
            }

            $notionStatus = $props['Status']['status']['name'] ?? 'Not started';
            $status = 'pending';
            if ($notionStatus === 'Done') $status = 'done';
            elseif ($notionStatus === 'In progress') $status = 'active';

            $pyqs = 0;
            if (preg_match('/\+\s*(\d+)\s*(?:timed\s*)?(?:PYQs|questions)/i', $taskName, $m)) {
                $pyqs = (int) $m[1];
            }

            $subject = $this->detectSubject($taskName);
            $url = $row['url'] ?? null;
            $notionId = $row['id'] ?? null;

            $existing = $this->db->table('daily_plan')->where('task_date', $date)->get()->getRowArray();
            if ($existing) {
                $this->db->table('daily_plan')->where('id', $existing['id'])->update([
                    'weekday' => $day,
                    'subject' => $subject,
                    'task' => $taskName,
                    'pyqs' => $pyqs,
                    'status' => $status,
                    'note' => $note ?: ($existing['note'] ?? null),
                    'notion_id' => $notionId,
                    'notion_url' => $url,
                ]);
            } else {
                $this->db->table('daily_plan')->insert([
                    'task_date' => $date,
                    'weekday' => $day,
                    'subject' => $subject,
                    'task' => $taskName,
                    'pyqs' => $pyqs,
                    'status' => $status,
                    'note' => $note,
                    'notion_id' => $notionId,
                    'notion_url' => $url,
                ]);
            }
            $count++;
        }

        return $count;
    }

    public function syncTodoDatabase(): int
    {
        $todoDbId = '3d5e1176-b072-80a8-9e67-d7879ffe0930';
        $rows = $this->queryDatabase($todoDbId);
        $count = 0;

        foreach ($rows as $row) {
            $props = $row['properties'] ?? [];
            $title = '';
            if (isset($props['Task name']['title'])) {
                $title = implode('', array_column($props['Task name']['title'], 'plain_text'));
            } elseif (isset($props['Name']['title'])) {
                $title = implode('', array_column($props['Name']['title'], 'plain_text'));
            }
            if ($title === '') continue;

            $status = $props['Status']['status']['name'] ?? 'Not started';
            $checked = ($status === 'Done') ? 1 : 0;
            $notionBlockId = $row['id'] ?? '';

            $exists = $this->db->table('checklists')->where('notion_block_id', $notionBlockId)->get()->getRowArray();
            if ($exists) {
                $this->db->table('checklists')->where('id', $exists['id'])->update([
                    'title' => $title,
                    'checked' => $checked,
                ]);
            } else {
                $this->db->table('checklists')->insert([
                    'group_name' => 'Notion Tasks',
                    'title' => $title,
                    'checked' => $checked,
                    'notion_block_id' => $notionBlockId,
                ]);
                $count++;
            }
        }
        return $count;
    }

    public function pushDayStatus(string $pageId, string $status): bool
    {
        if (!$this->hasToken() || empty($pageId)) {
            return false;
        }

        $notionStatus = 'Not started';
        if ($status === 'done') {
            $notionStatus = 'Done';
        } elseif ($status === 'active' || $status === 'in_progress') {
            $notionStatus = 'In progress';
        }

        try {
            $this->request('PATCH', "$this->api/pages/$pageId", [
                'properties' => [
                    'Status' => [
                        'status' => [
                            'name' => $notionStatus,
                        ],
                    ],
                ],
            ]);
            return true;
        } catch (\Throwable $e) {
            log_message('error', 'Push day status to Notion failed: ' . $e->getMessage());
            return false;
        }
    }

    public function detectSubject(string $task): string
    {
        if (preg_match('/\b(DM|Propositions|truth tables|Implication|equivalence|Sets|Relations|Functions|Counting|permutations|Pigeonhole|inclusion|Recurrence|Graph theory|Trees and properties|Connectivity|BFS\/DFS concepts|Eulerian|Hamiltonian|Planar graphs|graph coloring|Lattices|boolean algebra|Group theory|groups|subgroups|Lagrange)\b/i', $task)) return 'DM';
        if (preg_match('/\b(C basics|C pointers|C recursion|C programming|dynamic memory|structures|unions|arrays)\b/i', $task)) return 'C';
        if (preg_match('/\b(DS|Stacks|queues|deque|Linked lists|trees|BST|AVL|binary heap|heaps|Hashing|collisions)\b/i', $task)) return 'DS';
        if (preg_match('/\b(Algo|Algorithms|Asymptotic|divide and conquer|Greedy|Dynamic programming|DP|All-pairs shortest paths|Floyd|Warshall|Matrix chain|LCS|Huffman|MST|Kruskal|Prim|Dijkstra|Bellman|Ford|Graph algorithms|NP-complete|P vs NP)\b/i', $task)) return 'Algo';
        if (preg_match('/\b(DBMS|ER model|Relational algebra|tuple relational calculus|SQL queries|joins|aggregation|subqueries|Functional dependencies|canonical cover|Normalization|1NF|2NF|3NF|BCNF|4NF|Transactions|ACID|serializability|recoverability|Concurrency control|2PL|timestamp|Indexing|B-trees|B\+ trees)\b/i', $task)) return 'DBMS';
        if (preg_match('/\b(OS|Processes|threads|PCB|CPU scheduling|Process synchronization|Peterson|semaphores|monitors|Deadlocks|prevention|avoidance|Banker|Memory management|paging|segmentation|Virtual memory|page replacement|FIFO|LRU|File systems|disk scheduling|SSTF|SCAN|LOOK)\b/i', $task)) return 'OS';
        if (preg_match('/\b(COA|Data representation|IEEE 754|ALU|Booth|Machine instructions|ISA|addressing modes|CPU control|hardwired|microprogrammed|Memory hierarchy|cache mapping|direct|associative|set-associative|Cache write policies|replacement|Virtual memory translation|TLB|Pipelining|hazards|data hazards|forwarding|I\/O organization|interrupts|DMA)\b/i', $task)) return 'COA';
        if (preg_match('/\b(CN|OSI|TCP\/IP|Framing|error detection|CRC|checksum|Flow control|Stop-and-Wait|Go-Back-N|Selective Repeat|MAC|Ethernet|CSMA\/CD|IPv4|IPv6|subnetting|CIDR|supernetting|Routing algorithms|Distance Vector|Link State|OSPF|BGP|ARP|DHCP|ICMP|NAT|Transport layer|TCP|UDP|congestion control|Application layer|DNS|HTTP|FTP|SMTP|Network security|RSA|DES|AES|firewalls)\b/i', $task)) return 'CN';
        if (preg_match('/\b(TOC|DFA|NFA|regular languages|Pumping lemma for regular|Grammars|Chomsky|Context-free|PDA|Pumping lemma for CFL|Turing machines|decidability|Halting|Rice)\b/i', $task)) return 'TOC';
        if (preg_match('/\b(Compiler|Lexical analysis|lexemes|tokens|LL\(1\)|LR\(0\)|SLR\(1\)|LALR\(1\)|LR\(1\)|parsing|Syntax-directed|SDT|intermediate code|TAC|Code optimization|DAG|loop optimization|Data flow|live variable|available expressions)\b/i', $task)) return 'Compiler';
        if (preg_match('/\b(Linear algebra|matrices|determinants|rank|systems of equations|eigenvalues|eigenvectors|Calculus|limits|continuity|derivatives|maxima|minima|integrals|Probability|conditional probability|Bayes|random variables|distributions|uniform|exponential|Poisson|normal|mean|variance)\b/i', $task)) return 'Engg Math';
        if (preg_match('/\b(Aptitude|General Aptitude|verbal|quantitative|analytical|spatial|data interpretation)\b/i', $task)) return 'Aptitude';
        if (preg_match('/\b(mock|sectional test|Full length|analysis|revision)\b/i', $task)) return 'Revision / Mock';
        return 'Core';
    }

    private function setting(string $key, string $default = ''): string
    {
        $row = $this->db->table('settings')->where('skey', $key)->get()->getRowArray();
        return $row ? (string) $row['svalue'] : $default;
    }

    private function blockText(array $block): string
    {
        $type = $block['type'] ?? '';
        $txt = '';
        if (isset($block[$type]['rich_text']) && is_array($block[$type]['rich_text'])) {
            foreach ($block[$type]['rich_text'] as $r) {
                $txt .= $r['plain_text'] ?? '';
            }
        }
        return $txt;
    }

    private function upsertByName(string $table, string $name, array $extra = []): int
    {
        $row = $this->db->table($table)->where('name', $name)->get()->getRowArray();
        if ($row) {
            return (int) $row['id'];
        }
        $this->db->table($table)->insert(array_merge(['name' => $name], $extra));
        return (int) $this->db->insertID();
    }

    private function syncSubjectsPriority(string $pageId): int
    {
        $blocks = $this->getPageBlocks($pageId);
        $seen = false;
        $count = 0;
        $priority = 0;
        foreach ($blocks as $b) {
            $t = $b['type'];
            $text = $this->blockText($b);
            if ($t === 'heading_2' && str_starts_with($text, 'Subject priority')) {
                $seen = true;
                continue;
            }
            if ($seen && $t === 'numbered_list_item') {
                $priority++;
                $name = trim($text);
                if ($name === '') continue;
                $count += $this->upsertByName('subjects', $name, [
                    'priority' => $priority,
                    'notion_id' => $b['id'] ?? null,
                ]);
            }
            if ($seen && !in_array($t, ['numbered_list_item'], true)) {
                // stop at next heading
                if (str_starts_with($t, 'heading_')) break;
            }
        }
        return $count;
    }

    private function syncPhasesTopics(string $pageId): int
    {
        $blocks = $this->getPageBlocks($pageId);
        $phaseId = null;
        $subjectId = null;
        $sort = 0;
        $count = 0;
        foreach ($blocks as $b) {
            $t = $b['type'];
            $text = trim($this->blockText($b));

            if ($t === 'heading_2') {
                $phaseId = $this->upsertByPhase($text, $b['id'] ?? null);
                $subjectId = null;
                $sort = 0;
            } elseif (in_array($t, ['heading_3'], true)) {
                $subjectId = $this->findSubjectId($text);
                continue;
            } elseif (in_array($t, ['bulleted_list_item', 'paragraph'], true) && $phaseId) {
                if ($text === '' || str_contains($text, 'Goal:')) continue;
                $existing = $this->db->table('topics')
                    ->where('phase_id', $phaseId)->where('name', $text)->get()->getRowArray();
                if ($existing) {
                    continue;
                }
                $sort++;
                $this->db->table('topics')->insert([
                    'phase_id' => $phaseId,
                    'subject_id' => $subjectId,
                    'name' => $text,
                    'sort_order' => $sort,
                    'notion_id' => $b['id'] ?? null,
                ]);
                $this->map('topics', (int) $this->db->insertID(), 'block', $b['id'] ?? '');
                $count++;
            }
        }
        return $count;
    }

    private function findSubjectId(string $text): ?int
    {
        foreach ($this->db->table('subjects')->select('id,name,code')->get()->getResultArray() as $s) {
            if (mb_stripos($text, $s['name']) !== false || mb_stripos($s['name'], $text) !== false) {
                return (int) $s['id'];
            }
            if ($s['code'] && mb_stripos($text, $s['code']) !== false) {
                return (int) $s['id'];
            }
        }
        return null;
    }

    private function upsertByPhase(string $heading, ?string $notionId): int
    {
        [$name, $start, $end] = $this->parsePhase($heading);
        $row = $this->db->table('phases')->where('name', $name)->get()->getRowArray();
        if ($row) {
            return (int) $row['id'];
        }
        $this->db->table('phases')->insert([
            'name' => $name,
            'start_date' => $start,
            'end_date' => $end,
            'notion_id' => $notionId,
        ]);
        return (int) $this->db->insertID();
    }

    private function parsePhase(string $heading): array
    {
        $name = $heading;
        $start = null;
        $end = null;
        if (preg_match('/\b(\d{1,2})\s+(\w+)\b.*?\b(\d{1,2})\s+(\w+)\b/', $heading, $m)) {
            $start = $this->toDate((int) $m[1], $m[2]);
            $end = $this->toDate((int) $m[3], $m[4]);
        } elseif (preg_match('/(\d{1,2})\s+(\w+)(?:\s*→|\s*-)/', $heading, $m)) {
            $start = $this->toDate((int) $m[1], $m[2]);
        }
        return [$name, $start, $end];
    }

    private function toDate(int $day, string $month): string
    {
        $mon = date('m', strtotime($month . ' 1'));
        $g = (int) $mon;
        $year = ($g >= 9) ? 2026 : 2027; // Sep-Dec 2026, Jan-Feb 2027
        return sprintf('%04d-%02d-%02d', $year, $g, $day);
    }

    private function syncDailyPlan(string $pageId): int
    {
        $blocks = $this->getPageBlocks($pageId);
        $count = 0;
        $year = 2026;
        foreach ($blocks as $b) {
            $t = $b['type'];
            $text = trim($this->blockText($b));
            if ($t === 'bulleted_list_item' && preg_match('/^(Mon|Tue|Wed|Thu|Fri|Sat|Sun)\s+(\d{1,2})\s+(\w+):\s*(.+)$/i', $text, $m)) {
                $date = $this->toDate((int) $m[2], $m[3]);
                $task = $m[4];
                $pyqs = 0;
                if (preg_match('/\+\s*(\d+)\s*PYQs?/i', $task, $pm)) {
                    $pyqs = (int) $pm[1];
                    $task = trim(preg_replace('/\s*\+\s*\d+\s*PYQs?/i', '', $task));
                }
                $subj = '';
                if (preg_match('/^([^—:]+?)\s*—\s*(.*)$/', $task, $sm)) {
                    $subj = trim($sm[1]);
                    $task = trim($sm[2]);
                }
                $exists = $this->db->table('daily_plan')->where('task_date', $date)->get()->getRowArray();
                if ($exists) {
                    $this->db->table('daily_plan')->where('id', $exists['id'])->update([
                        'weekday' => $m[1],
                        'subject' => $subj,
                        'task' => $task,
                        'pyqs' => $pyqs,
                        'notion_id' => $b['id'] ?? null,
                    ]);
                } else {
                    $this->db->table('daily_plan')->insert([
                        'task_date' => $date,
                        'weekday' => $m[1],
                        'subject' => $subj,
                        'task' => $task,
                        'pyqs' => $pyqs,
                        'notion_id' => $b['id'] ?? null,
                    ]);
                }
                $count++;
            }
            if ($t === 'heading_2' && preg_match('/^\d{2}/', $text)) {
                // Week heading — nothing to parse, but we could store year hints here.
                $year = (preg_match('/2027/', $text)) ? 2027 : 2026;
            }
        }
        return $count;
    }

    private function syncChecklists(string $pageId): int
    {
        $blocks = $this->getPageBlocks($pageId);
        $count = 0;
        $group = 'General';
        foreach ($blocks as $b) {
            $t = $b['type'];
            $text = trim($this->blockText($b));
            if (str_starts_with($t, 'heading_')) {
                $group = $text === '' ? 'General' : $text;
                continue;
            }
            if ($t === 'to_do') {
                $blockId = $b['id'] ?? '';
                $checked = !empty($b['to_do']['checked']) ? 1 : 0;
                $exists = $this->db->table('checklists')->where('notion_block_id', $blockId)->get()->getRowArray();
                if ($exists) {
                    $this->db->table('checklists')->where('id', $exists['id'])->update([
                        'group_name' => $group,
                        'title' => $text,
                        'checked' => $checked,
                    ]);
                } else {
                    $this->db->table('checklists')->insert([
                        'group_name' => $group,
                        'title' => $text,
                        'checked' => $checked,
                        'notion_block_id' => $blockId,
                    ]);
                    $count++;
                }
            }
        }
        return $count;
    }

    private function syncTargets(string $pageId): array
    {
        $blocks = $this->getPageBlocks($pageId);
        $tableId = null;
        foreach ($blocks as $b) {
            if ($b['type'] === 'table') {
                $tableId = $b['id'] ?? null;
                break;
            }
        }
        if (!$tableId) {
            return [];
        }
        $rows = $this->getPageBlocks($tableId);
        $count = 0;
        foreach ($rows as $r) {
            $cells = [];
            foreach ($r['table_row']['cells'] ?? [] as $cell) {
                $cells[] = implode('', array_column($cell ?? [], 'plain_text'));
            }
            if (count($cells) < 2 || stripos($cells[0], 'Period') !== false) {
                continue;
            }
            [$min, $max] = $this->parseRange($cells[1]);
            $this->db->table('mock_targets')->upsert([[
                'period' => trim($cells[0]),
                'target_min' => $min,
                'target_max' => $max,
            ]]);
            $count++;
        }
        return ['rows' => $count];
    }

private function parseRange(string $s): array
    {
        if (preg_match('/(\d+)\s*[-\x{2010}\x{2011}\x{2012}\x{2013}\x{2014}\x{2212}]\s*(\d+)/u', $s, $m)) {
            return [(int) $m[1], (int) $m[2]];
        }
        if (preg_match('/(\d+)\s*\+/', $s, $m)) {
            return [(int) $m[1], (int) $m[1]];
        }
        if (preg_match('/\d+/', $s, $m)) {
            return [(int) $m[0], (int) $m[0]];
        }
        return [0, 0];
    }

    public function pushToDo(string $blockId, bool $checked): bool
    {
        if (!$this->hasToken()) {
            return false;
        }
        $this->request('PATCH', "$this->api/blocks/$blockId", [
            'to_do' => ['checked' => $checked],
        ]);
        return true;
    }

    private function map(string $table, int $localId, string $type, string $notionId): void
    {
        if ($notionId === '') return;
        $exists = $this->db->table('notion_map')->where('notion_id', $notionId)->get()->getRowArray();
        if ($exists) return;
        $this->db->table('notion_map')->insert([
            'local_table' => $table,
            'local_id' => $localId,
            'notion_type' => $type,
            'notion_id' => $notionId,
        ]);
    }
}