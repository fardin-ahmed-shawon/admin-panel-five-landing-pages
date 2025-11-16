<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['phone'])) {
    $phone = trim($_POST['phone']);

    if (!preg_match('/^01[3-9]\d{8}$/', $phone)) {
        echo json_encode(['error' => 'Invalid Bangladesh phone number.']);
        exit;
    }

    $apiKey = "92bd6c2acb7dd3a56fa1130ed25fc82c";
    $url = "https://fraudchecker.link/api/v1/qc/";
    $postData = http_build_query(['phone' => $phone]);

    $opts = [
        "http" => [
            "method" => "POST",
            "header" => "Authorization: Bearer $apiKey\r\n" .
                        "Content-type: application/x-www-form-urlencoded\r\n",
            "content" => $postData,
            "timeout" => 10
        ]
    ];

    $context = stream_context_create($opts);
    $response = @file_get_contents($url, false, $context);

    if ($response === false) {
        echo json_encode(['error' => 'API Error: Unable to fetch data.']);
        exit;
    }

    $data = json_decode($response, true);
    if (!$data || !isset($data['apis'])) {
        echo json_encode(['error' => 'No data found for this phone number.']);
        exit;
    }

    echo json_encode($data);
    exit;
}
echo json_encode(['error' => 'Phone number not provided.']);
?>