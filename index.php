<?php
$token = "8177096885:AAFugF6dh2YFcAfgdaRwBZCiIys6FqK8GoE";
$admin_id = "7342925788";

$content = file_get_contents("php://input");
$update = json_decode($content, true);

$chat_id = $update["message"]["chat"]["id"] ?? $update["callback_query"]["message"]["chat"]["id"];
$text = $update["message"]["text"] ?? null;
$callback = $update["callback_query"]["data"] ?? null;

// Yuborish funksiyasi (inline buttons bilan)
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

    $options = [
        "http" => [
            "method" => "POST",
            "header" => "Content-type: application/x-www-form-urlencoded\r\n",
            "content" => http_build_query($data)
        ]
    ];

    $context = stream_context_create($options);
    file_get_contents($url, false, $context);
}

// Inline tugmaga javob yozuvchi
function answerCallback($callback_id) {
    $url = "https://api.telegram.org/bot" . $GLOBALS['token'] . "/answerCallbackQuery";
    $data = ["callback_query_id" => $callback_id];

    $options = [
        "http" => [
            "method" => "POST",
            "header" => "Content-type: application/x-www-form-urlencoded\r\n",
            "content" => http_build_query($data)
        ]
    ];
    $context = stream_context_create($options);
    file_get_contents($url, false, $context);
}

// Foydalanuvchi /start yozsa
if ($text == "/start") {
    $buttons = [
        [["text" => "🧠 Bugungi Savol", "callback_data" => "savol"]],
        [["text" => "🎥 1 daqiqa Ilm", "callback_data" => "ilm"]],
        [["text" => "🧩 Mantiqiy topshiriq", "callback_data" => "mantik"]],
        [["text" => "💬 Kun so‘zi", "callback_data" => "soz"]],
        [["text" => "📊 Reyting", "callback_data" => "reyting"]],
        [["text" => "👑 Intellekt Klub", "callback_data" => "klub"]]
    ];
    $text = "🧠 <b>Assalomu alaykum!</b>\nBu — Zakovat Pro! Har kuni sizga ilm, zakovat va tafakkur sovg‘a qilamiz.";
    sendMessage($chat_id, $text, $buttons);
}

// Admin panel
elseif ($text == "/admin" && $chat_id == $admin_id) {
    $buttons = [
        [["text" => "➕ Savol yuklash", "callback_data" => "add_savol"]],
        [["text" => "🎥 Ilm qo‘shish", "callback_data" => "add_ilm"]],
        [["text" => "🧩 Mantiqiy topshiriq", "callback_data" => "add_mantik"]],
        [["text" => "💬 Kun so‘zi qo‘shish", "callback_data" => "add_soz"]],
        [["text" => "📈 Statistika", "callback_data" => "stats"]],
        [["text" => "📢 Xabar yuborish", "callback_data" => "broadcast"]]
    ];
    sendMessage($chat_id, "👨‍💼 Admin Panel:", $buttons);
}

// Callback ishlovchilar
elseif ($callback) {
    $callback_id = $update["callback_query"]["id"];
    answerCallback($callback_id); // Tugmaga bosilganda loading yo‘qolsin

    switch ($callback) {
        case "savol":
            sendMessage($chat_id, "📌 Bugungi savol: Yer sayyorasi nechta qit'adan iborat?");
            break;
        case "ilm":
            sendMessage($chat_id, "📚 Bugungi ilmiy fakt: Suv muzlab qolganda kengayadi.");
            break;
        case "mantik":
            sendMessage($chat_id, "🤔 Mantiqiy savol: Mashina 100km masofani 1 soatda bosib o‘tdi. Tezligi nechchi?");
            break;
        case "soz":
            sendMessage($chat_id, "📖 Kun so‘zi: 'Ilm' – bilim olish va uni hayotga tadbiq qilish.");
            break;
        case "reyting":
            sendMessage($chat_id, "🏆 Sizning reytingiz: 0 ball\n(Endi qatnashishni boshlang)");
            break;
        case "klub":
            sendMessage($chat_id, "👑 Intellekt Klubga xush kelibsiz!");
            break;
        case "add_savol":
            sendMessage($chat_id, "➕ Yangi savol yuboring:");
            break;
        case "add_ilm":
            sendMessage($chat_id, "🎥 Yangi ilmiy fakt yuboring:");
            break;
        case "add_mantik":
            sendMessage($chat_id, "🧩 Yangi mantiqiy topshiriq yuboring:");
            break;
        case "add_soz":
            sendMessage($chat_id, "💬 Kun so‘zini yuboring:");
            break;
        case "stats":
            sendMessage($chat_id, "📈 Statistika: Hozircha 0 foydalanuvchi.");
            break;
        case "broadcast":
            sendMessage($chat_id, "📢 Xabar matnini yuboring:");
            break;
        default:
            sendMessage($chat_id, "🤖 Noma’lum tugma bosildi.");
            break;
    }
}
?>
