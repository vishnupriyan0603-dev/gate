<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    protected $db;
    protected $helpers = ['url'];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->db = \Config\Database::connect();
    }

    protected function pageData(string $page, string $title, string $active): array
    {
        $totalXp = (int) $this->db->table('xp_logs')->selectSum('amount', 'total')->get()->getRowArray()['total'];
        $level = intdiv($totalXp, 500) + 1;
        $inLevel = $totalXp % 500;

        $examDate = $this->setting('exam_date', '2027-02-05');
        $days = ceil((strtotime($examDate . ' 00:00:00') - strtotime('today')) / 86400);

        return [
            'page' => $page,
            'title' => $title,
            'active' => $active,
            'xpTotal' => $totalXp,
            'level' => $level,
            'inLevel' => $inLevel,
            'streak' => $this->streak(),
            'examDate' => $examDate,
            'daysLeft' => max(0, $days),
            'baseUrl' => rtrim(base_url(), '/'),
        ];
    }

    protected function setting(string $key, string $default = ''): string
    {
        $row = $this->db->table('settings')->where('skey', $key)->get()->getRowArray();
        return $row ? (string) $row['svalue'] : $default;
    }

    protected function streak(): int
    {
        $dates = [];
        foreach ($this->db->table('study_sessions')->select('session_date')->orderBy('session_date', 'DESC')->get()->getResultArray() as $r) {
            $dates[] = $r['session_date'];
        }
        $dates = array_values(array_unique($dates));
        if (count($dates) === 0) {
            return 0;
        }
        $cur = new \DateTime($dates[0]);
        $today = new \DateTime(date('Y-m-d'));
        if ($cur->format('Y-m-d') !== $today->format('Y-m-d')) {
            // allow streak continuing from yesterday
            $yesterday = (new \DateTime('yesterday'))->format('Y-m-d');
            if ($cur->format('Y-m-d') !== $yesterday) {
                return 0;
            }
        }
        $streak = 1;
        $set = array_flip($dates);
        $d = $cur;
        while (true) {
            $d = $d->modify('-1 day');
            if (isset($set[$d->format('Y-m-d')])) {
                $streak++;
            } else {
                break;
            }
        }
        return $streak;
    }
}