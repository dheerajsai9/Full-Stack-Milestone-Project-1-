<?php
require_once 'includes/auth.php';
requireLogin();

$attemptId = (int)($_GET['id'] ?? 0);
if (!$attemptId) {
    header('Location: dashboard.php');
    exit;
}
$user = getUserData();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Submitted - Quiz Engine</title>
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

    <div class="page-center">
        <div class="container">
            <div class="card">
                <div style="text-align: center; padding: 20px;">
                    <div style="font-size: 4rem; margin-bottom: 16px;">✅</div>
                    <h1 class="card-title">Quiz Submitted!</h1>
                    <p class="card-subtitle">Your answers have been recorded successfully.</p>
                    <p style="margin-bottom: 24px;">Redirecting to your results...</p>
                    <a href="result.php?id=<?= $attemptId ?>" class="btn btn-primary">View Results Now</a>
                </div>
            </div>
        </div>
    </div>

    <script>
    setTimeout(() => {
        window.location.href = 'result.php?id=<?= $attemptId ?>';
    }, 2000);
    </script>
</body>
</html>
