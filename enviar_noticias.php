<?php
// Cargar el autoloader de Composer
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// --- CONFIGURACIÓN ---
$newsApiKey = getenv('NEWS_API_KEY');
$smtpUsername = getenv('GMAIL_USER');
$smtpPassword = getenv('GMAIL_APP_PASSWORD');
$recipientEmail = getenv('RECIPIENT_EMAIL');

// --- DATOS FIJOS ---
$smtpFromName = 'Agente de Noticias IA';
$recipientName = 'Destinatario';

if (!$newsApiKey || !$smtpUsername || !$smtpPassword || !$recipientEmail) {
    die("Error: Faltan variables de entorno. Asegúrate de configurar los Secrets e:wq
    n GitHub.");
}

function getAiNews($apiKey, $language) {
    $query = urlencode('"inteligencia artificial" OR "artificial intelligence" OR "machine learning" OR "deep learning" OR "GPT" OR "LLM"');
    $apiUrl = "https://newsapi.org/v2/everything?q={$query}&language={$language}&sortBy=publishedAt&pageSize=7&apiKey={$apiKey}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'AI News Agent/1.0'); 
    $response = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($response, true);
    if ($data && $data['status'] === 'ok' && !empty($data['articles'])) {
        return $data['articles'];
    }
    return [];
}

$noticiasEnIngles = getAiNews($newsApiKey, 'en');
$noticiasEnEspanol = getAiNews($newsApiKey, 'es');

if (empty($noticiasEnIngles) && empty($noticiasEnEspanol)) {
    echo "No se encontraron noticias relevantes. No se enviará correo.";
    exit;
}

// --- INICIO DE LA CONSTRUCCIÓN DEL NUEVO CORREO HTML ---

$htmlBody = '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen Semanal de Noticias IA</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, sans-serif;">

    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td style="padding: 20px 0;">
                <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse; background-color: #ffffff; border: 1px solid #cccccc;">
                    
                    <!-- Encabezado -->
                    <tr>
                        <td align="center" style="padding: 30px 20px; background-color: #004aad; color: #ffffff;">
                            <h1 style="margin: 0; font-size: 28px;">Resumen Semanal de IA</h1>
                            <p style="margin: 5px 0 0; font-size: 16px;">Tu dosis de noticias sobre Inteligencia Artificial</p>
                        </td>
                    </tr>

                    <!-- Contenido principal -->
                    <tr>
                        <td style="padding: 30px 25px;">';

// Sección de Noticias en Español
if (!empty($noticiasEnEspanol)) {
    $htmlBody .= '<h2 style="font-size: 22px; color: #333333; border-bottom: 2px solid #eeeeee; padding-bottom: 10px;">Noticias en Español</h2>';
    foreach ($noticiasEnEspanol as $articulo) {
        $htmlBody .= '
                            <div style="margin-bottom: 25px;">
                                <h3 style="margin: 0 0 5px; font-size: 18px;">
                                    <a href="' . htmlspecialchars($articulo['url']) . '" style="color: #004aad; text-decoration: none;">' . htmlspecialchars($articulo['title']) . '</a>
                                </h3>
                                <p style="margin: 0 0 10px; font-size: 14px; color: #888888;">
                                    <em>Fuente: ' . htmlspecialchars($articulo['source']['name']) . '</em>
                                </p>
                                <p style="margin: 0; font-size: 15px; color: #555555; line-height: 1.6;">' . htmlspecialchars($articulo['description']) . '</p>
                            </div>';
    }
}

// Sección de Noticias en Inglés
if (!empty($noticiasEnIngles)) {
    $htmlBody .= '<h2 style="font-size: 22px; color: #333333; border-bottom: 2px solid #eeeeee; padding-bottom: 10px; margin-top: 40px;">News in English</h2>';
    foreach ($noticiasEnIngles as $articulo) {
        $htmlBody .= '
                            <div style="margin-bottom: 25px;">
                                <h3 style="margin: 0 0 5px; font-size: 18px;">
                                    <a href="' . htmlspecialchars($articulo['url']) . '" style="color: #004aad; text-decoration: none;">' . htmlspecialchars($articulo['title']) . '</a>
                                </h3>
                                <p style="margin: 0 0 10px; font-size: 14px; color: #888888;">
                                    <em>Source: ' . htmlspecialchars($articulo['source']['name']) . '</em>
                                </p>
                                <p style="margin: 0; font-size: 15px; color: #555555; line-height: 1.6;">' . htmlspecialchars($articulo['description']) . '</p>
                            </div>';
    }
}

$htmlBody .= '
                        </td>
                    </tr>

                    <!-- Pie de página -->
                    <tr>
                        <td align="center" style="padding: 20px; background-color: #eeeeee; color: #888888; font-size: 12px;">
                            <p style="margin: 0;">Este es un correo automatizado por tu Agente de Noticias IA.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>';
// --- FIN DE LA CONSTRUCCIÓN DEL NUEVO CORREO HTML ---

$mail = new PHPMailer(true);
try {
    // Configuración del servidor SMTP
    $mail->isSMTP();
    $mail->CharSet = 'UTF-8';
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = $smtpUsername;
    $mail->Password   = $smtpPassword;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;

    // Remitente y destinatario
    $mail->setFrom($smtpUsername, $smtpFromName);
    $mail->addAddress($recipientEmail, $recipientName);

    // Contenido del correo
    $mail->isHTML(true);
    $mail->Subject = '[AI NEWS] Tu Resumen Semanal de Inteligencia Artificial';
    $mail->Body    = $htmlBody;
    $mail->AltBody = 'Para ver este correo, por favor, utiliza un cliente compatible con HTML.';

    $mail->send();
    echo '¡Correo de noticias enviado con éxito!';
} catch (Exception $e) {
    echo "El mensaje no pudo ser enviado. Error de PHPMailer: {$mail->ErrorInfo}";
}
?>