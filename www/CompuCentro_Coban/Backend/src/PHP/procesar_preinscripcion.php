<?php
require_once '../../admin/src/conexiondb.php';

// ===============================
// DATOS DEL INTERESADO
// ===============================
$nombre_completo = trim($_POST['nombre']);
$apellido_completo = trim($_POST['apellido']);
$fecha_nacimiento = $_POST['fecha_nacimiento'];
$telefono = $_POST['telefono'];
$genero = $_POST['genero'];

// Separar nombres y apellidos (máximo en 2 partes)
$partes_nombre = explode(' ', $nombre_completo, 2);
$nombre1 = $partes_nombre[0] ?? '';
$nombre2 = $partes_nombre[1] ?? '';

$partes_apellido = explode(' ', $apellido_completo, 2);
$apellido1 = $partes_apellido[0] ?? '';
$apellido2 = $partes_apellido[1] ?? '';

// ===============================
// CURSO Y JORNADA SELECCIONADOS
// ===============================
$id_curso = $_POST['curso_preferencia'];
$id_jornada = $_POST['jornada'];

// Buscar id_oferta correspondiente
$stmt = $pdo->prepare("
    SELECT id_oferta 
    FROM oferta_cursos 
    WHERE id_curso = ? AND id_jornada = ?
    LIMIT 1
");
$stmt->execute([$id_curso, $id_jornada]);
$id_oferta = $stmt->fetchColumn();

if (!$id_oferta) {
    die("❌ No existe oferta para ese curso en esa jornada.");
}

// ===============================
// DATOS DEL ENCARGADO (opcional)
// ===============================
$nombre_enc = trim($_POST['nombre_encargado'] ?? '');
$apellido_enc = trim($_POST['apellido_encargado'] ?? '');
$telefono_enc = $_POST['telefono_encargado'] ?? null;

$id_encargado = null;

// Si el encargado llenó algún campo, dividirlo igual en partes
if (!empty($nombre_enc) || !empty($apellido_enc) || !empty($telefono_enc)) {
    $partes_nombre_enc = explode(' ', $nombre_enc, 2);
    $nombre1_enc = $partes_nombre_enc[0] ?? '';
    $nombre2_enc = $partes_nombre_enc[1] ?? '';

    $partes_apellido_enc = explode(' ', $apellido_enc, 2);
    $apellido1_enc = $partes_apellido_enc[0] ?? '';
    $apellido2_enc = $partes_apellido_enc[1] ?? '';

    // Insertar encargado
    $stmt = $pdo->prepare("
        INSERT INTO encargados (nombre1, nombre2, apellido1, apellido2, telefono)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$nombre1_enc, $nombre2_enc, $apellido1_enc, $apellido2_enc, $telefono_enc]);
    $id_encargado = $pdo->lastInsertId();
}

// ===============================
// REGISTRAR INTERESADO
// ===============================
$stmt = $pdo->prepare("
    INSERT INTO interesados 
    (id_encargado, id_genero, nombre1, nombre2, apellido1, apellido2, fecha_nacimiento, telefono, fecha_registro)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
");
$stmt->execute([$id_encargado, $genero, $nombre1, $nombre2, $apellido1, $apellido2, $fecha_nacimiento, $telefono]);
$id_interesado = $pdo->lastInsertId();

// ===============================
// REGISTRAR PREINSCRIPCIÓN
// ===============================
$stmt = $pdo->prepare("
    INSERT INTO preinscripciones (id_interesado, id_oferta)
    VALUES (?, ?)
");
$stmt->execute([$id_interesado, $id_oferta]);

// ===============================
// NOTIFICACIÓN POR CORREO
// ===============================
require_once '../../admin/src/mailer_config.php';

use PHPMailer\PHPMailer\Exception;

try {
    $mail = crearMailer();
    $mail->addAddress('tutyose77@gmail.com', 'Administrador CompuCentro');

    $mail->isHTML(true);
    $mail->Subject = "📩 Nueva Preinscripción - $nombre1 $apellido1";
    $mail->Body = "
        <h3>Nueva Preinscripción Recibida</h3>
        <p><strong>Interesado:</strong> $nombre1 $nombre2 $apellido1 $apellido2</p>
        <p><strong>Teléfono:</strong> $telefono</p>
        <p><strong>Fecha de nacimiento:</strong> $fecha_nacimiento</p>
        <p><strong>Género:</strong> $genero</p>
        <hr>
        <p><strong>Curso:</strong> $id_curso</p>
        <p><strong>Jornada:</strong> $id_jornada</p>
        <hr>
        <p><strong>Encargado:</strong> $nombre_enc $apellido_enc</p>
        <p><strong>Teléfono Encargado:</strong> $telefono_enc</p>
        <p>📅 Fecha y hora del envío: " . date('d/m/Y H:i:s') . "</p>
    ";

    $mail->send();
} catch (Exception $e) {
    error_log("❌ Error al enviar notificación: {$mail->ErrorInfo}");
}

// ===============================
// REDIRECCIÓN EXITOSA
// ===============================
header("Location: /HTML/preinscripcion.html?ok=1");
exit;
?>
