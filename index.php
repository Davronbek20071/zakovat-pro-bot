<?php
$token = "8177096885:AAFugF6dh2YFcAfgdaRwBZCiIys6FqK8GoE";
$admin_id = "7342925788";

$update = json_decode(file_get_contents('php://input'), true);
$chat_id = $update["message"]["chat"]["id"];
$text = $update["message"]["text"];

function sendMessage($chat_id, $text, $buttons = null) {
    $url = "https://api.telegram.org/bot" . $GLOBALS['token'] . "/sendMessage";

    $data = [
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => 'HTML'
    ];

    if ($buttons) {
        $data['reply_markup'] = json_encode([
            'keyboard' => $buttons,
            'resize_keyboard' => true,
            'one_time_keyboard' => false
        ]);
    }

    $options = ['http' => [
        'method' => 'POST',
        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
        'content' => http_build_query($data)
    ]];

    $context = stream_context_create($options);
    file_get_contents($url, false, $context);
}

if ($text == "/start") {
    $text = "🧠 <b>Assalomu alaykum!</b>\nBu — Zakovat Pro! Har kuni sizga ilm, zakovat va tafakkur sovg‘a qilamiz.\n\n<b>Menyu:</b>";
    $buttons = [
        [["text" => "🧠 Bugungi Savol"], ["text" => "🎥 1 daqiqa Ilm"]],
        [["text" => "🧩 Mantiqiy topshiriq"], ["text" => "💬 Kun so‘zi"]],
        [["text" => "📊 Reyting"], ["text" => "👑 Intellekt Klub"]]
    ];
    sendMessage($chat_id, $text, $buttons);

} elseif ($text == "/admin" && $chat_id == $admin_id) {
    $text = "👨‍💼 Admin Panel:\nQuyidagilardan birini tanlang:";
    $buttons = [
        [["text" => "➕ Savol yuklash"], ["text" => "🎥 Ilm qo‘shish"]],
        [["text" => "🧩 Mantiqiy topshiriq qo‘shish"], ["text" => "💬 Kun so‘zi qo‘shish"]],
        [["text" => "📈 Statistika"], ["text" => "📢 Xabar yuborish"]]
    ];
    sendMessage($chat_id, $text, $buttons);

} elseif ($text == "🧠 Bugungi Savol") {
    sendMessage($chat_id, "📌 Bugungi savol: Yer sayyorasi nechta qit'adan iborat?");

} elseif ($text == "🎥 1 daqiqa Ilm") {
    sendMessage($chat_id, "📚 Bugungi ilmiy fakt: Suv muzlab qolganda kengayadi.");

} elseif ($text == "🧩 Mantiqiy topshiriq") {
    sendMessage($chat_id, "🤔 Mantiqiy savol: Mashina 100km masofani 1 soatda bosib o‘tdi. Tezligi nechchi?");

} elseif ($text == "💬 Kun so‘zi") {
    sendMessage($chat_id, "📖 Kun so‘zi: 'Ilm' – bilim olish va uni hayotga tadbiq qilish.");

} elseif ($text == "📊 Reyting") {
    sendMessage($chat_id, "🏆 Sizning reytingiz: 0 ball\n(Endi qatnashishni boshlang)");

} elseif ($text == "👑 Intellekt Klub") {
    sendMessage($chat_id, "🧠 Intellekt Klubga hush kelibsiz! Bu yerda eng faol qatnashuvchilar chiqadi.");

} elseif ($text == "➕ Savol yuklash" && $chat_id == $admin_id) {
    sendMessage($chat_id, "✍️ Yangi savolni yuboring:");

} else {
    sendMessage($chat_id, "🤖 Buyruq tanilmadi. Iltimos, menyudan biror tugmani tanlang yoki /start ni bosing.");
}
?>
