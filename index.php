<?php
require_once 'includes/auth.php';
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Engine - General Knowledge Quiz</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" style="color: inherit; text-decoration: none;"><span class="logo">📚 Quiz Engine</span></a>
            <nav class="nav-links">
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            </nav>
        </div>
    </header>

    <div class="page-center">
        <div class="container-wide">
            <div class="card" style="text-align: center;">
                <h1 class="card-title">Automated Quiz Engine</h1>
                <p class="card-subtitle">Test your general knowledge • Earn badges • Get your certificate</p>
                <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; margin-top: 24px;">
                    <a href="register.php" class="btn btn-primary">Get Started</a>
                    <a href="login.php" class="btn btn-secondary">Login</a>
                </div>
                <div style="margin-top: 32px; padding: 24px; background: white; border-radius: var(--radius); border: 1px solid var(--border);">
                    <h3 style="margin-bottom: 12px;">Features</h3>
                    <ul style="list-style: none; color: var(--text-muted);">
                        <li>✓ 10 General Knowledge Questions</li>
                        <li>✓ 30 seconds per question</li>
                        <li>✓ Gold, Silver & Bronze badges</li>
                        <li>✓ PDF Certificate download</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">Automated Quiz Engine with PDF Certification Generation</footer>
</body>
</html>
