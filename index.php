<?php
// text_states.php

function loadJson($file) {
    return file_exists($file) ? json_decode(file_get_contents($file), true) : [];
}

function saveJson($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

if ($text == "/start") {
    $buttons = [
        [
            ["text" => "🧪 Quiz"], ["text" => "🧠 Mantiq"]
        ],
        [
            ["text" => "📚 Ilm"], ["text" => "📝 Kun so‘zi"]
        ],
        [
            ["text" => "📌 Savol"]
        ]
    ];
    sendMessage($chat_id, "Assalomu alaykum! \n<b>Zakovat Pro</b> botiga xush kelibsiz! Tanlang:", $buttons);
    return;
}

if ($text == "📚 Ilm") {
    sendMessage($chat_id, "📚 Ilm menyusida hozircha yangi bilimlar tayyorlanmoqda.");
    return;
}

if ($text == "🧠 Mantiq") {
    sendMessage($chat_id, "🧠 Mantiq mashqlari tez orada qo‘shiladi.");
    return;
}

if ($text == "📝 Kun so‘zi") {
    sendMessage($chat_id, "📝 Bugungi kun so‘zi: <b>Mas’uliyat</b> — Harakatda baraka.");
    return;
}

if ($text == "📌 Savol") {
    sendMessage($chat_id, "📌 Bugungi savol: Dunyodagi eng katta okean qaysi?\nA) Atlantika\nB) Hind\nC) Tinch\nD) Arktika");
    return;
}

if ($text == "🧪 Quiz") {
    $all = loadJson("quiz.json");
    shuffle($all);
    $questions = array_slice($all, 0, 10);

    $session = [
        "index" => 0,
        "score" => 0,
        "questions" => $questions
    ];

    saveJson("quiz_session_$user_id.json", $session);

    $q = $questions[0];
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

    sendMessage($chat_id, "🧪 <b>10 ta savoldan iborat quiz boshlandi!</b>\n\n❓ <b>Savol 1:</b>\n{$q['savol']}", $buttons);
    return;
}
