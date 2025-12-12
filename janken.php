<?php
// ゲームの論理部分
$result = '';
$player_hand = '';
$computer_hand = '';

// じゃんけんの手を定義
$hands = [
    'rock'    => 'グー',
    'scissors' => 'チョキ',
    'paper'   => 'パー'
];

// ユーザーの手がPOSTされた場合
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['player_hand'])) {
    $player_hand = $_POST['player_hand'];

    // ユーザーの手が有効かチェック
    if (isset($hands[$player_hand])) {
        // 1. コンピュータの手をランダムに決定
        $computer_keys = array_keys($hands);
        $computer_hand = $computer_keys[array_rand($computer_keys)];

        // 2. 勝敗判定
        // 0: 引き分け, 1: 勝ち, -1: 負け
        $score = 0; 
        
        if ($player_hand === $computer_hand) {
            $score = 0; // 引き分け
        } elseif (
            ($player_hand === 'rock' && $computer_hand === 'scissors') ||
            ($player_hand === 'scissors' && $computer_hand === 'paper') ||
            ($player_hand === 'paper' && $computer_hand === 'rock')
        ) {
            $score = 1; // 勝ち
        } else {
            $score = -1; // 負け
        }

        // 3. 結果メッセージの設定
        $player_display = $hands[$player_hand];
        $computer_display = $hands[$computer_hand];

        if ($score === 1) {
            $result = "🎉 あなたの勝ちです！ ($player_display vs $computer_display)";
        } elseif ($score === -1) {
            $result = "😢 あなたの負けです... ($player_display vs $computer_display)";
        } else {
            $result = "🤝 引き分けです。 ($player_display vs $computer_display)";
        }

    } else {
        $result = "⚠️ 無効な選択です。";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>じゃんけんゲーム</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin-top: 50px; }
        .container { width: 80%; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; box-shadow: 2px 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .result { margin: 20px 0; padding: 10px; font-size: 1.2em; font-weight: bold; min-height: 40px; }
        .win { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .lose { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .draw { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .choices button { padding: 10px 20px; margin: 5px; font-size: 1.5em; cursor: pointer; border: none; border-radius: 5px; transition: background-color 0.3s; }
        .choices button:hover { opacity: 0.8; }
        #rock { background-color: #007bff; color: white; }
        #scissors { background-color: #28a745; color: white; }
        #paper { background-color: #ffc107; color: #333; }
    </style>
</head>
<body>

<div class="container">
    <h1>✊✋✌️ じゃんけんゲーム 💻</h1>

    <?php 
    // 結果がある場合に表示
    if (!empty($result)) {
        $class = 'draw';
        if (isset($score)) {
            if ($score === 1) {
                $class = 'win';
            } elseif ($score === -1) {
                $class = 'lose';
            }
        }
        echo "<div class='result $class'>{$result}</div>";
    }
    ?>

    <h2>あなたの手を選んでください:</h2>
    
    <form method="POST" action="janken.php" class="choices">
        <button type="submit" name="player_hand" value="rock" id="rock">
            グー (✊)
        </button>
        
        <button type="submit" name="player_hand" value="scissors" id="scissors">
            チョキ (✌️)
        </button>
        
        <button type="submit" name="player_hand" value="paper" id="paper">
            パー (✋)
        </button>
    </form>
    
    <?php if (empty($result)): ?>
        <p style="margin-top: 30px; color: #666;">ボタンをクリックして、ゲームを開始してください。</p>
    <?php endif; ?>
</div>

</body>
</html>
