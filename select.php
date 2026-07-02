<?php
require_once __DIR__ . '/db.php';

// 1. DBへ接続
$pdo = dbConnect();

// 2. 新しい記録から順に全件取得
$stmt = $pdo->prepare('SELECT * FROM gs_bm_table ORDER BY id DESC');
$stmt->execute();
$logs = $stmt->fetchAll();

$currentRating = $logs ? (int) $logs[0]['rating_after'] : 1000;
$character = getCharacter($currentRating);
$remaining = $character['next'] ? $character['next'] - $currentRating : 0;
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>成長記録 | Skill Log</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="container">
        <header class="site-header">
            <a href="index.php" class="logo">Skill Log<span>.</span></a>
            <a href="index.php" class="primary-link">＋ 新しい挑戦</a>
        </header>

        <section class="log-heading">
            <div>
                <p class="eyebrow">MY GROWTH BOOKMARKS</p>
                <h1>未来へ残した記録</h1>
                <p>成長記録</p>
            </div>
            <div class="mini-character">
                <span><?= h($character['emoji']) ?></span>
                <div>
                    <small>現在の姿</small>
                    <strong><?= h($character['name']) ?></strong>
                    <p>Rating <?= h($currentRating) ?></p>
                    <?php if ($character['next']): ?>
                        <p>次の進化まで <?= h($remaining) ?>pt</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <?php if (isset($_GET['saved'])): ?>
            <p class="message success">成長をブックマークしました！</p>
        <?php endif; ?>

        <?php if (!$logs): ?>
            <section class="empty-state">
                <div>🥚</div>
                <h2>まだ記録がありません</h2>
                <p>最初の挑戦を未来の自分へ残してみましょう。</p>
                <a href="index.php" class="primary-link">最初の記録を作る</a>
            </section>
        <?php else: ?>
            <section class="log-list">
                <?php foreach ($logs as $log): ?>
                    <?php $change = (int) $log['rating_after'] - (int) $log['rating_before']; ?>
                    <article class="log-card">
                        <div class="log-top">
                            <span class="category-tag"><?= h($log['category']) ?></span>
                            <time><?= h(date('Y.m.d H:i', strtotime($log['date']))) ?></time>
                        </div>
                        <h2><?= h($log['title']) ?></h2>
                        <div class="log-meta">
                            <span>難易度：<b><?= h(difficultyLabel($log['difficulty'])) ?></b></span>
                            <span>結果：<b><?= h(resultLabel($log['result'])) ?></b></span>
                        </div>
                        <?php if ($log['memo'] !== ''): ?>
                            <p class="memo"><?= nl2br(h($log['memo'])) ?></p>
                        <?php endif; ?>
                        <div class="rating-history">
                            <span><?= h($log['rating_before']) ?></span>
                            <i>→</i>
                            <strong><?= h($log['rating_after']) ?></strong>
                            <b class="<?= $change >= 0 ? 'plus' : 'minus' ?>">
                                <?= $change > 0 ? '+' : '' ?><?= h($change) ?>
                            </b>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </main>
</body>
</html>
