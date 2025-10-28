<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'src/auth.php';
require_once 'src/conexiondb.php';

// Cargar Dompdf
require_once __DIR__ . '/vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

// Configuración Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

// Convertir logo en Base64 (SOLUCIÓN para que SIEMPRE aparezca)
$logoPath = __DIR__ . "../../Frontend/IMG/logo_compucentr.png";
$logoBase64 = "";
if (file_exists($logoPath)) {
    $logoBase64 = "data:image/png;base64," . base64_encode(file_get_contents($logoPath));
}

// Consulta datos para el reporte
$stmt = $pdo->query("
    SELECT 
        CONCAT(i.nombre1, ' ', i.apellido1) AS interesado,
        c.nombre AS curso,
        j.nombre AS jornada,
        DATE_FORMAT(p.fecha, '%d/%m/%Y') AS fecha
    FROM preinscripciones p
    INNER JOIN interesados i ON p.id_interesado = i.id_interesado
    INNER JOIN oferta_cursos o ON p.id_oferta = o.id_oferta
    INNER JOIN cursos c ON o.id_curso = c.id_curso
    INNER JOIN jornadas j ON o.id_jornada = j.id_jornada
    ORDER BY p.fecha DESC
");
$registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

$fechaHoy = date("d/m/Y H:i:s");

// Generar HTML del PDF
$html = '
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #333; margin: 30px; }
    header { text-align: center; margin-bottom: 20px; }
    .logo { width: 90px; margin-bottom: 5px; }
    h2 { color: #063B70; margin-bottom: 0; }
    h4 { color: #555; margin-top: 3px; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th { background-color: #063B70; color: white; padding: 8px; border: 1px solid #ccc; }
    td { padding: 6px; border: 1px solid #ccc; text-align: center; }
    tr:nth-child(even) { background-color: #f2f2f2; }
    footer { position: fixed; bottom: 10px; left: 0; right: 0; text-align: center; font-size: 10px; color: #777; }
</style>
</head>
<body>

<header>
    <img src="'. $logoBase64 .'" class="logo">
    <h2>CompuCentro Cobán</h2>
    <h4>Reporte General de Preinscripciones</h4>
    <p><strong>Fecha de generación:</strong> '. $fechaHoy .'</p>
</header>

<table>
    <tr>
        <th>#</th>
        <th>Interesado</th>
        <th>Curso</th>
        <th>Jornada</th>
        <th>Fecha de Registro</th>
    </tr>';

$contador = 1;
foreach ($registros as $r) {
    $html .= "
    <tr>
        <td>{$contador}</td>
        <td>{$r['interesado']}</td>
        <td>{$r['curso']}</td>
        <td>{$r['jornada']}</td>
        <td>{$r['fecha']}</td>
    </tr>";
    $contador++;
}

$html .= '
</table>

<footer>
    © ' . date("Y") . ' CompuCentro Cobán — Reporte generado automáticamente desde el panel administrativo.
</footer>

</body>
</html>
';

// Generar PDF
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait'); // Cambia a 'landscape' si lo deseas horizontal
$dompdf->render();

// Descargar PDF
$dompdf->stream("Reporte_Preinscripciones_" . date("Ymd_His") . ".pdf", ["Attachment" => true]);
exit;
?>
