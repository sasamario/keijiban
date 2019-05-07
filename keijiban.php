<?php

$dataFile='keijiban.txt';

//csrf対策　
session_start();
function setToken(){
  $token=sha1(uniqid(mt_rand(),true));
  $_SESSION['token']=$token;
}
function checkToken(){
  if(empty($_SESSION['token']) || ($_SESSION['token'] !=$_POST['token'])){
    echo "不正なPOSTが行われました！";
    exit;
  }
}

function h($s){
   //htmlspecialchars()はHTMLにおいて特殊な意味を持つ文字を、そのまま表示できるようHTMLの表示形式に変換する
  return htmlspecialchars($s,ENT_QUOTES,'UTF-8');
}

if($_SERVER['REQUEST_METHOD']=='POST' &&
  isset($_POST['message'])&&  //issetは、変数がセットされていることと、NULLでないことを検査する関数
  isset($_POST['user'])){

  checkToken();

  $message=trim($_POST['message']);  //trim関数は文字列の先頭と末尾の空白文字を取り除くことができる
  $user=trim($_POST['user']);

  if($message !==''){  //$messageが空じゃなかったとき

    $user=($user==='')?$user='名無しさん':$user; //$userが空の場合名無し　名前が書いてあったらその名前を適用
    //str_replace ("検索文字列", "置換え文字列", "対象文字列");
    $message=str_replace("\t",'',$message);  // str_replace関数は検索文字列に一致したすべての文字列を置換し、置き換えた後の文字列を返す関数
    $user=str_replace("\t",'',$user);
    $time=date('Y-m-d H:i:s');
    $newData=$message."\t".$user."\t".$time."\n";

    $fp=fopen($dataFile,'a');  //$fpはファイルハンドル　fopenはファイルを開く
    fwrite($fp,$newData);
    fclose($fp);
  }
}else{
  setToken();
}

$posts=file($dataFile,FILE_IGNORE_NEW_LINES);  //最後の改行記号を取り除く
$posts=array_reverse($posts); //配列の中身はそのまま　時系列は新しい順になる

 ?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>簡易掲示板</title>
  </head>
  <body>
    <h1>簡易掲示板</h1>
    <form action="" method="post">
      message:<input type="text" name="message">
      user:<input type="text" name="user">
      <input type="submit" value="投稿">
      <input type="hidden" name="token" value="<?php echo h($_SESSION['token']); ?>">
    </form>
    <h2>投稿一覧(<?php echo count($posts); ?>件) </h2>
    <ul>
      <?php if(count($posts)) : ?>
        <?php foreach($posts as $post) : ?>
        <?php list($message,$user,$time)=explode("\t",$post); ?>
          <li><?php echo h($message); ?> (<?php echo h($user); ?>) -<?php echo h($time); ?></li>
        <?php endforeach; ?>
      <?php else : ?>
        <li>まだ投稿はありません。</li>
      <?php endif; ?>
    </ul>
  </body>
</html>
