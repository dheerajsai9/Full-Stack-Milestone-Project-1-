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

$badge = $attempt['badge'];
$score = $attempt['score'];
$total = $attempt['total_questions'];
$date = date('F j, Y', strtotime($attempt['completed_at']));

// Category label for certificate
$categoryKey = $attempt['quiz_category'] ?? 'general_knowledge';
$categoryNames = [
    'movies' => 'Movie Quiz',
    'indian_capitals' => 'Indian Capitals Quiz',
    'famous_places' => 'Famous Places in India Quiz',
    'general_knowledge' => 'General Knowledge Quiz',
    'sports' => 'Sports Quiz',
];
$categoryLabel = $categoryNames[$categoryKey] ?? 'General Knowledge Quiz';

// Helper to escape text for PDF
$escapePdfText = function (string $text): string {
    $text = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    return $text;
};

$nameText = $user['name'] ?? 'Participant';
$title = 'CERTIFICATE OF ACHIEVEMENT';
$subtitle = 'Automated Quiz Engine - ' . $categoryLabel;
$line1 = 'This certificate is proudly presented to';
$line2 = 'has successfully completed the ' . $categoryLabel;
$lineDate = 'Completed on ' . $date;
$badgeTitle = $badge . ' Badge';
$scoreLine = 'Score: ' . $score . ' / ' . $total;

// Build a one-page PDF (A4 landscape) styled similar to the blue/yellow HTML certificate
// Coordinates are in points (1/72 inch). A4 landscape ~ 842 x 595.
$content = '';

// Full-page dark blue background
$content .= "q\n";
$content .= "0.03 0.15 0.40 rg\n";
$content .= "0 0 842 595 re f\n";
$content .= "Q\n";

// White rounded card (approximate with rectangle)
$content .= "q\n";
$content .= "1 1 1 rg 0.80 0.88 1 RG 2 w\n";
$content .= "60 70 722 455 re B\n";
$content .= "Q\n";

// Inner light border
$content .= "q\n";
$content .= "0.80 0.88 1 RG 1 w\n";
$content .= "80 90 682 415 re S\n";
$content .= "Q\n";

// Title in strong blue
$content .= "BT\n/F1 28 Tf 0.15 0.36 0.90 rg\n";
$content .= "220 450 Td (" . $escapePdfText($title) . ") Tj\nET\n";

// Subtitle
$content .= "BT\n/F1 14 Tf 0.30 0.30 0.30 rg\n";
$content .= "215 420 Td (" . $escapePdfText($subtitle) . ") Tj\nET\n";

// Intro text
$content .= "BT\n/F1 13 Tf 0.35 0.35 0.40 rg\n";
$content .= "250 385 Td (" . $escapePdfText($line1) . ") Tj\nET\n";

// Recipient name
$content .= "BT\n/F1 24 Tf 0.15 0.25 0.65 rg\n";
$content .= "260 355 Td (" . $escapePdfText($nameText) . ") Tj\nET\n";

// Description
$content .= "BT\n/F1 14 Tf 0.25 0.25 0.30 rg\n";
$content .= "150 325 Td (" . $escapePdfText($line2) . ") Tj\nET\n";

// Badge box color based on badge type
$badgeFill = "0.996 0.953 0.780";  // default gold-like
if (strcasecmp($badge, 'Silver') === 0) {
    $badgeFill = "0.898 0.906 0.922";
} elseif (strcasecmp($badge, 'Bronze') === 0) {
    $badgeFill = "0.996 0.843 0.667";
}

// Badge rounded box (approx rectangle)
$content .= "q\n";
$content .= $badgeFill . " rg 0.95 0.85 0.70 RG 1.5 w\n";
$content .= "140 255 562 80 re B\n";
$content .= "Q\n";

// Badge title
$content .= "BT\n/F1 16 Tf 0.35 0.24 0.05 rg\n";
$content .= "260 300 Td (" . $escapePdfText($badgeTitle) . ") Tj\nET\n";

// Score line
$content .= "BT\n/F1 13 Tf 0.20 0.20 0.20 rg\n";
$content .= "260 280 Td (" . $escapePdfText($scoreLine) . ") Tj\nET\n";

// Date line
$content .= "BT\n/F1 12 Tf 0.45 0.45 0.50 rg\n";
$content .= "260 235 Td (" . $escapePdfText($lineDate) . ") Tj\nET\n";

$contentLength = strlen($content);

// Start building PDF
$objects = [];

// 1: Catalog
$objects[] = "<< /Type /Catalog /Pages 2 0 R >>\n";

// 2: Pages
$objects[] = "<< /Type /Pages /Kids [3 0 R] /Count 1 >>\n";

// 3: Page
$objects[] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 842 595] /Resources << /Font << /F1 5 0 R >> >> /Contents 4 0 R >>\n";

// 4: Content stream
$objects[] = "<< /Length $contentLength >>\nstream\n" . $content . "endstream\n";

// 5: Font
$objects[] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\n";

// Build PDF output with xref
$pdf = "%PDF-1.4\n";
$offsets = [];

foreach ($objects as $index => $obj) {
    $id = $index + 1;
    $offsets[$id] = strlen($pdf);
    $pdf .= $id . " 0 obj\n" . $obj . "endobj\n";
}

$xrefOffset = strlen($pdf);
$count = count($objects) + 1;

$pdf .= "xref\n0 $count\n";
$pdf .= "0000000000 65535 f \n";

for ($i = 1; $i <= count($objects); $i++) {
    $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
}

$pdf .= "trailer << /Size $count /Root 1 0 R >>\n";
$pdf .= "startxref\n$xrefOffset\n%%EOF";

$filename = 'Quiz_Certificate_' . preg_replace('/[^a-zA-Z0-9]+/', '_', $nameText) . '.pdf';

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . strlen($pdf));

echo $pdf;
exit;
