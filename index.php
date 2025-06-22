<?php
$TOKEN = "8177096885:AAFugF6dh2YFcAfgdaRwBZCiIys6FqK8GoE";
define("BOT_TOKEN", $TOKEN);

function sendMessage($chat_id, $text, $buttons = null) {
    $url = "https://api.telegram.org/bot" . BOT_TOKEN . "/sendMessage";
    $data = ['chat_id' => $chat_id, 'text' => $text, 'parse_mode' => 'HTML'];
    if ($buttons) {
        $data['reply_markup'] = json_encode(['inline_keyboard' => $buttons]);
    }
    file_get_contents($url . "?" . http_build_query($data));
}

$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (isset($update["callback_query"])) {
    $callback = $update["callback_query"]["data"];
    $chat_id = $update["callback_query"]["message"]["chat"]["id"];
    $user_id = $update["callback_query"]["from"]["id"];
    include("callbacks.php");
    exit;
}

if (isset($update["message"])) {
    $text = $update["message"]["text"];
    $chat_id = $update["message"]["chat"]["id"];
    $user_id = $update["message"]["from"]["id"];
    include("text_states.php");
    exit;
}
