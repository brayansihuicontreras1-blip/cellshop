<?php
ob_start();
require __DIR__.'/db.php';
function leer($n){
  $pdo=db();
  if($n==='celulares') return array_map(fn($r)=>['id'=>(int)$r['id'],'marca'=>$r['marca'],'modelo'=>$r['modelo'],'ram'=>(string)$r['ram'],'almacenamiento'=>(string)$r['almacenamiento'],'pantalla'=>(string)$r['pantalla'],'precio'=>(float)$r['precio'],'stock'=>(int)$r['stock'],'imagen'=>(string)$r['imagen']],$pdo->query('SELECT * FROM celulares ORDER BY id')->fetchAll());
  if($n==='clientes') return array_map(fn($r)=>['id'=>(int)$r['id'],'nombre'=>$r['nombre'],'dni'=>$r['dni'],'telefono'=>(string)$r['telefono'],'email'=>(string)$r['email']],$pdo->query('SELECT * FROM clientes ORDER BY id')->fetchAll());
  if($n==='cotizaciones'){
    $items=[];
    foreach($pdo->query('SELECT * FROM cotizacion_items ORDER BY id') as $i) $items[$i['cotizacion_id']][]=['producto'=>$i['producto'],'cantidad'=>(int)$i['cantidad'],'precio'=>(float)$i['precio'],'total'=>(float)$i['total']];
    $out=[];
    foreach($pdo->query("SELECT *,DATE_FORMAT(fecha,'%Y-%m-%d %H:%i') AS f FROM cotizaciones ORDER BY id") as $r) $out[]=['id'=>(int)$r['id'],'fecha'=>$r['f'],'cliente'=>$r['cliente'],'dni'=>$r['dni'],'telefono'=>(string)$r['telefono'],'email'=>(string)$r['email'],'items'=>$items[$r['id']]??[],'subtotal'=>(float)$r['subtotal'],'descuento_pct'=>(float)$r['descuento_pct'],'descuento'=>(float)$r['descuento'],'igv'=>(float)$r['igv'],'total'=>(float)$r['total'],'estado'=>$r['estado']];
    return $out;
  }
  return [];
}
function h($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function soles($n){return 'S/ '.number_format((float)$n,2);}
const IGV=0.18;

function ico($n){
  $p=[
   'home'=>'<path d="M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"/>',
   'phone'=>'<rect x="7" y="2" width="10" height="20" rx="2.5"/><path d="M11 18h2"/>',
   'users'=>'<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c0-3.6 2.9-6 6.5-6s6.5 2.4 6.5 6"/><path d="M16 4.5a3.5 3.5 0 0 1 0 7M18 14.5c2.2.7 3.5 2.6 3.5 5.5"/>',
   'doc'=>'<path d="M6 2h8l5 5v15H6z"/><path d="M14 2v5h5M9 13h7M9 17h7"/>',
   'chart'=>'<path d="M5 21v-8M12 21V4M19 21V9"/>',
   'search'=>'<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
   'sun'=>'<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>',
   'plus'=>'<path d="M12 5v14M5 12h14"/>',
   'edit'=>'<path d="M4 20h4L19 9l-4-4L4 16z"/><path d="m13.5 6.5 4 4"/>',
   'trash'=>'<path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/>',
   'arrow'=>'<path d="M5 12h14M13 6l6 6-6 6"/>',
   'back'=>'<path d="M19 12H5M11 6l-6 6 6 6"/>',
   'shield'=>'<path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6z"/><path d="m9 12 2 2 4-4"/>',
   'tag'=>'<path d="M3 12V4h8l10 10-8 8z"/><circle cx="7.5" cy="8.5" r="1.2"/>',
   'headset'=>'<path d="M4 14v-2a8 8 0 0 1 16 0v2"/><rect x="3" y="14" width="4" height="6" rx="1.5"/><rect x="17" y="14" width="4" height="6" rx="1.5"/>',
   'bolt'=>'<path d="M13 2 4 14h7l-1 8 9-12h-7z"/>',
   'print'=>'<path d="M7 9V3h10v6M7 17H4v-7h16v7h-3"/><rect x="7" y="14" width="10" height="7"/>',
   'download'=>'<path d="M12 3v12M7 10l5 5 5-5M4 21h16"/>',
   'user'=>'<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.5-6 8-6s8 2 8 6"/>',
   'x'=>'<path d="M6 6l12 12M18 6 6 18"/>',
   'call'=>'<path d="M5 3h4l2 5-2.5 1.5a11 11 0 0 0 6 6L16 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 5a2 2 0 0 1 2-2z"/>',
   'mail'=>'<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
  ];
  return '<svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.($p[$n]??'').'</svg>';
}

function tarjeta($p,$claro=false,$badge=''){
  $sin=(int)$p['stock']<=0; ?>
<article class="prod<?= $claro?' claro':'' ?>" data-buscar="<?= h(strtolower($p['marca'].' '.$p['modelo'])) ?>">
 <?php if($badge): ?><span class="insignia"><?= h($badge) ?></span><?php endif; ?>
 <div class="foto"><?php if($p['imagen']): ?><img src="img/celulares/<?= h($p['imagen']) ?>" alt="<?= h($p['modelo']) ?>" onerror="this.style.visibility='hidden'"><?php else: ?><span class="sinimg"><?= ico('phone') ?></span><?php endif; ?></div>
 <h3><?= h($p['marca'].' '.$p['modelo']) ?></h3>
 <p class="sub"><?= h($p['ram']) ?> RAM | <?= h($p['almacenamiento']) ?></p>
 <p class="precio"><?= soles($p['precio']) ?></p>
 <?php if($sin): ?><span class="btn-cot off">Sin stock</span>
 <?php else: ?><a class="btn-cot" href="cotizacion.php?add=<?= (int)$p['id'] ?>">Cotizar</a><?php endif; ?>
</article>
<?php }

$titulo=$titulo??'Cotizador de Celulares';
?><!DOCTYPE html>
<html lang="es"><head><meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= h($titulo) ?> | CellShop</title>
<script>try{var t=localStorage.getItem('tema');if(t)document.documentElement.setAttribute('data-tema',t)}catch(e){}</script>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/estilos.css"></head>
<body>
<?php include __DIR__.'/menu.php'; ?>
<main class="contenido">
<header class="top">
 <form class="buscador" action="celulares.php" method="get" role="search">
  <?= ico('search') ?><input id="buscar" name="q" type="search" placeholder="Buscar celulares, clientes..." value="<?= h($_GET['q']??'') ?>" autocomplete="off" aria-label="Buscar">
 </form>
 <button type="button" class="icono" id="tema" title="Cambiar tema" aria-label="Cambiar tema"><?= ico('sun') ?></button>
 <span class="usuario"><?= ico('user') ?> Usuario</span>
</header>
