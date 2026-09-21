<?php

namespace App\Services;

/**
 * Service to manage local game data in Excel format (.xlsx) without external dependencies.
 * Uses PHP's native ZipArchive and OpenXML standard to read and write workbooks.
 * Thread-safe with exclusive file locking (flock) and atomic writes.
 */
class GameExcelService
{
    private string $filePath;
    private string $lockPath;
    private string $backupDir;

    private array $headers = [
        'ID',
        'Username',
        'Created_Date',
        'Created_Time',
        'Last_Played_Date',
        'Last_Played_Time',
        'Session_ID',
        'Current_Level',
        'Current_Score',
        'High_Score',
        'Snake_Length',
        'Games_Played',
        'Games_Completed',
        'Last_Save_Point',
        'Game_Status',
    ];

    public function __construct(?string $customPath = null)
    {
        $dir = WRITEPATH . 'game_data/';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $this->backupDir = $dir . 'backups/';
        if (!is_dir($this->backupDir)) {
            @mkdir($this->backupDir, 0755, true);
        }

        $this->filePath = $customPath ?? ($dir . 'game_players.xlsx');
        $this->lockPath = $dir . 'game_players.lock';

        $this->ensureWorkbookExists();
    }

    /**
     * Ensure the Excel workbook exists with header row.
     */
    public function ensureWorkbookExists(): void
    {
        if (!file_exists($this->filePath) || filesize($this->filePath) === 0) {
            $this->writeRows([]);
        }
    }

    /**
     * Start or fetch existing session/player data.
     */
    public function startOrResumeSession(string $username): array
    {
        $username = trim($username);
        if ($username === '') {
            throw new \InvalidArgumentException('Username cannot be empty');
        }

        $lockHandle = $this->acquireLock();
        try {
            $rows = $this->readRows();
            $nowDate = date('Y-m-d');
            $nowTime = date('H:i:s');

            $existingPlayerRow = null;
            $existingIndex = -1;

            foreach ($rows as $idx => $row) {
                if (strcasecmp((string)($row['Username'] ?? ''), $username) === 0) {
                    $existingPlayerRow = $row;
                    $existingIndex = $idx;
                    break;
                }
            }

            $sessionId = bin2hex(random_bytes(8));

            if ($existingPlayerRow !== null) {
                $hasActiveSession = in_array($existingPlayerRow['Game_Status'] ?? '', ['IN_PROGRESS', 'PAUSED'], true)
                    && (int)($existingPlayerRow['Current_Score'] ?? 0) > 0;

                // Update session ID & last played time
                $existingPlayerRow['Last_Played_Date'] = $nowDate;
                $existingPlayerRow['Last_Played_Time'] = $nowTime;
                $existingPlayerRow['Session_ID'] = $sessionId;

                $rows[$existingIndex] = $existingPlayerRow;
                $this->writeRows($rows);

                return [
                    'isNew' => false,
                    'canResume' => $hasActiveSession,
                    'player' => $existingPlayerRow,
                ];
            }

            // Create new player record
            $nextId = count($rows) + 1;
            $newRow = [
                'ID' => $nextId,
                'Username' => $username,
                'Created_Date' => $nowDate,
                'Created_Time' => $nowTime,
                'Last_Played_Date' => $nowDate,
                'Last_Played_Time' => $nowTime,
                'Session_ID' => $sessionId,
                'Current_Level' => 1,
                'Current_Score' => 0,
                'High_Score' => 0,
                'Snake_Length' => 3,
                'Games_Played' => 1,
                'Games_Completed' => 0,
                'Last_Save_Point' => 0,
                'Game_Status' => 'IN_PROGRESS',
            ];

            $rows[] = $newRow;
            $this->writeRows($rows);

            return [
                'isNew' => true,
                'canResume' => false,
                'player' => $newRow,
            ];
        } finally {
            $this->releaseLock($lockHandle);
        }
    }

