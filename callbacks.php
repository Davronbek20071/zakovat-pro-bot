<?php
switch ($callback) {
    case "bugungi_savol":
        $data = loadJson($data_files['savol']);
        if ($data) {
            saveState($user_id, "awaiting_answer");
            sendMessage($chat_id, "📌 <b>Savol:</b> {$data['savol']}\n\n✍️ Javobingizni yozing:");
        } else {
            sendMessage($chat_id, "⛔ Bugungi savol hali yuklanmagan.");
        }
        break;

    case "mantiq":
        $data = loadJson($data_files['mantiq']);
        sendMessage($chat_id, "🧠 <b>Mantiqiy topshiriq:</b>\n{$data['text']}\n\n✍️ Javobingizni yozing:");
        saveState($user_id, "awaiting_mantiq_answer");
        break;

    case "ilm":
        $data = loadJson($data_files['ilm']);
        sendMessage($chat_id, "🧾 <b>1 daqiqa ilm:</b>\n{$data['text']}");
        break;

    case "kunsozi":
        $data = loadJson($data_files['kunsozi']);
        sendMessage($chat_id, "💬 <b>Kun so‘zi:</b>\n{$data['text']}");
        break;

    case "reyting":
        sendMessage($chat_id, "📊 Reyting: Hozircha demo rejimda.");
        break;

    case "add_savol":
        saveState($user_id, "awaiting_savol");
        sendMessage($chat_id, "📝 Bugungi savolni yuboring:");
        break;

    case "add_ilm":
        saveState($user_id, "awaiting_ilm");
        sendMessage($chat_id, "🧾 1 daqiqa ilm matnini yuboring:");
        break;

    case "add_kunsozi":
        saveState($user_id, "awaiting_kunsozi");
        sendMessage($chat_id, "💬 Kun so‘zini yuboring:");
        break;

    case "add_mantiq":
        saveState($user_id, "awaiting_mantiq");
        sendMessage($chat_id, "🧠 Mantiqiy topshiriq matnini yuboring:");
        break;

    case "add_quiz":
        saveState($user_id, "awaiting_quiz_question");
        sendMessage($chat_id, "🧪 Quiz savol matnini yuboring:\n\nFormat: savol | A | B | C | D | to‘g‘ri variant harfi\n\nMasalan:\nO‘zbekiston poytaxti? | Toshkent | Samarqand | Buxoro | Andijon | A");
        break;

    case "start_quiz":
        $quiz = loadJson($data_files['quiz']);
        if (!$quiz || count($quiz) < 1) {
            sendMessage($chat_id, "🧪 Hozircha quiz savollari mavjud emas.");
        } else {
            $shuffled = $quiz;
            shuffle($shuffled);
            $selected = array_slice($shuffled, 0, 10);
            saveJson("quiz_session_$user_id.json", ["index" => 0, "score" => 0, "questions" => $selected]);
            include "quiz_logic.php";
        }
        break;
}
