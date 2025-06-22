<?php
$session_file = "quiz_session_$user_id.json";
$session = loadJson($session_file);

$index = $session["index"];
$questions = $session["questions"];

if (strpos($callback, "quiz_answer_") === 0) {
    $variant = strtoupper(substr($callback, -1));
    $correct = $questions[$index]['togri'];

    if ($variant == $correct) {
        $session['score']++;
    }

    $session['index']++;
    saveJson($session_file, $session);
    include "quiz_logic.php";
    return;
}

if ($index >= count($questions)) {
    $score = $session["score"];
    $foiz = round(($score / count($questions)) * 100);
    unlink($session_file);
    sendMessage($chat_id, "🧪 <b>Quiz yakuni:</b>\nTo‘g‘ri javoblar: <b>$score/10</b>\n📈 Natija: <b>$foiz%</b>");
    return;
}

$q = $questions[$index];
$buttons = [
    [
        ["text" => "A) {$q['variantlar'][0]}", "callback_data" => "quiz_answer_A"],
        ["text" => "B) {$q['variantlar'][1]}", "callback_data" => "quiz_answer_B"]
    ],
    [
        ["text" => "C) {$q['variantlar'][2]}", "callback_data" => "quiz_answer_C"],
        ["text" => "D) {$q['variantlar'][3]}", "callback_data" => "quiz_answer_D"]
    ]
];

sendMessage($chat_id, "❓ <b>Savol " . ($index + 1) . "/10:</b>\n{$q['savol']}", $buttons);

// So‘nggi bosilgan variantni yozib olish uchun state saqlanmaydi, balki callback orqali ishlaydi
define("QUIZ_ACTIVE", true);
