<?php

// callback.php

$accessToken = 'v7aoaesRYrsd5s/9K8Ad7uH53/Tws/ewEWwDjJc3MuWOjbC/PRcVG1ZWorOZfvNzEg4NZ3eGO/iYAcnY2NT6gU5UEv7Uli0zamiRIxGM8e7sA0Up+tdAS6aUOkoE9rhs9H+AOe3eIMHKl/7Vr71uLAdB04t89/1O/w1cDnyilFU=';

$jsonString = file_get_contents('php://input');
error_log($jsonString);
$jsonObj = json_decode($jsonString);

$message = $jsonObj->{"events"}[0]->{"message"};
$replyToken = $jsonObj->{"events"}[0]->{"replyToken"};

// 送られてきたメッセージの中身からレスポンスのタイプを選択
if ($message->{"text"} == '確認') {
    // 確認ダイアログタイプ
    $messageData = [
        'type' => 'template',
        'altText' => '確認ダイアログ',
        'template' => [
            'type' => 'confirm',
            'text' => '元気ですかー？',
            'actions' => [
                [
                    'type' => 'message',
                    'label' => '元気です',
                    'text' => '元気で何よりです'
                ],
                [
                    'type' => 'message',
                    'label' => 'まあまあです',
                    'text' => 'まあまあですか、がんばってください！'
                ],
            ]
        ]
    ];
} elseif ($message->{"text"} == 'ボタン') {
    // ボタンタイプ
    $messageData = [
        'type' => 'template',
        'altText' => 'ボタン',
        'template' => [
            'type' => 'buttons',
            'title' => 'タイトルです',
            'text' => '選択してね',
            'actions' => [
                [
                    'type' => 'postback',
                    'label' => 'webhookにpost送信',
                    'data' => 'value'
                ],
                [
                    'type' => 'uri',
                    'label' => 'googleへ移動',
                    'uri' => 'https://google.com'
                ]
            ]
        ]
    ];
} elseif ($message->{"text"} == 'カルーセル' || $message->{"text"} == 'help') {
    // カルーセルタイプ
    $messageData = [
        'type' => 'template',
        'altText' => 'カルーセル',
        'template' => [
            'type' => 'carousel',
            'columns' => [
                [
                    'title' => 'Google',
                    'text' => 'カルーセル1です',
                    'actions' => [
                        [
                            'type' => 'postback',
                            'label' => 'webhookにpost送信',
                            'data' => 'value'
                        ],
                        [
                            'type' => 'uri',
                            'label' => 'Google',
                            'uri' => 'https://google.com'
                        ]
                    ]
                ],
                [
                    'title' => 'Apple',
                    'text' => 'カルーセル2',
                    'actions' => [
                        [
                            'type' => 'uri',
                            'label' => 'Apple 公式ホームページ',
                            'uri' => 'https://www.apple.com/jp/'
                        ],
                        [
                            'type' => 'uri',
                            'label' => 'iMac Pro 新登場',
                            'uri' => 'https://www.apple.com/jp/imac-pro/'
                        ]
                    ]
                ],
				[
					'title' => '選択したテキストを返す',
					'text' => 'カルーセル3',
					'actions' => [
						[
							'type' => 'postback',
							'label' => '確認',
							'data' => '確認'
						],
						[
							'type' => 'postback',
							'label' => 'ボタン',
							'data' => 'ボタン'
						],
						[
							'type' => 'postback',
							'label' => 'ボタン',
							'data' => 'ボタン'
						]
					]
				]
            ]
        ]
    ];
} elseif ($message->{"text"} == '金沢' || $message->{"text"} == "壮真" || $message->{"text"} == "かなざわ" || $message->{"text"} == "そうま" || $message->{"text"} == "金沢壮真") {
	// そうま
	$messageData = [
		'type' => 'text',
		'text' => '金沢壮真 19歳 男性'
	];
	
}else {
    // それ以外は送られてきたテキストをオウム返し
    $messageData = [
        'type' => 'text',
        'text' => 'そのメッセージには対応していません。正しいメッセージを入力してください。'
    ];
}

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