    /**
     * Save progress checkpoint.
     */
    public function saveCheckpoint(string $sessionId, array $data): ?array
    {
        $lockHandle = $this->acquireLock();
        try {
            $rows = $this->readRows();
            $nowDate = date('Y-m-d');
            $nowTime = date('H:i:s');

            foreach ($rows as $idx => &$row) {
                if (($row['Session_ID'] ?? '') === $sessionId) {
                    $score = isset($data['score']) ? max(0, (int)$data['score']) : (int)($row['Current_Score'] ?? 0);
                    $level = isset($data['level']) ? max(1, (int)$data['level']) : (int)($row['Current_Level'] ?? 1);
                    $length = isset($data['snakeLength']) ? max(3, (int)$data['snakeLength']) : (int)($row['Snake_Length'] ?? 3);
                    $status = $data['status'] ?? ($row['Game_Status'] ?? 'IN_PROGRESS');

                    $highScore = max((int)($row['High_Score'] ?? 0), $score);

                    $row['Last_Played_Date'] = $nowDate;
                    $row['Last_Played_Time'] = $nowTime;
                    $row['Current_Score'] = $score;
                    $row['Current_Level'] = $level;
                    $row['High_Score'] = $highScore;
                    $row['Snake_Length'] = $length;
                    $row['Last_Save_Point'] = $score;
                    $row['Game_Status'] = $status;

                    $this->writeRows($rows);
                    return $row;
                }
            }

            return null;
        } finally {
            $this->releaseLock($lockHandle);
        }
    }

    /**
     * Conclude session (Game Over or Completed).
     */
    public function endSession(string $sessionId, array $finalData): ?array
    {
        $lockHandle = $this->acquireLock();
        try {
            $rows = $this->readRows();
            $nowDate = date('Y-m-d');
            $nowTime = date('H:i:s');

            foreach ($rows as $idx => &$row) {
                if (($row['Session_ID'] ?? '') === $sessionId) {
                    $score = isset($finalData['score']) ? max(0, (int)$finalData['score']) : (int)($row['Current_Score'] ?? 0);
                    $level = isset($finalData['level']) ? max(1, (int)$finalData['level']) : (int)($row['Current_Level'] ?? 1);
                    $length = isset($finalData['snakeLength']) ? max(3, (int)$finalData['snakeLength']) : (int)($row['Snake_Length'] ?? 3);
                    $completed = !empty($finalData['completed']);

                    $highScore = max((int)($row['High_Score'] ?? 0), $score);
                    $gamesCompleted = (int)($row['Games_Completed'] ?? 0) + ($completed ? 1 : 0);
                    $gamesPlayed = (int)($row['Games_Played'] ?? 0) + 1;

                    $row['Last_Played_Date'] = $nowDate;
                    $row['Last_Played_Time'] = $nowTime;
                    $row['Current_Score'] = $score;
                    $row['Current_Level'] = $level;
                    $row['High_Score'] = $highScore;
                    $row['Snake_Length'] = $length;
                    $row['Games_Played'] = $gamesPlayed;
                    $row['Games_Completed'] = $gamesCompleted;
                    $row['Last_Save_Point'] = $score;
                    $row['Game_Status'] = 'GAME_OVER';

                    $this->writeRows($rows);
                    return $row;
                }
            }

            return null;
        } finally {
            $this->releaseLock($lockHandle);
        }
    }

    /**
     * Start a new game for an existing session/player.
     */
    public function resetSessionForNewGame(string $sessionId): ?array
    {
        $lockHandle = $this->acquireLock();
        try {
            $rows = $this->readRows();
            $nowDate = date('Y-m-d');
            $nowTime = date('H:i:s');

            foreach ($rows as $idx => &$row) {
                if (($row['Session_ID'] ?? '') === $sessionId) {
                    $row['Last_Played_Date'] = $nowDate;
                    $row['Last_Played_Time'] = $nowTime;
                    $row['Current_Score'] = 0;
                    $row['Current_Level'] = 1;
                    $row['Snake_Length'] = 3;
                    $row['Last_Save_Point'] = 0;
                    $row['Game_Status'] = 'IN_PROGRESS';
                    $row['Games_Played'] = ((int)($row['Games_Played'] ?? 0)) + 1;

                    $this->writeRows($rows);
                    return $row;
                }
            }

            return null;
        } finally {
            $this->releaseLock($lockHandle);
        }
    }

    /**
     * Get player by session ID.
     */
    public function getPlayerBySession(string $sessionId): ?array
    {
        $rows = $this->readRows();
        foreach ($rows as $row) {
            if (($row['Session_ID'] ?? '') === $sessionId) {
                return $row;
            }
        }
        return null;
    }

