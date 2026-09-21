<?php

namespace App\Controllers;

class Course extends BaseController
{
    public function index(): string
    {
        $data = $this->pageData('course', 'Course Mode', 'course');
        $data['phases'] = $this->db->table('phases')->orderBy('sort_order', 'ASC')->orderBy('start_date', 'ASC')->get()->getResultArray();
        $data['topics'] = $this->db->table('topics')
            ->select('topics.*, subjects.name AS subject_name, subjects.color, subjects.icon')
            ->join('subjects', 'subjects.id = topics.subject_id', 'left')
            ->orderBy('topics.id', 'ASC')->get()->getResultArray();
        return view('partials/layout', array_merge($data, ['content' => view('pages/course', $data)]));
    }

    public function learn(string $id): string|\CodeIgniter\HTTP\ResponseInterface
    {
        $topic = $this->db->table('topics')
            ->select('topics.*, subjects.name AS subject_name, subjects.color, subjects.icon')
            ->join('subjects', 'subjects.id = topics.subject_id', 'left')
            ->where('topics.id', (int) $id)->get()->getRowArray();
        if (!$topic) {
            return redirect()->to('/course');
        }
        $data = $this->pageData('course', 'Learn · ' . $topic['name'], 'course');
        $data['topic'] = $topic;
        $data['docs'] = $this->db->table('documents')
            ->groupStart()
                ->where('subject_id', $topic['subject_id'] ?? 0)
                ->orWhere('topic_id', (int) $id)
            ->groupEnd()
            ->get()->getResultArray();
        $data['mcqs'] = $this->db->table('mcqs')->where('subject_id', $topic['subject_id'] ?? 0)
            ->orderBy('RAND()')->limit(20)->get()->getResultArray();
        return view('partials/layout', array_merge($data, ['content' => view('pages/learn', $data)]));
    }
}