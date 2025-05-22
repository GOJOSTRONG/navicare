<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = $_POST['question'] ?? '';

    if (empty($question)) {
        echo json_encode(['error' => 'No question provided.']);
        exit;
    }

    $ngrok_url = 'http://localhost:11434/api/generate'; 

    $payload = json_encode([
        'model' => 'WVSU-Navicare-AI:latest', 
        'prompt' => $question
    ]);

    $ch = curl_init($ngrok_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo json_encode(['error' => curl_error($ch)]);
    } else {
        $decoded = json_decode($response, true);
        echo json_encode(['answer' => $decoded['response'] ?? 'No answer received.']);
    }

    curl_close($ch);
} else {
    echo json_encode(['error' => 'Invalid request method.']);
}
?>
