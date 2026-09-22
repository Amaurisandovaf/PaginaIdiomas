<?php
require "config.php";
$secciones = $pdo->query("SELECT s.*, COUNT(p.id) AS total FROM secciones s LEFT JOIN palabras p ON p.seccion_id=s.id GROUP BY s.id ORDER BY s.id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>IdiomaLab</title><link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
<header><h1>IdiomaLab</h1><p>Aprende vocabulario y practica por secciones.</p></header>
<div class="topbar"><h2>Mis secciones</h2><a class="btn" href="seccion.php">+ Nueva sección</a></div>
<?php if (!$secciones): ?>
<div class="empty">Todavía no tienes secciones. Crea la primera.</div>
<?php else: ?>
<div class="grid">
<?php foreach ($secciones as $s): ?>
<div class="card">
<h3><?= htmlspecialchars($s["titulo"]) ?></h3>
<p><?= nl2br(htmlspecialchars($s["descripcion"] ?? "")) ?></p>
<div class="muted"><?= $s["total"] ?> palabra(s)</div>
<div class="actions">
<a class="btn" href="prueba.php?id=<?= $s["id"] ?>">Iniciar prueba</a>
<a class="btn secondary" href="seccion.php?id=<?= $s["id"] ?>">Editar</a>
<a class="btn danger" href="eliminar.php?id=<?= $s["id"] ?>" onclick="return confirm('¿Eliminar esta sección y todas sus palabras?')">Eliminar</a>
</div>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>
</div>
</body></html>