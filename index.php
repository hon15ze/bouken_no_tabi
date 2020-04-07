<?php

ini_set('log_errors','on');  //ログを取る
ini_set('error_log','php.log');  //ログの出力ファイルを指定
session_start();

// モンスター格納用
$monsters = array();

// 生き物クラス
abstract class Creature{
    protected $name;
    protected $hp;
    protected $attackMin;
    protected $attackMax;
    protected $xp;
    public function setName($str){
        $this->name = $str;
    }
    public function getName(){
        return $this->name;
    }
    // HP
    public function setHp($num){
        $this->hp = $num;
    }
    public function getHp(){
        return $this->hp;
    }
    // 攻撃
    public function setAttackMin($num){
        $this->attackMin = $num;
    }
    public function getAttackMin(){
        return $this->attackMin;
    }
    public function setAttackMax($num){
        $this->attackMax = $num;
    }
    public function getAttackMax(){
        return $this->attackMax;
    }
    public function attack($targetObj){
        $attackPoint = mt_rand($this->getAttackMin(), $this->getAttackMax());
        if(!mt_rand(0,9)){ //10分の1の確率でクリティカル
            $attackPoint = $attackPoint * 1.5;
            $attackPoint = (int)$attackPoint;
            History::set($this->getName().'の クリティカルヒット!!');
        }
        $targetObj->setHp($targetObj->getHp()-$attackPoint);
        History::set($attackPoint.'ポイントの ダメージ！');
    }
    //経験値
    public function setXp($num){
        $this->xp = $num;
    }
    public function getXp(){
        return $this->xp;
    }
    
}
// 人クラス
$human = array();
class Human extends Creature{
    protected $mp, $level;
    public function __construct($name, $hp, $mp, $attackMin, $attackMax, $xp, $level) {
        $this->name = $name;
        $this->hp = $hp;
        $this->mp = $mp;
        $this->attackMin = $attackMin;
        $this->attackMax = $attackMax;
        $this->xp = $xp;
        $this->level = $level;
    }

    public function ex(){
        $_SESSION['human']->setXp( $_SESSION['monster']->getXp() + $this->xp );
        History::set($this->name.'は '.$_SESSION['monster']->getXp().'ポイントの けいけんちを もらった！');
}
//レベル
public function setLevel($num){
    $this->level = $num;
}
public function getLevel(){
    return $this->level;
}
//MP
public function setMp($num){
    $this->mp = $num;
}
public function getMp(){
    return $this->mp;
}
public function recover(){
    if(!mt_rand(0,7)){ //8分の1の確率で失敗
    History::set($this->name.'は かいふくに しっぱいした');
    $_SESSION['human']->setMp( $_SESSION['human']->getMp() - 50 );
}else{
    $_SESSION['human']->setMp( $_SESSION['human']->getMp() - 50 );
    $_SESSION['human']->setHp( $_SESSION['human']->getHp() + mt_rand(50,100));
    History::set($this->name.'は かいふくまほうを つかった！');
}

}
}

