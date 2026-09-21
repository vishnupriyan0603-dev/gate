<?php

namespace App\Controllers;

use App\Services\GameExcelService;
use CodeIgniter\HTTP\ResponseInterface;

class Game extends BaseController
{
    private GameExcelService $excelService;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->excelService = new GameExcelService();
    }

    /**
     * Render the isolated Game page.
     */
    public function index(): string
    {
        $data = $this->pageData('game', 'Snake Chase Ball · Arcade', 'game');
        $data['leaderboard'] = $this->excelService->getLeaderboard(5);

        return view('partials/layout', array_merge($data, [
            'content' => view('pages/game', $data),
        ]));
    }

    /**
     * Start or authenticate a player session.
     * POST /game/session/start
     */
    public function sessionStart(): ResponseInterface
    {
        $json = $this->request->getJSON(true) ?? $this->request->getPost();
        $username = trim((string)($json['username'] ?? ''));

        if ($username === '' || strlen($username) > 30) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Valid username (1-30 characters) is required.',
            ]);
        }

        // Sanitize username
        $username = preg_replace('/[^\w\s\-\.]/u', '', $username);

        try {
            $result = $this->excelService->startOrResumeSession($username);
            return $this->response->setJSON([
                'status' => 'success',
                'isNew' => $result['isNew'],
                'canResume' => $result['canResume'],
                'player' => $result['player'],
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to initialize player record: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Save session checkpoint periodically.
     * POST /game/session/checkpoint
     */
    public function sessionCheckpoint(): ResponseInterface
    {
        $json = $this->request->getJSON(true) ?? $this->request->getPost();
        $sessionId = trim((string)($json['sessionId'] ?? ''));

        if ($sessionId === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Session ID is required.',
            ]);
        }

        $data = [
            'score' => (int)($json['score'] ?? 0),
            'level' => (int)($json['level'] ?? 1),
            'snakeLength' => (int)($json['snakeLength'] ?? 3),
            'status' => (string)($json['status'] ?? 'IN_PROGRESS'),
        ];

        $updated = $this->excelService->saveCheckpoint($sessionId, $data);
        if (!$updated) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error',
                'message' => 'Session not found.',
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'player' => $updated,
        ]);
    }

    /**
     * Manual session save.
     * POST /game/session/save
     */
    public function sessionSave(): ResponseInterface
    {
        return $this->sessionCheckpoint();
    }

    /**
     * Retrieve session details.
     * GET /game/session/{sessionId}
     */
    public function getSession(string $sessionId): ResponseInterface
    {
        $player = $this->excelService->getPlayerBySession($sessionId);
        if (!$player) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error',
                'message' => 'Session not found.',
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'player' => $player,
        ]);
    }

    /**
     * Start a new game with the current session.
     * POST /game/session/new
     */
    public function sessionNew(): ResponseInterface
    {
        $json = $this->request->getJSON(true) ?? $this->request->getPost();
        $sessionId = trim((string)($json['sessionId'] ?? ''));

        if ($sessionId === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Session ID is required.',
            ]);
        }

        $updated = $this->excelService->resetSessionForNewGame($sessionId);
        if (!$updated) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error',
                'message' => 'Session not found.',
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'player' => $updated,
        ]);
    }

    /**
     * Conclude session (Game Over).
     * POST /game/session/end
     */
    public function sessionEnd(): ResponseInterface
    {
        $json = $this->request->getJSON(true) ?? $this->request->getPost();
        $sessionId = trim((string)($json['sessionId'] ?? ''));

        if ($sessionId === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Session ID is required.',
            ]);
        }

        $finalData = [
            'score' => (int)($json['score'] ?? 0),
            'level' => (int)($json['level'] ?? 1),
            'snakeLength' => (int)($json['snakeLength'] ?? 3),
            'completed' => !empty($json['completed']),
        ];

        $updated = $this->excelService->endSession($sessionId, $finalData);
        if (!$updated) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error',
                'message' => 'Session not found.',
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'player' => $updated,
        ]);
    }

    /**
     * Get top high scores from Excel database.
     * GET /game/leaderboard
     */
    public function leaderboard(): ResponseInterface
    {
        $leaders = $this->excelService->getLeaderboard(10);
        return $this->response->setJSON([
            'status' => 'success',
            'leaderboard' => $leaders,
        ]);
    }
}
