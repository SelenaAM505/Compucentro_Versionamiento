<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../Backend/admin/src/conexiondb.php';

// Obtener imágenes de la base de datos
$stmt = $pdo->query("SELECT * FROM galeria ORDER BY fecha DESC");
$imagenes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Galería - CompuCentro</title>

  <link rel="icon" href="../IMG/logo_compucentr.png" type="image/png">
  <link rel="stylesheet" href="../CSS/style.css">
  <link rel="stylesheet" href="../CSS/galeria.css">
</head>
<body>

<!-- Header -->
  <header>
    <div class="logo">
      <img src="../IMG/logo_compucentr.png" alt="Logo CompuCentro" class="logo-img">
      <span class="brand"><b>Compu</b><span class="color-naranja">Centro</span></span>
    </div>
    <nav id="nav">
      <ul>
        <li><a href="../index.html">Inicio</a></li>
        <li><a href="nosotros.php">Nosotros</a></li>
        <li><a href="cursos.php">Cursos</a></li>
        <li><a href="convocatorias.php">Convocatorias</a></li>
        <li><a href="preinscripcion.html">Preinscríbete</a></li>
        <li><a href="galeria.php" class="active">Galería</a></li>
        <li><a href="contacto.html">Contacto</a></li>
      </ul>
    </nav>
    <div class="menu-toggle" id="menu-toggle">☰</div>
  </header>

<main>
<section class="galeria">
  <h2 class="titulo">Nuestra Galería</h2>
  <div class="grid-galeria">

    <?php foreach ($imagenes as $foto): ?>
      <div class="item" data-title="<?= htmlspecialchars($foto['titulo']) ?>">
        <img src="../IMG/GALERIA/<?= htmlspecialchars($foto['imagen']) ?>" alt="<?= htmlspecialchars($foto['titulo']) ?>">
      </div>
    <?php endforeach; ?>

  </div>
</section>

  <!-- Modal Carrusel -->
  <div class="modal" id="modal">
    <span class="cerrar" id="cerrar">&times;</span>
    <div class="modal-contenedor">
      <img class="modal-contenido" id="imgModal">
      <div class="caption" id="caption"></div>
      <div class="nav-arrows">
        <span id="prev">&#10094;</span>
        <span id="next">&#10095;</span>
      </div>
    </div>
    <!-- Thumbnails -->
    <div class="miniaturas" id="miniaturas"></div>
  </div>

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
<script src="../JS/galeria.js"></script>
</body>
</html>