// モンスタークラス
class Monster extends Creature{
    // プロパティ
    protected $img;
    // コンストラクタ
    public function __construct($name, $hp, $img, $attackMin, $attackMax, $xp) {
        $this->name = $name;
        $this->hp = $hp;
        $this->img = $img;
        $this->attackMin = $attackMin;
        $this->attackMax = $attackMax;
        $this->xp = $xp;
    }
    // ゲッター
    public function getImg(){
        return $this->img;
    }

}
// 魔法を使えるモンスタークラス
class MagicMonster extends Monster{
    private $magicAttack;
    function __construct($name, $hp, $img, $attackMin, $attackMax, $magicAttack, $xp) {
        parent::__construct($name, $hp, $img, $attackMin, $attackMax, $xp);
        $this->magicAttack = $magicAttack;
    }
    public function getMagicAttack(){
        return $this->magicAttack;
    }
    public function attack($targetObj){
        if(!mt_rand(0,4)){ //5分の1の確率で魔法攻撃
        History::set($this->name.'の まほうこうげき!!');
        $targetObj->setHp( $targetObj->getHp() - $this->magicAttack );
        History::set($this->magicAttack.'ポイントの ダメージを うけた！');
    }else{
        parent::attack($targetObj);
}
}
}
// ラスボスモンスタークラス
class LastBossMonster extends Monster{
    private $superAttack;
    function __construct($name, $hp, $img, $attackMin, $attackMax, $superAttack, $xp) {
        parent::__construct($name, $hp, $img, $attackMin, $attackMax, $xp);
        $this->superAttack = $superAttack;
    }
    public function getSuperAttack(){
        return $this->superAttack;
    }
    public function attack($targetObj){
        if($_SESSION['monster']->getHp() <= 0){
            $_SESSION = array();
            header("Location:gameclear.php"); //ゲームクリア画面へ
        }else{
        if(!mt_rand(0,2)){ //3分の1の確率で必殺攻撃
        History::set($this->name.'の ひっさつこうげき!!');
        $targetObj->setHp( $targetObj->getHp() - $this->superAttack );
        History::set($this->superAttack.'ポイントの ダメージを うけた！');
    }else{
        parent::attack($targetObj);
}
}
}
}
// 履歴管理クラス
class History{
    public static function set($str){
        // セッションhistoryがなければ作る
        if(empty($_SESSION['history'])) $_SESSION['history'] = '';
        // 文字列をセッションhistoryへ格納
        $_SESSION['history'] .= $str.'<br>';
    }
    public static function clear(){
        unset($_SESSION['history']);
    }
}

// インスタンス生成
$human = new Human('ぼうけんしゃ', 500, 200, 40, 120, 0, 1);
$monsters[] = new Monster( 'みどりスライム', 100, 'img/みどりスライム.png', 20, 40, mt_rand(20, 30) );
$monsters[] = new MagicMonster( 'ウンディーネ', 300, 'img/ウンディーネ.png', 30, 70,mt_rand(50, 100), mt_rand(200, 300) );
$monsters[] = new Monster( 'オーク', 500, 'img/オーク.png', 30, 50, mt_rand(400, 500) );
$monsters[] = new Monster( 'ケルベロス', 400, 'img/ケルベロス.png', 30, 50, mt_rand(300, 400) );
$monsters[] = new Monster( 'ゴースト', 100, 'img/ゴースト.png', 20, 40, mt_rand(20, 30) );
$monsters[] = new Monster( 'ゴーレム', 200, 'img/ゴーレム.png', 20, 60, mt_rand(100, 200) );
$monsters[] = new Monster( 'コボルト', 100, 'img/コボルト.png', 20, 40, mt_rand(20, 30) );
$monsters[] = new Monster( 'サイクロプス', 100, 'img/サイクロプス.png', 20, 50, mt_rand(20, 30) );
$monsters[] = new MagicMonster( 'サラマンダー', 300, 'img/サラマンダー.png', 30, 70,mt_rand(50,100), mt_rand(200, 300)  );
$monsters[] = new Monster( 'スケルトン', 100, 'img/スケルトン.png', 20, 40, mt_rand(20, 30) );
$monsters[] = new Monster( 'デュラハン', 400, 'img/デュラハン.png', 40, 80, mt_rand(300, 400) );
$monsters[] = new Monster( 'ドラゴン', 1000, 'img/ドラゴン.png', 80, 120, mt_rand(800, 1000) );
$monsters[] = new Monster( 'トレント', 200, 'img/トレント.png', 40, 60, mt_rand(100, 200) );
$monsters[] = new Monster( 'ヒュドラ', 200, 'img/ヒュドラ.png', 40, 60, mt_rand(100, 200) );
$monsters[] = new Monster( 'マンドラゴラ', 100, 'img/マンドラゴラ.png', 20, 40, mt_rand(20, 30) );
$monsters[] = new Monster( 'ミミック', 300, 'img/ミミック.png', 20, 60, mt_rand(200, 300) );
$monsters[] = new Monster( 'リザードマン', 100, 'img/リザードマン.png', 20, 40, mt_rand(20, 30) );
$monsters[] = new MagicMonster( 'リッチ', 300, 'img/リッチ.png', 50, 100,mt_rand(70,130), mt_rand(200, 300)  );
$monsters[] = new LastBossMonster( 'リバイアサン', 10000, 'img/リバイアサン.png', 100, 500, mt_rand(1000, 1100), mt_rand(2000, 2100) );
$monsters[] = new Monster( 'しにがみ', 600, 'img/死神.png', 50, 90, mt_rand(400, 500) );

