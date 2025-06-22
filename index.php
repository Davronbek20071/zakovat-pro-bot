<?php
$token = "8177096885:AAFugF6dh2YFcAfgdaRwBZCiIys6FqK8GoE";
$admin_id = "7342925788";

$content = file_get_contents("php://input");
$update = json_decode($content, true);

$chat_id = $update["message"]["chat"]["id"] ?? $update["callback_query"]["message"]["chat"]["id"];
$text = $update["message"]["text"] ?? null;
$callback = $update["callback_query"]["data"] ?? null;
$user_id = $update["message"]["from"]["id"] ?? $update["callback_query"]["from"]["id"];

$state_file = "state_$user_id.txt";
$data_files = [
    "quiz" => "quiz.json",
    "savol" => "data.json",
    "ilm" => "ilm.json",
    "kunsozi" => "kunsozi.json",
    "mantiq" => "mantiq.json"
];

function sendMessage($chat_id, $text, $buttons = null) {
    $url = "https://api.telegram.org/bot" . $GLOBALS['token'] . "/sendMessage";
    $data = ["chat_id" => $chat_id, "text" => $text, "parse_mode" => "HTML"];
    if ($buttons) {
        $data["reply_markup"] = json_encode(["inline_keyboard" => $buttons]);
    }
    file_get_contents($url, false, stream_context_create([
        "http" => ["method" => "POST", "header" => "Content-Type: application/x-www-form-urlencoded", "content" => http_build_query($data)]
    ]));
}

function saveState($user_id, $state) {
    file_put_contents("state_$user_id.txt", $state);
}
function getState($user_id) {
    return file_exists("state_$user_id.txt") ? file_get_contents("state_$user_id.txt") : null;
}
function clearState($user_id) {
    @unlink("state_$user_id.txt");
}

function answerCallback($id) {
    file_get_contents("https://api.telegram.org/bot" . $GLOBALS['token'] . "/answerCallbackQuery?callback_query_id=$id");
}

function loadJson($file) {
    return file_exists($file) ? json_decode(file_get_contents($file), true) : [];
}
function saveJson($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// BOSHLANISHI
if ($text == "/start") {
    $menu = [
        [["text" => "📌 Bugungi Savol", "callback_data" => "bugungi_savol"]],
        [["text" => "🧪 Quiz", "callback_data" => "start_quiz"]],
        [["text" => "🧠 Mantiqiy topshiriq", "callback_data" => "mantiq"]],
        [["text" => "🧾 1 daqiqa ilm", "callback_data" => "ilm"]],
        [["text" => "💬 Kun so‘zi", "callback_data" => "kunsozi"]],
        [["text" => "📊 Reyting", "callback_data" => "reyting"]]
    ];
    sendMessage($chat_id, "🧠 <b>Assalomu alaykum!</b>\nBu — Zakovat Pro. Har kuni bilim va tafakkur bilan yasha!", $menu);
}

// ADMIN PANEL
elseif ($text == "/admin" && $chat_id == $admin_id) {
    $panel = [
        [["text" => "➕ Bugungi savol", "callback_data" => "add_savol"]],
        [["text" => "➕ 1 daqiqa ilm", "callback_data" => "add_ilm"]],
        [["text" => "➕ Kun so‘zi", "callback_data" => "add_kunsozi"]],
        [["text" => "➕ Mantiqiy topshiriq", "callback_data" => "add_mantiq"]],
        [["text" => "➕ Quiz savol", "callback_data" => "add_quiz"]]
    ];
    sendMessage($chat_id, "👨‍💼 Admin Panel:", $panel);
}

// Callback qayta ishlash (qisqartirilgan)
elseif ($callback) {
    answerCallback($update["callback_query"]["id"]);
    include "callbacks.php";
}

// Matnli holatlar
elseif ($text && getState($user_id)) {
    include "text_states.php";
}
