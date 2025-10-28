<?php
require_once __DIR__ . '/src/auth.php';
require_once __DIR__ . '/src/conexiondb.php';

if(!isset($_GET['id'])) {
    die("Convocatoria no encontrada.");
}

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM convocatorias WHERE id_convocatoria = ?");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$data){
    die("Registro no existe.");
}
?>

<?php include "includes/header.php"; ?>
<?php include "includes/sidebar.php"; ?>

<link rel="stylesheet" href="assets/css/modulos.css">

<div class="content">
    <h2>Editar Convocatoria</h2>

    <div class="form-contenido">
        <form action="src/procesar_editar_convocatoria.php" method="POST" enctype="multipart/form-data">

            <input type="hidden" name="id_convocatoria" value="<?= $data['id_convocatoria'] ?>">

            <label>Título:</label>
            <input type="text" name="titulo" value="<?= htmlspecialchars($data['titulo']) ?>" required>

            <label>Descripción:</label>
            <textarea name="descripcion" rows="4" required><?= htmlspecialchars($data['descripcion']) ?></textarea>

            <label>Fecha de inicio:</label>
            <input type="date" name="fecha_inicio" value="<?= $data['fecha_inicio'] ?>" required>

            <label>Fecha de cierre (opcional):</label>
            <input type="date" name="fecha_fin" value="<?= $data['fecha_fin'] ?>">

            <label>Estado:</label>
            <select name="estado">
                <option value="Vigente" <?= $data['estado']=='Vigente' ? 'selected' : '' ?>>Vigente</option>
                <option value="Cerrada" <?= $data['estado']=='Cerrada' ? 'selected' : '' ?>>Cerrada</option>
            </select>

            <label>Imagen actual:</label>
            <?php if($data['imagen']): ?>
                <img src="assets/uploads/convocatorias/<?= $data['imagen'] ?>" class="preview-edit">
            <?php else: ?>
                <p><i>No tiene imagen</i></p>
            <?php endif; ?>

            <label>Subir nueva imagen (opcional):</label>
            <input type="file" name="imagen" accept="image/*">

            <div class="botones-acciones">
                <button type="submit" class="btn-guardar">Guardar Cambios</button>
                <a href="convocatorias.php" class="btn-cancelar">Cancelar</a>
            </div>

        </form>
    </div>
</div>

<?php include "includes/footer.php"; ?>
