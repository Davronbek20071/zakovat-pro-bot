<?php
$token = "8177096885:AAFugF6dh2YFcAfgdaRwBZCiIys6FqK8GoE";
$admin_id = 7342925788;

$content = file_get_contents("php://input");
$update = json_decode($content, true);
$message = $update["message"] ?? null;
$chat_id = $message["chat"]["id"] ?? null;
$text = $message["text"] ?? '';

if ($text == "/start") {
    sendMessage($chat_id, "🧠 <b>Assalomu alaykum!</b>\nBu — Zakovat Pro! Har kuni sizga ilm, zakovat va tafakkur sovg‘a qilamiz.\n\n<b>Menyu:</b>\n🧠 Bugungi Savol\n🎥 1 daqiqa Ilm\n🧩 Mantiqiy topshiriq\n💬 Kun so‘zi\n📊 Reyting\n👑 Intellekt Klub");
} elseif ($text == "/admin" && $chat_id == $admin_id) {
    sendMessage($chat_id, "👨‍💼 Admin Panel:\n➕ Savol yuklash\n🎥 Ilm qo‘shish\n🧩 Mantiqiy topshiriq qo‘shish\n💬 Kun so‘zi qo‘shish\n📈 Statistika\n📢 Xabar yuborish");
} else {
    sendMessage($chat_id, "🤖 Buyruq tanilmadi. /start ni bosing.");
}

function sendMessage($chat_id, $text) {
    $url = "https://api.telegram.org/bot" . $GLOBALS['token'] . "/sendMessage";
    $data = [
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => 'HTML'
    ];
    $options = ['http' => [
        'method' => 'POST',
        'header' => "Content-type: application/x-www-form-urlencoded
",
        'content' => http_build_query($data)
    ]];
    $context = stream_context_create($options);
    file_get_contents($url, false, $context);
}
?>
