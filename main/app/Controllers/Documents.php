<?php

namespace App\Controllers;

class Documents extends BaseController
{
    public function index(): string
    {
        $data = $this->pageData('documents', 'Document Hub', 'documents');
        $data['docs'] = $this->db->table('documents')
            ->select('documents.*, subjects.name AS subject_name')
            ->join('subjects', 'subjects.id = documents.subject_id', 'left')
            ->orderBy('documents.kind', 'DESC')->orderBy('documents.id', 'ASC')->get()->getResultArray();
        $data['subjects'] = $this->db->table('subjects')->orderBy('priority', 'ASC')->get()->getResultArray();
        return view('partials/layout', array_merge($data, ['content' => view('pages/documents', $data)]));
    }

    public function stream(string $id): \CodeIgniter\HTTP\ResponseInterface
    {
        $doc = $this->db->table('documents')->where('id', (int) $id)->where('kind', 'pdf')->get()->getRowArray();
        if (!$doc || !$doc['filename']) {
            return $this->response->setStatusCode(404)->setBody('Not found');
        }
        $name = basename($doc['filename']);
        $candidates = [
            ROOTPATH . 'document' . DIRECTORY_SEPARATOR . $name,
            ROOTPATH . 'main' . DIRECTORY_SEPARATOR . 'document' . DIRECTORY_SEPARATOR . $name,
            FCPATH . 'main' . DIRECTORY_SEPARATOR . 'document' . DIRECTORY_SEPARATOR . $name,
            FCPATH . 'document' . DIRECTORY_SEPARATOR . $name,
        ];
        $file = null;
        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                $file = $candidate;
                break;
            }
        }
        if (!$file) {
            return $this->response->setStatusCode(404)->setBody('File missing: ' . $name);
        }
        // Allow range requests so PDF.js can stream large files.
        $size = filesize($file);
        $safeName = str_replace(['"', "\r", "\n"], '', $name);
        $this->response->setHeader('Content-Type', 'application/pdf');
        $this->response->setHeader('Accept-Ranges', 'bytes');
        $this->response->setHeader('Content-Length', (string) $size);
        $this->response->setHeader('Content-Disposition', 'inline; filename="' . $safeName . '"');

        $range = $this->request->getServer('HTTP_RANGE');
        if ($range && preg_match('/bytes=(\d*)-(\d*)/', $range, $m)) {
            if ($m[1] === '' && $m[2] === '') {
                return $this->response->setStatusCode(416)->setHeader('Content-Range', "bytes */$size")->setBody('Invalid range');
            }
            if ($m[1] === '') {
                // Suffix range: last N bytes.
                $len = min((int) $m[2], $size);
                $start = $size - $len;
                $end = $size - 1;
            } else {
                $start = (int) $m[1];
                $end = $m[2] !== '' ? min((int) $m[2], $size - 1) : $size - 1;
            }
            if ($start >= $size || $start > $end || $size === 0) {
                return $this->response->setStatusCode(416)->setHeader('Content-Range', "bytes */$size")->setBody('Range not satisfiable');
            }
            $len = $end - $start + 1;
            $this->response->setStatusCode(206);
            $this->response->setHeader('Content-Range', "bytes $start-$end/$size");
            $this->response->setHeader('Content-Length', (string) $len);
            $fh = fopen($file, 'rb');
            if ($fh === false || fseek($fh, $start) !== 0) {
                if (is_resource($fh)) {
                    fclose($fh);
                }
                return $this->response->setStatusCode(500)->setBody('Read error');
            }
            // Stream in 1 MB chunks to bound memory on large ranges.
            $left = $len;
            while ($left > 0 && !feof($fh)) {
                echo fread($fh, min(1048576, $left));
                $left -= 1048576;
            }
            fclose($fh);
            return $this->response->send();
        }

        readfile($file);
        return $this->response->send();
    }
}