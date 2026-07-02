<?php
// MySQLへ接続する関数
function dbConnect()
{

    try {
        return new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } catch (PDOException $e) {
        exit('DB接続エラー：' . h($e->getMessage()));
    }
}

// HTMLに表示するときのXSS対策
function h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// レートから成長キャラクターの情報を返す。
function getCharacter($rating)
{
    if ($rating >= 2000) return ['emoji' => '🐦‍🔥', 'name' => '不死鳥', 'next' => null];
    if ($rating >= 1700) return ['emoji' => '🦅', 'name' => '鷹', 'next' => 2000];
    if ($rating >= 1450) return ['emoji' => '🕊️', 'name' => '鳥', 'next' => 1700];
    if ($rating >= 1250) return ['emoji' => '🐤', 'name' => '小鳥', 'next' => 1450];
    if ($rating >= 1100) return ['emoji' => '🐣', 'name' => 'ひよこ', 'next' => 1250];
    return ['emoji' => '🥚', 'name' => '卵', 'next' => 1100];
}

function difficultyLabel($difficulty)
{
    $labels = [
        900 => 'かんたん',
        1000 => 'ふつう',
        1200 => 'ちょいむず',
        1400 => 'かなり挑戦',
        1600 => 'ボス戦',
    ];
    return $labels[(int) $difficulty] ?? '不明';
}

function resultLabel($result)
{
    $labels = [
        'win' => '達成！',
        'draw' => '半分できた',
        'lose' => '今回は見送り',
    ];
    return $labels[$result] ?? $result;
}