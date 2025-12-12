<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ゲーム一覧</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin-top: 50px; background-color: #f4f4f9; }
        .container { 
            width: 80%; 
            max-width: 600px; 
            margin: auto; 
            padding: 30px; 
            border: 1px solid #ccc; 
            border-radius: 10px; 
            box-shadow: 0 4px 8px rgba(0,0,0,0.1); 
            background-color: white;
        }
        h1 { 
            color: #333; 
            border-bottom: 2px solid #5cb85c; 
            padding-bottom: 10px; 
            margin-bottom: 25px;
        }
        ul { 
            list-style: none; 
            padding: 0; 
            text-align: left;
        }
        li {
            margin-bottom: 15px;
        }
        li a {
            display: block;
            padding: 12px 15px;
            background-color: #5cb85c;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1.1em;
            transition: background-color 0.3s;
        }
        li a:hover {
            background-color: #4cae4c;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>ゲーム一覧</h1>

    <ul>
        <li>
            <a href="./janken.php">じゃんけんゲーム (✊✋✌️)</a>
        </li>
        <li>
            <a href="./gomoku.php">五目並べ</a>
        </li>
        </ul>
</div>

</body>
</html>
