<?php
require_once "../Backend/admin/src/conexiondb.php";

$stmt = $pdo->query("SELECT * FROM convocatorias ORDER BY fecha_inicio ASC");
$convocatorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Convocatorias</title>
  <link rel="icon" href="../IMG/logo_compucentr.png" type="image/png">
  <link rel="stylesheet" href="../CSS/style.css">
  <link rel="stylesheet" href="../CSS/convocatorias.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

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
      <li><a href="convocatorias.php" class="active">Convocatorias</a></li>
      <li><a href="preinscripcion.html">Preinscríbete</a></li>
      <li><a href="galeria.php">Galería</a></li>
      <li><a href="contacto.html">Contacto</a></li>
    </ul>
  </nav>
  <div class="menu-toggle" id="menu-toggle">☰</div>
</header>


<main class="contenido-principal">
<section class="convocatorias">
  <h2 class="titulo">Convocatorias Vigentes</h2>
  <div class="grid-convocatorias">

<?php foreach($convocatorias as $c): ?>

  <div class="card">

    <!-- Estado -->
    <span class="estado <?= $c['estado'] === 'Vigente' ? 'abierta' : 'proxima' ?>">
      <?= $c['estado'] === 'Vigente' ? 'Abierta' : 'Cerrada' ?>
    </span>

    <!-- Imagen si existe -->
    <?php if(!empty($c['imagen'])): ?>
      <img src="../../Backend/admin/assets/uploads/convocatorias/<?= $c['imagen'] ?>" class="img-convocatoria" alt="Imagen Convocatoria">
    <?php endif; ?>

    <!-- Título -->
    <h3><?= htmlspecialchars($c['titulo']) ?></h3>

    <!-- Descripción -->
    <p><?= nl2br(htmlspecialchars($c['descripcion'])) ?></p>

    <!-- Fechas -->
    <?php if($c['fecha_inicio'] && $c['fecha_fin']): ?>
      <p class="fecha">📅 Del <?= date("d/m/Y", strtotime($c['fecha_inicio'])) ?> al <?= date("d/m/Y", strtotime($c['fecha_fin'])) ?></p>
    <?php elseif($c['fecha_inicio']): ?>
      <p class="fecha">📅 Inicio: <?= date("d/m/Y", strtotime($c['fecha_inicio'])) ?></p>
    <?php endif; ?>

    <!-- Botón -->
    <a 
      href="<?= $c['estado'] === 'Vigente' ? 'preinscripcion.html' : '#' ?>" 
      class="btn <?= $c['estado'] !== 'Vigente' ? 'disabled' : '' ?>">
      <?= $c['estado'] === 'Vigente' ? 'Preinscribirse' : 'No disponible' ?>
    </a>

  </div>

<?php endforeach; ?>

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
<script src="../JS/convocatorias.js"></script>

</body>
</html>
