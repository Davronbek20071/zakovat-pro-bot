<?php
$token = "8177096885:AAFugF6dh2YFcAfgdaRwBZCiIys6FqK8GoE";
$admin_id = "7342925788";

$content = file_get_contents("php://input");
$update = json_decode($content, true);

$chat_id = $update["message"]["chat"]["id"] ?? $update["callback_query"]["message"]["chat"]["id"];
$text = $update["message"]["text"] ?? null;
$callback = $update["callback_query"]["data"] ?? null;
$user_id = $update["message"]["from"]["id"] ?? $update["callback_query"]["from"]["id"];

// Foydalanuvchi holatini eslab qolish uchun
$state_file = "state_$user_id.txt";
$data_file = "data.json";

function sendMessage($chat_id, $text, $buttons = null) {
    $url = "https://api.telegram.org/bot" . $GLOBALS['token'] . "/sendMessage";

    $data = [
        "chat_id" => $chat_id,
        "text" => $text,
        "parse_mode" => "HTML"
    ];

    if ($buttons) {
        $data["reply_markup"] = json_encode(["inline_keyboard" => $buttons]);
    }

    file_get_contents($url, false, stream_context_create([
        "http" => [
            "method"  => "POST",
            "header"  => "Content-Type: application/x-www-form-urlencoded",
            "content" => http_build_query($data)
        ]
    ]));
}

function answerCallback($callback_id) {
    file_get_contents("https://api.telegram.org/bot" . $GLOBALS['token'] . "/answerCallbackQuery?callback_query_id=$callback_id");
}

// START komandasi
if ($text == "/start") {
    $menu = [
        [["text" => "📌 Bugungi Savol", "callback_data" => "savol"]],
        [["text" => "📊 Reyting (demo)", "callback_data" => "reyting"]],
        [["text" => "👑 Intellekt Klub", "callback_data" => "klub"]]
    ];
    sendMessage($chat_id, "🧠 <b>Assalomu alaykum!</b>\nBu — Zakovat Pro! Har kuni sizga ilm, zakovat va tafakkur sovg‘a qilamiz.", $menu);
}

// ADMIN PANEL
elseif ($text == "/admin" && $chat_id == $admin_id) {
    $panel = [
        [["text" => "➕ Savol qo‘shish", "callback_data" => "add_savol"]],
        [["text" => "📈 Statistika", "callback_data" => "stats"]]
    ];
    sendMessage($chat_id, "👨‍💼 Admin Panel:", $panel);
}

// CALLBACK tugmalar
elseif ($callback) {
    $callback_id = $update["callback_query"]["id"];
    answerCallback($callback_id);

    switch ($callback) {
        case "savol":
            if (file_exists($data_file)) {
                $data = json_decode(file_get_contents($data_file), true);
                file_put_contents($state_file, "awaiting_answer");
                sendMessage($chat_id, "📌 <b>Savol:</b> {$data['savol']}\n\n✍️ Javobingizni yozing:");
            } else {
                sendMessage($chat_id, "⛔ Hozircha savol mavjud emas.");
            }
            break;

        case "reyting":
            sendMessage($chat_id, "📊 Reyting: Hozircha demo rejimda.");
            break;

        case "klub":
            sendMessage($chat_id, "👑 Intellekt Klubga hush kelibsiz!");
            break;

        case "add_savol":
            file_put_contents($state_file, "awaiting_question");
            sendMessage($chat_id, "📝 Savolni yuboring (so‘ngra javobni):");
            break;

        case "stats":
            sendMessage($chat_id, "📈 Statistika: Demo foydalanuvchi soni: 1");
            break;
    }
}

// Javobni tekshirish yoki savol yozish jarayoni
elseif ($text && file_exists($state_file)) {
    $state = file_get_contents($state_file);

    if ($chat_id == $admin_id && $state == "awaiting_question") {
        file_put_contents("temp_savol.txt", $text);
        file_put_contents($state_file, "awaiting_answer_save");
        sendMessage($chat_id, "✅ Endi to‘g‘ri javobni yuboring:");
    }
    elseif ($chat_id == $admin_id && $state == "awaiting_answer_save") {
        $savol = file_get_contents("temp_savol.txt");
        $javob = $text;
        $json = ["savol" => $savol, "javob" => strtolower($javob)];
        file_put_contents($data_file, json_encode($json));
        unlink("temp_savol.txt");
        unlink($state_file);
        sendMessage($chat_id, "✅ Savol saqlandi!");
    }
    elseif ($state == "awaiting_answer") {
        $user_javob = strtolower(trim($text));
        $data = json_decode(file_get_contents($data_file), true);
        $to_gri = strtolower($data["javob"]);

        if ($user_javob == $to_gri) {
            sendMessage($chat_id, "✅ <b>To‘g‘ri!</b>\nSiz savolga to‘g‘ri javob berdingiz. 👍");
        } else {
            sendMessage($chat_id, "❌ <b>Noto‘g‘ri.</b>\nTo‘g‘ri javob: <b>$to_gri</b>");
        }

        unlink($state_file);
    }
}
?>
