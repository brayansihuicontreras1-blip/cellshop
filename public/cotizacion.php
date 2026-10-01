<?php $titulo='Nueva cotización'; require '../includes/header.php';
$cel=leer('celulares'); $cli=leer('clientes'); $err=''; $pre=[]; $cid=0; $desc=0;
if($_SERVER['REQUEST_METHOD']==='POST'){
  $cid=(int)($_POST['cliente']??0); $c=null; $desc=max(0,min(50,(float)($_POST['descuento']??0)));
  foreach($cli as $x) if($x['id']===$cid) $c=$x;
  $items=[]; $sub=0;
  foreach($cel as $p){
    $q=(int)($_POST['cant'][$p['id']]??0);
    if($q<=0) continue;
    $pre[$p['id']]=$q;
    if($q>$p['stock']){ $err='Stock insuficiente: '.$p['marca'].' '.$p['modelo']; break; }
    $t=$q*$p['precio']; $sub+=$t;
    $items[]=['celular_id'=>$p['id'],'producto'=>$p['marca'].' '.$p['modelo'],'cantidad'=>$q,'precio'=>$p['precio'],'total'=>$t];
  }
  if(!$err&&!$c) $err='Selecciona un cliente.';
  if(!$err&&!$items) $err='Agrega al menos un celular.';
  if(!$err){
    $dto=round($sub*$desc/100,2); $igv=round(($sub-$dto)*IGV,2); $tot=$sub-$dto+$igv;
    $pdo=db();
    try{
      $pdo->beginTransaction();
      $pdo->prepare('INSERT INTO cotizaciones (fecha,cliente_id,cliente,dni,telefono,email,subtotal,descuento_pct,descuento,igv,total,estado) VALUES (NOW(),?,?,?,?,?,?,?,?,?,?,?)')
          ->execute([$c['id'],$c['nombre'],$c['dni'],$c['telefono']??'',$c['email']??'',$sub,$desc,$dto,$igv,$tot,'Pendiente']);
      $id=(int)$pdo->lastInsertId();
      $st=$pdo->prepare('INSERT INTO cotizacion_items (cotizacion_id,celular_id,producto,cantidad,precio,total) VALUES (?,?,?,?,?,?)');
      foreach($items as $i) $st->execute([$id,$i['celular_id'],$i['producto'],$i['cantidad'],$i['precio'],$i['total']]);
      $pdo->commit();
      header("Location: detalle_cotizacion.php?id=$id"); exit;
    }catch(PDOException $e){ if($pdo->inTransaction()) $pdo->rollBack(); $err='No se pudo guardar la cotización.'; }
  }
} elseif(isset($_GET['add'])) $pre[(int)$_GET['add']]=1;
?>
<form method="post" class="cot" id="cotizador" data-precarga="<?= h(json_encode($pre?:new stdClass)) ?>" data-descuento="<?= (float)$desc ?>">
 <div class="col">
  <section class="panel claro">
   <h3 class="paso"><?= ico('users') ?> 1. Selecciona el cliente</h3>
   <?php if($err): ?><p class="error"><?= h($err) ?></p><?php endif; ?>
   <div class="sel-cli">
    <select name="cliente" id="cliente" required aria-label="Cliente"><option value="">Seleccionar cliente</option>
    <?php foreach($cli as $c): ?><option value="<?= $c['id'] ?>" data-tel="<?= h($c['telefono']) ?>" data-mail="<?= h($c['email']) ?>" <?= $cid===$c['id']?'selected':'' ?>><?= h($c['nombre'].' ('.$c['dni'].')') ?></option><?php endforeach; ?>
    </select>
    <a class="btn chico" href="clientes.php?nuevo=1#form"><?= ico('plus') ?> Nuevo cliente</a>
   </div>
   <div class="cli-info" id="cli-info"></div>
  </section>
  <section class="panel claro">
   <h3 class="paso"><?= ico('phone') ?> 2. Agrega productos</h3>
   <div class="buscador" style="background:#fff;border-color:#dbe2f6;color:#5b6894;margin-bottom:12px"><?= ico('search') ?><input id="buscar-prod" type="search" placeholder="Buscar celular" style="border:0;color:#0f1738;background:none;flex:1"></div>
   <div class="tab-scroll"><table id="catalogo">
    <tr><th>Producto</th><th>Precio</th><th>Stock</th><th>Acciones</th></tr>
    <?php foreach($cel as $p): ?>
    <tr data-id="<?= $p['id'] ?>" data-nombre="<?= h($p['marca'].' '.$p['modelo']) ?>" data-precio="<?= (float)$p['precio'] ?>" data-stock="<?= (int)$p['stock'] ?>" data-buscar="<?= h(strtolower($p['marca'].' '.$p['modelo'])) ?>">
     <td><?php if($p['imagen']): ?><img class="mini" src="img/celulares/<?= h($p['imagen']) ?>" alt="" onerror="this.style.visibility='hidden'"><?php endif; ?><?= h($p['marca'].' '.$p['modelo']) ?></td>
     <td><?= soles($p['precio']) ?></td><td><?= (int)$p['stock'] ?></td>
     <td><button type="button" class="ib vd agregar" style="width:auto;padding:0 12px;font-weight:700" <?= $p['stock']>0?'':'disabled' ?>>Agregar</button></td>
    </tr>
    <?php endforeach; ?>
   </table></div>
  </section>
 </div>
 <section class="panel claro">
  <h3>Productos en la cotización</h3>
  <div class="tab-scroll"><table>
   <thead><tr><th>Producto</th><th>Cant.</th><th>Precio unit.</th><th class="der">Total</th><th></th></tr></thead>
   <tbody id="carrito"></tbody>
  </table></div>
  <div class="resumen">
   <h3>Resumen</h3>
   <div class="lin"><span>Subtotal</span><b id="sub">S/ 0.00</b></div>
   <div class="lin"><span>Descuento (%)</span>
    <select name="descuento" id="desc" aria-label="Descuento"><?php foreach([0,5,10,15,20] as $d): ?><option value="<?= $d ?>" <?= (float)$desc===(float)$d?'selected':'' ?>><?= $d ?></option><?php endforeach; ?></select></div>
   <div class="lin"><span>IGV (18%)</span><b id="igv">S/ 0.00</b></div>
   <div class="total"><span>Total</span><span id="tot">S/ 0.00</span></div>
   <div class="acciones"><button type="button" class="btn suave" id="limpiar"><?= ico('trash') ?> Limpiar</button><button class="btn" id="generar" disabled><?= ico('doc') ?> Generar cotización</button></div>
  </div>
 </section>
</form>
<?php require '../includes/footer.php'; ?>
