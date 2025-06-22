<?php
$state = getState($user_id);

switch ($state) {
    case "awaiting_savol":
        saveJson($data_files['savol'], ["savol" => $text, "javob" => "pending"]);
        saveState($user_id, "awaiting_savol_answer");
        sendMessage($chat_id, "✅ Endi savolning to‘g‘ri javobini yozing:");
        break;

    case "awaiting_savol_answer":
        $data = loadJson($data_files['savol']);
        $data['javob'] = strtolower(trim($text));
        saveJson($data_files['savol'], $data);
        clearState($user_id);
        sendMessage($chat_id, "✅ Savol saqlandi!");
        break;

    case "awaiting_ilm":
        saveJson($data_files['ilm'], ["text" => $text]);
        clearState($user_id);
        sendMessage($chat_id, "✅ Ilm saqlandi!");
        break;

    case "awaiting_kunsozi":
        saveJson($data_files['kunsozi'], ["text" => $text]);
        clearState($user_id);
        sendMessage($chat_id, "✅ Kun so‘zi saqlandi!");
        break;

    case "awaiting_mantiq":
        saveJson($data_files['mantiq'], ["text" => $text]);
        clearState($user_id);
        sendMessage($chat_id, "✅ Mantiqiy topshiriq saqlandi!");
        break;

    case "awaiting_quiz_question":
        $parts = explode("|", $text);
        if (count($parts) != 6) {
            sendMessage($chat_id, "❗ Noto‘g‘ri format. Qaytadan kiriting.");
            break;
        }
        $question = trim($parts[0]);
        $options = array_map('trim', array_slice($parts, 1, 4));
        $correct = strtoupper(trim($parts[5]));

        $quiz = loadJson($data_files['quiz']);
        $quiz[] = ["savol" => $question, "variantlar" => $options, "togri" => $correct];
        saveJson($data_files['quiz'], $quiz);
        clearState($user_id);
        sendMessage($chat_id, "✅ Quiz savol saqlandi!");
        break;

    case "awaiting_answer":
        $data = loadJson($data_files['savol']);
        $user_javob = strtolower(trim($text));
        $togri = strtolower($data['javob']);
        clearState($user_id);
        if ($user_javob == $togri) {
            sendMessage($chat_id, "✅ <b>To‘g‘ri!</b> Siz savolga to‘g‘ri javob berdingiz.");
        } else {
            sendMessage($chat_id, "❌ <b>Noto‘g‘ri.</b> To‘g‘ri javob: <b>$togri</b>");
        }
        break;

    case "awaiting_mantiq_answer":
        clearState($user_id);
        sendMessage($chat_id, "📌 Sizning javobingiz qabul qilindi. Tez orada baholanadi (yoki tekshiruvsiz). ✅");
        break;
}
