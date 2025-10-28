<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'PHPMailer/PHPMailer.php';
require_once 'PHPMailer/SMTP.php';
require_once 'PHPMailer/Exception.php';

/**
 * Configuración general del correo para notificaciones
 * de formularios (contacto, preinscripción, etc.)
 */
function crearMailer(): PHPMailer {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'tutyose77@gmail.com'; // tu correo
    $mail->Password = 'ivskowvscglgksef';   // clave de aplicación (no la normal)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // 💡 Importante: codificación correcta y sin debug visible
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    $mail->SMTPDebug = 0; // Desactiva el log SMTP (para que el usuario no vea nada raro)

    $mail->setFrom('tutyose77@gmail.com', 'Formulario Web CompuCentro');
    return $mail;
}
?>
