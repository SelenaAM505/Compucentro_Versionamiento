<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/../Backend/admin/src/conexiondb.php';
// ==============================
// CONSULTA ACTUALIZADA
// ==============================
// Agrupa jornadas, horarios y días desde las tablas nuevas
$stmt = $pdo->query("
  SELECT 
    c.*,
    GROUP_CONCAT(DISTINCT j.nombre SEPARATOR ', ') AS jornadas,
    GROUP_CONCAT(DISTINCT CONCAT(j.hora_inicio, ' - ', j.hora_fin) SEPARATOR ', ') AS horarios,
    GROUP_CONCAT(DISTINCT d.nombre SEPARATOR ', ') AS dias
  FROM cursos c
  LEFT JOIN oferta_cursos o ON c.id_curso = o.id_curso
  LEFT JOIN jornadas j ON o.id_jornada = j.id_jornada
  LEFT JOIN oferta_dias od ON o.id_oferta = od.id_oferta
  LEFT JOIN dias d ON od.id_dia = d.id_dia
  WHERE c.estado = 'activo'
  GROUP BY c.id_curso
  ORDER BY c.id_curso DESC
");

$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cursos | CompuCentro Cobán</title>
  <link rel="icon" href="../IMG/logo_compucentr.png" type="image/png">

  <!-- Estilos -->
  <link rel="stylesheet" href="../CSS/style.css">
  <link rel="stylesheet" href="../CSS/cursos.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
  <!-- HEADER -->
  <header>
    <div class="logo">
      <img src="../IMG/logo_compucentr.png" alt="Logo CompuCentro" class="logo-img">
      <span class="brand"><b>Compu</b><span class="color-naranja">Centro</span></span>
    </div>
    <nav id="nav">
      <ul>
        <li><a href="../index.html">Inicio</a></li>
        <li><a href="nosotros.php">Nosotros</a></li>
        <li><a href="cursos.php" class="active">Cursos</a></li>
        <li><a href="convocatorias.php">Convocatorias</a></li>
        <li><a href="galeria.php">Galería</a></li>
        <li><a href="contacto.html">Contacto</a></li>
      </ul>
    </nav>
    <div class="menu-toggle" id="menu-toggle">☰</div>
  </header>

  <!-- CONTENIDO PRINCIPAL -->
  <main>
    <section class="cursos">
      <h2 class="titulo">Nuestros Cursos</h2>
      <p class="subtitulo">"Aprende hoy y transforma tu futuro"</p>

      <div class="contenedor-cursos" id="contenedorCursos">
        <?php if (!empty($cursos)): ?>
          <?php foreach ($cursos as $curso): ?>
            <div class="curso-card">
              <img 
                src="../../Backend/admin/assets/uploads/<?= htmlspecialchars($curso['imagen']); ?>" 
                alt="<?= htmlspecialchars($curso['nombre']); ?>" 
                class="curso-img">

              <div class="curso-info">
                <h3><?= htmlspecialchars($curso['nombre']); ?></h3>

                <?php if (!empty($curso['subtitulo'])): ?>
                  <h4 class="subtitulo-curso"><?= htmlspecialchars($curso['subtitulo']); ?></h4>
                <?php endif; ?>

                <p><?= nl2br(htmlspecialchars($curso['descripcion'])); ?></p>

                <p class="detalles">
                  <span><strong>Duración:</strong> <?= htmlspecialchars($curso['duracion'] ?: 'Por definir'); ?></span><br>
                  <span><strong>Modalidad:</strong> <?= htmlspecialchars($curso['modalidad'] ?: 'Presencial'); ?></span><br>

                  <?php if (!empty($curso['jornadas'])): ?>
                    <span><strong>Jornadas:</strong> <?= htmlspecialchars($curso['jornadas']); ?></span><br>
                    <span><strong>Horarios:</strong> <?= htmlspecialchars($curso['horarios']); ?></span><br>
                    <?php if (!empty($curso['dias'])): ?>
                      <span><strong>Días:</strong> <?= htmlspecialchars($curso['dias']); ?></span>
                    <?php endif; ?>
                  <?php else: ?>
                    <span><strong>Jornadas:</strong> Próximamente disponible</span>
                  <?php endif; ?>
                </p>

                <a href="preinscripcion.html?curso=<?= urlencode($curso['nombre']); ?>" class="boton">
                  Preinscríbete
                </a>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p style="text-align:center; font-size:1.2rem; color:#555;">No hay cursos activos en este momento.</p>
        <?php endif; ?>
      </div>
    </section>
  </main>

  <!-- FOOTER -->
  <footer class="footer-elegante">
  <div class="onda-superior">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 150">
      <path fill="#063b70"
        d="M0,64L60,74.7C120,85,240,107,360,117.3C480,128,600,128,720,122.7C840,117,960,107,1080,101.3C1200,96,1320,96,1380,96L1440,96L1440,0L0,0Z">
      </path>
    </svg>
  </div>
  <div class="contenido-footer">
    <h3>Contáctanos</h3>
  <p><i class="fa fa-phone"></i>+502 4650 4401</p>
  <p><i class="fa fa-envelope"></i> compucentrocoban@gmail.com</p>
  </div>
    <div class="copyright">
      <p>&copy; 2025  Trabajo Educativo SVE | Todos los derechos reservados</p>
    </div>
  </footer>

  <script src="../JS/layout.js"></script>
  <script src="../JS/cursos.js"></script>
</body>
</html>