<?php
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// 1. フォームからPOSTで送られた値を受け取る
$title = trim($_POST['title'] ?? '');
$category = trim($_POST['category'] ?? '');
$difficulty = (int) ($_POST['difficulty'] ?? 0);
$result = $_POST['result'] ?? '';
$memo = trim($_POST['memo'] ?? '');

$allowedCategories = ['学習', '行動', '挑戦', 'タスク', 'その他'];
$allowedDifficulties = [900, 1000, 1200, 1400, 1600];
$ratingChanges = ['win' => 30, 'draw' => 10, 'lose' => -10];

if (
    $title === '' ||
    !in_array($category, $allowedCategories, true) ||
    !in_array($difficulty, $allowedDifficulties, true) ||
    !array_key_exists($result, $ratingChanges)
) {
    header('Location: index.php?error=1');
    exit;
}

// 2. DBへ接続
$pdo = dbConnect();

// 3. 最新レートをSELECT（最初の記録は1000から開始）
$stmt = $pdo->prepare('SELECT rating_after FROM gs_bm_table ORDER BY id DESC LIMIT 1');
$stmt->execute();
$latestLog = $stmt->fetch();
$ratingBefore = $latestLog ? (int) $latestLog['rating_after'] : 1000;

// 4. 結果に応じてレートを計算
$ratingAfter = $ratingBefore + $ratingChanges[$result];

// 5. プレースホルダーを使ってINSERT
$sql = 'INSERT INTO gs_bm_table
        (title, category, difficulty, result, rating_before, rating_after, memo, `date`)
        VALUES
        (:title, :category, :difficulty, :result, :rating_before, :rating_after, :memo, NOW())';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':title', $title, PDO::PARAM_STR);
$stmt->bindValue(':category', $category, PDO::PARAM_STR);
$stmt->bindValue(':difficulty', $difficulty, PDO::PARAM_INT);
$stmt->bindValue(':result', $result, PDO::PARAM_STR);
$stmt->bindValue(':rating_before', $ratingBefore, PDO::PARAM_INT);
$stmt->bindValue(':rating_after', $ratingAfter, PDO::PARAM_INT);
$stmt->bindValue(':memo', $memo, PDO::PARAM_STR);
$stmt->execute();

header('Location: select.php?saved=1');
exit;
