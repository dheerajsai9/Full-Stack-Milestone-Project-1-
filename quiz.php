<?php
require_once 'includes/auth.php';
requireLogin();

// Quiz categories and labels
$category = $_GET['category'] ?? 'general_knowledge';
$quizMeta = [
    'movies' => [
        'title' => 'Movie Quiz',
        'subtitle' => 'Guess the movies from the famous heroes and roles'
    ],
    'indian_capitals' => [
        'title' => 'Indian Capitals Quiz',
        'subtitle' => 'Match Indian states with their capitals'
    ],
    'famous_places' => [
        'title' => 'Famous Places in India Quiz',
        'subtitle' => 'Identify iconic monuments and locations'
    ],
    'general_knowledge' => [
        'title' => 'General Knowledge Quiz',
        'subtitle' => 'Mix of simple GK questions'
    ],
    'sports' => [
        'title' => 'Sports Quiz',
        'subtitle' => 'Indian sports, cricket and Olympics'
    ],
];

if (!isset($quizMeta[$category])) {
    $category = 'general_knowledge';
}

$quizTitle = $quizMeta[$category]['title'];
$quizSubtitle = $quizMeta[$category]['subtitle'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($quizTitle) ?> - Quiz Engine</title>
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

    <div class="container-wide" style="padding-top: 32px;">
        <div class="card" style="margin-bottom: 24px;">
            <h1 class="card-title"><?= htmlspecialchars($quizTitle) ?></h1>
            <p class="card-subtitle"><?= htmlspecialchars($quizSubtitle) ?> • 10 questions • 8 seconds per question</p>
        </div>
        <div id="loading" class="card">
            <p class="card-subtitle">Loading questions...</p>
        </div>

        <div id="quiz-container" style="display: none;">
            <div class="progress-dots" id="progress-dots"></div>
            <div class="quiz-card">
                <div class="timer-text" id="timer-text">30</div>
                <div class="timer-bar">
                    <div class="timer-fill" id="timer-fill" style="width: 100%"></div>
                </div>
                <span class="question-number" id="question-number">Question 1</span>
                <p class="question-text" id="question-text"></p>
                <ul class="options-list" id="options-list"></ul>
            </div>
            <div style="display: flex; gap: 12px; justify-content: space-between;">
                <button type="button" class="btn btn-secondary" id="btn-prev" disabled>Previous</button>
                <button type="button" class="btn btn-primary" id="btn-next">Next</button>
            </div>
        </div>
    </div>

    <footer class="footer">Automated Quiz Engine with PDF Certification</footer>

    <script>
    const TIME_PER_QUESTION = 8;
    const QUIZ_CATEGORY = '<?= htmlspecialchars($category, ENT_QUOTES) ?>';
    let questions = [];
    let currentIndex = 0;
    let answers = {};
    let timerInterval = null;
    let timeLeft = TIME_PER_QUESTION;
    let startTime = null;

    async function loadQuestions() {
        const res = await fetch('api/get_questions.php?category=' + encodeURIComponent(QUIZ_CATEGORY));
        const data = await res.json();
        if (data.error) {
            alert(data.error);
            window.location = 'dashboard.php';
            return;
        }
        questions = data.questions;
        startTime = Date.now();
        renderQuestion();
        document.getElementById('loading').style.display = 'none';
        document.getElementById('quiz-container').style.display = 'block';
        renderProgressDots();
        startTimer();
    }

    function renderProgressDots() {
        const container = document.getElementById('progress-dots');
        container.innerHTML = questions.map((_, i) => 
            `<span class="progress-dot ${i < currentIndex ? 'completed' : i === currentIndex ? 'active' : ''}" data-index="${i}"></span>`
        ).join('');
    }

    function startTimer() {
        timeLeft = TIME_PER_QUESTION;
        const timerText = document.getElementById('timer-text');
        const timerFill = document.getElementById('timer-fill');
        timerText.textContent = timeLeft;
        timerFill.style.width = '100%';
        timerFill.classList.remove('warning', 'danger');

        if (timerInterval) clearInterval(timerInterval);
        timerInterval = setInterval(() => {
            timeLeft--;
            timerText.textContent = timeLeft;
            timerFill.style.width = (timeLeft / TIME_PER_QUESTION * 100) + '%';
            if (timeLeft <= 3) timerFill.classList.add('danger');
            else if (timeLeft <= 5) timerFill.classList.add('warning');
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                goNext();
            }
        }, 1000);
    }

    function renderQuestion() {
        const q = questions[currentIndex];
        if (!q) {
            submitQuiz();
            return;
        }

        document.getElementById('question-number').textContent = `Question ${currentIndex + 1} of 10`;
        document.getElementById('question-text').textContent = q.question_text;

        const optionsList = document.getElementById('options-list');
        optionsList.innerHTML = q.options.map(opt => `
            <li class="option-item ${answers[q.id] === opt.key ? 'selected' : ''}" data-key="${opt.key}">
                <label style="cursor: pointer; display: flex; align-items: center;">
                    <input type="radio" name="answer" value="${opt.key}" ${answers[q.id] === opt.key ? 'checked' : ''}>
                    <span>${opt.key}. ${opt.text}</span>
                </label>
            </li>
        `).join('');

        optionsList.querySelectorAll('.option-item').forEach(el => {
            el.addEventListener('click', () => {
                answers[q.id] = el.dataset.key;
                optionsList.querySelectorAll('.option-item').forEach(o => o.classList.remove('selected'));
                el.classList.add('selected');
                el.querySelector('input').checked = true;
            });
        });

        document.getElementById('btn-prev').disabled = currentIndex === 0;
        document.getElementById('btn-next').textContent = currentIndex === questions.length - 1 ? 'Submit' : 'Next';
        renderProgressDots();
    }

    function goPrev() {
        if (currentIndex > 0) {
            clearInterval(timerInterval);
            currentIndex--;
            renderQuestion();
            startTimer();
        }
    }

    function goNext() {
        if (currentIndex < questions.length - 1) {
            clearInterval(timerInterval);
            currentIndex++;
            renderQuestion();
            startTimer();
        } else {
            submitQuiz();
        }
    }

    async function submitQuiz() {
        if (timerInterval) clearInterval(timerInterval);
        const timeTaken = Math.round((Date.now() - startTime) / 1000);
        const res = await fetch('api/submit_quiz.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ answers: answers, time_taken: timeTaken, category: QUIZ_CATEGORY })
        });
        const data = await res.json();
        if (data.success) {
            window.location = 'submit.php?id=' + data.attempt_id;
        } else {
            alert(data.error || 'Submission failed');
        }
    }

    document.getElementById('btn-prev').addEventListener('click', goPrev);
    document.getElementById('btn-next').addEventListener('click', goNext);

    loadQuestions();
    </script>
</body>
</html>
