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
        <h2>GAME CLEAR!!</h2>
            <p class="outline">あなたは ラスボスを たおしました！<br>
                これで このせかいにも へいわが おとずれることでしょう！<br>
                THANK YOU FOR PLAYING!!<br>
                <img src="img/クリア.png" class="over"><br>
                もういちど あそぶ？
            </p>
        <div class="b-c">
            <a href="index.php" class="reset">▶︎はい</a>
        </div>
    </div>
    
    </body>
</html>
