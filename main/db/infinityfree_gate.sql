-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: gateeaxmtraining
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `checklists`
--

DROP TABLE IF EXISTS `checklists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `checklists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(150) NOT NULL,
  `title` varchar(500) NOT NULL,
  `checked` tinyint(4) NOT NULL DEFAULT 0,
  `notion_block_id` varchar(64) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_notion` (`notion_block_id`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `checklists`
--

LOCK TABLES `checklists` WRITE;
/*!40000 ALTER TABLE `checklists` DISABLE KEYS */;
INSERT INTO `checklists` VALUES (1,'Weekly tracking','18 study hours completed',0,'72537bd3-528c-44e0-9871-c9f10b57d2e2','2026-09-12 13:14:32'),(2,'Weekly tracking','Concepts completed',0,'66b1d014-52bf-4b7d-9460-cc5ba957063d','2026-09-12 13:14:32'),(3,'Weekly tracking','PYQs completed',0,'f15bbf45-f84d-4a9d-acfe-98d5973e398e','2026-09-12 13:14:32'),(4,'Weekly tracking','Weekly test completed',0,'e15afd68-3424-43f0-a031-5bd6c5760334','2026-09-12 13:14:32'),(5,'Weekly tracking','Test analyzed',0,'f963f981-4b4d-46bd-b967-972015017865','2026-09-12 13:14:32'),(6,'Weekly tracking','Mistakes added to mistake notebook',0,'699178df-f57e-4465-bd49-a3daca58e79f','2026-09-12 13:14:32'),(7,'Weekly tracking','Weak topics identified',0,'1b064c32-d21b-4972-88cc-9ea6636fe8f1','2026-09-12 13:14:32'),(8,'Monday–Friday — 2 hours','0:00–0:10: Recall yesterday\'s topic without notes; write 3 things you remember.',0,'3477c994-f874-4cd7-922b-596c2c232221','2026-09-12 13:14:35'),(9,'Monday–Friday — 2 hours','0:10–1:00: Learn today\'s concept from your main GATE resource; make 8–12 short notes/formulas.',0,'5a89e427-e816-4868-a0a7-9f6803419cbc','2026-09-12 13:14:35'),(10,'Monday–Friday — 2 hours','1:00–1:45: Solve the day\'s listed PYQs/problems without looking at the solution first.',0,'8d90cd87-7356-407d-b0cd-f8629f1e953b','2026-09-12 13:14:35'),(11,'Monday–Friday — 2 hours','1:45–2:00: Check answers; record every mistake, guessed answer, and time-consuming question.',0,'3eb2a901-96a5-4ec0-950c-469bb40684be','2026-09-12 13:14:35'),(12,'Saturday — 2 hours','0:00–0:30: Revise all topics studied Monday–Friday.',0,'618ed829-8d4a-4ed5-8b40-e06fbf19e370','2026-09-12 13:14:35'),(13,'Saturday — 2 hours','0:30–1:30: Solve the week\'s PYQs again, prioritizing questions you previously got wrong.',0,'07660608-3377-4b98-82b3-47b74e1f2096','2026-09-12 13:14:35'),(14,'Saturday — 2 hours','1:30–2:00: Take a timed mixed set and calculate accuracy.',0,'3b0734b7-c988-4c65-8832-cd9116941121','2026-09-12 13:14:35'),(15,'Saturday — 2 hours','Carry forward your top 3 weak topics to Sunday analysis.',0,'e318f55f-29f5-4d8a-a4de-f600438f3319','2026-09-12 13:14:35'),(16,'Sunday — 6 hours','2h: Revise the week\'s concepts and short notes.',0,'94fdf6b6-159b-443a-b06b-c31cfff9043e','2026-09-12 13:14:35'),(17,'Sunday — 6 hours','2h: Solve 40–60 PYQs; mark questions as Easy / Medium / Hard.',0,'c5df9cf4-a711-4635-ac60-12a7583b450b','2026-09-12 13:14:35'),(18,'Sunday — 6 hours','1h: Take the scheduled sectional or full mock under exam conditions.',0,'79b55da2-001a-4ef6-bd61-5ac228e7591c','2026-09-12 13:14:35'),(19,'Sunday — 6 hours','1h: Analyze the test: concept error, calculation error, silly mistake, time issue, or blind guess.',0,'146a4332-2269-489f-8f89-f4807eca89cf','2026-09-12 13:14:35'),(20,'Sunday — 6 hours','Add the most important mistakes to the mistake notebook.',0,'35c5db68-8da8-4239-9011-dc334309b26c','2026-09-12 13:14:35'),(21,'Discrete Mathematics','Learn definitions and standard properties.',0,'5bdbdf3f-280f-4af9-9c73-6c12dc5121ea','2026-09-12 13:14:35'),(22,'Discrete Mathematics','Derive/understand formulas instead of only memorizing them.',0,'e8f47ec2-0a16-4421-ac39-ba4a3eebb33b','2026-09-12 13:14:35'),(23,'Discrete Mathematics','Solve topic-wise PYQs, then mixed PYQs.',0,'bcec4259-a8b5-4842-9576-695f70b8ebbb','2026-09-12 13:14:35'),(24,'Discrete Mathematics','Maintain a one-page formula/property sheet.',0,'5eb59f61-81a4-470f-ba2d-36c12e8dcb1a','2026-09-12 13:14:35'),(25,'C Programming','Dry-run every code question on paper.',0,'d28490e9-cbfe-4cb4-a1d8-5e83f2197f29','2026-09-12 13:14:35'),(26,'C Programming','Track pointers, arrays, recursion, scope, and evaluation order carefully.',0,'4f5f88e6-6508-47cd-9c1d-b10c53958fd1','2026-09-12 13:14:35'),(27,'C Programming','Solve output-prediction questions under a timer.',0,'5cb9a5a3-a182-4f78-b8ff-fcf4de3d2066','2026-09-12 13:14:35'),(28,'C Programming','Record recurring traps separately.',0,'fdb0776c-f95d-45f2-9d04-a254a7616f14','2026-09-12 13:14:35'),(29,'Data Structures & Algorithms','Know the operation, invariant, and complexity of each structure/algorithm.',0,'e7672980-37fb-4a2d-9247-2923a9e01aaf','2026-09-12 13:14:35'),(30,'Data Structures & Algorithms','Trace algorithms manually on small examples.',0,'bdb0de9f-e3ed-405c-89fc-d89b6d526dac','2026-09-12 13:14:35'),(31,'Data Structures & Algorithms','Memorize complexity only after understanding why it occurs.',0,'16f86a01-6eda-4828-a18b-266b497f7583','2026-09-12 13:14:35'),(32,'Data Structures & Algorithms','For DP/graphs, practice identifying the correct state/representation before solving.',0,'e1de6912-4f38-4ba3-918a-65b652edb312','2026-09-12 13:14:35'),(33,'DBMS','Practice SQL by writing queries, not just reading them.',0,'2d5ca102-04bd-48d3-b571-627604d91568','2026-09-12 13:14:35'),(34,'DBMS','Solve functional-dependency and normalization problems step-by-step.',0,'6c359fee-77d1-41a4-936c-c73613b71ce9','2026-09-12 13:14:35'),(35,'DBMS','Practice transaction schedules and indexing questions.',0,'5ea4b860-0913-4d4d-a9bf-daedbe8d84f5','2026-09-12 13:14:35'),(36,'DBMS','Keep a compact SQL + normalization cheat sheet.',0,'342f4322-e1a6-4c39-a159-84528844f5e1','2026-09-12 13:14:35'),(37,'Operating Systems','Draw process/state, scheduling, synchronization, and memory diagrams.',0,'dcf0ab0c-3e44-4239-8bf6-cf96a5cb1b41','2026-09-12 13:14:35'),(38,'Operating Systems','Practice scheduling, paging, deadlock, and memory numericals.',0,'0326691e-6e6c-4072-91d5-8e4aeb9a7c08','2026-09-12 13:14:35'),(39,'Operating Systems','Compare similar concepts in a table.',0,'997d6a81-c5b8-4b52-9bae-0110ce0c66c8','2026-09-12 13:14:35'),(40,'Operating Systems','Re-solve every incorrect numerical after 2–3 days.',0,'f0c0c0f6-05a5-4a45-bc4b-af019730edc3','2026-09-12 13:14:35'),(41,'Computer Networks','Draw protocol-layer relationships.',0,'af1fefe2-749d-458c-a984-1b7fbad7d640','2026-09-12 13:14:35'),(42,'Computer Networks','Practice subnetting and numerical questions regularly.',0,'4844d685-87f3-4906-a528-5e4c479af98d','2026-09-12 13:14:35'),(43,'Computer Networks','Understand TCP/UDP, routing, flow control, and congestion mechanisms.',0,'950347b3-a552-4caf-bb89-fad682350b92','2026-09-12 13:14:35'),(44,'Computer Networks','Maintain a one-page formula + protocol table.',0,'c77f560b-4074-4c7c-aea6-759c7d01d7c0','2026-09-12 13:14:35'),(45,'COA','Practice binary/number representation and numerical questions.',0,'0dacb9f4-3710-4766-9a63-93c6318769cb','2026-09-12 13:14:35'),(46,'COA','Understand datapath, ISA, pipelining, cache, and memory hierarchy.',0,'2567da12-7a8b-49bf-8215-65f095c7f011','2026-09-12 13:14:35'),(47,'COA','Solve cache/pipeline numericals step-by-step.',0,'338bf0eb-b4b5-4ca0-b30c-e96a6466bbbb','2026-09-12 13:14:35'),(48,'COA','Record formulas and common assumptions.',0,'4ba528ae-e255-4647-9c5f-a8d9cb9c5c54','2026-09-12 13:14:35'),(49,'TOC / Compiler / Digital Logic','Draw automata and state transitions instead of only reading them.',0,'49b5dc05-d125-4d2d-81af-18c9b8ecd9e6','2026-09-12 13:14:35'),(50,'TOC / Compiler / Digital Logic','Practice grammar, parsing, and decidability questions systematically.',0,'33c05412-968f-405b-ba0e-cda91dddd44e','2026-09-12 13:14:35'),(51,'TOC / Compiler / Digital Logic','For Compiler, trace lexical/parsing/intermediate-code steps.',0,'eb9c999b-c622-4074-8cb1-3648aab947be','2026-09-12 13:14:35'),(52,'TOC / Compiler / Digital Logic','For Digital Logic, simplify expressions and draw circuits by hand.',0,'8ab91969-6fae-4585-b4f8-42fdce5febcd','2026-09-12 13:14:35'),(53,'Engineering Mathematics + General Aptitude','Revise formulas for 15–20 minutes on scheduled days.',0,'15420117-eafc-4cab-9829-bf9613180b91','2026-09-12 13:14:35'),(54,'Engineering Mathematics + General Aptitude','Solve mixed timed questions.',0,'21d84676-ec3d-42d3-ad0a-7d1040fb580a','2026-09-12 13:14:35'),(55,'Engineering Mathematics + General Aptitude','Maintain a separate formula sheet for quick final-week revision.',0,'2af071ca-9cd9-42dd-86a5-7c855832fc04','2026-09-12 13:14:35'),(56,'Daily completion rule','Concept completed',0,'1baee66e-bb42-4faf-af02-9a29de3c75fe','2026-09-12 13:14:35'),(57,'Daily completion rule','Target PYQs completed',0,'74a4a748-c705-440f-a666-486f177a9f17','2026-09-12 13:14:35'),(58,'Daily completion rule','Wrong questions re-solved',0,'5176ff91-355c-4625-827b-db7974c90fda','2026-09-12 13:14:35'),(59,'Daily completion rule','Error log updated',0,'1aba2954-81e5-47d6-bcf6-bae84067c2a2','2026-09-12 13:14:35'),(60,'Daily completion rule','3–5 key facts/formulas recalled without notes',0,'c0cdd76f-88c1-48b0-b397-8603292466c0','2026-09-12 13:14:35'),(61,'Weekly completion checklist','18 study hours',0,'c3013a67-4b2d-4b3c-859b-ff73543d52e6','2026-09-12 13:14:35'),(62,'Weekly completion checklist','All planned concepts completed',0,'6b644c6e-7d3f-4697-b5c7-b24b30cf354e','2026-09-12 13:14:35'),(63,'Weekly completion checklist','PYQs completed',0,'1243351b-4b83-48ca-946f-e13ff3c59d37','2026-09-12 13:14:35'),(64,'Weekly completion checklist','Weekly test completed',0,'c26347b8-6c58-4d24-8832-13ce5e5a7151','2026-09-12 13:14:35'),(65,'Weekly completion checklist','Test analyzed',0,'72827680-f74f-4f4f-9dfd-ae5dae5db4a3','2026-09-12 13:14:35'),(66,'Weekly completion checklist','Mistakes added to notebook',0,'719ee1f5-d859-4ddd-a7af-7becd82b1c57','2026-09-12 13:14:35'),(67,'Weekly completion checklist','Weak topics carried into next week\'s revision',0,'aef18f71-4416-4006-986d-eaa2a23d00bb','2026-09-12 13:14:35'),(68,'Notion Tasks','GATE CS 2027 — Day-wise Tasks',0,'36c4f4bb-655c-4f12-a3c6-7f91d5b9f277','2026-09-17 18:09:33'),(69,'Notion Tasks','Check the box to mark items as done',1,'3d5e1176-b072-8027-abd0-c5a2025992aa','2026-09-17 18:09:33'),(70,'Notion Tasks','See finished items in the “Done” view',0,'3d5e1176-b072-80c5-af2e-e554000ed18e','2026-09-17 18:09:33'),(71,'Notion Tasks','Click the blue New button to add a task',0,'3d5e1176-b072-80d3-912d-eb03c0f8e6fd','2026-09-17 18:09:33'),(72,'Notion Tasks','Click me to learn how to see your content your way',0,'3d5e1176-b072-80ed-a8f4-e1899231e195','2026-09-17 18:09:33'),(73,'Notion Tasks','Click me to see even more detail',0,'3d5e1176-b072-800c-aa08-ed7642d3575f','2026-09-17 18:09:33'),(74,'Notion Tasks','Click the due date to change it',0,'3d5e1176-b072-8031-a43b-e2a0df6b7148','2026-09-17 18:09:33'),(75,'Notion Tasks','Click me to learn how to hide checked items',0,'3d5e1176-b072-8087-b073-f5517c73b817','2026-09-17 18:09:33');
/*!40000 ALTER TABLE `checklists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `daily_plan`
--

