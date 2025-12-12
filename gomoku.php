<?php
session_start();

// 定数定義
const BOARD_SIZE = 9;
const EMPTY = 0;
const PLAYER = 1; // プレイヤー: 黒 (●)
const COMPUTER = 2; // コンピュータ: 白 (○)
const WIN_COUNT = 5;

// --- セッションの初期化またはリセット ---

if (!isset($_SESSION['board']) || isset($_POST['reset'])) {
    // 盤面をすべて空（0）で初期化
    $_SESSION['board'] = array_fill(0, BOARD_SIZE, array_fill(0, BOARD_SIZE, EMPTY));
    $_SESSION['current_player'] = PLAYER;
    $_SESSION['message'] = "新しいゲームを開始します。あなたの番です (●)。";
    $_SESSION['game_over'] = false;
}

$board = &$_SESSION['board'];
$current_player = &$_SESSION['current_player'];
$message = &$_SESSION['message'];
$game_over = &$_SESSION['game_over'];

// --- メインゲームロジック ---

if (!$game_over && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['row']) && isset($_POST['col'])) {
    $r = (int)$_POST['row'];
    $c = (int)$_POST['col'];

    // 1. プレイヤーの着手処理
    if ($current_player === PLAYER) {
        if ($board[$r][$c] === EMPTY) {
            $board[$r][$c] = PLAYER;
            
            if (check_win($board, PLAYER)) {
                $message = "🎉 あなたの勝ちです！おめでとうございます！";
                $game_over = true;
            } elseif (is_board_full($board)) {
                $message = "🤝 引き分けです。";
                $game_over = true;
            } else {
                $current_player = COMPUTER;
                $message = "コンピュータの番です (○)...";
                
                // 2. コンピュータの着手処理 (簡単なランダムAI)
                if (!$game_over) {
                    computer_move($board);

                    if (check_win($board, COMPUTER)) {
                        $message = "😢 コンピュータの勝ちです... (○)";
                        $game_over = true;
                    } elseif (is_board_full($board)) {
                        $message = "🤝 引き分けです。";
                        $game_over = true;
                    } else {
                        $current_player = PLAYER;
                        $message = "あなたの番です (●)。";
                    }
                }
            }
        } else {
            // 無効なマスへの着手
            $message = "そのマスには既に石が置かれています。";
        }
    }
}

// --- 関数定義 ---

/**
 * 盤面がすべて埋まったかチェック
 */
function is_board_full($board) {
    foreach ($board as $row) {
        if (in_array(EMPTY, $row)) {
            return false;
        }
    }
    return true;
}

/**
 * 勝敗判定ロジック
 */
function check_win($board, $player) {
    // 勝利条件のチェック（水平、垂直、対角線）
    
    // 1. 水平チェック
    for ($r = 0; $r < BOARD_SIZE; $r++) {
        for ($c = 0; $c <= BOARD_SIZE - WIN_COUNT; $c++) {
            $count = 0;
            for ($k = 0; $k < WIN_COUNT; $k++) {
                if ($board[$r][$c + $k] === $player) {
                    $count++;
                }
            }
            if ($count === WIN_COUNT) return true;
        }
    }

    // 2. 垂直チェック
    for ($c = 0; $c < BOARD_SIZE; $c++) {
        for ($r = 0; $r <= BOARD_SIZE - WIN_COUNT; $r++) {
            $count = 0;
            for ($k = 0; $k < WIN_COUNT; $k++) {
                if ($board[$r + $k][$c] === $player) {
                    $count++;
                }
            }
            if ($count === WIN_COUNT) return true;
        }
    }

    // 3. 右下がり対角線チェック ( \ )
    for ($r = 0; $r <= BOARD_SIZE - WIN_COUNT; $r++) {
        for ($c = 0; $c <= BOARD_SIZE - WIN_COUNT; $c++) {
            $count = 0;
            for ($k = 0; $k < WIN_COUNT; $k++) {
                if ($board[$r + $k][$c + $k] === $player) {
                    $count++;
                }
            }
            if ($count === WIN_COUNT) return true;
        }
    }

    // 4. 左下がり対角線チェック ( / )
    for ($r = 0; $r <= BOARD_SIZE - WIN_COUNT; $r++) {
        for ($c = WIN_COUNT - 1; $c < BOARD_SIZE; $c++) {
            $count = 0;
            for ($k = 0; $k < WIN_COUNT; $k++) {
                if ($board[$r + $k][$c - $k] === $player) {
                    $count++;
                }
            }
            if ($count === WIN_COUNT) return true;
        }
    }

    return false;
}

/**
 * コンピュータのランダムな着手 (AI)
 */
