<?php
// Cargar el autoloader de Composer
require 'vendor/autoload.php';

// Importar las clases de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// -----------------------------------------------------------------------------
// --- CONFIGURACIÓN (RELLENA TUS DATOS AQUÍ) ----------------------------------
// -----------------------------------------------------------------------------


$newsApiKey = getenv('NEWS_API_KEY');
$smtpUsername = getenv('GMAIL_USER');
$smtpPassword = getenv('GMAIL_APP_PASSWORD');
$recipientEmail = getenv('RECIPIENT_EMAIL');

// --- DATOS FIJOS ---
$smtpFromName = 'Agente de Noticias IA';
$recipientName = 'Destinatario';

// -----------------------------------------------------------------------------
// --- FIN DE LA CONFIGURACIÓN -------------------------------------------------
// -----------------------------------------------------------------------------

/**
 * Función para obtener noticias de la API
 * @param string $apiKey Tu clave de NewsAPI
 * @param string $language 'es' para español, 'en' para inglés
 * @return array Lista de artículos o un array vacío si hay error
 */
function getAiNews($apiKey, $language) {
    // Términos de búsqueda sobre IA
    $query = urlencode('"inteligencia artificial" OR "artificial intelligence" OR "machine learning" OR "deep learning" OR "GPT" OR "LLM"');
    
    // URL de la API
    $apiUrl = "https://newsapi.org/v2/everything?q={$query}&language={$language}&sortBy=publishedAt&pageSize=7&apiKey={$apiKey}";

    // Hacemos la petición a la API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // Necesario para que NewsAPI acepte la petición desde un script
    curl_setopt($ch, CURLOPT_USERAGENT, 'AI News Agent/1.0'); 
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    if ($data && $data['status'] === 'ok' && !empty($data['articles'])) {
        return $data['articles'];
    }
    return [];
}

// Obtener noticias en ambos idiomas
$noticiasEnIngles = getAiNews($newsApiKey, 'en');
$noticiasEnEspanol = getAiNews($newsApiKey, 'es');

// Si no hay noticias, no hacemos nada
if (empty($noticiasEnIngles) && empty($noticiasEnEspanol)) {
    echo "No se encontraron noticias relevantes. No se enviará correo.";
    exit;
}

// Construir el cuerpo del correo en HTML
$htmlBody = "<html><body>";
$htmlBody .= "<h1>Resumen Semanal de Noticias sobre Inteligencia Artificial</h1>";

if (!empty($noticiasEnEspanol)) {
    $htmlBody .= "<h2>Noticias en Español</h2>";
    $htmlBody .= "<ul>";
    foreach ($noticiasEnEspanol as $articulo) {
        $htmlBody .= "<li>";
        $htmlBody .= "<strong><a href='{$articulo['url']}'>{$articulo['title']}</a></strong><br>";
        $htmlBody .= "<em>Fuente: {$articulo['source']['name']}</em><br>";
        $htmlBody .= "<p>{$articulo['description']}</p>";
        $htmlBody .= "</li>";
    }
    $htmlBody .= "</ul>";
}

if (!empty($noticiasEnIngles)) {
    $htmlBody .= "<h2>News in English</h2>";
    $htmlBody .= "<ul>";
    foreach ($noticiasEnIngles as $articulo) {
        $htmlBody .= "<li>";
        $htmlBody .= "<strong><a href='{$articulo['url']}'>{$articulo['title']}</a></strong><br>";
        $htmlBody .= "<em>Source: {$articulo['source']['name']}</em><br>";
        $htmlBody .= "<p>{$articulo['description']}</p>";
        $htmlBody .= "</li>";
    }
    $htmlBody .= "</ul>";
}

$htmlBody .= "</body></html>";

// Configurar y enviar el correo con PHPMailer
$mail = new PHPMailer(true);

try {
    // Configuración del servidor SMTP de Gmail
    $mail->isSMTP();
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
    // ¡ASUNTO CLAVE PARA EL FILTRO DE GMAIL!
    $mail->Subject = '[AI NEWS] Resumen Semanal de Noticias / Weekly News Digest';
    $mail->Body    = $htmlBody;
    $mail->AltBody = 'Para ver este correo, por favor, utiliza un cliente compatible con HTML.';

    $mail->send();
    echo '¡Correo de noticias enviado con éxito!';
} catch (Exception $e) {
    echo "El mensaje no pudo ser enviado. Error de PHPMailer: {$mail->ErrorInfo}";
}

?>