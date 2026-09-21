<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Pages
$routes->get('/', 'Home::index');
$routes->get('dashboard', 'Home::index');
$routes->get('course', 'Course::index');
$routes->get('course/(:num)', 'Course::learn/$1');
$routes->get('training', 'Training::index');
$routes->get('challenge', 'Training::challenge');
$routes->get('performance', 'Performance::index');
$routes->get('documents', 'Documents::index');
$routes->get('documents/stream/(:num)', 'Documents::stream/$1');
$routes->get('calendar', 'Calendar::index');
$routes->get('settings', 'Settings::index');
$routes->get('game', 'Game::index');

// Game Isolated JSON API
$routes->group('game', static function ($routes) {
    $routes->post('session/start', 'Game::sessionStart');
    $routes->post('session/checkpoint', 'Game::sessionCheckpoint');
    $routes->post('session/save', 'Game::sessionSave');
    $routes->post('session/new', 'Game::sessionNew');
    $routes->get('session/(:segment)', 'Game::getSession/$1');
    $routes->post('session/end', 'Game::sessionEnd');
    $routes->get('leaderboard', 'Game::leaderboard');
});

// JSON API
$routes->group('api', static function ($routes) {
    $routes->get('overview', 'Api::overview');
    $routes->get('subjects', 'Api::subjects');
    $routes->get('topics', 'Api::topics');
    $routes->get('phases', 'Api::phases');
    $routes->post('topics/(:num)/status', 'Api::setTopicStatus/$1');
    $routes->get('mcqs', 'Api::mcqs');
    $routes->post('mcqs', 'Api::saveMcq');
    $routes->post('mcqs/(:num)', 'Api::saveMcq/$1');
    $routes->delete('mcqs/(:num)', 'Api::deleteMcq/$1');
    $routes->post('quiz/result', 'Api::quizResult');
    $routes->post('study/log', 'Api::studyLog');
    $routes->get('study/heatmap', 'Api::heatmap');
    $routes->post('documents/(:num)/tag', 'Api::tagDocument/$1');
    $routes->post('documents/rename', 'Api::renameDocument');
    $routes->get('calendar', 'Api::calendar');
    $routes->post('calendar/(:segment)/status', 'Api::setDayStatus/$1');
    $routes->get('checklists', 'Api::checklists');
    $routes->post('checklists/(:num)/toggle', 'Api::toggleChecklist/$1');
    $routes->post('notion/sync', 'Api::notionSync');
    $routes->get('notion/status', 'Api::notionStatus');
    $routes->get('targets', 'Api::targets');
    $routes->get('xp', 'Api::xp');
    $routes->post('settings/save', 'Api::saveSettings');
    $routes->get('todaytask', 'Api::todayTask');
    $routes->get('daily-plan', 'Api::dailyPlan');
    $routes->get('doc', 'Api::dailyPlan'); // legacy alias, keep 1 release
    $routes->get('ai/study-level', 'Api::studyLevel');
    $routes->get('ai/status', 'Api::aiStatus');
    $routes->post('ai/generate', 'Api::aiGenerate');
    $routes->get('ai/latest', 'Api::aiLatest');
    $routes->post('ai/explain', 'Api::aiExplain');
});