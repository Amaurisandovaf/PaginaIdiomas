<?php
require "config.php";
$id=(int)($_POST["id"]??0);$sid=(int)($_POST["seccion_id"]??0);$pal=trim($_POST["palabra"]??"");$tra=trim($_POST["traduccion"]??"");
if(!$sid||!$pal||!$tra) die("Faltan datos.");
if($id){$st=$pdo->prepare("UPDATE palabras SET palabra=?,traduccion=? WHERE id=?");$st->execute([$pal,$tra,$id]);}
else{$st=$pdo->prepare("INSERT INTO palabras(seccion_id,palabra,traduccion) VALUES(?,?,?)");$st->execute([$sid,$pal,$tra]);}
header("Location: seccion.php?id=".$sid);exit;