<?php

/*****************************************************************************************
 　ぐるなびWebサービスのレストラン検索APIで緯度経度検索を実行しパースするプログラム
 　注意：緯度、経度、範囲の値は固定で入れています。
 　　　　アクセスキーはユーザ登録時に発行されたキーを指定してください。
*****************************************************************************************/

// Line API
$accessToken = 'v7aoaesRYrsd5s/9K8Ad7uH53/Tws/ewEWwDjJc3MuWOjbC/PRcVG1ZWorOZfvNzEg4NZ3eGO/iYAcnY2NT6gU5UEv7Uli0zamiRIxGM8e7sA0Up+tdAS6aUOkoE9rhs9H+AOe3eIMHKl/7Vr71uLAdB04t89/1O/w1cDnyilFU=';

$jsonString = file_get_contents('php://input');
error_log($jsonString);
$jsonObj = json_decode($jsonString);

$message = $jsonObj->{"events"}[0]->{"message"};
$replyToken = $jsonObj->{"events"}[0]->{"replyToken"};

// GURUNAVI Service
 
//エンドポイントのURIとフォーマットパラメータを変数に入れる
$uri   = "http://api.gnavi.co.jp/RestSearchAPI/20150630/";
//APIアクセスキーを変数に入れる
$acckey= "198d3912d63f7394f59748a3a797e742";
//返却値のフォーマットを変数に入れる
$format= "json";
//緯度・経度、範囲を変数に入れる
//緯度経度は日本測地系で日比谷シャンテのもの。範囲はrange=1で300m以内を指定している。
//$lat   = 35.670083;
//$lon   = 139.763267;
//$range = 1;
 
//URL組み立て
$url  = sprintf("%s%s%s%s%s%s%s%s%s%s%s", $uri, "?format=", $format, "&keyid=", $acckey, "&freeword=", $message->{"text"});
//API実行
$json = file_get_contents($url);
//取得した結果をオブジェクト化
$obj  = json_decode($json);
 
//結果をパース
//トータルヒット件数、店舗番号、店舗名、最寄の路線、最寄の駅、最寄駅から店までの時間、店舗の小業態を出力
foreach((array)$obj as $key => $val){
   if(strcmp($key, "total_hit_count" ) == 0 ){
       echo "total:".$val."\n";
   }
 
   if(strcmp($key, "rest") == 0){
       foreach((array)$val as $restArray){
            if(checkString($restArray->{'id'}))   echo $restArray->{'id'}."\t";
            if(checkString($restArray->{'name'})) echo $restArray->{'name'}."\t";
            if(checkString($restArray->{'access'}->{'line'}))    echo (string)$restArray->{'access'}->{'line'}."\t";
            if(checkString($restArray->{'access'}->{'station'})) echo (string)$restArray->{'access'}->{'station'}."\t";
            if(checkString($restArray->{'access'}->{'walk'}))    echo (string)$restArray->{'access'}->{'walk'}."分\t";
 
            foreach((array)$restArray->{'code'}->{'category_name_s'} as $v){
                if(checkString($v)) echo $v."\t";
            }
            echo "\n";
       }
 
   }
}
 
//文字列であるかをチェック
function checkString($input)
{
 
    if(isset($input) && is_string($input)) {
        return true;
    }else{
        return false;
    }
 
}


$messageData = [
		'type' => 'text',
		'text' => $restArray->{'name'}."\t"
	];

$response = [
    'replyToken' => $replyToken,
    'messages' => [$messageData]
];
error_log(json_encode($response));

$ch = curl_init('https://api.line.me/v2/bot/message/reply');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json; charser=UTF-8',
    'Authorization: Bearer ' . $accessToken
));
$result = curl_exec($ch);
error_log($result);
curl_close($ch);