function computer_move(&$board) {
    $empty_cells = [];
    for ($r = 0; $r < BOARD_SIZE; $r++) {
        for ($c = 0; $c < BOARD_SIZE; $c++) {
            if ($board[$r][$c] === EMPTY) {
                $empty_cells[] = ['r' => $r, 'c' => $c];
            }
        }
    }

    if (!empty($empty_cells)) {
        $move = $empty_cells[array_rand($empty_cells)];
        $board[$move['r']][$move['c']] = COMPUTER;
    }
}

// --- HTML出力開始 ---
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>五目並べ (Gomoku)</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; text-align: center; background-color: #f0f0f5; }
        .container { width: fit-content; margin: 30px auto; padding: 20px; background-color: #fff; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .message { margin: 20px 0; padding: 10px; font-size: 1.2em; font-weight: bold; border-radius: 5px; }
        .board { display: grid; grid-template-columns: repeat(<?php echo BOARD_SIZE; ?>, 40px); margin: 20px auto; border: 3px solid #663300; background-color: #f9e3cc; }
        .cell {
            width: 40px; height: 40px; 
            box-sizing: border-box; 
            border: 1px solid #663300; 
            display: flex; justify-content: center; align-items: center; 
            cursor: pointer;
            position: relative;
        }
        /* 線の装飾をマスの上に配置 (五目並べらしい見た目) */
        .cell::before {
            content: '';
            position: absolute;
            top: 50%; left: 50%;
            width: 1px; height: 100%;
            background-color: #663300;
            transform: translate(-50%, -50%);
        }
        .cell::after {
            content: '';
            position: absolute;
            top: 50%; left: 50%;
            height: 1px; width: 100%;
            background-color: #663300;
            transform: translate(-50%, -50%);
        }
        
        /* 角と端の処理 */
        .board > div:nth-child(<?php echo BOARD_SIZE; ?>n) { border-right: none; }
        .board > div:nth-child(n) { border-top: none; }
        .board > div:nth-child(<?php echo BOARD_SIZE; ?>n + 1) { border-left: none; }
        .board > div:nth-child(n):nth-child(-n+<?php echo BOARD_SIZE; ?>) { border-top: none; }

        .stone { 
            width: 80%; height: 80%; 
            border-radius: 50%; 
            position: relative;
            z-index: 10;
        }
        .stone-player { background-color: black; box-shadow: 1px 1px 2px rgba(0,0,0,0.5); } /* 黒 (Player) */
        .stone-computer { background-color: white; border: 1px solid #333; box-shadow: 1px 1px 2px rgba(0,0,0,0.5); } /* 白 (Computer) */

        .disabled { cursor: default; }
        .reset-button { padding: 10px 20px; font-size: 1em; background-color: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer; margin-top: 20px; }
        .reset-button:hover { background-color: #c82333; }
        .info { margin-top: 15px; color: #666; font-size: 0.9em; }
    </style>
</head>
<body>

<div class="container">
    <h1>五目並べ (Gomoku)</h1>
    <p class="info">盤面: <?php echo BOARD_SIZE; ?>x<?php echo BOARD_SIZE; ?> / 勝利条件: <?php echo WIN_COUNT; ?>連</p>
    
    <div class="message" style="background-color: <?php echo $game_over ? '#ffdddd' : ($current_player === PLAYER ? '#ddffdd' : '#ddddff'); ?>;">
        <?php echo htmlspecialchars($message); ?>
    </div>

    <div class="board">
        <?php for ($r = 0; $r < BOARD_SIZE; $r++): ?>
            <?php for ($c = 0; $c < BOARD_SIZE; $c++): ?>
                <?php
                $cell_content = '';
                $is_empty = $board[$r][$c] === EMPTY;
                
                if ($board[$r][$c] === PLAYER) {
                    $cell_content = '<div class="stone stone-player"></div>';
                } elseif ($board[$r][$c] === COMPUTER) {
                    $cell_content = '<div class="stone stone-computer"></div>';
                }
                
                // クリック可能なマス、かつゲームオーバーでない場合のみフォームを設置
                if ($is_empty && !$game_over) {
                    echo "<form method='POST' action='gomoku.php' style='display: contents;'>";
                    echo "<input type='hidden' name='row' value='$r'>";
                    echo "<input type='hidden' name='col' value='$c'>";
                    echo "<button type='submit' class='cell' style='border: 1px solid #663300;'>$cell_content</button>";
                    echo "</form>";
                } else {
                    // 石が置かれているマス、またはゲームオーバーの場合
                    echo "<div class='cell disabled' style='border: 1px solid #663300;'>$cell_content</div>";
                }
                ?>
            <?php endfor; ?>
        <?php endfor; ?>
    </div>
    
    <form method='POST' action='gomoku.php'>
        <button type='submit' name='reset' value='1' class="reset-button">
            ゲームをリセット
        </button>
    </form>
</div>

</body>
</html>
