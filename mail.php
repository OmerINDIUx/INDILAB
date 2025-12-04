<?php
header('Content-Type: application/json');

// REPLACE WITH YOUR EMAIL ADDRESS
$to = 'contacto@indi-lab.com'; 
$subject = 'Nueva Suscripción al Boletín';

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'No data received']);
    exit;
}

$email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
$name = htmlspecialchars($data['name']);
$source = htmlspecialchars($data['source']);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email']);
    exit;
}

$message = "Nueva suscripción recibida:\n\n";
$message .= "Nombre: " . ($name ? $name : 'No proporcionado') . "\n";
$message .= "Email: " . $email . "\n";
$message .= "Página de origen: " . $source . "\n";
$message .= "Fecha: " . date('Y-m-d H:i:s') . "\n";

$headers = 'From: noreply@indixlab.com' . "\r\n" .
    'Reply-To: ' . $email . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

if (mail($to, $subject, $message, $headers)) {
    echo json_encode(['message' => 'Subscription successful']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to send email']);
}
?>