    /**
     * Get top high scores / leaderboard.
     */
    public function getLeaderboard(int $limit = 10): array
    {
        $rows = $this->readRows();
        usort($rows, static function ($a, $b) {
            return ((int)($b['High_Score'] ?? 0)) <=> ((int)($a['High_Score'] ?? 0));
        });

        $leaders = [];
        $rank = 1;
        foreach (array_slice($rows, 0, $limit) as $r) {
            $leaders[] = [
                'rank' => $rank++,
                'username' => $r['Username'] ?? 'Anonymous',
                'highScore' => (int)($r['High_Score'] ?? 0),
                'currentLevel' => (int)($r['Current_Level'] ?? 1),
                'snakeLength' => (int)($r['Snake_Length'] ?? 3),
                'gamesPlayed' => (int)($r['Games_Played'] ?? 0),
                'lastPlayed' => trim(($r['Last_Played_Date'] ?? '') . ' ' . ($r['Last_Played_Time'] ?? '')),
            ];
        }

        return $leaders;
    }

    /**
     * Read rows from the XLSX file.
     */
    public function readRows(): array
    {
        if (!file_exists($this->filePath) || filesize($this->filePath) === 0) {
            return [];
        }

        $zip = new \ZipArchive();
        if ($zip->open($this->filePath) !== true) {
            return [];
        }

        $sheetXmlContent = $zip->getFromName('xl/worksheets/sheet1.xml');
        $sharedStringsXmlContent = $zip->getFromName('xl/sharedStrings.xml');
        $zip->close();

        if ($sheetXmlContent === false) {
            return [];
        }

        $sharedStrings = [];
        if ($sharedStringsXmlContent !== false) {
            $sDom = new \DOMDocument();
            if (@$sDom->loadXML($sharedStringsXmlContent)) {
                $siNodes = $sDom->getElementsByTagName('si');
                foreach ($siNodes as $si) {
                    $tNodes = $si->getElementsByTagName('t');
                    $text = '';
                    foreach ($tNodes as $t) {
                        $text .= $t->textContent;
                    }
                    $sharedStrings[] = $text;
                }
            }
        }

        $dom = new \DOMDocument();
        if (!@$dom->loadXML($sheetXmlContent)) {
            return [];
        }

        $rows = [];
        $rowNodes = $dom->getElementsByTagName('row');
        $headersRead = [];

        foreach ($rowNodes as $rowIdx => $rowNode) {
            $cellNodes = $rowNode->getElementsByTagName('c');
            $rowValues = [];

            foreach ($cellNodes as $c) {
                $r = $c->getAttribute('r'); // e.g. "A1", "B2"
                $colLetters = preg_replace('/[0-9]/', '', $r);
                $colIndex = $this->colLetterToIndex($colLetters);

                $t = $c->getAttribute('t');
                $v = '';
                $vNode = $c->getElementsByTagName('v')->item(0);
                if ($vNode) {
                    $rawVal = $vNode->textContent;
                    if ($t === 's' && isset($sharedStrings[(int)$rawVal])) {
                        $v = $sharedStrings[(int)$rawVal];
                    } else {
                        $v = $rawVal;
                    }
                } elseif ($t === 'inlineStr') {
                    $isNode = $c->getElementsByTagName('is')->item(0);
                    if ($isNode) {
                        $v = $isNode->textContent;
                    }
                }

                $rowValues[$colIndex] = $v;
            }

            if ($rowIdx === 0) {
                // Header row
                $headersRead = $rowValues;
            } else {
                $assoc = [];
                foreach ($this->headers as $colIdx => $headerName) {
                    $assoc[$headerName] = $rowValues[$colIdx] ?? '';
                }
                if (!empty($assoc['Username'])) {
                    $rows[] = $assoc;
                }
            }
        }

        return $rows;
    }

