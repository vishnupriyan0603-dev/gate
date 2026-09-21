CREATE DATABASE IF NOT EXISTS gateeaxmtraining CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gateeaxmtraining;

-- Subjects (from Notion "Subject priority")
CREATE TABLE IF NOT EXISTS subjects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  code VARCHAR(40) DEFAULT NULL,
  priority INT DEFAULT 0,
  color VARCHAR(20) DEFAULT '#6366f1',
  icon VARCHAR(10) DEFAULT '📘',
  notion_id VARCHAR(64) DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Study phases (roadmap)
CREATE TABLE IF NOT EXISTS phases (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(200) NOT NULL,
  start_date DATE DEFAULT NULL,
  end_date DATE DEFAULT NULL,
  sort_order INT DEFAULT 0,
  goal TEXT,
  notion_id VARCHAR(64) DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Course topics
CREATE TABLE IF NOT EXISTS topics (
  id INT AUTO_INCREMENT PRIMARY KEY,
  phase_id INT DEFAULT NULL,
  subject_id INT DEFAULT NULL,
  name VARCHAR(200) NOT NULL,
  sort_order INT DEFAULT 0,
  status ENUM('locked','active','done') DEFAULT 'locked',
  is_lesson TINYINT DEFAULT 0,
  notion_id VARCHAR(64) DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_phase (phase_id),
  KEY idx_subject (subject_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Document / resource hub (PDF, Notion, link)
CREATE TABLE IF NOT EXISTS documents (
  id INT AUTO_INCREMENT PRIMARY KEY,
  subject_id INT DEFAULT NULL,
  topic_id INT DEFAULT NULL,
  title VARCHAR(300) NOT NULL,
  kind ENUM('pdf','notion','link') DEFAULT 'pdf',
  url VARCHAR(500) DEFAULT NULL,
  filename VARCHAR(300) DEFAULT NULL,
  descr TEXT,
  notion_id VARCHAR(64) DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  KEY idx_subject (subject_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Study time tracker
CREATE TABLE IF NOT EXISTS study_sessions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  session_date DATE NOT NULL,
  minutes INT NOT NULL DEFAULT 0,
  subject_id INT DEFAULT NULL,
  note VARCHAR(255) DEFAULT NULL,
  source ENUM('app','extension') DEFAULT 'app',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  KEY idx_date (session_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- MCQ question bank
CREATE TABLE IF NOT EXISTS mcqs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  subject_id INT NOT NULL,
  topic_id INT DEFAULT NULL,
  question TEXT NOT NULL,
  opt_a VARCHAR(500) NOT NULL,
  opt_b VARCHAR(500) NOT NULL,
  opt_c VARCHAR(500) NOT NULL,
  opt_d VARCHAR(500) NOT NULL,
  answer CHAR(1) NOT NULL,
  explanation TEXT,
  difficulty ENUM('E','M','H') DEFAULT 'M',
  status ENUM('active','archived') DEFAULT 'active',
  source VARCHAR(30) DEFAULT 'manual',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  KEY idx_subject (subject_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Aggregate quiz results (feeds performance)
CREATE TABLE IF NOT EXISTS quiz_sessions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  session_type VARCHAR(20) NOT NULL,
  subject_id INT DEFAULT NULL,
  total INT NOT NULL DEFAULT 0,
  correct INT NOT NULL DEFAULT 0,
  attempted INT NOT NULL DEFAULT 0,
  seconds INT DEFAULT 0,
  comment VARCHAR(255),
  xp_earned INT DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  KEY idx_date (created_at),
  KEY idx_subject (subject_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Day-by-day plan from Notion calendar
CREATE TABLE IF NOT EXISTS daily_plan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  task_date DATE NOT NULL,
  weekday VARCHAR(10) DEFAULT NULL,
  subject VARCHAR(150) DEFAULT NULL,
  task VARCHAR(500) NOT NULL,
  pyqs INT DEFAULT 0,
  status ENUM('pending','done','skipped') DEFAULT 'pending',
  notion_id VARCHAR(64) DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_date (task_date),
  KEY idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- To-do checklists (two-way sync with Notion)
CREATE TABLE IF NOT EXISTS checklists (
  id INT AUTO_INCREMENT PRIMARY KEY,
  group_name VARCHAR(150) NOT NULL,
  title VARCHAR(500) NOT NULL,
  checked TINYINT NOT NULL DEFAULT 0,
  notion_block_id VARCHAR(64) DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_notion (notion_block_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Mock score targets (from Notion table)
CREATE TABLE IF NOT EXISTS mock_targets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  period VARCHAR(60) NOT NULL,
  target_min INT DEFAULT 0,
  target_max INT DEFAULT 0,
  sort_order INT DEFAULT 0,
  notion_id VARCHAR(64) DEFAULT NULL,
  UNIQUE KEY uq_period (period)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- XP ledger
CREATE TABLE IF NOT EXISTS xp_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  amount INT NOT NULL DEFAULT 0,
  reason VARCHAR(120) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Settings key/value
CREATE TABLE IF NOT EXISTS settings (
  skey VARCHAR(60) PRIMARY KEY,
  svalue TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Notion raw cache
CREATE TABLE IF NOT EXISTS notion_cache (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nkey VARCHAR(120) NOT NULL UNIQUE,
  payload LONGTEXT,
  synced_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Notion id mapping for two-way sync
CREATE TABLE IF NOT EXISTS notion_map (
  id INT AUTO_INCREMENT PRIMARY KEY,
  local_table VARCHAR(60) NOT NULL,
  local_id INT NOT NULL,
  notion_type VARCHAR(40) DEFAULT NULL,
  notion_id VARCHAR(64) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_notion (notion_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============ SEED ============

INSERT INTO subjects (name, code, priority, color, icon) VALUES
('Algorithms','ALGO',1,'#4f46e5','🧮'),
('Data Structures','DS',2,'#0ea5e9','🌲'),
('DBMS','DBMS',3,'#10b981','🗄️'),
('Operating Systems','OS',4,'#f59e0b','⚙️'),
('Computer Networks','CN',5,'#ef4444','🌐'),
('Discrete Mathematics','DM',6,'#8b5cf6','⊢'),
('Theory of Computation','TOC',7,'#ec4899','λ'),
('Computer Organization','COA',8,'#f97316','🖥️'),
('Engineering Mathematics','MATHS',9,'#14b8a6','∑'),
('Digital Logic','DL',10,'#a855f7','🔢'),
('Compiler Design','COMPILER',11,'#06b6d4','🧬'),
('General Aptitude','APT',12,'#64748b','🧠');

INSERT INTO mock_targets (period, target_min, target_max, sort_order) VALUES
('October',30,40,1),
('November',40,50,2),
('December',50,60,3),
('Early January',60,70,4),
('Late January',70,80,5),
('GATE 2027',80,90,6);

-- Link the 12 existing PDFs (tag them by subject in the Documents tab)
INSERT INTO documents (title, kind, filename) VALUES
('Study Document 1','pdf','document/1c29fc66-868a-4f54-be30-df0a813790b9.pdf'),
('Study Document 2','pdf','document/2fd06899-b7f2-44c7-b859-e608cf490de4.pdf'),
('Study Document 3','pdf','document/30aa7d74-582f-4314-ad40-c211dd49d268.pdf'),
('Study Document 4','pdf','document/44297ff4-cbe0-4fc4-87e1-948f9daeb5b6.pdf'),
('Study Document 5','pdf','document/7290303c-b37a-40c7-b486-a298ffe6aa60.pdf'),
('Study Document 6','pdf','document/8d2a418c-7299-40d9-bb15-9ea32e768a88.pdf'),
('Study Document 7','pdf','document/92030e28-da6b-4f49-8920-ec1ed0b61684.pdf'),
('Study Document 8','pdf','document/a483295c-2895-4375-bfa7-21b750de1c8b.pdf'),
('Study Document 9','pdf','document/acc041c0-74ff-4608-9ef9-ab759d0953f2.pdf'),
('Study Document 10','pdf','document/e71de686-d85c-4910-a7c8-f5623dfb17d1.pdf'),
('Study Document 11','pdf','document/e751d87f-9074-4f6b-83a2-e97c4e168117.pdf'),
('Study Document 12','pdf','document/fcb3fb4a-2ae6-4a7d-8b2f-280497391bb1.pdf');

-- Notion pages
INSERT INTO documents (title, kind, url, descr) VALUES
('🎯 GATE CS 2027 — 80–90 Marks Plan','notion','https://app.notion.com/p/GATE-CS-2027-80-90-Marks-Plan-3d5e1176b072819bae83e42557dc8f29','Main Notion study plan'),
('📅 GATE CS 2027 — Day-by-Day Calendar','notion','https://app.notion.com/p/GATE-CS-2027-Day-by-Day-Calendar-3d5e1176b072819bae83e42557dc8f29','Daily schedule from 14 Sep');

-- Starter MCQs
INSERT INTO mcqs (subject_id, question, opt_a, opt_b, opt_c, opt_d, answer, explanation, difficulty) VALUES
(6,'How many edges does a complete graph with n vertices have?','n','n(n-1)/2','n(n+1)/2','2n','B','Every pair of distinct vertices is connected, giving C(n,2) = n(n-1)/2 edges.','E'),
(1,'Which of the following has the worst-case time complexity O(n²)?','Merge Sort','Quick Sort','Heap Sort','Binary Search','B','Quick Sort degrades to O(n²) when the pivot choice is consistently poor (already sorted input).','E'),
(3,'Which normal form removes transitive dependencies that depend on a candidate key?','1NF','2NF','3NF','BCNF','C','3NF removes transitive dependencies on non-prime attributes.','E'),
(4,'Which scheduling algorithm can cause starvation?','FCFS','Round Robin','Priority Scheduling','Shortest Job Next','C','Low-priority processes can starve indefinitely under priority scheduling without aging.','M'),
(5,'Which IPv4 class is 192.168.1.1 in?','Class A','Class B','Class C','Class D','C','192–223 is Class C; private range 192.168.0.0/16 used for local networks.','E'),
(7,'Which language is NOT recursively enumerable?','Halting problem language','All regular languages','Complements of halting problem','All context-free languages','C','The complement of the halting problem is not even recursively enumerable.','H'),
(8,'In which pipeline stage is the instruction decoded?','IF','ID','EX','WB','B','Instruction Decode (ID) stage decodes the fetched instruction.','E'),
(10,'Which gate implements NAND as AND followed by NOT?','XNOR','NAND','NOR','XOR','B','NAND = AND + NOT. It is functionally complete.','E'),
(5,'A subnet mask of 255.255.255.0 on the network 192.168.1.0 has how many usable host addresses per subnet?','254','256','255','251','A','2^8 - 2 = 254 usable hosts (network + broadcast excluded).','E'),
(2,'What is the time complexity of binary search on a sorted array of size n?','O(1)','O(log n)','O(n)','O(n log n)','B','Each comparison halves the search space → O(log n).','E');

INSERT INTO settings (skey, svalue) VALUES
('exam_date','2027-02-05'),
('target_score','85'),
('weekly_hour_goal','18'),
('notion_token',''),
('notion_plan_page','3d5e1176b072819bae83e42557dc8f29'),
('notion_calendar_page','3d5e1176-b072-8102-902d-f58a68795f44'),
('daily_streak','0'),
('last_active_date',''),
('notion_synced_map','plan:1'),
('groq_api_key',''),
('groq_model','openai/gpt-oss-120b');