DROP TABLE IF EXISTS `daily_plan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `daily_plan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_date` date NOT NULL,
  `weekday` varchar(10) DEFAULT NULL,
  `subject` varchar(150) DEFAULT NULL,
  `task` varchar(500) NOT NULL,
  `note` text DEFAULT NULL,
  `pyqs` int(11) DEFAULT 0,
  `status` enum('pending','done','skipped') DEFAULT 'pending',
  `notion_id` varchar(64) DEFAULT NULL,
  `notion_url` varchar(500) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_date` (`task_date`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=428 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `daily_plan`
--

LOCK TABLES `daily_plan` WRITE;
/*!40000 ALTER TABLE `daily_plan` DISABLE KEYS */;
INSERT INTO `daily_plan` VALUES (283,'2026-09-21','Monday','DM','Propositions, truth tables + 20 PYQs','',20,'pending','3d5e1176-b072-8165-849e-f9e3c27a5d83','https://app.notion.com/p/Propositions-truth-tables-20-PYQs-3d5e1176b0728165849ef9e3c27a5d83','2026-09-17 18:33:29'),(284,'2026-09-22','Tuesday','DM','Implication, equivalence, normal forms + 20 PYQs','',20,'pending','3d5e1176-b072-81a1-930f-cd736e76732d','https://app.notion.com/p/Implication-equivalence-normal-forms-20-PYQs-3d5e1176b07281a1930fcd736e76732d','2026-09-17 18:33:29'),(285,'2026-09-23','Wednesday','DM','Sets: operations, identities + 25 PYQs','',25,'pending','3d5e1176-b072-8173-8b7d-c80748a25557','https://app.notion.com/p/Sets-operations-identities-25-PYQs-3d5e1176b07281738b7dc80748a25557','2026-09-17 18:33:29'),(286,'2026-09-24','Thursday','DM','Relations: properties + 20 PYQs','',20,'pending','3d5e1176-b072-81c3-9de1-c5e8e21bcb95','https://app.notion.com/p/Relations-properties-20-PYQs-3d5e1176b07281c39de1c5e8e21bcb95','2026-09-17 18:33:29'),(287,'2026-09-25','Friday','C','C basics, I/O, operators + 20 questions','',20,'pending','3d5e1176-b072-8177-9338-eca64fa592d4','https://app.notion.com/p/C-basics-I-O-operators-20-questions-3d5e1176b07281779338eca64fa592d4','2026-09-17 18:33:29'),(288,'2026-09-26','Saturday','Revision / Mock','Weekly revision + 40 mixed PYQs','',0,'pending','3d5e1176-b072-8126-aaa5-dc87c03db642','https://app.notion.com/p/Weekly-revision-40-mixed-PYQs-3d5e1176b0728126aaa5dc87c03db642','2026-09-17 18:33:29'),(289,'2026-09-27','Sunday','Revision / Mock','6h: revision + 40 PYQs + mini test + analysis','',40,'pending','3d5e1176-b072-81c3-821c-e6d7292b495f','https://app.notion.com/p/6h-revision-40-PYQs-mini-test-analysis-3d5e1176b07281c3821ce6d7292b495f','2026-09-17 18:33:29'),(290,'2026-09-28','Monday','DM','Functions: types and composition + 20 PYQs','',20,'pending','3d5e1176-b072-8142-b9b7-e9d2ce48f7a5','https://app.notion.com/p/Functions-types-and-composition-20-PYQs-3d5e1176b0728142b9b7e9d2ce48f7a5','2026-09-17 18:33:29'),(291,'2026-09-29','Tuesday','DM','Counting: permutations and combinations + 25 PYQs','',25,'pending','3d5e1176-b072-813e-906d-f79de2deeec4','https://app.notion.com/p/Counting-permutations-and-combinations-25-PYQs-3d5e1176b072813e906df79de2deeec4','2026-09-17 18:33:29'),(292,'2026-09-30','Wednesday','DM','Pigeonhole + inclusion-exclusion + 20 PYQs','',20,'pending','3d5e1176-b072-81e9-83a3-f65649622a0c','https://app.notion.com/p/Pigeonhole-inclusion-exclusion-20-PYQs-3d5e1176b07281e983a3f65649622a0c','2026-09-17 18:33:29'),(293,'2026-10-01','Thursday','DM','Recurrence relations basics + 20 PYQs','',20,'pending','3d5e1176-b072-8154-a7aa-cf789f3d7b6a','https://app.notion.com/p/Recurrence-relations-basics-20-PYQs-3d5e1176b0728154a7aacf789f3d7b6a','2026-09-17 18:33:29'),(294,'2026-10-02','Friday','C','C pointers + arrays + 25 questions','',25,'pending','3d5e1176-b072-8178-a091-d88f620c62b2','https://app.notion.com/p/C-pointers-arrays-25-questions-3d5e1176b0728178a091d88f620c62b2','2026-09-17 18:33:29'),(295,'2026-10-03','Saturday','Revision / Mock','Revision + 40 mixed PYQs, focus weak areas','',0,'pending','3d5e1176-b072-81ff-8dff-f16f2e933fbf','https://app.notion.com/p/Revision-40-mixed-PYQs-focus-weak-areas-3d5e1176b07281ff8dfff16f2e933fbf','2026-09-17 18:33:29'),(296,'2026-10-04','Sunday','DM','6h: revision + 40 PYQs + DM/C sectional test + analysis','',40,'pending','3d5e1176-b072-81e4-b06c-c59668a06c38','https://app.notion.com/p/6h-revision-40-PYQs-DM-C-sectional-test-analysis-3d5e1176b07281e4b06cc59668a06c38','2026-09-17 18:33:29'),(297,'2026-10-05','Monday','DM','Graph theory basics + 20 PYQs','',20,'pending','3d5e1176-b072-813d-a77b-e4efd4d0fb7f','https://app.notion.com/p/Graph-theory-basics-20-PYQs-3d5e1176b072813da77be4efd4d0fb7f','2026-09-17 18:33:29'),(298,'2026-10-06','Tuesday','DM','Trees and properties + 20 PYQs','',20,'pending','3d5e1176-b072-81e9-ba87-d37933b8419d','https://app.notion.com/p/Trees-and-properties-20-PYQs-3d5e1176b07281e9ba87d37933b8419d','2026-09-17 18:33:29'),(299,'2026-10-07','Wednesday','DM','Connectivity and components + 20 PYQs','',20,'pending','3d5e1176-b072-81f7-8ddf-e896d29e2819','https://app.notion.com/p/Connectivity-and-components-20-PYQs-3d5e1176b07281f78ddfe896d29e2819','2026-09-17 18:33:29'),(300,'2026-10-08','Thursday','DM','BFS/DFS concepts + 20 PYQs','',20,'pending','3d5e1176-b072-8117-8327-f2b71a482c67','https://app.notion.com/p/BFS-DFS-concepts-20-PYQs-3d5e1176b07281178327f2b71a482c67','2026-09-17 18:33:29'),(301,'2026-10-09','Friday','C','C recursion + 25 questions','',25,'pending','3d5e1176-b072-81a3-9c95-f7972ea61282','https://app.notion.com/p/C-recursion-25-questions-3d5e1176b07281a39c95f7972ea61282','2026-09-17 18:33:29'),(302,'2026-10-10','Saturday','DM','C pointers, functions, recursion mix + 35 questions','',35,'pending','3d5e1176-b072-8128-8e32-c33cce87f8c0','https://app.notion.com/p/C-pointers-functions-recursion-mix-35-questions-3d5e1176b07281288e32c33cce87f8c0','2026-09-17 18:33:29'),(303,'2026-10-11','Sunday','Revision / Mock','6h: revision + 50 PYQs + sectional test + analysis','',50,'pending','3d5e1176-b072-811c-bf01-e7c24c8a61cb','https://app.notion.com/p/6h-revision-50-PYQs-sectional-test-analysis-3d5e1176b072811cbf01e7c24c8a61cb','2026-09-17 18:33:29'),(304,'2026-10-12','Monday','DM','DM revision: logic, sets, relations, functions + 30 PYQs','',30,'pending','3d5e1176-b072-81e0-90d9-def73f1b5657','https://app.notion.com/p/DM-revision-logic-sets-relations-functions-30-PYQs-3d5e1176b07281e090d9def73f1b5657','2026-09-17 18:33:29'),(305,'2026-10-13','Tuesday','DM','Counting + recurrence revision + 30 PYQs','',30,'pending','3d5e1176-b072-8130-a32e-e022576ff2cd','https://app.notion.com/p/Counting-recurrence-revision-30-PYQs-3d5e1176b0728130a32ee022576ff2cd','2026-09-17 18:33:29'),(306,'2026-10-14','Wednesday','DM','Graph theory revision + 30 PYQs','',30,'pending','3d5e1176-b072-81ef-a87d-c1dab7d5b00f','https://app.notion.com/p/Graph-theory-revision-30-PYQs-3d5e1176b07281efa87dc1dab7d5b00f','2026-09-17 18:33:29'),(307,'2026-10-15','Thursday','C','C revision: arrays, pointers, recursion + 35 questions','',35,'pending','3d5e1176-b072-8122-8b12-c1652191d23d','https://app.notion.com/p/C-revision-arrays-pointers-recursion-35-questions-3d5e1176b07281228b12c1652191d23d','2026-09-17 18:33:29'),(308,'2026-10-16','Friday','DM','DM + C timed mixed practice + review','',0,'pending','3d5e1176-b072-81eb-af76-ec6fccfa4e55','https://app.notion.com/p/DM-C-timed-mixed-practice-review-3d5e1176b07281ebaf76ec6fccfa4e55','2026-09-17 18:33:29'),(309,'2026-10-17','Saturday','Core','Phase 1 wrap-up + 60 mixed PYQs + short notes','',0,'pending','3d5e1176-b072-81ec-a032-ed5ae02f1446','https://app.notion.com/p/Phase-1-wrap-up-60-mixed-PYQs-short-notes-3d5e1176b07281eca032ed5ae02f1446','2026-09-17 18:33:29'),(310,'2026-10-18','Sunday','Revision / Mock','6h: Mock 1 + deep analysis + weak-topic list','',0,'pending','3d5e1176-b072-8185-9b75-cb5478ce6092','https://app.notion.com/p/6h-Mock-1-deep-analysis-weak-topic-list-3d5e1176b07281859b75cb5478ce6092','2026-09-17 18:33:29'),(311,'2026-10-19','Monday','C','Arrays, prefix sums, two pointers + 25 PYQs','',25,'pending','3dee1176-b072-816f-9550-f4b7ec0f2e56','https://app.notion.com/p/Arrays-prefix-sums-two-pointers-25-PYQs-3dee1176b072816f9550f4b7ec0f2e56','2026-09-17 18:33:29'),(312,'2026-10-20','Tuesday','DS','Linked lists: operations, reversal, cycles + 25 PYQs','',25,'pending','3dee1176-b072-81e3-8b00-f05c2b93bd64','https://app.notion.com/p/Linked-lists-operations-reversal-cycles-25-PYQs-3dee1176b07281e38b00f05c2b93bd64','2026-09-17 18:33:29'),(313,'2026-10-21','Wednesday','DS','Stacks, queues, deque + 25 PYQs','',25,'pending','3dee1176-b072-81ea-83eb-c68f0cd0a493','https://app.notion.com/p/Stacks-queues-deque-25-PYQs-3dee1176b07281ea83ebc68f0cd0a493','2026-09-17 18:33:29'),(314,'2026-10-22','Thursday','DS','Hashing, collisions, applications + 25 PYQs','',25,'pending','3dee1176-b072-81b9-b298-c55b8eebd5e3','https://app.notion.com/p/Hashing-collisions-applications-25-PYQs-3dee1176b07281b9b298c55b8eebd5e3','2026-09-17 18:33:29'),(315,'2026-10-23','Friday','Algo','Asymptotic analysis, Big-O/Theta/Omega + 25 PYQs','',25,'pending','3dee1176-b072-815f-92d6-ed8f94302679','https://app.notion.com/p/Asymptotic-analysis-Big-O-Theta-Omega-25-PYQs-3dee1176b072815f92d6ed8f94302679','2026-09-17 18:33:29'),(316,'2026-10-24','Saturday','DS','Weekly DS revision + 40–60 mixed questions + error log','',0,'pending','3dee1176-b072-81e3-b846-f2408eba41e1','https://app.notion.com/p/Weekly-DS-revision-40-60-mixed-questions-error-log-3dee1176b07281e3b846f2408eba41e1','2026-09-17 18:33:29'),(317,'2026-10-25','Sunday','DS','6h: revision + 40–60 PYQs + DS/Algo sectional test + analysis','',0,'pending','3dee1176-b072-811b-bad2-e6f135d5a4bd','https://app.notion.com/p/6h-revision-40-60-PYQs-DS-Algo-sectional-test-analysis-3dee1176b072811bbad2e6f135d5a4bd','2026-09-17 18:33:29'),(318,'2026-10-26','Monday','DS','Binary trees, traversals, properties + 25 PYQs','',25,'pending','3dee1176-b072-818d-bbf1-e01a5946b767','https://app.notion.com/p/Binary-trees-traversals-properties-25-PYQs-3dee1176b072818dbbf1e01a5946b767','2026-09-17 18:33:29'),(319,'2026-10-27','Tuesday','DS','BST operations and complexity + 25 PYQs','',25,'pending','3dee1176-b072-818d-9f94-ff918f339749','https://app.notion.com/p/BST-operations-and-complexity-25-PYQs-3dee1176b072818d9f94ff918f339749','2026-09-17 18:33:29'),(320,'2026-10-28','Wednesday','DS','Heaps and priority queues + 25 PYQs','',25,'pending','3dee1176-b072-8102-8491-f52947f16457','https://app.notion.com/p/Heaps-and-priority-queues-25-PYQs-3dee1176b07281028491f52947f16457','2026-09-17 18:33:29'),(321,'2026-10-29','Thursday','Core','Elementary sorting and searching + 25 PYQs','',25,'pending','3dee1176-b072-81f5-9dfe-d8b84a744dc1','https://app.notion.com/p/Elementary-sorting-and-searching-25-PYQs-3dee1176b07281f59dfed8b84a744dc1','2026-09-17 18:33:29'),(322,'2026-10-30','Friday','Core','Merge sort, quicksort, heapsort + 30 PYQs','',30,'pending','3dee1176-b072-814b-839b-cbec60400869','https://app.notion.com/p/Merge-sort-quicksort-heapsort-30-PYQs-3dee1176b072814b839bcbec60400869','2026-09-17 18:33:29'),(323,'2026-10-31','Saturday','DS','Weekly trees/sorting revision + 40–60 mixed questions','',0,'pending','3dee1176-b072-81b0-b8cd-e9f1f0218102','https://app.notion.com/p/Weekly-trees-sorting-revision-40-60-mixed-questions-3dee1176b07281b0b8cde9f1f0218102','2026-09-17 18:33:29'),(324,'2026-11-01','Sunday','Revision / Mock','6h: revision + 50 PYQs + sectional test + analysis','',50,'pending','3dee1176-b072-818a-9d28-e0c7037e220b','https://app.notion.com/p/6h-revision-50-PYQs-sectional-test-analysis-3dee1176b072818a9d28e0c7037e220b','2026-09-17 18:33:29'),(325,'2026-11-02','Monday','Core','Graph representations + BFS/DFS + 25 PYQs','',25,'pending','3dee1176-b072-81a5-a3d7-daf2cf37229b','https://app.notion.com/p/Graph-representations-BFS-DFS-25-PYQs-3dee1176b07281a5a3d7daf2cf37229b','2026-09-17 18:33:29'),(326,'2026-11-03','Tuesday','Algo','Shortest paths: Dijkstra/Bellman-Ford + 25 PYQs','',25,'pending','3dee1176-b072-812f-b227-e6f0cefe6818','https://app.notion.com/p/Shortest-paths-Dijkstra-Bellman-Ford-25-PYQs-3dee1176b072812fb227e6f0cefe6818','2026-09-17 18:33:29'),(327,'2026-11-04','Wednesday','Algo','Greedy algorithms + MST (Prim/Kruskal) + 25 PYQs','',25,'pending','3dee1176-b072-81b3-84bc-fbc249c0623c','https://app.notion.com/p/Greedy-algorithms-MST-Prim-Kruskal-25-PYQs-3dee1176b07281b384bcfbc249c0623c','2026-09-17 18:33:29'),(328,'2026-11-05','Thursday','Algo','Divide and conquer + 25 PYQs','',25,'pending','3dee1176-b072-81e3-837d-e2c33a44da25','https://app.notion.com/p/Divide-and-conquer-25-PYQs-3dee1176b07281e3837de2c33a44da25','2026-09-17 18:33:29'),(329,'2026-11-06','Friday','Algo','Dynamic programming: patterns and classic problems + 30 PYQs','',30,'pending','3dee1176-b072-811a-9649-eea10f7b166d','https://app.notion.com/p/Dynamic-programming-patterns-and-classic-problems-30-PYQs-3dee1176b072811a9649eea10f7b166d','2026-09-17 18:33:29'),(330,'2026-11-07','Saturday','Algo','Weekly algorithms revision + 40–60 mixed PYQs','',0,'pending','3dee1176-b072-81f7-87ab-d46546817769','https://app.notion.com/p/Weekly-algorithms-revision-40-60-mixed-PYQs-3dee1176b07281f787abd46546817769','2026-09-17 18:33:29'),(331,'2026-11-08','Sunday','DS','6h: DS/Algo mock + deep analysis + weak-topic list','',0,'pending','3dee1176-b072-81d6-b2d2-c2d629c123a6','https://app.notion.com/p/6h-DS-Algo-mock-deep-analysis-weak-topic-list-3dee1176b07281d6b2d2c2d629c123a6','2026-09-17 18:33:29'),(332,'2026-11-09','Monday','C','Mixed arrays/lists/stacks/queues + 50 timed PYQs','',50,'pending','3dee1176-b072-81a8-a376-e8923cd46503','https://app.notion.com/p/Mixed-arrays-lists-stacks-queues-50-timed-PYQs-3dee1176b07281a8a376e8923cd46503','2026-09-17 18:33:29'),(333,'2026-11-10','Tuesday','DS','Trees/BST/heaps/hashing + 50 timed PYQs','',50,'pending','3dee1176-b072-81ce-b883-e198f4011c17','https://app.notion.com/p/Trees-BST-heaps-hashing-50-timed-PYQs-3dee1176b07281ceb883e198f4011c17','2026-09-17 18:33:29'),(334,'2026-11-11','Wednesday','Core','Sorting/searching + 50 timed PYQs','',50,'pending','3dee1176-b072-813a-a8ba-ca5c1b2385e6','https://app.notion.com/p/Sorting-searching-50-timed-PYQs-3dee1176b072813aa8baca5c1b2385e6','2026-09-17 18:33:29'),(335,'2026-11-12','Thursday','Core','Graphs + 50 timed PYQs','',50,'pending','3dee1176-b072-81d4-ba29-c1c3bc38b655','https://app.notion.com/p/Graphs-50-timed-PYQs-3dee1176b07281d4ba29c1c3bc38b655','2026-09-17 18:33:29'),(336,'2026-11-13','Friday','Algo','DP/greedy/divide-conquer timed set + error-log repair','',0,'pending','3dee1176-b072-8181-950d-e3f0d838ea31','https://app.notion.com/p/DP-greedy-divide-conquer-timed-set-error-log-repair-3dee1176b0728181950de3f0d838ea31','2026-09-17 18:33:29'),(337,'2026-11-14','Saturday','DS','Full DS/Algo revision + 60 mixed PYQs','',0,'pending','3dee1176-b072-8132-a7ba-e72162d5116b','https://app.notion.com/p/Full-DS-Algo-revision-60-mixed-PYQs-3dee1176b0728132a7bae72162d5116b','2026-09-17 18:33:29'),(338,'2026-11-15','Sunday','Revision / Mock','6h: Mock 2 + deep analysis + revise top 10 weak areas','',0,'pending','3dee1176-b072-8175-ad77-c8fb94690ae6','https://app.notion.com/p/6h-Mock-2-deep-analysis-revise-top-10-weak-areas-3dee1176b0728175ad77c8fb94690ae6','2026-09-17 18:33:29'),(339,'2026-11-16','Monday','DBMS','DBMS ER model, keys, constraints + 25 PYQs','',25,'pending','3dee1176-b072-8162-9569-fdb630283dd3','https://app.notion.com/p/DBMS-ER-model-keys-constraints-25-PYQs-3dee1176b07281629569fdb630283dd3','2026-09-17 18:33:29'),(340,'2026-11-17','Tuesday','DBMS','Relational model + relational algebra + 25 PYQs','',25,'pending','3dee1176-b072-8156-9c18-eb8328e21b59','https://app.notion.com/p/Relational-model-relational-algebra-25-PYQs-3dee1176b07281569c18eb8328e21b59','2026-09-17 18:33:29'),(341,'2026-11-18','Wednesday','DBMS','SQL: joins, subqueries, aggregation + 30 PYQs','',30,'pending','3dee1176-b072-8142-8667-d0b5d661c88b','https://app.notion.com/p/SQL-joins-subqueries-aggregation-30-PYQs-3dee1176b07281428667d0b5d661c88b','2026-09-17 18:33:29'),(342,'2026-11-19','Thursday','DBMS','Functional dependencies and closure + 25 PYQs','',25,'pending','3dee1176-b072-81f0-9434-cc17685b1875','https://app.notion.com/p/Functional-dependencies-and-closure-25-PYQs-3dee1176b07281f09434cc17685b1875','2026-09-17 18:33:29'),(343,'2026-11-20','Friday','DBMS','Normalization: 1NF–BCNF + 30 PYQs','',30,'pending','3dee1176-b072-8156-ba0d-e461c8951b3e','https://app.notion.com/p/Normalization-1NF-BCNF-30-PYQs-3dee1176b0728156ba0de461c8951b3e','2026-09-17 18:33:29'),(344,'2026-11-21','Saturday','DBMS','Weekly DBMS revision + 40–60 mixed PYQs','',0,'pending','3dee1176-b072-815e-9b4b-c7278570aa43','https://app.notion.com/p/Weekly-DBMS-revision-40-60-mixed-PYQs-3dee1176b072815e9b4bc7278570aa43','2026-09-17 18:33:29'),(345,'2026-11-22','Sunday','DBMS','6h: DBMS sectional test + analysis + error log','',0,'pending','3dee1176-b072-814f-ac73-cdee9697f79d','https://app.notion.com/p/6h-DBMS-sectional-test-analysis-error-log-3dee1176b072814fac73cdee9697f79d','2026-09-17 18:33:29'),(346,'2026-11-23','Monday','OS','OS processes, threads, states, PCB + 25 PYQs','',25,'pending','3dee1176-b072-812a-ae8e-f7a9767ce4de','https://app.notion.com/p/OS-processes-threads-states-PCB-25-PYQs-3dee1176b072812aae8ef7a9767ce4de','2026-09-17 18:33:29'),(347,'2026-11-24','Tuesday','Algo','CPU scheduling algorithms + 25 PYQs','',25,'pending','3dee1176-b072-812e-892a-c3e8f646370a','https://app.notion.com/p/CPU-scheduling-algorithms-25-PYQs-3dee1176b072812e892ac3e8f646370a','2026-09-17 18:33:29'),(348,'2026-11-25','Wednesday','OS','Synchronization, semaphores, monitors + 25 PYQs','',25,'pending','3dee1176-b072-81b0-92b0-ed400cbf654c','https://app.notion.com/p/Synchronization-semaphores-monitors-25-PYQs-3dee1176b07281b092b0ed400cbf654c','2026-09-17 18:33:29'),(349,'2026-11-26','Thursday','OS','Deadlocks: detection, prevention, avoidance + 25 PYQs','',25,'pending','3dee1176-b072-817a-8421-f0566a0260a0','https://app.notion.com/p/Deadlocks-detection-prevention-avoidance-25-PYQs-3dee1176b072817a8421f0566a0260a0','2026-09-17 18:33:29'),(350,'2026-11-27','Friday','DBMS','DBMS transactions, serializability, locking + 30 PYQs','',30,'pending','3dee1176-b072-817e-b291-cb7553fd175a','https://app.notion.com/p/DBMS-transactions-serializability-locking-30-PYQs-3dee1176b072817eb291cb7553fd175a','2026-09-17 18:33:29'),(351,'2026-11-28','Saturday','DBMS','OS + DBMS weekly revision + 50 mixed PYQs','',0,'pending','3dee1176-b072-814c-9f03-c024632acc36','https://app.notion.com/p/OS-DBMS-weekly-revision-50-mixed-PYQs-3dee1176b072814c9f03c024632acc36','2026-09-17 18:33:29'),(352,'2026-11-29','Sunday','DBMS','6h: OS/DBMS mock + deep analysis','',0,'pending','3dee1176-b072-81a1-b453-c3eeaa1c555c','https://app.notion.com/p/6h-OS-DBMS-mock-deep-analysis-3dee1176b07281a1b453c3eeaa1c555c','2026-09-17 18:33:29'),(353,'2026-11-30','Monday','OS','OS memory management, paging, segmentation + 25 PYQs','',25,'pending','3dee1176-b072-8110-8b91-cc98a3abd421','https://app.notion.com/p/OS-memory-management-paging-segmentation-25-PYQs-3dee1176b07281108b91cc98a3abd421','2026-09-17 18:33:29'),(354,'2026-12-01','Tuesday','OS','Virtual memory and page replacement + 25 PYQs','',25,'pending','3dee1176-b072-8100-a04a-e12815088c72','https://app.notion.com/p/Virtual-memory-and-page-replacement-25-PYQs-3dee1176b0728100a04ae12815088c72','2026-09-17 18:33:29'),(355,'2026-12-02','Wednesday','OS','File systems, allocation, directories + 25 PYQs','',25,'pending','3dee1176-b072-81e8-a371-d68f88366f74','https://app.notion.com/p/File-systems-allocation-directories-25-PYQs-3dee1176b07281e8a371d68f88366f74','2026-09-17 18:33:29'),(356,'2026-12-03','Thursday','DS','DBMS indexing, B/B+ trees, hashing + 25 PYQs','',25,'pending','3dee1176-b072-813b-8e8f-e8a43053fef6','https://app.notion.com/p/DBMS-indexing-B-B-trees-hashing-25-PYQs-3dee1176b072813b8e8fe8a43053fef6','2026-09-17 18:33:29'),(357,'2026-12-04','Friday','DBMS','DBMS recovery + full DBMS revision + 30 PYQs','',30,'pending','3dee1176-b072-81b3-b7a3-ceda283960db','https://app.notion.com/p/DBMS-recovery-full-DBMS-revision-30-PYQs-3dee1176b07281b3b7a3ceda283960db','2026-09-17 18:33:29'),(358,'2026-12-05','Saturday','DBMS','OS + DBMS mixed revision + 50 PYQs','',50,'pending','3dee1176-b072-81b4-bf94-c00f39d366ea','https://app.notion.com/p/OS-DBMS-mixed-revision-50-PYQs-3dee1176b07281b4bf94c00f39d366ea','2026-09-17 18:33:29'),(359,'2026-12-06','Sunday','Revision / Mock','6h: sectional mock + analysis + error-log repair','',0,'pending','3dee1176-b072-81e7-847a-cdfa423c8d19','https://app.notion.com/p/6h-sectional-mock-analysis-error-log-repair-3dee1176b07281e7847acdfa423c8d19','2026-09-17 18:33:29'),(360,'2026-12-07','Monday','DBMS','OS+DBMS mixed timed PYQs + 50 questions','',50,'pending','3dee1176-b072-812c-9bb6-f5dce2895300','https://app.notion.com/p/OS-DBMS-mixed-timed-PYQs-50-questions-3dee1176b072812c9bb6f5dce2895300','2026-09-17 18:33:29'),(361,'2026-12-08','Tuesday','Core','Scheduling, synchronization, deadlock timed drill + 40 PYQs','',40,'pending','3dee1176-b072-8119-9066-de84826f8665','https://app.notion.com/p/Scheduling-synchronization-deadlock-timed-drill-40-PYQs-3dee1176b07281199066de84826f8665','2026-09-17 18:33:29'),(362,'2026-12-09','Wednesday','DBMS','SQL, functional dependencies, normalization + 40 PYQs','',40,'pending','3dee1176-b072-8173-92a5-d969b830bb45','https://app.notion.com/p/SQL-functional-dependencies-normalization-40-PYQs-3dee1176b072817392a5d969b830bb45','2026-09-17 18:33:29'),(363,'2026-12-10','Thursday','OS','Memory management + file systems timed drill + 40 PYQs','',40,'pending','3dee1176-b072-818d-a2f6-d1533cbe0727','https://app.notion.com/p/Memory-management-file-systems-timed-drill-40-PYQs-3dee1176b072818da2f6d1533cbe0727','2026-09-17 18:33:29'),(364,'2026-12-11','Friday','DBMS','Full OS+DBMS sectional drill + error-log repair','',0,'pending','3dee1176-b072-81e0-9e8f-fa2310c82b98','https://app.notion.com/p/Full-OS-DBMS-sectional-drill-error-log-repair-3dee1176b07281e09e8ffa2310c82b98','2026-09-17 18:33:29'),(365,'2026-12-12','Saturday','DBMS','Full OS+DBMS revision + 60 mixed PYQs','',0,'pending','3dee1176-b072-8174-ba4e-c46ec539b1d1','https://app.notion.com/p/Full-OS-DBMS-revision-60-mixed-PYQs-3dee1176b0728174ba4ec46ec539b1d1','2026-09-17 18:33:29'),(366,'2026-12-13','Sunday','Revision / Mock','6h: Mock 3 + deep analysis + weak-topic list','',0,'pending','3dee1176-b072-8147-b552-e91d614167c4','https://app.notion.com/p/6h-Mock-3-deep-analysis-weak-topic-list-3dee1176b0728147b552e91d614167c4','2026-09-17 18:33:29'),(367,'2026-12-14','Monday','CN','CN OSI/TCP-IP, encapsulation + 25 PYQs','',25,'pending','3dee1176-b072-81e7-98c2-d41f6cacad4e','https://app.notion.com/p/CN-OSI-TCP-IP-encapsulation-25-PYQs-3dee1176b07281e798c2d41f6cacad4e','2026-09-17 18:33:29'),(368,'2026-12-15','Tuesday','CN','Data link, Ethernet, framing, error detection + 25 PYQs','',25,'pending','3dee1176-b072-816d-bc9f-d1216d021209','https://app.notion.com/p/Data-link-Ethernet-framing-error-detection-25-PYQs-3dee1176b072816dbc9fd1216d021209','2026-09-17 18:33:29'),(369,'2026-12-16','Wednesday','CN','IPv4, subnetting, CIDR + 30 PYQs','',30,'pending','3dee1176-b072-8122-a242-d9f56801d932','https://app.notion.com/p/IPv4-subnetting-CIDR-30-PYQs-3dee1176b0728122a242d9f56801d932','2026-09-17 18:33:29'),(370,'2026-12-17','Thursday','Algo','Routing algorithms and routing tables + 25 PYQs','',25,'pending','3dee1176-b072-8113-9546-d29e6dbd237d','https://app.notion.com/p/Routing-algorithms-and-routing-tables-25-PYQs-3dee1176b07281139546d29e6dbd237d','2026-09-17 18:33:29'),(371,'2026-12-18','Friday','COA','COA number systems, signed arithmetic, representation + 25 PYQs','',25,'pending','3dee1176-b072-81cf-be56-e8f0f3166dd5','https://app.notion.com/p/COA-number-systems-signed-arithmetic-representation-25-PYQs-3dee1176b07281cfbe56e8f0f3166dd5','2026-09-17 18:33:29'),(372,'2026-12-19','Saturday','COA','CN + COA weekly revision + 50 mixed PYQs','',0,'pending','3dee1176-b072-8164-97cc-f698b1ee30d0','https://app.notion.com/p/CN-COA-weekly-revision-50-mixed-PYQs-3dee1176b072816497ccf698b1ee30d0','2026-09-17 18:33:29'),(373,'2026-12-20','Sunday','COA','6h: CN/COA sectional tests + analysis','',0,'pending','3dee1176-b072-8190-88a1-c6796adf5e46','https://app.notion.com/p/6h-CN-COA-sectional-tests-analysis-3dee1176b072819088a1c6796adf5e46','2026-09-17 18:33:29'),(374,'2026-12-21','Monday','CN','TCP/UDP, ports, sockets + 25 PYQs','',25,'pending','3dee1176-b072-81f2-b7b7-e97fceca9a53','https://app.notion.com/p/TCP-UDP-ports-sockets-25-PYQs-3dee1176b07281f2b7b7e97fceca9a53','2026-09-17 18:33:29'),(375,'2026-12-22','Tuesday','CN','Flow and congestion control + 25 PYQs','',25,'pending','3dee1176-b072-817f-acdc-e033e7598967','https://app.notion.com/p/Flow-and-congestion-control-25-PYQs-3dee1176b072817facdce033e7598967','2026-09-17 18:33:29'),(376,'2026-12-23','Wednesday','CN','DNS, HTTP, email, DHCP + 25 PYQs','',25,'pending','3dee1176-b072-812f-a510-d0fd1b727816','https://app.notion.com/p/DNS-HTTP-email-DHCP-25-PYQs-3dee1176b072812fa510d0fd1b727816','2026-09-17 18:33:29'),(377,'2026-12-24','Thursday','COA','ISA, instruction formats, addressing modes + 25 PYQs','',25,'pending','3dee1176-b072-8110-a504-e00986d98eeb','https://app.notion.com/p/ISA-instruction-formats-addressing-modes-25-PYQs-3dee1176b0728110a504e00986d98eeb','2026-09-17 18:33:29'),(378,'2026-12-25','Friday','Core','Datapath, control unit, instruction execution + 25 PYQs','',25,'pending','3dee1176-b072-8164-aac5-fb2aa2fed3bc','https://app.notion.com/p/Datapath-control-unit-instruction-execution-25-PYQs-3dee1176b0728164aac5fb2aa2fed3bc','2026-09-17 18:33:29'),(379,'2026-12-26','Saturday','COA','CN + COA revision + 50 mixed PYQs','',0,'pending','3dee1176-b072-8127-8310-e8eba1eccfe7','https://app.notion.com/p/CN-COA-revision-50-mixed-PYQs-3dee1176b07281278310e8eba1eccfe7','2026-09-17 18:33:29'),(380,'2026-12-27','Sunday','Revision / Mock','6h: sectional mock + analysis + error log','',0,'pending','3dee1176-b072-8159-8e2e-fbd2a38aac8e','https://app.notion.com/p/6h-sectional-mock-analysis-error-log-3dee1176b07281598e2efbd2a38aac8e','2026-09-17 18:33:29'),(381,'2026-12-28','Monday','COA','Pipelining, hazards, speedup + 25 PYQs','',25,'pending','3dee1176-b072-81cd-bb9d-eaeae8f91d06','https://app.notion.com/p/Pipelining-hazards-speedup-25-PYQs-3dee1176b07281cdbb9deaeae8f91d06','2026-09-17 18:33:29'),(382,'2026-12-29','Tuesday','COA','Cache mapping, hit/miss, AMAT + 25 PYQs','',25,'pending','3dee1176-b072-8186-9776-eb125951119c','https://app.notion.com/p/Cache-mapping-hit-miss-AMAT-25-PYQs-3dee1176b07281869776eb125951119c','2026-09-17 18:33:29'),(383,'2026-12-30','Wednesday','OS','Memory hierarchy + virtual memory + 25 PYQs','',25,'pending','3dee1176-b072-8175-a905-c424c9759b91','https://app.notion.com/p/Memory-hierarchy-virtual-memory-25-PYQs-3dee1176b0728175a905c424c9759b91','2026-09-17 18:33:29'),(384,'2026-12-31','Thursday','CN','CN IP/routing/TCP revision + 30 PYQs','',30,'pending','3dee1176-b072-819a-a16a-c44440efe15d','https://app.notion.com/p/CN-IP-routing-TCP-revision-30-PYQs-3dee1176b072819aa16ac44440efe15d','2026-09-17 18:33:29'),(385,'2027-01-01','Friday','COA','COA I/O, interrupts, DMA + 25 PYQs','',25,'pending','3dee1176-b072-8127-97f4-f22bc155d22d','https://app.notion.com/p/COA-I-O-interrupts-DMA-25-PYQs-3dee1176b072812797f4f22bc155d22d','2026-09-17 18:33:29'),(386,'2027-01-02','Saturday','COA','CN + COA weekly revision + 60 mixed PYQs','',0,'pending','3dee1176-b072-8135-8c4b-fda6401de3e7','https://app.notion.com/p/CN-COA-weekly-revision-60-mixed-PYQs-3dee1176b07281358c4bfda6401de3e7','2026-09-17 18:33:29'),(387,'2027-01-03','Sunday','COA','6h: CN/COA mock + deep analysis','',0,'pending','3dee1176-b072-81a4-ada7-cfe75500cf16','https://app.notion.com/p/6h-CN-COA-mock-deep-analysis-3dee1176b07281a4ada7cfe75500cf16','2026-09-17 18:33:29'),(388,'2027-01-04','Monday','CN','CN subnetting/routing numericals + 40 timed PYQs','',40,'pending','3dee1176-b072-81c1-937c-e45d29e14fb6','https://app.notion.com/p/CN-subnetting-routing-numericals-40-timed-PYQs-3dee1176b07281c1937ce45d29e14fb6','2026-09-17 18:33:29'),(389,'2027-01-05','Tuesday','COA','COA cache/pipeline numericals + 40 timed PYQs','',40,'pending','3dee1176-b072-817e-a243-f0f4265eea36','https://app.notion.com/p/COA-cache-pipeline-numericals-40-timed-PYQs-3dee1176b072817ea243f0f4265eea36','2026-09-17 18:33:29'),(390,'2027-01-06','Wednesday','CN','CN full protocol revision + 40 PYQs','',40,'pending','3dee1176-b072-811b-a105-ebd7c2f7ffee','https://app.notion.com/p/CN-full-protocol-revision-40-PYQs-3dee1176b072811ba105ebd7c2f7ffee','2026-09-17 18:33:29'),(391,'2027-01-07','Thursday','COA','COA full architecture revision + 40 PYQs','',40,'pending','3dee1176-b072-811b-b64a-f6ea718bb2e2','https://app.notion.com/p/COA-full-architecture-revision-40-PYQs-3dee1176b072811bb64af6ea718bb2e2','2026-09-17 18:33:29'),(392,'2027-01-08','Friday','COA','CN+COA timed mixed set + error-log repair','',0,'pending','3dee1176-b072-81a0-8bb8-fb5e6b3d679f','https://app.notion.com/p/CN-COA-timed-mixed-set-error-log-repair-3dee1176b07281a08bb8fb5e6b3d679f','2026-09-17 18:33:29'),(393,'2027-01-09','Saturday','COA','Full CN+COA revision + 60 mixed PYQs','',0,'pending','3dee1176-b072-81a1-9a3b-cc1d79da1b20','https://app.notion.com/p/Full-CN-COA-revision-60-mixed-PYQs-3dee1176b07281a19a3bcc1d79da1b20','2026-09-17 18:33:29'),(394,'2027-01-10','Sunday','Revision / Mock','6h: Mock 4 + deep analysis + weak-topic list','',0,'pending','3dee1176-b072-81eb-8cf2-c8eb362a4efa','https://app.notion.com/p/6h-Mock-4-deep-analysis-weak-topic-list-3dee1176b07281eb8cf2c8eb362a4efa','2026-09-17 18:33:29'),(395,'2027-01-11','Monday','TOC','TOC regular languages, DFA/NFA, regex + 25 PYQs','',25,'pending','3dee1176-b072-8120-ad18-dc1c69698ae4','https://app.notion.com/p/TOC-regular-languages-DFA-NFA-regex-25-PYQs-3dee1176b0728120ad18dc1c69698ae4','2026-09-17 18:33:29'),(396,'2027-01-12','Tuesday','Core','CFG, derivations, ambiguity, CNF + 25 PYQs','',25,'pending','3dee1176-b072-81c6-a133-e6db7080451e','https://app.notion.com/p/CFG-derivations-ambiguity-CNF-25-PYQs-3dee1176b07281c6a133e6db7080451e','2026-09-17 18:33:29'),(397,'2027-01-13','Wednesday','TOC','PDA and context-free languages + 25 PYQs','',25,'pending','3dee1176-b072-81d6-a23a-db33c16a0fc0','https://app.notion.com/p/PDA-and-context-free-languages-25-PYQs-3dee1176b07281d6a23adb33c16a0fc0','2026-09-17 18:33:29'),(398,'2027-01-14','Thursday','TOC','Turing machines, decidability, reducibility + 25 PYQs','',25,'pending','3dee1176-b072-810d-b12e-e29886f1d4f4','https://app.notion.com/p/Turing-machines-decidability-reducibility-25-PYQs-3dee1176b072810db12ee29886f1d4f4','2026-09-17 18:33:29'),(399,'2027-01-15','Friday','Compiler','Compiler lexical analysis + 25 PYQs','',25,'pending','3dee1176-b072-819d-a779-f929d9448a93','https://app.notion.com/p/Compiler-lexical-analysis-25-PYQs-3dee1176b072819da779f929d9448a93','2026-09-17 18:33:29'),(400,'2027-01-16','Saturday','TOC','TOC + Compiler weekly revision + 50 mixed PYQs','',0,'pending','3dee1176-b072-81f7-aa7e-eeef4348a1f2','https://app.notion.com/p/TOC-Compiler-weekly-revision-50-mixed-PYQs-3dee1176b07281f7aa7eeeef4348a1f2','2026-09-17 18:33:29'),(401,'2027-01-17','Sunday','TOC','6h: TOC/Compiler sectional tests + analysis','',0,'pending','3dee1176-b072-8103-8b27-d63a18e7e9b6','https://app.notion.com/p/6h-TOC-Compiler-sectional-tests-analysis-3dee1176b07281038b27d63a18e7e9b6','2026-09-17 18:33:29'),(402,'2027-01-18','Monday','Compiler','Compiler parsing: LL/LR + 30 PYQs','',30,'pending','3dee1176-b072-8119-ba98-d7be18ad9154','https://app.notion.com/p/Compiler-parsing-LL-LR-30-PYQs-3dee1176b0728119ba98d7be18ad9154','2026-09-17 18:33:29'),(403,'2027-01-19','Tuesday','Compiler','Syntax-directed translation + intermediate code + 25 PYQs','',25,'pending','3dee1176-b072-8132-a4b1-e24c10676f9d','https://app.notion.com/p/Syntax-directed-translation-intermediate-code-25-PYQs-3dee1176b0728132a4b1e24c10676f9d','2026-09-17 18:33:29'),(404,'2027-01-20','Wednesday','Core','Runtime environments + code generation + 25 PYQs','',25,'pending','3dee1176-b072-81fc-8c95-d6c59201d7ae','https://app.notion.com/p/Runtime-environments-code-generation-25-PYQs-3dee1176b07281fc8c95d6c59201d7ae','2026-09-17 18:33:29'),(405,'2027-01-21','Thursday','DM','Digital Logic: Boolean algebra, K-maps, combinational circuits + 30 PYQs','',30,'pending','3dee1176-b072-8126-a9a5-f46e17069f23','https://app.notion.com/p/Digital-Logic-Boolean-algebra-K-maps-combinational-circuits-30-PYQs-3dee1176b0728126a9a5f46e17069f23','2026-09-17 18:33:29'),(406,'2027-01-22','Friday','Core','Digital Logic: sequential circuits, FSMs, flip-flops + 30 PYQs','',30,'pending','3dee1176-b072-81e6-b012-e43446bbe2d2','https://app.notion.com/p/Digital-Logic-sequential-circuits-FSMs-flip-flops-30-PYQs-3dee1176b07281e6b012e43446bbe2d2','2026-09-17 18:33:29'),(407,'2027-01-23','Saturday','Compiler','Compiler + Digital Logic revision + 50 mixed PYQs','',0,'pending','3dee1176-b072-8187-a94b-e2601a525311','https://app.notion.com/p/Compiler-Digital-Logic-revision-50-mixed-PYQs-3dee1176b0728187a94be2601a525311','2026-09-17 18:33:29'),(408,'2027-01-24','Sunday','Compiler','6h: Compiler/Digital sectional tests + analysis','',0,'pending','3dee1176-b072-81f8-8bf8-e80bca13aae2','https://app.notion.com/p/6h-Compiler-Digital-sectional-tests-analysis-3dee1176b07281f88bf8e80bca13aae2','2026-09-17 18:33:29'),(409,'2027-01-25','Monday','TOC','TOC full revision + 30 PYQs + error log','',30,'pending','3dee1176-b072-8145-b824-e8bafcd5848c','https://app.notion.com/p/TOC-full-revision-30-PYQs-error-log-3dee1176b0728145b824e8bafcd5848c','2026-09-17 18:33:29'),(410,'2027-01-26','Tuesday','Compiler','Compiler full revision + 30 PYQs + error log','',30,'pending','3dee1176-b072-8108-ae3b-e7f6f5fdaaeb','https://app.notion.com/p/Compiler-full-revision-30-PYQs-error-log-3dee1176b0728108ae3be7f6f5fdaaeb','2026-09-17 18:33:29'),(411,'2027-01-27','Wednesday','Revision / Mock','Digital Logic full revision + 30 PYQs + error log','',30,'pending','3dee1176-b072-81c8-985b-cb967728c600','https://app.notion.com/p/Digital-Logic-full-revision-30-PYQs-error-log-3dee1176b07281c8985bcb967728c600','2026-09-17 18:33:29'),(412,'2027-01-28','Thursday','TOC','TOC+Compiler timed mixed PYQs + error-log repair','',0,'pending','3dee1176-b072-8197-8c12-e37d63011098','https://app.notion.com/p/TOC-Compiler-timed-mixed-PYQs-error-log-repair-3dee1176b07281978c12e37d63011098','2026-09-17 18:33:29'),(413,'2027-01-29','Friday','TOC','Digital+TOC timed mixed PYQs + error-log repair','',0,'pending','3dee1176-b072-81ea-b608-d9c8b0234f25','https://app.notion.com/p/Digital-TOC-timed-mixed-PYQs-error-log-repair-3dee1176b07281eab608d9c8b0234f25','2026-09-17 18:33:29'),(414,'2027-01-30','Saturday','Revision / Mock','Full Phase 5 revision + 60 mixed PYQs','',0,'pending','3dee1176-b072-8105-99ef-db05fd10a636','https://app.notion.com/p/Full-Phase-5-revision-60-mixed-PYQs-3dee1176b072810599efdb05fd10a636','2026-09-17 18:33:29'),(415,'2027-01-31','Sunday','Revision / Mock','6h: Mock 5 + deep analysis + final weak-topic list','',0,'pending','3dee1176-b072-81a1-8d29-fec8aabcb370','https://app.notion.com/p/6h-Mock-5-deep-analysis-final-weak-topic-list-3dee1176b07281a18d29fec8aabcb370','2026-09-17 18:33:29'),(416,'2027-02-01','Monday','Engg Math','Engineering Math: linear algebra + 30 PYQs','',30,'pending','3dee1176-b072-8191-88fd-dfce59592a3d','https://app.notion.com/p/Engineering-Math-linear-algebra-30-PYQs-3dee1176b072819188fddfce59592a3d','2026-09-17 18:33:29'),(417,'2027-02-02','Tuesday','Engg Math','Engineering Math: calculus + 30 PYQs','',30,'pending','3dee1176-b072-8103-bef0-c5d9be737b47','https://app.notion.com/p/Engineering-Math-calculus-30-PYQs-3dee1176b0728103bef0c5d9be737b47','2026-09-17 18:33:29'),(418,'2027-02-03','Wednesday','Engg Math','Engineering Math: probability/statistics + 30 PYQs','',30,'pending','3dee1176-b072-8146-8af3-e67fbf96f0d8','https://app.notion.com/p/Engineering-Math-probability-statistics-30-PYQs-3dee1176b07281468af3e67fbf96f0d8','2026-09-17 18:33:29'),(419,'2027-02-04','Thursday','Aptitude','General Aptitude: verbal + quantitative + 30 PYQs','',30,'pending','3dee1176-b072-8164-9dc8-cc45d08a517c','https://app.notion.com/p/General-Aptitude-verbal-quantitative-30-PYQs-3dee1176b07281649dc8cc45d08a517c','2026-09-17 18:33:29'),(420,'2027-02-05','Friday','','Full revision (CN+COA)',NULL,40,'pending','b1e4023f-3cda-4786-94d4-3bb86fa93dc6',NULL,'2026-09-17 18:33:29'),(421,'2027-02-06','Saturday','','Full revision (DM+TOC+Compiler+Digital)',NULL,40,'pending','17ae8526-94f6-4f6d-ac8b-8d5644d3e721',NULL,'2026-09-17 18:33:29'),(422,'2027-02-07','Sunday','','Full mock + deep analysis (2h+ if needed) + update mistake notebook',NULL,0,'pending','41a022e6-37a7-47ae-8f62-bb8a747e4168',NULL,'2026-09-17 18:33:29'),(423,'2027-02-08','Monday','','Repair day: top 10 mistake-notebook items',NULL,30,'pending','c894d205-e8ef-44aa-bde6-82027ca67016',NULL,'2026-09-17 18:33:29'),(424,'2027-02-09','Tuesday','','Mixed timed practice set (1h) + analysis + revise formulas',NULL,0,'pending','9d6ba07e-bf55-419a-a740-098e5359a442',NULL,'2026-09-17 18:33:29'),(425,'2027-02-10','Wednesday','','DBMS/OS/CN/COA quick notes',NULL,25,'pending','1572d4f7-fe13-4a5e-9a4e-9778c2ee917a',NULL,'2026-09-17 18:33:29'),(426,'2027-02-11','Thursday','','DSA + DM quick notes',NULL,25,'pending','3c745175-8d5e-4473-b303-6a59be4a5141',NULL,'2026-09-17 18:33:29'),(427,'2027-02-12','Friday','','Light revision only (2h max): formulas + mistake notebook + rest well',NULL,0,'pending','031d4f8a-516c-417f-8b4d-f2b51eb019a0',NULL,'2026-09-17 18:33:29');
/*!40000 ALTER TABLE `daily_plan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_id` int(11) DEFAULT NULL,
  `topic_id` int(11) DEFAULT NULL,
  `title` varchar(300) NOT NULL,
  `kind` enum('pdf','notion','link') DEFAULT 'pdf',
  `url` varchar(500) DEFAULT NULL,
  `filename` varchar(300) DEFAULT NULL,
  `descr` text DEFAULT NULL,
  `notion_id` varchar(64) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_subject` (`subject_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documents`
--

LOCK TABLES `documents` WRITE;
/*!40000 ALTER TABLE `documents` DISABLE KEYS */;
INSERT INTO `documents` VALUES (1,NULL,NULL,'Study Document 1','pdf',NULL,'document/1c29fc66-868a-4f54-be30-df0a813790b9.pdf',NULL,NULL,'2026-09-12 13:02:56'),(2,NULL,NULL,'Study Document 2','pdf',NULL,'document/2fd06899-b7f2-44c7-b859-e608cf490de4.pdf',NULL,NULL,'2026-09-12 13:02:56'),(3,NULL,NULL,'Study Document 3','pdf',NULL,'document/30aa7d74-582f-4314-ad40-c211dd49d268.pdf',NULL,NULL,'2026-09-12 13:02:56'),(4,NULL,NULL,'Study Document 4','pdf',NULL,'document/44297ff4-cbe0-4fc4-87e1-948f9daeb5b6.pdf',NULL,NULL,'2026-09-12 13:02:56'),(5,NULL,NULL,'Study Document 5','pdf',NULL,'document/7290303c-b37a-40c7-b486-a298ffe6aa60.pdf',NULL,NULL,'2026-09-12 13:02:56'),(6,NULL,NULL,'Study Document 6','pdf',NULL,'document/8d2a418c-7299-40d9-bb15-9ea32e768a88.pdf',NULL,NULL,'2026-09-12 13:02:56'),(7,NULL,NULL,'Study Document 7','pdf',NULL,'document/92030e28-da6b-4f49-8920-ec1ed0b61684.pdf',NULL,NULL,'2026-09-12 13:02:56'),(8,NULL,NULL,'Study Document 8','pdf',NULL,'document/a483295c-2895-4375-bfa7-21b750de1c8b.pdf',NULL,NULL,'2026-09-12 13:02:56'),(9,NULL,NULL,'Study Document 9','pdf',NULL,'document/acc041c0-74ff-4608-9ef9-ab759d0953f2.pdf',NULL,NULL,'2026-09-12 13:02:56'),(10,NULL,NULL,'Study Document 10','pdf',NULL,'document/e71de686-d85c-4910-a7c8-f5623dfb17d1.pdf',NULL,NULL,'2026-09-12 13:02:56'),(11,NULL,NULL,'Study Document 11','pdf',NULL,'document/e751d87f-9074-4f6b-83a2-e97c4e168117.pdf',NULL,NULL,'2026-09-12 13:02:56'),(12,NULL,NULL,'Study Document 12','pdf',NULL,'document/fcb3fb4a-2ae6-4a7d-8b2f-280497391bb1.pdf',NULL,NULL,'2026-09-12 13:02:56'),(13,NULL,NULL,'­ƒÄ» GATE CS 2027 ÔÇö 80ÔÇô90 Marks Plan','notion','https://app.notion.com/p/GATE-CS-2027-80-90-Marks-Plan-3d5e1176b072819bae83e42557dc8f29',NULL,'Main Notion study plan',NULL,'2026-09-12 13:02:56'),(14,NULL,NULL,'­ƒôà GATE CS 2027 ÔÇö Day-by-Day Calendar','notion','https://app.notion.com/p/GATE-CS-2027-Day-by-Day-Calendar-3d5e1176b072819bae83e42557dc8f29',NULL,'Daily schedule from 14 Sep',NULL,'2026-09-12 13:02:56');
/*!40000 ALTER TABLE `documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mcqs`
--

DROP TABLE IF EXISTS `mcqs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mcqs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_id` int(11) NOT NULL,
  `topic_id` int(11) DEFAULT NULL,
  `phase_id` int(11) DEFAULT NULL,
  `question` text NOT NULL,
  `opt_a` varchar(500) NOT NULL,
  `opt_b` varchar(500) NOT NULL,
  `opt_c` varchar(500) NOT NULL,
  `opt_d` varchar(500) NOT NULL,
  `answer` char(1) NOT NULL,
  `explanation` text DEFAULT NULL,
  `difficulty` enum('E','M','H') DEFAULT 'M',
  `status` enum('active','archived') DEFAULT 'active',
  `source` varchar(30) DEFAULT 'manual',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_subject` (`subject_id`),
  KEY `idx_mcq_phase` (`phase_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mcqs`
--

LOCK TABLES `mcqs` WRITE;
/*!40000 ALTER TABLE `mcqs` DISABLE KEYS */;
INSERT INTO `mcqs` VALUES (1,6,NULL,NULL,'How many edges does a complete graph with n vertices have?','n','n(n-1)/2','n(n+1)/2','2n','B','Every pair of distinct vertices is connected, giving C(n,2) = n(n-1)/2 edges.','E','active','manual','2026-09-12 13:02:56'),(2,1,NULL,NULL,'Which of the following has the worst-case time complexity O(n┬▓)?','Merge Sort','Quick Sort','Heap Sort','Binary Search','B','Quick Sort degrades to O(n┬▓) when the pivot choice is consistently poor (already sorted input).','E','active','manual','2026-09-12 13:02:56'),(3,3,NULL,NULL,'Which normal form removes transitive dependencies that depend on a candidate key?','1NF','2NF','3NF','BCNF','C','3NF removes transitive dependencies on non-prime attributes.','E','active','manual','2026-09-12 13:02:56'),(4,4,NULL,NULL,'Which scheduling algorithm can cause starvation?','FCFS','Round Robin','Priority Scheduling','Shortest Job Next','C','Low-priority processes can starve indefinitely under priority scheduling without aging.','M','active','manual','2026-09-12 13:02:56'),(5,5,NULL,NULL,'Which IPv4 class is 192.168.1.1 in?','Class A','Class B','Class C','Class D','C','192ÔÇô223 is Class C; private range 192.168.0.0/16 used for local networks.','E','active','manual','2026-09-12 13:02:56'),(6,7,NULL,NULL,'Which language is NOT recursively enumerable?','Halting problem language','All regular languages','Complements of halting problem','All context-free languages','C','The complement of the halting problem is not even recursively enumerable.','H','active','manual','2026-09-12 13:02:56'),(7,8,NULL,NULL,'In which pipeline stage is the instruction decoded?','IF','ID','EX','WB','B','Instruction Decode (ID) stage decodes the fetched instruction.','E','active','manual','2026-09-12 13:02:56'),(8,10,NULL,NULL,'Which gate implements NAND as AND followed by NOT?','XNOR','NAND','NOR','XOR','B','NAND = AND + NOT. It is functionally complete.','E','active','manual','2026-09-12 13:02:56'),(9,5,NULL,NULL,'A subnet mask of 255.255.255.0 on the network 192.168.1.0 has how many usable host addresses per subnet?','254','256','255','251','A','2^8 - 2 = 254 usable hosts (network + broadcast excluded).','E','active','manual','2026-09-12 13:02:56'),(10,2,NULL,NULL,'What is the time complexity of binary search on a sorted array of size n?','O(1)','O(log n)','O(n)','O(n log n)','B','Each comparison halves the search space ÔåÆ O(log n).','E','active','manual','2026-09-12 13:02:56'),(11,1,NULL,NULL,'In a binary search tree, to delete a node that has two children, which replacement strategy is conventionally used?','Replace the node with its inorder predecessor and delete the predecessor node','Replace the node with its inorder successor and delete the successor node','Replace the node with its left child','Replace the node with its right child','B','The standard method is to replace the node with its inorder successor (the smallest node in its right subtree) and then delete that successor, which has at most one child, preserving the BST property.','M','active','groq','2026-09-12 14:33:15'),(12,1,NULL,NULL,'A max‑heap is stored in an array starting at index 1. For an element at index i (i>1), which formula gives the index of its parent?','i/2','(i-1)/2','floor(i/2)','floor((i-1)/2)','C','In a 1‑based array representation of a heap, the parent of node i is located at floor(i/2). Integer division in most programming languages implements this directly.','M','active','groq','2026-09-12 14:33:16'),(13,1,NULL,NULL,'Solve the recurrence T(n)=2T(n/2)+n\\log n (for n>1, T(1)=Θ(1)). What is the asymptotic bound for T(n)?','Θ(n)','Θ(n\\log n)','Θ(n\\log^2 n)','Θ(n^2)','C','Here a=2, b=2, so n^{log_b a}=n. f(n)=n\\log n = Θ(n^{log_b a}\\log^1 n). By the Master theorem case 2 with k=1, T(n)=Θ(n\\log^{k+1} n)=Θ(n\\log^2 n).','M','active','groq','2026-09-12 14:33:16'),(14,1,NULL,NULL,'In merge sort, the recurrence for the worst‑case number of element comparisons is T(n)=2T(n/2)+n‑1. What is T(8)?','7','12','17','19','C','T(1)=0; T(2)=2·0+1=1; T(4)=2·1+3=5; T(8)=2·5+7=17 comparisons.','M','active','groq','2026-09-12 14:33:34'),(15,1,NULL,NULL,'Given only a pointer to a node (not the head) in a singly linked list, which method correctly deletes that node?','Copy data from next node and delete next node','Traverse from head to find previous node','Cannot delete','Set node\'s next to NULL','A','By copying the successor\'s data into the given node and then deleting the successor, the list remains linked without needing the head.','M','active','groq','2026-09-12 14:33:34'),(16,1,NULL,NULL,'The inorder traversal of a BST yields a sorted sequence. For a BST containing the keys {20,8,22,4,12,10,14}, what is the 4th smallest key?','10','12','14','20','B','Inorder yields 4,8,10,12,14,20,22; the 4th element is 12.','M','active','groq','2026-09-12 14:33:34'),(17,1,NULL,NULL,'In an undirected unweighted graph with vertices A‑E and edges A‑B, A‑C, B‑D, C‑D, D‑E, a BFS is started at A. Which vertex is the third one visited?','B','C','D','E','C','BFS order from A (assuming alphabetical adjacency) is A, B, C, then D (distance 2), so D is visited third.','M','active','groq','2026-09-12 14:33:34'),(19,1,NULL,NULL,'Given an array A[0..n-1] of distinct integers, which of the following algorithms correctly finds the maximum element in O(log n) time?','Linear scan from left to right','Binary search assuming the array is sorted','Repeatedly divide the array into halves and compare the maximum of each half','Use a min‑heap to extract the maximum','C','Dividing the array recursively and comparing the maxima of the two halves yields a recurrence T(n)=T(n/2)+O(1), solving to O(log n). Linear scan is O(n), binary search requires sorted order, and a min‑heap gives O(log n) per extraction but building it is O(n).','M','active','groq','2026-09-19 13:15:52'),(20,1,NULL,NULL,'In a singly linked list, Floyd’s cycle‑finding algorithm (tortoise‑and‑hare) uses two pointers moving at different speeds. If the list contains a cycle of length μ, after how many steps will the two pointers first meet?','μ steps','2μ steps','At most μ steps','At most 2μ steps','D','The fast pointer moves 2 steps while the slow moves 1 step each iteration, so their relative speed is 1 step per iteration. They meet after at most μ iterations, i.e., at most 2μ individual pointer moves. Hence option D.','M','active','groq','2026-09-19 13:15:53'),(21,1,NULL,NULL,'Which of the following statements about building a binary max‑heap from an unsorted array of n elements is FALSE?','The build‑heap operation runs in Θ(n) time','The heap property is established by percolating down from the last internal node','The height of the heap is ⌊log₂ n⌋','All leaf nodes must be examined during heap construction','D','During heap construction only internal nodes (⌊n/2⌋ of them) are processed; leaf nodes are already heaps of size one and need not be examined. Hence statement D is false. The other statements are true.','M','active','groq','2026-09-19 13:15:53'),(22,1,NULL,NULL,'Consider the recurrence T(n)=T(n‑1)+T(n‑2)+Θ(1) with T(0)=T(1)=Θ(1). Which asymptotic bound correctly describes T(n)?','Θ(n)','Θ(n log n)','Θ(2^n)','Θ(n^2)','C','The recurrence is identical to the Fibonacci recurrence, whose solution grows as the golden ratio to the power n, i.e., Θ(φ^n) which is Θ(2^n) up to constant factors. Therefore option C is correct.','M','active','groq','2026-09-19 13:15:53');
/*!40000 ALTER TABLE `mcqs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2021-07-04-041948','CodeIgniter\\Settings\\Database\\Migrations\\CreateSettingsTable','default','CodeIgniter\\Settings',1789802196,1),(2,'2026-09-19-124700','App\\Database\\Migrations\\AddPhaseIdToMcqs','default','App',1789802206,2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mock_targets`
--

DROP TABLE IF EXISTS `mock_targets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mock_targets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `period` varchar(60) NOT NULL,
  `target_min` int(11) DEFAULT 0,
  `target_max` int(11) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `notion_id` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_period` (`period`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mock_targets`
--

LOCK TABLES `mock_targets` WRITE;
/*!40000 ALTER TABLE `mock_targets` DISABLE KEYS */;
INSERT INTO `mock_targets` VALUES (1,'October',30,40,1,NULL),(2,'November',40,50,2,NULL),(3,'December',50,60,3,NULL),(4,'Early January',60,70,4,NULL),(5,'Late January',70,80,5,NULL),(6,'GATE 2027',80,90,6,NULL);
/*!40000 ALTER TABLE `mock_targets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notion_cache`
--

DROP TABLE IF EXISTS `notion_cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notion_cache` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nkey` varchar(120) NOT NULL,
  `payload` longtext DEFAULT NULL,
  `synced_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nkey` (`nkey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notion_cache`
--

LOCK TABLES `notion_cache` WRITE;
/*!40000 ALTER TABLE `notion_cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `notion_cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notion_map`
--

DROP TABLE IF EXISTS `notion_map`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notion_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `local_table` varchar(60) NOT NULL,
  `local_id` int(11) NOT NULL,
  `notion_type` varchar(40) DEFAULT NULL,
  `notion_id` varchar(64) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_notion` (`notion_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notion_map`
--

LOCK TABLES `notion_map` WRITE;
/*!40000 ALTER TABLE `notion_map` DISABLE KEYS */;
INSERT INTO `notion_map` VALUES (1,'topics',1,'block','005a3a3b-264d-4f4b-a1e9-f6b0e128e94e','2026-09-12 13:14:20'),(2,'topics',2,'block','feb0774e-ef36-4eb9-96d5-75b8df916eb5','2026-09-12 13:14:20'),(3,'topics',3,'block','1b1ab4a1-6c1a-4be7-812c-1af679b04f8e','2026-09-12 13:14:20'),(4,'topics',4,'block','3c8951d2-fe4c-497d-af55-cbacb5acc5e9','2026-09-12 13:14:28'),(5,'topics',5,'block','04751dcb-4a43-4eef-b9fd-cb75e6217385','2026-09-12 13:14:28'),(6,'topics',6,'block','d51e7bdf-de6f-47fc-a2f4-9acb86f7c220','2026-09-12 13:14:28'),(7,'topics',7,'block','ca8de64c-b307-4899-96c7-be58971d989a','2026-09-12 13:14:28'),(8,'topics',8,'block','71e7f8c9-68c8-41d4-9ac5-9c37abe9f239','2026-09-12 13:14:28'),(9,'topics',9,'block','01e235a9-b09e-41f0-8682-521513d60536','2026-09-12 13:14:28'),(10,'topics',10,'block','e6847b82-1201-4327-9e71-73a60ce6f245','2026-09-12 13:14:28'),(11,'topics',11,'block','702ef493-77fb-43a5-a4b9-18e3bb450ce3','2026-09-12 13:14:28'),(12,'topics',12,'block','65326f2c-643c-418d-a868-e6a7d4743a9e','2026-09-12 13:14:28'),(13,'topics',13,'block','d4e7be75-289c-4fcb-86f2-fb44cb9d7806','2026-09-12 13:14:28'),(14,'topics',14,'block','10108c72-f518-4848-b957-06fea74508f6','2026-09-12 13:14:28'),(15,'topics',15,'block','000e9154-1a2e-42fa-ad45-e0e9db5694b0','2026-09-12 13:14:28'),(16,'topics',16,'block','7a30b1bb-bb0f-48b7-a249-ef28efb4bddb','2026-09-12 13:14:28'),(17,'topics',17,'block','6269b4d9-847d-47e1-8975-33a7343e39ea','2026-09-12 13:14:28'),(18,'topics',18,'block','8c9d1fd0-d570-43ab-9a72-b9be37f9798d','2026-09-12 13:14:28'),(19,'topics',19,'block','951dbb7f-4b61-4e68-889b-e8b6b4ba5f10','2026-09-12 13:14:28'),(20,'topics',20,'block','98f1b85e-a73b-40b1-9bf8-361863b7065f','2026-09-12 13:14:28'),(21,'topics',21,'block','de69dd32-7041-4a93-bd4b-301aade36edf','2026-09-12 13:14:28'),(22,'topics',22,'block','9af04091-02ee-4bd0-9b47-ab3aa9f85a93','2026-09-12 13:14:28'),(23,'topics',23,'block','aec6c155-c662-49fa-a2d4-fe2146b71ee7','2026-09-12 13:14:28'),(24,'topics',24,'block','84dcd640-dd7d-4616-bff8-101124568967','2026-09-12 13:14:28');
/*!40000 ALTER TABLE `notion_map` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phases`
--

DROP TABLE IF EXISTS `phases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `phases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `goal` text DEFAULT NULL,
  `notion_id` varchar(64) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `phases`
--

LOCK TABLES `phases` WRITE;
/*!40000 ALTER TABLE `phases` DISABLE KEYS */;
INSERT INTO `phases` VALUES (2,'Phase 1 — 21 Sep to 18 Oct','2026-09-21','2026-10-18',1,NULL,'2444201a-84cd-4549-b465-9a76ed3207fc','2026-09-12 13:14:20'),(3,'Phase 2 — 19 Oct to 15 Nov','2026-10-19','2026-11-15',2,NULL,'c631515d-6bfb-4f6f-ba2c-ac563c47cc3b','2026-09-12 13:14:28'),(4,'Phase 3 — 16 Nov to 13 Dec','2026-11-16','2026-12-13',3,NULL,'b35661c7-26ca-4e76-aff7-c4073d0c9d15','2026-09-12 13:14:28'),(5,'Phase 4 — 14 Dec to 10 Jan','2026-12-14','2027-01-10',4,NULL,'d0b58ca9-090b-4fce-a428-166680017a75','2026-09-12 13:14:28'),(6,'Phase 5 — 11 Jan to 31 Jan','2027-01-11','2027-01-31',5,NULL,'4d55a052-8bfb-49d6-ba56-2198af7c3b79','2026-09-12 13:14:28'),(7,'25 Jan → Exam','2027-01-25',NULL,0,NULL,'f46a4ae1-f841-4968-a64b-3b144d5ade0c','2026-09-12 13:14:28'),(17,'Phase 1 — 21 Sep to 11 Oct','2026-09-21','2026-10-11',0,NULL,'2444201a-84cd-4549-b465-9a76ed3207fc','2026-09-17 18:09:34'),(18,'Phase 6 — 1 Feb → Exam','2027-02-01','2027-02-06',6,NULL,NULL,'2026-09-17 18:41:28');
/*!40000 ALTER TABLE `phases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quiz_sessions`
--

DROP TABLE IF EXISTS `quiz_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quiz_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_type` varchar(20) NOT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `total` int(11) NOT NULL DEFAULT 0,
  `correct` int(11) NOT NULL DEFAULT 0,
  `attempted` int(11) NOT NULL DEFAULT 0,
  `seconds` int(11) DEFAULT 0,
  `comment` varchar(255) DEFAULT NULL,
  `xp_earned` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_date` (`created_at`),
  KEY `idx_subject` (`subject_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quiz_sessions`
--

LOCK TABLES `quiz_sessions` WRITE;
/*!40000 ALTER TABLE `quiz_sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `quiz_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `skey` varchar(60) NOT NULL,
  `svalue` text DEFAULT NULL,
  PRIMARY KEY (`skey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES ('daily_streak','0'),('exam_date','2027-02-06'),('groq_api_key',''),('groq_model','openai/gpt-oss-120b'),('last_active_date',''),('notion_calendar_page','7eaffc35-b21b-49d8-942d-a238d562359a'),('notion_database_id','7eaffc35-b21b-49d8-942d-a238d562359a'),('notion_plan_page','3d5e1176b072819bae83e42557dc8f29'),('notion_synced_at','2026-09-17 13:10:36'),('notion_synced_map','plan:1'),('notion_token',''),('target_score','85'),('weekly_hour_goal','18');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `study_sessions`
--

DROP TABLE IF EXISTS `study_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `study_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_date` date NOT NULL,
  `minutes` int(11) NOT NULL DEFAULT 0,
  `subject_id` int(11) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `source` enum('app','extension') DEFAULT 'app',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_date` (`session_date`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `study_sessions`
--

LOCK TABLES `study_sessions` WRITE;
/*!40000 ALTER TABLE `study_sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `study_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subjects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `code` varchar(40) DEFAULT NULL,
  `priority` int(11) DEFAULT 0,
  `color` varchar(20) DEFAULT '#6366f1',
  `icon` varchar(10) DEFAULT '­ƒôÿ',
  `notion_id` varchar(64) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subjects`
--

LOCK TABLES `subjects` WRITE;
/*!40000 ALTER TABLE `subjects` DISABLE KEYS */;
INSERT INTO `subjects` VALUES (1,'Algorithms','ALGO',1,'#4f46e5','­ƒº«',NULL,'2026-09-12 13:02:56','2026-09-12 13:02:56'),(2,'Data Structures','DS',2,'#0ea5e9','­ƒî▓',NULL,'2026-09-12 13:02:56','2026-09-12 13:02:56'),(3,'DBMS','DBMS',3,'#10b981','­ƒùä´©Å',NULL,'2026-09-12 13:02:56','2026-09-12 13:02:56'),(4,'Operating Systems','OS',4,'#f59e0b','ÔÜÖ´©Å',NULL,'2026-09-12 13:02:56','2026-09-12 13:02:56'),(5,'Computer Networks','CN',5,'#ef4444','­ƒîÉ',NULL,'2026-09-12 13:02:56','2026-09-12 13:02:56'),(6,'Discrete Mathematics','DM',6,'#8b5cf6','Ôèó',NULL,'2026-09-12 13:02:56','2026-09-12 13:02:56'),(7,'Theory of Computation','TOC',7,'#ec4899','╬╗',NULL,'2026-09-12 13:02:56','2026-09-12 13:02:56'),(8,'COA','COA',8,'#f97316','­ƒûÑ´©Å',NULL,'2026-09-12 13:02:56','2026-09-12 13:15:44'),(9,'Engineering Mathematics','MATHS',9,'#14b8a6','Ôêæ',NULL,'2026-09-12 13:02:56','2026-09-12 13:02:56'),(10,'Digital Logic','DL',10,'#a855f7','­ƒöó',NULL,'2026-09-12 13:02:56','2026-09-12 13:02:56'),(11,'Compiler','COMPILER',11,'#06b6d4','­ƒº¼',NULL,'2026-09-12 13:02:56','2026-09-12 13:15:44'),(12,'General Aptitude','APT',12,'#64748b','­ƒºá',NULL,'2026-09-12 13:02:56','2026-09-12 13:02:56');
/*!40000 ALTER TABLE `subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tests_factories`
--

DROP TABLE IF EXISTS `tests_factories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tests_factories` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `name` varchar(31) NOT NULL,
  `uid` varchar(31) NOT NULL,
  `class` varchar(63) NOT NULL,
  `icon` varchar(31) NOT NULL,
  `summary` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `uid` (`uid`),
  KEY `deleted_at_id` (`deleted_at`,`id`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tests_factories`
--

LOCK TABLES `tests_factories` WRITE;
/*!40000 ALTER TABLE `tests_factories` DISABLE KEYS */;
INSERT INTO `tests_factories` VALUES (1,'Test Factory','test001','Factories\\Tests\\NewFactory','fas fa-puzzle-piece','Longer sample text for testing',NULL,'2026-09-19 16:03:30','2026-09-19 16:03:30'),(2,'Widget Factory','widget','Factories\\Tests\\WidgetPlant','fas fa-puzzle-piece','Create widgets in your factory',NULL,NULL,NULL),(3,'Evil Factory','evil-maker','Factories\\Evil\\MyFactory','fas fa-book-dead','Abandon all hope, ye who enter here',NULL,NULL,NULL);
/*!40000 ALTER TABLE `tests_factories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tests_migrations`
--

DROP TABLE IF EXISTS `tests_migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tests_migrations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tests_migrations`
--

LOCK TABLES `tests_migrations` WRITE;
/*!40000 ALTER TABLE `tests_migrations` DISABLE KEYS */;
INSERT INTO `tests_migrations` VALUES (12,'2020-02-22-222222','Tests\\Support\\Database\\Migrations\\ExampleMigration','tests','Tests\\Support',1789814010,1);
/*!40000 ALTER TABLE `tests_migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `topics`
--

DROP TABLE IF EXISTS `topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `topics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `phase_id` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` enum('locked','active','done') DEFAULT 'locked',
  `is_lesson` tinyint(4) DEFAULT 0,
  `notion_id` varchar(64) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_phase` (`phase_id`),
  KEY `idx_subject` (`subject_id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `topics`
--

LOCK TABLES `topics` WRITE;
/*!40000 ALTER TABLE `topics` DISABLE KEYS */;
INSERT INTO `topics` VALUES (4,2,6,'Logic, propositions, sets, relations',1,'locked',0,'3c8951d2-fe4c-497d-af55-cbacb5acc5e9','2026-09-12 13:14:28','2026-09-12 14:41:17'),(5,2,6,'Functions, counting, combinatorics',2,'locked',0,'04751dcb-4a43-4eef-b9fd-cb75e6217385','2026-09-12 13:14:28','2026-09-12 13:14:28'),(6,2,6,'Graph theory',3,'locked',0,'d51e7bdf-de6f-47fc-a2f4-9acb86f7c220','2026-09-12 13:14:28','2026-09-12 13:14:28'),(7,2,6,'C programming, pointers, recursion',4,'locked',0,'ca8de64c-b307-4899-96c7-be58971d989a','2026-09-12 13:14:28','2026-09-12 13:14:28'),(8,3,1,'Arrays, linked lists, stacks, queues',1,'locked',0,'71e7f8c9-68c8-41d4-9ac5-9c37abe9f239','2026-09-12 13:14:28','2026-09-12 13:14:28'),(9,3,1,'Trees, BST, heaps',2,'locked',0,'01e235a9-b09e-41f0-8682-521513d60536','2026-09-12 13:14:28','2026-09-12 13:14:28'),(10,3,1,'Graphs, BFS, DFS',3,'locked',0,'e6847b82-1201-4327-9e71-73a60ce6f245','2026-09-12 13:14:28','2026-09-12 13:14:28'),(11,3,1,'Sorting, searching, hashing',4,'locked',0,'702ef493-77fb-43a5-a4b9-18e3bb450ce3','2026-09-12 13:14:28','2026-09-12 13:14:28'),(12,3,1,'Complexity, recurrences, greedy, dynamic programming, divide & conquer',5,'locked',0,'65326f2c-643c-418d-a868-e6a7d4743a9e','2026-09-12 13:14:28','2026-09-12 13:14:28'),(13,4,3,'DBMS: ER model, relational algebra, SQL, functional dependencies, normalization, transactions, concurrency, indexing',1,'locked',0,'d4e7be75-289c-4fcb-86f2-fb44cb9d7806','2026-09-12 13:14:28','2026-09-12 13:14:28'),(14,4,3,'OS: processes, threads, scheduling, synchronization, deadlocks, memory management, virtual memory, file systems',2,'locked',0,'10108c72-f518-4848-b957-06fea74508f6','2026-09-12 13:14:28','2026-09-12 13:14:28'),(15,5,5,'Networks: OSI/TCP-IP, data link, Ethernet, IP addressing, routing, TCP/UDP, flow/congestion control, application layer',1,'locked',0,'000e9154-1a2e-42fa-ad45-e0e9db5694b0','2026-09-12 13:14:28','2026-09-12 13:14:28'),(16,5,5,'COA: number representation, arithmetic basics, instruction set, CPU, pipelining, cache, memory hierarchy, I/O',2,'locked',0,'7a30b1bb-bb0f-48b7-a249-ef28efb4bddb','2026-09-12 13:14:28','2026-09-12 13:14:28'),(17,6,7,'TOC: DFA/NFA, regular expressions, CFG, PDA, Turing machines, decidability',1,'locked',0,'6269b4d9-847d-47e1-8975-33a7343e39ea','2026-09-12 13:14:28','2026-09-12 13:14:28'),(18,6,7,'Compiler: lexical analysis, parsing, syntax-directed translation, intermediate code, runtime environment',2,'locked',0,'8c9d1fd0-d570-43ab-9a72-b9be37f9798d','2026-09-12 13:14:28','2026-09-12 13:14:28'),(19,6,7,'Digital Logic: Boolean algebra, combinational circuits, sequential circuits, number systems',3,'locked',0,'951dbb7f-4b61-4e68-889b-e8b6b4ba5f10','2026-09-12 13:14:28','2026-09-12 13:14:28'),(20,6,7,'Revise Engineering Mathematics and General Aptitude',4,'locked',0,'98f1b85e-a73b-40b1-9bf8-361863b7065f','2026-09-12 13:14:28','2026-09-12 13:14:28'),(21,7,NULL,'PYQ → Mock → Analyze → Weak topic → Revision',1,'locked',0,'de69dd32-7041-4a93-bd4b-301aade36edf','2026-09-12 13:14:28','2026-09-12 13:14:28'),(22,7,NULL,'No major new learning',2,'locked',0,'9af04091-02ee-4bd0-9b47-ab3aa9f85a93','2026-09-12 13:14:28','2026-09-12 13:14:28'),(23,7,NULL,'Build mock scores progressively toward 80+',3,'locked',0,'aec6c155-c662-49fa-a2d4-fe2146b71ee7','2026-09-12 13:14:28','2026-09-12 13:14:28'),(25,12,NULL,'Mon–Fri (2h): 60 min concepts + 45 min PYQs/problems + 15 min revision',1,'locked',0,'005a3a3b-264d-4f4b-a1e9-f6b0e128e94e','2026-09-12 14:38:13','2026-09-12 14:38:13'),(26,12,NULL,'Saturday (2h): 30 min revision + 90 min PYQs/problems',2,'locked',0,'feb0774e-ef36-4eb9-96d5-75b8df916eb5','2026-09-12 14:38:13','2026-09-12 14:38:13'),(27,12,NULL,'Sunday (6h): 2h revision + 2h PYQs + 1h test + 1h test analysis',3,'locked',0,'1b1ab4a1-6c1a-4be7-812c-1af679b04f8e','2026-09-12 14:38:13','2026-09-12 14:38:13'),(28,16,NULL,'Keep the PHP developer job while preparing. For GATE, prioritize C → Data Structures → Algorithms rather than spending preparation time on additional PHP learning. After GATE, evaluate top IIT/IISc fu',1,'locked',0,'84dcd640-dd7d-4616-bff8-101124568967','2026-09-12 14:38:13','2026-09-12 14:38:13'),(29,17,6,'Logic, propositions, sets, relations',1,'locked',0,'3c8951d2-fe4c-497d-af55-cbacb5acc5e9','2026-09-17 18:09:34','2026-09-17 18:09:34'),(30,17,6,'Functions, counting, combinatorics',2,'locked',0,'04751dcb-4a43-4eef-b9fd-cb75e6217385','2026-09-17 18:09:35','2026-09-17 18:09:35'),(31,17,6,'Graph theory',3,'locked',0,'d51e7bdf-de6f-47fc-a2f4-9acb86f7c220','2026-09-17 18:09:35','2026-09-17 18:09:35'),(32,17,6,'C programming, pointers, recursion',4,'locked',0,'ca8de64c-b307-4899-96c7-be58971d989a','2026-09-17 18:09:35','2026-09-17 18:09:35'),(33,16,NULL,'Keep the PHP developer job while preparing. For GATE, prioritize C → Data Structures → Algorithms rather than spending preparation time on additional PHP learning. After GATE, evaluate top IIT/IISc fu',1,'locked',0,'84dcd640-dd7d-4616-bff8-101124568967','2026-09-17 18:09:35','2026-09-17 18:09:35'),(34,16,NULL,'Keep the PHP developer job while preparing. For GATE, prioritize C → Data Structures → Algorithms rather than spending preparation time on additional PHP learning. After GATE, evaluate top IIT/IISc fu',1,'locked',0,'84dcd640-dd7d-4616-bff8-101124568967','2026-09-17 18:40:36','2026-09-17 18:40:36');
/*!40000 ALTER TABLE `topics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xp_logs`
--

DROP TABLE IF EXISTS `xp_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `xp_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `amount` int(11) NOT NULL DEFAULT 0,
  `reason` varchar(120) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xp_logs`
--

LOCK TABLES `xp_logs` WRITE;
/*!40000 ALTER TABLE `xp_logs` DISABLE KEYS */;
INSERT INTO `xp_logs` VALUES (1,20,'AI generated 3 MCQs','2026-09-12 14:33:16'),(5,20,'daily mission 2026-09-14','2026-09-17 14:43:00');
/*!40000 ALTER TABLE `xp_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'gateeaxmtraining'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-21 10:45:32
