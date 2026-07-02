<?php
die('INDEX FILE CHANGED');
require_once __DIR__ . '/db.php';

$pdo = dbConnect();
$stmt = $pdo->prepare('SELECT rating_after FROM gs_bm_table ORDER BY id DESC LIMIT 1');
$stmt->execute();
$latestLog = $stmt->fetch();

$currentRating = $latestLog ? (int) $latestLog['rating_after'] : 1000;
$character = getCharacter($currentRating);
$remaining = $character['next'] ? $character['next'] - $currentRating : 0;
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skill Log | 成長ブックマーク</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="container">
        <header class="site-header">
            <a href="index.php" class="logo">Skill Log<span>.</span></a>
            <a href="select.php" class="text-link">記録を見る →</a>
        </header>

        <section class="hero">
            <div class="hero-copy">
                <p class="eyebrow">BOOKMARK YOUR GROWTH</p>
                <h1>今日の挑戦を、<br>未来の自分へ。</h1>
                <p>学びや行動を保存する<br>成長ブックマークアプリです。</p>
            </div>

            <div class="character-card">
                <p>現在の姿</p>
                <div class="character"><?= h($character['emoji']) ?></div>
                <h2><?= h($character['name']) ?></h2>
                <small>Current Rating</small>
                <strong><?= h($currentRating) ?></strong>
                <?php if ($character['next']): ?>
                    <p class="next-evolution">次の進化まで <b><?= h($remaining) ?>pt</b></p>
                <?php else: ?>
                    <p class="next-evolution">最高の姿に進化しました！</p>
                <?php endif; ?>
            </div>
        </section>

        <?php if (isset($_GET['error'])): ?>
            <p class="message error">必須項目を正しく入力してください。</p>
        <?php endif; ?>

        <section class="form-section">
            <div class="section-title">
                <span>NEW LOG</span>
                <h2>挑戦をブックマーク</h2>
            </div>

            <form action="insert.php" method="POST" class="log-form">
                <label for="title">挑戦内容 <em>必須</em></label>
                <input type="text" id="title" name="title" required maxlength="255" placeholder="例：PHPでDB保存できた">

                <div class="form-grid">
                    <div>
                        <label for="category">カテゴリ <em>必須</em></label>
                        <select id="category" name="category" required>
                            <option value="">選択してください</option>
                            <option value="学習">学習</option>
                            <option value="行動">行動</option>
                            <option value="挑戦">挑戦</option>
                            <option value="タスク">タスク</option>
                            <option value="その他">その他</option>
                        </select>
                    </div>
                    <div>
                        <label for="difficulty">難易度 <em>必須</em></label>
                        <select id="difficulty" name="difficulty" required>
                            <option value="900">かんたん（900）</option>
                            <option value="1000" selected>ふつう（1000）</option>
                            <option value="1200">ちょいむず（1200）</option>
                            <option value="1400">かなり挑戦（1400）</option>
                            <option value="1600">ボス戦（1600）</option>
                        </select>
                    </div>
                </div>

                <fieldset>
                    <legend>結果 <em>必須</em></legend>
                    <div class="result-options">
                        <label class="result-option win">
                            <input type="radio" name="result" value="win" required>
                            <span>🎉 達成！<small>+30pt</small></span>
                        </label>
                        <label class="result-option draw">
                            <input type="radio" name="result" value="draw">
                            <span>🌱 半分できた<small>+10pt</small></span>
                        </label>
                        <label class="result-option lose">
                            <input type="radio" name="result" value="lose">
                            <span>☁️ 今回は見送り<small>-10pt</small></span>
                        </label>
                    </div>
                </fieldset>

                <label for="memo">メモ</label>
                <textarea id="memo" name="memo" rows="4" placeholder="例：PDOの流れが少し理解できた"></textarea>

                <button type="submit">未来の自分へ保存する</button>
            </form>
        </section>
    </main>
</body>
</html>
