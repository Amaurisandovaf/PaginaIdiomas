<?php
require "config.php";
$id = (int)($_GET["id"] ?? 0);
$st =$pdo->prepare("SELECT * FROM secciones WHERE id=?");
$st->execute([$id]);
$s =$st->fetch(PDO::FETCH_ASSOC);
if (!$s) die("Sección no encontrada.");

$st =$pdo->prepare("SELECT * FROM palabras WHERE seccion_id=?");
$st->execute([$id]);
$words =$st->fetchAll(PDO::FETCH_ASSOC);
if (!$words) die("Esta sección no tiene palabras. <a href='seccion.php?id=$id'>Agregar palabras</a>");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Prueba - <?= htmlspecialchars($s["titulo"]) ?></title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container test">
  <a href="index.php">← Salir</a>
  <h1><?= htmlspecialchars($s["titulo"]) ?></h1>
  <div class="description"><?= nl2br(htmlspecialchars($s["descripcion"] ?? "")) ?></div>
  <div id="app"></div>
</div>

<script>
const words = <?= json_encode($words, JSON_UNESCAPED_UNICODE) ?>;
const sectionId = <?= $id ?>;
let normal = [], difficult = [], errors = {}, current = null, difficultMode = false, answered = 0;

function shuffle(a) {
  return [...a].sort(() => Math.random() - 0.5);
}

function start() {
  normal = shuffle(words);
  difficult = [];
  errors = {};
  difficultMode = false;
  render();
}

function render() {
  if (!normal.length && !difficult.length) {
    document.getElementById('app').innerHTML = `
      <div class="success">
        <h2>🎉 Prueba terminada</h2>
        <p>¡Respondiste correctamente todas las palabras!</p>
        <a class="btn" href="index.php">Volver</a>
      </div>`;
    return;
  }
  
  if (!difficultMode && !normal.length) {
    difficult = shuffle(difficult);
    difficultMode = true;
  }
  
  if (!current || !((difficultMode ? difficult : normal).some(x => x.id === current.id))) {
    current = (difficultMode ? difficult : normal)[0];
  }
  
  const total = difficultMode ? difficult.length : normal.length;
  document.getElementById('app').innerHTML = `
    <div class="status">${difficultMode ? '🔥 PRUEBA DIFÍCIL' : '📚 PRUEBA NORMAL'} · Pendientes: ${total}</div>
    <div class="question">
      <div class="label">Traduce:</div>
      <h2>${escapeHtml(current.palabra)}</h2>
      <input id="answer" autocomplete="off" placeholder="Escribe tu respuesta..." autofocus>
      <button class="btn" onclick="check()">Comprobar</button>
      <button class="btn secondary" onclick="finish()">Acabar prueba</button>
      <div id="feedback"></div>
    </div>`;
  
  document.getElementById('answer').addEventListener('keydown', e => {
    if (e.key === 'Enter') check();
  });
}

function norm(s) {
  return s.toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/[¿?¡!.,;:]/g, '')
    .replace(/\s+/g, ' ')
    .trim();
}

function check() {
  const input = document.getElementById('answer').value.trim();
  if (!input) return;
  
  const ok = norm(input) === norm(current.traduccion);
  const fb = document.getElementById('feedback');
  
  if (ok) {
    if (difficultMode) difficult = difficult.filter(x => x.id !== current.id);
    else normal = normal.filter(x => x.id !== current.id);
    
    delete errors[current.id];
    fb.innerHTML = '<div class="good">✅ ¡Correcto!</div>';
    setTimeout(() => { current = null; render(); }, 650);
  } else {
    errors[current.id] = (errors[current.id] || 0) + 1;
    fb.innerHTML = '<div class="bad">❌ Incorrecto<br><strong>Respuesta correcta:</strong> ' + escapeHtml(current.traduccion) + '</div>';
    
    if (!difficultMode && errors[current.id] >= 3) {
      normal = normal.filter(x => x.id !== current.id);
      if (!difficult.some(x => x.id === current.id)) {
        // Al enviar a la lista difícil también se coloca en una posición al azar
        const randPos = Math.floor(Math.random() * (difficult.length + 1));
        difficult.splice(randPos, 0, current);
      }
      setTimeout(() => { current = null; render(); }, 1200);
    } else {
      const arr = difficultMode ? difficult : normal;
      const idx = arr.findIndex(x => x.id === current.id);
      arr.splice(idx, 1);
      
      // Reinserción aleatoria: si hay más de 1 pendiente, se coloca entre la posición 1 y el final
      // para evitar que se repita de forma inmediata en el turno consecutivo.
      if (arr.length > 1) {
        const randPos = Math.floor(Math.random() * arr.length) + 1;
        arr.splice(randPos, 0, current);
      } else {
        arr.push(current);
      }
      
      setTimeout(() => { current = null; render(); }, 1200);
    }
  }
}

function finish() {
  if (confirm('¿Quieres acabar la prueba? Tu progreso de esta prueba se perderá.')) {
    location.href = 'index.php';
  }
}

function escapeHtml(s) {
  return String(s).replace(/[&<>"']/g, m => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
  }[m]));
}

start();
</script>
</body>
</html>