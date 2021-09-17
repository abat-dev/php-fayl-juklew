<?php

// Juwap qaytarıwǵa járdemshi (helper) funkciya
function response(int $statusCode, string $message)
{
  header('Content-Type: application/json', true, $statusCode);
  echo json_encode([
    'statusCode' => $statusCode,
    'message' => $message
  ]);
  exit;
}