<?php
require_once 'includes/auth.php';
require_once 'config/database.php';
requireLogin();

$user = getUserData();
$pdo = getDBConnection();

// Get user's last attempt
$stmt = $pdo->prepare("SELECT * FROM attempts WHERE user_id = ? ORDER BY completed_at DESC LIMIT 1");
$stmt->execute([$user['id']]);
$lastAttempt = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Quiz Engine</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="dashboard.php" style="color: inherit; text-decoration: none;"><span class="logo">📚 Quiz Engine</span></a>
            <nav class="nav-links">
                <span style="color: rgba(255,255,255,0.9);">Hello, <?= htmlspecialchars($user['name']) ?></span>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container-wide" style="padding-top: 40px;">
        <div class="card">
            <h1 class="card-title">Dashboard</h1>
            <p class="card-subtitle">Choose a quiz category • 10 questions • 8 seconds per question</p>

            <div style="background: rgba(15,23,42,0.85); border-radius: var(--radius); padding: 20px; margin-bottom: 24px; color: #e5e7eb;">
                <h3 style="margin-bottom: 8px;">Quiz Rules</h3>
                <ul style="margin-left: 20px; font-size: 0.9rem;">
                    <li>10 multiple choice questions in each quiz</li>
                    <li>8 seconds per question – be quick!</li>
                    <li>Gold, Silver, Bronze badges based on your score</li>
                    <li>Certificate available to save as PDF after completion</li>
                </ul>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px;">
                <div style="background: var(--card-bg); border-radius: var(--radius); padding: 16px; border: 1px solid var(--border);">
                    <h3 style="margin-bottom: 4px;">Movie Quiz</h3>
                    <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 12px;">Identify movies from famous heroes and roles.</p>
                    <a href="quiz.php?category=movies" class="btn btn-primary" style="width: 100%;">Start Movie Quiz</a>
                </div>
                <div style="background: var(--card-bg); border-radius: var(--radius); padding: 16px; border: 1px solid var(--border);">
                    <h3 style="margin-bottom: 4px;">Indian Capitals</h3>
                    <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 12px;">Match Indian states with their capitals.</p>
                    <a href="quiz.php?category=indian_capitals" class="btn btn-primary" style="width: 100%;">Start Capitals Quiz</a>
                </div>
                <div style="background: var(--card-bg); border-radius: var(--radius); padding: 16px; border: 1px solid var(--border);">
                    <h3 style="margin-bottom: 4px;">Famous Places</h3>
                    <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 12px;">Recognise iconic places across India.</p>
                    <a href="quiz.php?category=famous_places" class="btn btn-primary" style="width: 100%;">Start Places Quiz</a>
                </div>
                <div style="background: var(--card-bg); border-radius: var(--radius); padding: 16px; border: 1px solid var(--border);">
                    <h3 style="margin-bottom: 4px;">General Knowledge</h3>
                    <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 12px;">Simple GK including leaders and basics.</p>
                    <a href="quiz.php?category=general_knowledge" class="btn btn-primary" style="width: 100%;">Start GK Quiz</a>
                </div>
                <div style="background: var(--card-bg); border-radius: var(--radius); padding: 16px; border: 1px solid var(--border);">
                    <h3 style="margin-bottom: 4px;">Sports</h3>
                    <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 12px;">Indian sports, cricket and Olympic facts.</p>
                    <a href="quiz.php?category=sports" class="btn btn-primary" style="width: 100%;">Start Sports Quiz</a>
                </div>
            </div>

            <?php if ($lastAttempt): ?>
            <div style="margin-bottom: 8px;">
                <h3 style="margin-bottom: 8px;">Last Attempt</h3>
                <p>Score: <strong><?= $lastAttempt['score'] ?>/<?= $lastAttempt['total_questions'] ?></strong> 
                   | Badge: <span class="badge-display badge-<?= strtolower($lastAttempt['badge']) ?>"><?= $lastAttempt['badge'] ?></span></p>
                <a href="result.php?id=<?= $lastAttempt['id'] ?>" class="btn btn-secondary" style="margin-top: 12px;">View Result</a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <footer class="footer">Automated Quiz Engine with PDF Certification</footer>
</body>
</html>