    /**
     * Write rows atomically into the XLSX file.
     */
    public function writeRows(array $rows): void
    {
        $tempPath = $this->filePath . '.tmp.' . uniqid('', true);

        // Build OpenXML XLSX structure
        $zip = new \ZipArchive();
        if ($zip->open($tempPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Failed to create Excel temporary workbook: ' . $tempPath);
        }

        // Shared strings dictionary
        $sharedStrings = [];
        $sharedStringMap = [];

        $getSharedStringId = function (string $str) use (&$sharedStrings, &$sharedStringMap): int {
            if (isset($sharedStringMap[$str])) {
                return $sharedStringMap[$str];
            }
            $id = count($sharedStrings);
            $sharedStrings[] = $str;
            $sharedStringMap[$str] = $id;
            return $id;
        };

        // 1. [Content_Types].xml
        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">' .
            '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>' .
            '<Default Extension="xml" ContentType="application/xml"/>' .
            '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>' .
            '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>' .
            '<Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>' .
            '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>' .
            '</Types>';
        $zip->addFromString('[Content_Types].xml', $contentTypes);

        // 2. _rels/.rels
        $rootRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
            '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>' .
            '</Relationships>';
        $zip->addFromString('_rels/.rels', $rootRels);

        // 3. xl/_rels/workbook.xml.rels
        $wbRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
            '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>' .
            '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>' .
            '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>' .
            '</Relationships>';
        $zip->addFromString('xl/_rels/workbook.xml.rels', $wbRels);

        // 4. xl/workbook.xml
        $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">' .
            '<bookViews><workbookView xWindow="0" yWindow="0" windowWidth="22260" windowHeight="12600"/></bookViews>' .
            '<sheets><sheet name="Players" sheetId="1" r:id="rId1"/></sheets>' .
            '</workbook>';
        $zip->addFromString('xl/workbook.xml', $workbook);

        // 5. xl/styles.xml (Simple modern styling)
        $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' .
            '<fonts count="2">' .
            '<font><sz val="11"/><color theme="1"/><name val="Segoe UI"/></font>' .
            '<font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Segoe UI"/></font>' .
            '</fonts>' .
            '<fills count="3">' .
            '<fill><patternFill patternType="none"/></fill>' .
            '<fill><patternFill patternType="gray125"/></fill>' .
            '<fill><patternFill patternType="solid"><fgColor rgb="FF7C3AED"/></patternFill></fill>' .
            '</fills>' .
            '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>' .
            '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>' .
            '<cellXfs count="2">' .
            '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>' .
            '<xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1"/>' .
            '</cellXfs>' .
            '</styleSheet>';
        $zip->addFromString('xl/styles.xml', $styles);

        // Build Sheet XML Rows
        $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' .
            '<sheetData>';

        // Row 1: Headers (styled with purple header)
        $sheetXml .= '<row r="1">';
        foreach ($this->headers as $cIdx => $header) {
            $colLetter = $this->indexToColLetter($cIdx);
            $sId = $getSharedStringId($header);
            $sheetXml .= '<c r="' . $colLetter . '1" t="s" s="1"><v>' . $sId . '</v></c>';
        }
        $sheetXml .= '</row>';

        // Data Rows
        $rowNum = 2;
        foreach ($rows as $row) {
            $sheetXml .= '<row r="' . $rowNum . '">';
            foreach ($this->headers as $cIdx => $header) {
                $colLetter = $this->indexToColLetter($cIdx);
                $val = (string)($row[$header] ?? '');

                if (is_numeric($val) && !preg_match('/^0[0-9]+/', $val) && !str_contains($val, '-')) {
                    // Numeric cell
                    $sheetXml .= '<c r="' . $colLetter . $rowNum . '"><v>' . htmlspecialchars($val, ENT_XML1) . '</v></c>';
                } else {
                    // String cell via shared strings
                    $sId = $getSharedStringId($val);
                    $sheetXml .= '<c r="' . $colLetter . $rowNum . '" t="s"><v>' . $sId . '</v></c>';
                }
            }
            $sheetXml .= '</row>';
            $rowNum++;
        }

        $sheetXml .= '</sheetData></worksheet>';
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);

        // 6. xl/sharedStrings.xml
        $sstXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . count($sharedStrings) . '" uniqueCount="' . count($sharedStrings) . '">';
        foreach ($sharedStrings as $str) {
            $sstXml .= '<si><t>' . htmlspecialchars($str, ENT_XML1) . '</t></si>';
        }
        $sstXml .= '</sst>';
        $zip->addFromString('xl/sharedStrings.xml', $sstXml);

        $zip->close();

        // Atomic file replace
        if (file_exists($this->filePath)) {
            @copy($this->filePath, $this->backupDir . 'game_players_' . date('Y-m-d') . '.xlsx');
        }
        rename($tempPath, $this->filePath);
    }

    private function indexToColLetter(int $index): string
    {
        $letter = '';
        while ($index >= 0) {
            $letter = chr($index % 26 + 65) . $letter;
            $index = intdiv($index, 26) - 1;
        }
        return $letter;
    }

    private function colLetterToIndex(string $letters): int
    {
        $letters = strtoupper($letters);
        $index = 0;
        $len = strlen($letters);
        for ($i = 0; $i < $len; $i++) {
            $index = $index * 26 + (ord($letters[$i]) - 64);
        }
        return $index - 1;
    }

    private function acquireLock()
    {
        $handle = fopen($this->lockPath, 'c+');
        if ($handle) {
            flock($handle, LOCK_EX);
        }
        return $handle;
    }

    private function releaseLock($handle): void
    {
        if ($handle) {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }
}
