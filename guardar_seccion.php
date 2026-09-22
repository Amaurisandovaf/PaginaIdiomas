<?php
require "config.php";
$id=(int)($_POST["id"]??0); $titulo=trim($_POST["titulo"]??""); $descripcion=trim($_POST["descripcion"]??"");
if (!$titulo) die("El título es obligatorio.");
if ($id) { $st=$pdo->prepare("UPDATE secciones SET titulo=?, descripcion=? WHERE id=?"); $st->execute([$titulo,$descripcion,$id]); }
else { $st=$pdo->prepare("INSERT INTO secciones(titulo,descripcion) VALUES(?,?)"); $st->execute([$titulo,$descripcion]); $id=$pdo->lastInsertId(); }
header("Location: seccion.php?id=".$id); exit;