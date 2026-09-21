<?php

namespace Tests\Unit;

use App\Services\GameExcelService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class GameExcelTest extends CIUnitTestCase
{
    private string $testExcelPath;
    private GameExcelService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->testExcelPath = WRITEPATH . 'game_data/test_game_players.xlsx';
        if (file_exists($this->testExcelPath)) {
            @unlink($this->testExcelPath);
        }
        $this->service = new GameExcelService($this->testExcelPath);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        if (file_exists($this->testExcelPath)) {
            @unlink($this->testExcelPath);
        }
    }

    public function testStartNewPlayerSession(): void
    {
        $res = $this->service->startOrResumeSession('Arya');

        $this->assertTrue($res['isNew']);
        $this->assertFalse($res['canResume']);
        $this->assertSame('Arya', $res['player']['Username']);
        $this->assertSame(0, (int)$res['player']['Current_Score']);
        $this->assertSame(1, (int)$res['player']['Current_Level']);
        $this->assertNotEmpty($res['player']['Session_ID']);
        $this->assertFileExists($this->testExcelPath);
    }

    public function testSaveCheckpointAndResume(): void
    {
        $start = $this->service->startOrResumeSession('Rohan');
        $sessionId = $start['player']['Session_ID'];

        $saved = $this->service->saveCheckpoint($sessionId, [
            'score' => 350,
            'level' => 3,
            'snakeLength' => 25,
            'status' => 'IN_PROGRESS',
        ]);

        $this->assertNotNull($saved);
        $this->assertSame(350, (int)$saved['Current_Score']);
        $this->assertSame(3, (int)$saved['Current_Level']);
        $this->assertSame(350, (int)$saved['High_Score']);

        // Test resume session check
        $resumeCheck = $this->service->startOrResumeSession('Rohan');
        $this->assertFalse($resumeCheck['isNew']);
        $this->assertTrue($resumeCheck['canResume']);
        $this->assertSame(350, (int)$resumeCheck['player']['Current_Score']);
    }

    public function testEndSessionAndLeaderboard(): void
    {
        $p1 = $this->service->startOrResumeSession('PlayerOne');
        $this->service->saveCheckpoint($p1['player']['Session_ID'], ['score' => 200, 'level' => 2, 'snakeLength' => 15]);
        $this->service->endSession($p1['player']['Session_ID'], ['score' => 200, 'level' => 2, 'snakeLength' => 15, 'completed' => false]);

        $p2 = $this->service->startOrResumeSession('PlayerTwo');
        $this->service->saveCheckpoint($p2['player']['Session_ID'], ['score' => 600, 'level' => 4, 'snakeLength' => 40]);
        $this->service->endSession($p2['player']['Session_ID'], ['score' => 600, 'level' => 4, 'snakeLength' => 40, 'completed' => true]);

        $leaderboard = $this->service->getLeaderboard(5);

        $this->assertCount(2, $leaderboard);
        $this->assertSame('PlayerTwo', $leaderboard[0]['username']);
        $this->assertSame(600, $leaderboard[0]['highScore']);
        $this->assertSame('PlayerOne', $leaderboard[1]['username']);
        $this->assertSame(200, $leaderboard[1]['highScore']);
    }
}
