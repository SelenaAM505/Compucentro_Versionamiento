<?php
require_once __DIR__ . '/src/auth.php';
require_once __DIR__ . '/src/conexiondb.php';

$stmt = $pdo->query("SELECT * FROM convocatorias ORDER BY fecha_registro DESC");
$convocatorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<link rel="stylesheet" href="assets/css/contenido.css"><!--llama archivo de estilos-->

<div class="content">
    <h2>Administrar Convocatorias</h2>

    <div class="form-contenido">
        <form action="src/procesar_convocatoria.php" method="POST" enctype="multipart/form-data">

            <label>Título:</label>
            <input type="text" name="titulo" required>

            <label>Descripción:</label>
            <textarea name="descripcion" required></textarea>

            <label>Fecha de inicio:</label>
            <input type="date" name="fecha_inicio" required>

            <label>Fecha de cierre (opcional):</label>
            <input type="date" name="fecha_fin">

            <label>Estado:</label>
            <select name="estado">
                <option value="Vigente">Vigente</option>
                <option value="Cerrada">Cerrada</option>
            </select>

            <label>Imagen (opcional):</label>
            <input type="file" name="imagen" accept="image/*">

            <button class="btn-guardar">Guardar Convocatoria</button>
        </form>
    </div>

    <hr><br>

    <table class="tabla-admin">
        <tr>
            <th>Título</th>
            <th>Estado</th>
            <th>Inicio</th>
            <th>Acciones</th>
        </tr>

        <?php foreach ($convocatorias as $c): ?>
        <tr>
            <td><?= $c['titulo'] ?></td>
            <td><?= $c['estado'] ?></td>
            <td><?= $c['fecha_inicio'] ?></td>
            <td>
                <a class="btn-editar" href="editar_convocatoria.php?id=<?= $c['id_convocatoria'] ?>">Editar</a>
                <a class="btn-cancelar" href="src/eliminar_convocatoria.php?id=<?= $c['id_convocatoria'] ?>">Eliminar</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

</div>

<?php include 'includes/footer.php'; ?>
