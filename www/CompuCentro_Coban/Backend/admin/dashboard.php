<?php
require_once 'src/auth.php';
require_once 'src/conexiondb.php';

// Total interesados
$total_interesados = $pdo->query("SELECT COUNT(*) FROM interesados")->fetchColumn();

// Total cursos activos
$total_cursos = $pdo->query("SELECT COUNT(*) FROM cursos WHERE estado='activo'")->fetchColumn();

// Total convocatorias vigentes
$total_convocatorias = $pdo->query("SELECT COUNT(*) FROM convocatorias WHERE estado='Vigente'")->fetchColumn();

// Estadísticas interesados por curso
$query = $pdo->query("
    SELECT c.nombre AS curso, COUNT(p.id_preinscripcion) AS total
    FROM cursos c
    LEFT JOIN oferta_cursos o ON c.id_curso = o.id_curso
    LEFT JOIN preinscripciones p ON p.id_oferta = o.id_oferta
    GROUP BY c.id_curso
    ORDER BY total DESC
");
$estadisticas = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | CompuCentro</title>
  <link rel="icon" href="assets/img/logo_compucentr.png" type="image/x-icon">
  <link rel="stylesheet" href="assets/css/modulos.css">
  <link rel="stylesheet" href="assets/css/dashboard.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
  <?php include 'includes/sidebar.php'; ?>

  <main class="main-content">
    <?php include 'includes/header.php'; ?>

    <section class="resumen">
      <div class="card">
        <h3><i class="fa-solid fa-user-graduate"></i> Interesados</h3>
        <p><?= $total_interesados ?> registrados</p>
      </div>

      <div class="card">
        <h3><i class="fa-solid fa-book"></i> Cursos activos</h3>
        <p><?= $total_cursos ?> cursos</p>
      </div>

      <div class="card">
        <h3><i class="fa-solid fa-bullhorn"></i> Convocatorias</h3>
        <p><?= $total_convocatorias ?> vigentes</p>
      </div>

      <div class="card link">
        <a href="estadisticas.php">
          <h3><i class="fa-solid fa-chart-line"></i> Estadísticas</h3>
          <p>Ver más →</p>
        </a>
      </div>
    </section>

    <section class="charts">
      <h2>📊 Interesados por Curso</h2>

      <div class="chart-card">
        <div class="chart-area">
          <canvas id="chartCursos"></canvas>
        </div>
      </div>
    </section>

    <?php include 'includes/footer.php'; ?>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <script>
  const ctx = document.getElementById('chartCursos');

  const data = {
    labels: <?= json_encode(array_column($estadisticas, 'curso')) ?>,
    datasets: [{
      label: 'Interesados',
      data: <?= json_encode(array_column($estadisticas, 'total')) ?>,
      backgroundColor: '#FF6A03'
    }]
  };
  
new Chart(document.getElementById('chartCursos'), {
  type: 'bar',
  data: data,
  options: {
    responsive: true,
    maintainAspectRatio: false,   // ← clave para respetar el alto del contenedor
    resizeDelay: 150,
    plugins: { legend: { display: false } },
    scales: {
      y: { beginAtZero: true, ticks: { precision: 0 } }
    }
  }
});

  </script>

</body>
</html>