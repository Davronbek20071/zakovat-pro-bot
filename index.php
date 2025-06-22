<?php
// callbacks.php

function loadJson($file) {
    return file_exists($file) ? json_decode(file_get_contents($file), true) : [];
}

function saveJson($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

if (strpos($callback, "quiz_answer_") === 0) {
    $variant = strtoupper(substr($callback, -1)); // A, B, C yoki D
    $session_file = "quiz_session_$user_id.json";
    $session = loadJson($session_file);

    if (!isset($session["questions"])) {
        sendMessage($chat_id, "⚠️ Quiz sessiyasi topilmadi. Iltimos, qaytadan boshlang.");
        return;
    }

    $index = $session["index"];
    $questions = $session["questions"];
    $correct = $questions[$index]['togri'];

    if ($variant == $correct) {
        $session['score']++;
    }

    $session['index']++;
    saveJson($session_file, $session);

    // Keyingi savol yoki yakuniy natija
    if ($session["index"] >= count($questions)) {
        $score = $session["score"];
        $foiz = round(($score / count($questions)) * 100);
        unlink($session_file);

        sendMessage($chat_id, "✅ <b>Quiz yakuni:</b>\n🟢 To‘g‘ri javoblar: <b>$score/10</b>\n📊 Natija: <b>$foiz%</b>");
        return;
    }

    // Keyingi savolni chiqarish
    $q = $questions[$session["index"]];
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

    sendMessage($chat_id, "❓ <b>Savol " . ($session["index"] + 1) . "/10:</b>\n{$q['savol']}", $buttons);
    return;
}
