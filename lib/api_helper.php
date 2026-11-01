<?php
// lib/api_helper.php

define('AWS_API', 'https://p8pibk61b3.execute-api.eu-north-1.amazonaws.com/dev/');

/**
 * Generic AWS API Gateway Call Function
 */
function awsapi(array $payload, string $funName)
{
    $url = rtrim(AWS_API, '/') . '/' . ltrim($funName, '/');

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json'
        ],
        CURLOPT_TIMEOUT        => 30
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Handle cURL Errors
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);

        return [
            'success' => false,
            'message' => 'API Connection Failed',
            'error'   => $error
        ];
    }

    curl_close($ch);

    // HTTP Error Handling
    if ($httpCode != 200) {
        return [
            'success' => false,
            'message' => 'HTTP Error',
            'code'    => $httpCode,
            'response'=> $response
        ];
    }

    // Decode Response
    $result = json_decode($response, true);
    //echo "<pre>";print_r($result);exit;

    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'success' => false,
            'message' => 'Invalid JSON Response',
            'response'=> $response
        ];
    }

    // Lambda Proxy Integration Response
    if (isset($result['body'])) {

        $body = json_decode($result['body'], true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $body;
        }

        return $result['body'];
    }

    return $result;
}
?>