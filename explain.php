<?php
require_once "db.php";

// 1. Read what the form sent
$code = $_POST["code"] ?? "";
$language = $_POST["language"] ?? "auto-detect";
$mode = $_POST["mode"] ?? "technical";

if (trim($code) === "") {
    echo json_encode(["error" => "No code submitted"]);
    exit;
}

// 2. Build the prompt for the AI
$styleInstruction = ($mode === "simple")
    ? "Explain this in very simple terms, like to a beginner."
    : "Explain this technically, line by line.";
$prompt = "$styleInstruction\n\nLanguage: $language\n\nCode:\n$code";

// 3. Call the Groq API
$payload = json_encode([
    "model" => "llama-3.3-70b-versatile",
    "messages" => [
        ["role" => "user", "content" => $prompt]
    ]
]);

$ch = curl_init("https://api.groq.com/openai/v1/chat/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer " . $_ENV['GROQ_API_KEY']
]);

$response = curl_exec($ch);
$curlError = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($curlError) {
    error_log("cURL error: " . $curlError);
}
if ($httpCode !== 200) {
    error_log("Groq API returned HTTP $httpCode: " . $response);
}

$data = json_decode($response, true);
$explanation = $data["choices"][0]["message"]["content"] ?? "Could not generate an explanation.";

// 4. Save it to the database
$stmt = $pdo->prepare(
    "INSERT INTO explanations (code, language, explanation, mode) VALUES (?, ?, ?, ?)"
);
$stmt->execute([$code, $language, $explanation, $mode]);

// 5. Send the result back to the page as JSON
header("Content-Type: application/json");
echo json_encode(["explanation" => $explanation]);
?>