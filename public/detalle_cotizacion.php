<?php $titulo='Cotizaciones'; require '../includes/header.php';
$cot=leer('cotizaciones'); $id=(int)($_GET['id']??0); $q=null; $qi=null;
foreach($cot as $i=>$x) if($x['id']===$id){ $q=$x; $qi=$i; }
if($q&&$_SERVER['REQUEST_METHOD']==='POST'&&in_array($_POST['estado']??'',['Pendiente','Aprobada','Rechazada'],true)){
  db()->prepare('UPDATE cotizaciones SET estado=? WHERE id=?')->execute([$_POST['estado'],$id]); header("Location: detalle_cotizacion.php?id=$id"); exit;
}
$num=fn($n)=>str_pad($n,4,'0',STR_PAD_LEFT);
if($q):
  $titulo='Cotización #'.$num($q['id']);
  $est=$q['estado']??'Pendiente'; $dp=(float)($q['descuento_pct']??0); $dto=(float)($q['descuento']??0);
  $tel=$q['telefono']??''; $mail=$q['email']??'';
  if($tel===''&&$mail=='') foreach(leer('clientes') as $c) if($c['dni']===$q['dni']){ $tel=$c['telefono']; $mail=$c['email']; }
  $f=date_create($q['fecha']); ?>
<section class="panel claro">
 <div class="cab"><h2><?= ico('doc') ?> Detalle de Cotización #<?= $num($q['id']) ?></h2><a class="btn suave chico" href="detalle_cotizacion.php"><?= ico('back') ?> Volver</a></div>
 <h3>Datos del cliente</h3>
 <div class="datos">
  <div class="persona"><span class="avatar"><?= ico('user') ?></span><div><b><?= h($q['cliente']) ?></b><p>DNI: <?= h($q['dni']) ?></p><?php if($tel): ?><p>Teléfono: <?= h($tel) ?></p><?php endif; ?><?php if($mail): ?><p>Correo: <?= h($mail) ?></p><?php endif; ?></div></div>
  <div class="meta">
   <p>Fecha de emisión<b><?= $f?$f->format('d/m/Y'):h($q['fecha']) ?></b></p>
   <p>Estado<span class="estado <?= strtolower($est) ?>"><?= h($est) ?></span>
    <form method="post" class="estado-form"><select name="estado" aria-label="Cambiar estado"><?php foreach(['Pendiente','Aprobada','Rechazada'] as $e): ?><option <?= $e===$est?'selected':'' ?>><?= $e ?></option><?php endforeach; ?></select><button class="btn chico">Actualizar</button></form></p>
  </div>
 </div>
 <h3>Productos</h3>
 <div class="tab-scroll"><table><tr><th>N°</th><th>Producto</th><th>Cant.</th><th>P. unitario</th><th class="der">Total</th></tr>
 <?php foreach($q['items'] as $n=>$i): ?><tr><td><?= $n+1 ?></td><td><?= h($i['producto']) ?></td><td><?= $i['cantidad'] ?></td><td><?= soles($i['precio']) ?></td><td class="der"><?= soles($i['total']) ?></td></tr><?php endforeach; ?></table></div>
 <div class="tot-caja">
  <div class="lin"><span>Subtotal</span><b><?= soles($q['subtotal']) ?></b></div>
  <div class="lin"><span>Descuento (<?= rtrim(rtrim(number_format($dp,2),'0'),'.') ?>%)</span><b><?= soles($dto) ?></b></div>
  <div class="lin"><span>IGV (18%)</span><b><?= soles($q['igv']) ?></b></div>
  <div class="total"><span>Total</span><span><?= soles($q['total']) ?></span></div>
 </div>
 <div class="pie-acc"><button class="btn suave" onclick="window.print()"><?= ico('print') ?> Imprimir</button><button class="btn" onclick="window.print()" title="En el cuadro de impresión elige «Guardar como PDF»"><?= ico('download') ?> Descargar PDF</button></div>
</section>
<?php else: ?>
<section class="panel claro">
 <div class="cab"><div><h2>Cotizaciones</h2><p class="pag-sub">Revisa y gestiona las cotizaciones emitidas.</p></div><a class="btn" href="cotizacion.php"><?= ico('plus') ?> Nueva cotización</a></div>
 <div class="tab-scroll"><table><tr><th>N°</th><th>Fecha</th><th>Cliente</th><th>Estado</th><th class="der">Total</th><th></th></tr>
 <?php foreach(array_reverse($cot) as $c): $e=$c['estado']??'Pendiente'; ?>
 <tr data-buscar="<?= h(strtolower($c['cliente'])) ?>"><td><?= $num($c['id']) ?></td><td><?= h($c['fecha']) ?></td><td><?= h($c['cliente']) ?></td><td><span class="estado <?= strtolower($e) ?>"><?= h($e) ?></span></td><td class="der"><?= soles($c['total']) ?></td><td><a class="btn chico" href="?id=<?= $c['id'] ?>">Ver</a></td></tr>
 <?php endforeach; ?></table></div>
 <?php if(!$cot): ?><p class="vacio">Aún no hay cotizaciones. Crea la primera con «Nueva cotización».</p><?php endif; ?>
</section>
<?php endif; require '../includes/footer.php'; ?>
