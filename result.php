<?php
require_once 'includes/auth.php';
require_once 'config/database.php';
requireLogin();

$attemptId = (int)($_GET['id'] ?? 0);
$user = getUserData();

if (!$attemptId) {
    header('Location: dashboard.php');
    exit;
}

$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT * FROM attempts WHERE id = ? AND user_id = ?");
$stmt->execute([$attemptId, $user['id']]);
$attempt = $stmt->fetch();

if (!$attempt) {
    header('Location: dashboard.php');
    exit;
}

$score = $attempt['score'];
$total = $attempt['total_questions'];
$badge = $attempt['badge'];
$percentage = round(($score / $total) * 100);

// Quiz category label (optional if column exists)
$categoryKey = $attempt['quiz_category'] ?? 'general_knowledge';
$categoryNames = [
    'movies' => 'Movie Quiz',
    'indian_capitals' => 'Indian Capitals Quiz',
    'famous_places' => 'Famous Places in India Quiz',
    'general_knowledge' => 'General Knowledge Quiz',
    'sports' => 'Sports Quiz',
];
$categoryLabel = $categoryNames[$categoryKey] ?? 'General Knowledge Quiz';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Result - Quiz Engine</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="dashboard.php" style="color: inherit; text-decoration: none;"><span class="logo">📚 Quiz Engine</span></a>
            <nav class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container-wide" style="padding-top: 40px;">
        <div class="card">
            <h1 class="card-title">Quiz Result</h1>
            <p class="card-subtitle">You completed: <?= htmlspecialchars($categoryLabel) ?></p>

            <div class="score-display">
                <div class="score-circle <?= strtolower($badge) ?>"><?= $score ?>/<?= $total ?></div>
                <p style="font-size: 1.1rem; margin-bottom: 8px;">You scored <strong><?= $percentage ?>%</strong></p>
                <p class="badge-display badge-<?= strtolower($badge) ?>">🏅 <?= $badge ?> Badge</p>
            </div>

            <div style="text-align: center; margin-top: 32px;">
                <a href="download.php?id=<?= $attemptId ?>" class="btn btn-primary">Download / Print Certificate (PDF)</a>
                <a href="dashboard.php" class="btn btn-secondary" style="margin-top: 12px;">Back to Dashboard</a>
            </div>
        </div>
    </div>

    <footer class="footer">Automated Quiz Engine with PDF Certification</footer>
</body>
</html>
