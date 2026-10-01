<?php $titulo='Inicio'; require '../includes/header.php';
$cel=leer('celulares'); $cot=leer('cotizaciones'); $uni=[];
foreach($cot as $c) foreach($c['items'] as $i) $uni[$i['producto']]=($uni[$i['producto']]??0)+$i['cantidad'];
$nom=fn($p)=>$p['marca'].' '.$p['modelo'];
usort($cel,fn($a,$b)=>[($uni[$nom($b)]??0),$b['id']*-1]<=>[($uni[$nom($a)]??0),$a['id']*-1]);
$pop=array_slice($cel,0,4); $imgs=array_values(array_filter(array_column($cel,'imagen')));
?>
<section class="inicio">
 <div class="hero">
  <h1>Los mejores celulares, en un <em>solo lugar</em></h1>
  <p>Tecnología, calidad y los mejores precios. Cotiza fácil y rápido.</p>
  <a class="btn" href="celulares.php">Ver celulares <?= ico('arrow') ?></a>
  <div class="tel" aria-hidden="true">
   <?php foreach(array_slice(array_merge($imgs,$imgs,$imgs),0,3) as $im): ?><img src="img/celulares/<?= h($im) ?>" alt=""><?php endforeach; ?>
  </div>
 </div>
 <div class="beneficios">
  <div><?= ico('tag') ?><span><b>Precios competitivos</b><small>Los mejores del mercado</small></span></div>
  <div><?= ico('shield') ?><span><b>Garantía</b><small>En todos los equipos</small></span></div>
  <div><?= ico('headset') ?><span><b>Atención personalizada</b><small>Te ayudamos a elegir</small></span></div>
  <div><?= ico('bolt') ?><span><b>Cotizaciones</b><small>Rápidas y sin complicaciones</small></span></div>
 </div>
</section>
<div class="sec-cab"><h2>Celulares más populares</h2><a href="celulares.php">Ver todos</a></div>
<section class="grid">
<?php foreach($pop as $k=>$p) tarjeta($p,false,($k===0&&($uni[$nom($p)]??0)>0)?'Más cotizado':''); ?>
</section>
<?php require '../includes/footer.php'; ?>
