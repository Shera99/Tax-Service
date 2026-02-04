<?php

$publicKey = 'pub_test_1234567890abcdef12345678';
$secretKey = 'sec_test_abcdef1234567890abcdef12';

$data = [
    'event_name' => 'Концерт',
    'organization_name' => 'ТОО Организатор',
    'date_time' => '2024-06-15 19:00:00',
    'total_tickets_available' => 1000,
    'total_amount_sold' => 150000.00,
    'total_tickets_sold' => 750,
    'free_tickets_count' => 200,
    'invitation_tickets_count' => 50,
    'refunded_tickets_count' => 10,
];

$timestamp = time();
$payload = json_encode($data);
$signature = hash_hmac('sha256', $timestamp . '.' . $payload, $secretKey);

$ch = curl_init('http://localhost:8080/api/v1/external/statistics');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    "Authorization: HMAC {$publicKey}:{$signature}:{$timestamp}",
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

echo $response;
