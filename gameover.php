<?php

ini_set('log_errors','on');  //ログを取る
ini_set('error_log','php.log');  //ログの出力ファイルを指定
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ぼうけんのたび</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link href="https://fonts.googleapis.com/earlyaccess/nicomoji.css" rel="stylesheet">

</head>
<body>

<h1>ぼうけんのたび</h1>
<div class="games">

<h2>GAME OVER…</h2>
<p class="outline">それいらい そのものを みたものは いなかった…<br>
<img src="img/ゲームオーバー.png" class="over"><br>
コンティニュー しますか？
</p>
<div class="b-c">
<a href="index.php" class="reset">▶︎はい</a>
</div>



</body>
</html>
