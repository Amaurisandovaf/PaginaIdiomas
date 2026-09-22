<?php
require "config.php";
$id=(int)($_GET["id"]??0); $sid=(int)($_GET["seccion_id"]??0);
$p=["palabra"=>"","traduccion"=>""];
if($id){$st=$pdo->prepare("SELECT * FROM palabras WHERE id=?");$st->execute([$id]);$p=$st->fetch(PDO::FETCH_ASSOC);$sid=(int)$p["seccion_id"];}
?>
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Palabra</title><link rel="stylesheet" href="style.css"></head>
<body><div class="container">
<a href="seccion.php?id=<?= $sid ?>">← Volver</a><h1><?= $id ? "Editar palabra" : "Agregar palabra" ?></h1>
<form class="form" method="post" action="guardar_palabra.php">
<input type="hidden" name="id" value="<?= $id ?>"><input type="hidden" name="seccion_id" value="<?= $sid ?>">
<label>Palabra o frase en inglés</label><input name="palabra" required value="<?= htmlspecialchars($p["palabra"]) ?>" placeholder="I work">
<label>Traducción</label><input name="traduccion" required value="<?= htmlspecialchars($p["traduccion"]) ?>" placeholder="Yo trabajo">
<button class="btn">Guardar</button>
</form></div></body></html>