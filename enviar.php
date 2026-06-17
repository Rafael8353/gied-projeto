<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Certifique-se de que a pasta PHPMailer com os arquivos está no mesmo diretório
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

// Garante que o PHP responda no formato JSON caso queira expandir o feedback do JS no futuro
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

// --- ARMADILHA HONEYPOT ---
    // Se o campo 'website' NÃO estiver vazio, encontramos um robô de spam!
    if (!empty($_POST["website"])) {
        // Devolvemos um status de sucesso (200) para enganar o robô, 
        // mas matamos a execução aqui sem disparar o PHPMailer.
        http_response_code(200);
        echo json_encode(["sucesso" => "Mensagem processada com sucesso!"]);
        exit;
    }
    // --------------------------


    // Captura e limpa os dados do formulário
    $nome      = strip_tags(trim($_POST["nome"]));
    $email     = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $telefone  = strip_tags(trim($_POST["telefone"]));
    $municipio = strip_tags(trim($_POST["municipio"]));
    $mensagem  = strip_tags(trim($_POST["mensagem"]));

    // Validação básica caso o JS falhe
    if (empty($nome) || empty($email) || empty($mensagem)) {
        http_response_code(400);
        echo json_encode(["erro" => "Por favor, preencha todos os campos obrigatórios."]);
        exit;
    }

    $mail = new PHPMailer(true);

    try {
        // --- CONFIGURAÇÃO DO SERVIDOR SMTP DA LOCAWEB (SSL) ---
        $mail->isSMTP();
        $mail->Host       = 'email-ssl.com.br';          
        $mail->SMTPAuth   = true;
        $mail->Username   = 'falecom@projetogied.com.br'; 
        $mail->Password   = 'Gied2026@';            // <--- COLOQUE A SENHA REAL DA CONTA AQUI
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
        $mail->Port       = 465;                         
        $mail->CharSet    = 'UTF-8';

        // --- REMETENTE E DESTINATÁRIO ---
        $mail->setFrom('falecom@projetogied.com.br', 'Site GIED'); 
        $mail->addAddress('camilagvalle@gmail.com');              
        $mail->addReplyTo($email, $nome);                         

// --- CONTEÚDO DO E-MAIL (Atualizado com o Telefone) ---
    $mail->isHTML(true);
    $mail->Subject = 'Novo Contato Projeto - GIED';
    
    $mail->Body = "
    <html>
    <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
      <div style='background-color: #1a1a1a; padding: 20px; text-align: center; color: #ffffff;'>
        <h2 style='margin: 0; color: #ffffff;'>GIED - Gestão Integrada de Educação Digital</h2>
      </div>
      <div style='padding: 20px; border: 1px solid #eee;'>
        <p>Olá Camila, você recebeu uma nova oportunidade de contato através do Projeto GIED:</p>
        <hr style='border: none; border-top: 1px solid #eee;'>
        <p><strong>Nome do Lead:</strong> {$nome}</p>
        <p><strong>E-mail:</strong> {$email}</p>
        <p><strong>Telefone / WhatsApp:</strong> {$telefone}</p> <p><strong>Município / Cargo:</strong> {$municipio}</p>
        <p><strong>Mensagem enviada:</strong><br>" . nl2br($mensagem) . "</p>
      </div>
    </body>
    </html>
    ";

        // Envia o e-mail
        $mail->send();
        
        // Retorna o código de sucesso 200 para o JavaScript capturar no .then()
        http_response_code(200);
        echo json_encode(["sucesso" => "Mensagem enviada com sucesso!"]);

    } catch (Exception $e) {
        // Retorna o código de erro 500 para o JavaScript capturar no .catch()
        http_response_code(500);
        echo json_encode(["erro" => "Erro ao enviar o e-mail: {$mail->ErrorInfo}"]);
    }
} else {
    // Se tentarem acessar o arquivo diretamente, nega o acesso
    http_response_code(403);
    echo json_encode(["erro" => "Acesso proibido."]);
    exit;
}
?>