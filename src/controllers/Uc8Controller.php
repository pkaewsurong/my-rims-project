<?php
// src/controllers/Uc8Controller.php

function mockupAction($pdo) {
    if (!isLoggedIn()) {
        redirect('/login');
    }

    $project_id = $_GET['project_id'] ?? null;
    if (!$project_id) {
        die("Project ID is required");
    }

    // Fetch the project details for the header
    $stmt = $pdo->prepare('
        SELECT p.*, pr.title, pr.user_id as pi_id, u.name as pi_name
        FROM projects p 
        LEFT JOIN proposals pr ON p.proposal_id = pr.id 
        LEFT JOIN users u ON pr.user_id = u.id
        WHERE p.id = ?
    ');
    $stmt->execute([$project_id]);
    $project = $stmt->fetch();

    if (!$project) {
        die("Project not found");
    }

    // Fetch team members based on the proposal ID attached to the project
    $teamStmt = $pdo->prepare('SELECT * FROM proposal_teams WHERE proposal_id = ?');
    $teamStmt->execute([$project['proposal_id']]);
    $teams = $teamStmt->fetchAll();

    // Optional: check ownership if needed
    // if ($project['user_id'] != authUser()['id'] && !hasRole('admin')) {
    //     die("Unauthorized");
    // }

    require __DIR__ . '/../../views/ip/uc8_mockup.php';
}
