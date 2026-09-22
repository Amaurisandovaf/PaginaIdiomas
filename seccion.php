<?php
require "config.php";
$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
$seccion = ["titulo"=>"","descripcion"=>""];
$palabras = [];
if ($id) {
    $st=$pdo->prepare("SELECT * FROM secciones WHERE id=?"); $st->execute([$id]);
    $seccion=$st->fetch(PDO::FETCH_ASSOC);
    if (!$seccion) die("Sección no encontrada.");
    $st=$pdo->prepare("SELECT * FROM palabras WHERE seccion_id=? ORDER BY id"); $st->execute([$id]);
    $palabras=$st->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title><?= $id ? "Editar" : "Nueva" ?> sección</title><link rel="stylesheet" href="style.css"></head>
<body><div class="container">
<a href="index.php">← Volver</a>
<h1><?= $id ? "Editar sección" : "Nueva sección" ?></h1>
<form method="post" action="guardar_seccion.php" class="form">
<input type="hidden" name="id" value="<?= $id ?>">
<label>Título</label><input name="titulo" required value="<?= htmlspecialchars($seccion["titulo"]) ?>" placeholder="Ej. Presente simple">
<label>Descripción / estructura</label><textarea name="descripcion" rows="7" placeholder="Explica cómo se usa, estructura, ejemplos..."><?= htmlspecialchars($seccion["descripcion"] ?? "") ?></textarea>
<button class="btn" type="submit">Guardar sección</button>
</form>

<?php if ($id): ?>
<div class="topbar"><h2>Palabras y frases</h2><a class="btn" href="palabra.php?seccion_id=<?= $id ?>">+ Agregar palabra</a></div>
<?php if (!$palabras): ?><div class="empty">Agrega palabras para poder hacer la prueba.</div><?php else: ?>
<div class="tablewrap"><table><tr><th>Inglés</th><th>Traducción</th><th>Acciones</th></tr>
<?php foreach ($palabras as $p): ?><tr>
<td><?= htmlspecialchars($p["palabra"]) ?></td><td><?= htmlspecialchars($p["traduccion"]) ?></td>
<td><a href="palabra.php?id=<?= $p["id"] ?>&seccion_id=<?= $id ?>">Editar</a> · <a href="eliminar_palabra.php?id=<?= $p["id"] ?>&seccion_id=<?= $id ?>" onclick="return confirm('¿Eliminar esta palabra?')">Eliminar</a></td>
</tr><?php endforeach; ?></table></div>
<?php endif; endif; ?>
</div></body></html>