function createMonster(){
    global $monsters;
    $monster = $monsters[mt_rand(0, 19)];
    History::set($monster->getName().'が あらわれた！');

    $_SESSION['monster'] =  $monster;
}
function createHuman(){
    global $human;
    $_SESSION['human'] = $human;
}
function init(){
    History::clear();
    History::set('はじめから！');
    $_SESSION['knockDownCount'] = 0;
    $_SESSION['levelCount'] = 0;
    createHuman();
    createMonster();
}
function gameOver(){
    $_SESSION = array();
    header("Location:gameover.php"); //ゲームオーバー画面へ
}


//1.post送信されていた場合
if(!empty($_POST)){
    $attackFlg = (!empty($_POST['attack'])) ? true : false;
    $startFlg = (!empty($_POST['start'])) ? true : false;
    $recoverFlg = (!empty($_POST['recover'])) ? true : false;
error_log('POSTされた！');

if($startFlg){
    History::set('ゲームスタート！');
    init();
}else{
    // 攻撃するを押した場合
    if($attackFlg){

        // モンスターに攻撃を与える
        History::clear();
        History::set($_SESSION['human']->getName().'の こうげき！');
        $_SESSION['human']->attack($_SESSION['monster']);

        // モンスターが攻撃をする
        History::set($_SESSION['monster']->getName().'の こうげき！');
        $_SESSION['monster']->attack($_SESSION['human']);


        // 自分のhpが0以下になったらゲームオーバー
        if($_SESSION['human']->getHp() <= 0){
            gameOver();
        }else{
            // hpが0以下になったら、別のモンスターを出現させる
            if($_SESSION['monster']->getHp() <= 0){
                History::set($_SESSION['monster']->getName().'を たおした！');
                //経験値
                $_SESSION['human']->ex($_SESSION['monster']);
                 //レベルアップ
                 if($_SESSION['human']->getXp() >= ($_SESSION['human']->getLevel()*80)){
                    History::set($_SESSION['human']->getName().'は '.floor($_SESSION['human']->getXp()/($_SESSION['human']->getLevel()*80)).'レベルアップ した！');
                    //上がるレベル数
                    $_SESSION['human']->setLevel( $_SESSION['human']->getLevel() +floor($_SESSION['human']->getXp()/100));
                    //経験値をリセット
                    $_SESSION['human']->setXp( $_SESSION['human']->getXp() - $_SESSION['human']->getXp() );
                    //HPを回復
                    $_SESSION['human']->setHp($_SESSION['human']->getHp() + 400);
                    History::set($_SESSION['human']->getName().'は HPを かいふくした！');
                    //MPを回復
                    $_SESSION['human']->setMp($_SESSION['human']->getMp() + 200);
                    History::set($_SESSION['human']->getName().'は MPを かいふくした！');
                    //攻撃力がアップ
                    $_SESSION['human']->setAttackMin($_SESSION['human']->getAttackMin() * 1.75);
                    $_SESSION['human']->setAttackMax($_SESSION['human']->getAttackMax() * 1.75);
                }
                $_SESSION['knockDownCount'] = $_SESSION['knockDownCount']+1;
                createMonster();
            }
           
        }
    }else{
        //回復するを押した場合
        if($recoverFlg){
            if($_SESSION['human']->getMp() <= 0){
                History::clear();
                History::set('だめだ! MPがなくなって まほうがつかえない！');
            }else{
                History::clear();
            $_SESSION['human']->recover();
            }

        }else{ //逃げるを押した場合
        if(!mt_rand(0,4)){ //5分の1の確率で失敗
            History::clear();
            History::set('だめだ！ まわりこまれて にげられない！');

        }else{
        History::clear();
        History::set($_SESSION['human']->getName().'は にげだした！');
        createMonster();
    }
}
}
}
$_POST = array();
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ぼうけんのたび</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link href="https://fonts.googleapis.com/earlyaccess/nicomoji.css" rel="stylesheet">
    <!-- フォントアイコン -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css" integrity="sha256-UzFD2WYH2U1dQpKDjjZK72VtPeWP50NoJjd26rnAdUI=" crossorigin="anonymous" />

    <!-- ツイッターカード-->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@hon15ze" />
    <meta property="og:url" content="hon15ze.php.xdomain.jp" />
    <meta property="og:title" content="ぼうけんのたび" />
    <meta property="og:description" content="オブジェクト指向アウトプットで作ったゲームです" />
    <meta property="og:image" content="https://box.c.yimg.jp/res/box-l-kpyxxrigoa7igebiycqjncqe2q-1001?uid=c35ed679-2f31-4975-b480-ee9d06c17fb5&etag=e5e24256ff84bec8a725a24c2c2a81be" />
</head>
<body>

    <?php if(empty($_SESSION)){ ?>
        <h1>ぼうけんのたび</h1>
        <div class="games">
            <h2>GAME START ?</h2>
            <p class="outline">よくあるRPGのせかいに まよいこんでしまった あなた！<br>
                ぼうけんしゃ として このせかいを たびしよう！<br>
                <img src="img/冒険者.png" class="over"><br>
                じゅんびは いいかな？
            </p>
            <form method="post">
                <div class="b-c">
                    <input type="submit" name="start" class="start"value="▶︎スタート">
                </div>
            </form>

            <?php }else{ ?>
            <div class="games">

                <h2><?php echo $_SESSION['monster']->getName().'が あらわれた!!'; ?></h2>
                <div class="monster-i">
                    <img src="<?php echo $_SESSION['monster']->getImg(); ?>" class="m-img">
                </div>
                <p class="m-hp">モンスターのHP：<?php echo $_SESSION['monster']->getHp(); ?></p>

            <div class="status">
                <p><?php echo $_SESSION['human']->getName(); ?></p>
                <p>HP<i class="fas fa-heart hp"></i><?php echo $_SESSION['human']->getHp(); ?></p>
                <p>MP<i class="fas fa-heart mp"></i><?php echo $_SESSION['human']->getMp(); ?></p>
                <p>EX：<?php echo $_SESSION['human']->getXp(); ?></p>
                <p>レベル<?php echo $_SESSION['human']->getLevel(); ?></p>
                <p>たおしたモンスターのかず：<?php echo $_SESSION['knockDownCount']; ?></p>
            </div>

            <form method="post">
                <input type="submit" name="attack" class="a" value="▶︎こうげき">
                <input type="submit" name="recover" class="r" value="▶︎かいふくまほう">
                <input type="submit" name="escape" class="e" value="▶︎にげる">
                <input type="submit" name="start" class="s" value="▶︎はじめから">
            </form>

        </div>

        <div id="scroll-box" class="history">
            <p id="typing"><?php echo (!empty($_SESSION['history'])) ? $_SESSION['history'] : ''; ?></p>
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

        <script>

        jQuery( function() {
	        autoScroll();
        } );
        var $scrollY = 0;
        function autoScroll() {
	        var $sampleBox = jQuery( '#typing' );
	        $sampleBox.scrollTop( ++$scrollY );
        	if( $scrollY < $sampleBox[0].scrollHeight - $sampleBox[0].clientHeight ){
        	setTimeout( "autoScroll()", 1 );
       	}else{
    	    $scrollY = 0;
        }}
        //function typing(str = ""){
        //    let buf = document.getElementById("typing").innerHTML; //書き込み済みの文字を要素から取得
        //    let writed = buf.length; //書き込み済みの文字数を取得
        //    let write = "";
        //   if(writed < str.length){
        //     write = str.charAt(writed); //1文字だけ取得する
        //     
        // }else{
        // }
        //   document.getElementById("typing").innerHTML = buf + write; //1文字だけ追加していく
        //}

        //const str = document.getElementById("typing").innerHTML; //書き込む文字を要素から取得
        //const delay = 100 //1文字が表示される時間

        //document.getElementById("typing").innerHTML = "";
        //window.setInterval(function(){typing(str);}, delay);
    </script>

    <?php } ?>

</body>
</html>
