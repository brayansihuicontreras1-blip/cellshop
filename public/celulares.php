<?php $titulo='Celulares'; require '../includes/header.php';
$todos=leer('celulares'); $err=''; $ok=isset($_GET['ok']);
if($_SERVER['REQUEST_METHOD']==='POST'){
  $m=trim($_POST['marca']??''); $mo=trim($_POST['modelo']??''); $ram=trim($_POST['ram']??'');
  $alm=trim($_POST['almacenamiento']??''); $pan=trim($_POST['pantalla']??'');
  $pre=(float)($_POST['precio']??0); $stk=(int)($_POST['stock']??0);
  if($m===''||$mo==='') $err='Marca y modelo son obligatorios.';
  elseif($pre<=0) $err='El precio debe ser mayor que 0.';
  elseif($stk<0) $err='El stock no puede ser negativo.';
  else{
    $img='';
    if(!empty($_FILES['imagen']['name'])){
      $f=$_FILES['imagen'];
      $tipos=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
      $mime=($f['error']===UPLOAD_ERR_OK)?mime_content_type($f['tmp_name']):'';
      if(!isset($tipos[$mime])||$f['size']>2*1024*1024) $err='Imagen no válida (JPG, PNG o WEBP, máx. 2 MB).';
      else{
        $slug=strtolower(trim(preg_replace('/[^a-z0-9]+/i','-',$m.' '.$mo),'-'));
        $img=$slug.'-'.time().'.'.$tipos[$mime];
        if(!move_uploaded_file($f['tmp_name'],__DIR__.'/img/celulares/'.$img)) $err='No se pudo guardar la imagen.';
      }
    }
    if(!$err){
      try{
        db()->prepare('INSERT INTO celulares (marca,modelo,ram,almacenamiento,pantalla,precio,stock,imagen) VALUES (?,?,?,?,?,?,?,?)')->execute([$m,$mo,$ram,$alm,$pan,$pre,$stk,$img]);
        header('Location: celulares.php?ok=1'); exit;
      }catch(PDOException $e){ $err='No se pudo guardar el celular en la base de datos.'; }
    }
  }
}
$marcas=array_values(array_unique(array_column($todos,'marca')));
$marca=$_GET['marca']??''; $orden=$_GET['orden']??'precio'; $q=strtolower(trim($_GET['q']??''));
$lista=array_filter($todos,fn($p)=>($marca===''||$p['marca']===$marca)&&($q===''||str_contains(strtolower($p['marca'].' '.$p['modelo']),$q)));
usort($lista,match($orden){'marca'=>fn($a,$b)=>strcmp($a['marca'].$a['modelo'],$b['marca'].$b['modelo']),'stock'=>fn($a,$b)=>$b['stock']<=>$a['stock'],default=>fn($a,$b)=>$a['precio']<=>$b['precio']});
$url=fn($m)=>'celulares.php?'.http_build_query(array_filter(['marca'=>$m,'orden'=>$orden,'q'=>$_GET['q']??'']));
?>
<h2 class="pag">Celulares</h2>
<p class="pag-sub">Explora nuestro catálogo de celulares y encuentra el ideal para ti.</p>
<?php if($ok): ?><p class="exito">Celular agregado al catálogo.</p><?php endif; ?>
<?php if($err): ?><p class="error"><?= h($err) ?></p><?php endif; ?>

<details class="nuevo oscuro" <?= $err?'open':'' ?> style="margin-bottom:18px">
 <summary class="btn chico"><?= ico('plus') ?> Agregar celular</summary>
 <form method="post" enctype="multipart/form-data" class="fila">
  <input name="marca" placeholder="Marca" required value="<?= h($_POST['marca']??'') ?>">
  <input name="modelo" placeholder="Modelo" required value="<?= h($_POST['modelo']??'') ?>">
  <input name="ram" placeholder="RAM (ej. 8 GB)" value="<?= h($_POST['ram']??'') ?>">
  <input name="almacenamiento" placeholder="Almacenamiento (ej. 128 GB)" value="<?= h($_POST['almacenamiento']??'') ?>">
  <input name="pantalla" placeholder="Pantalla" value="<?= h($_POST['pantalla']??'') ?>">
  <input name="precio" type="number" step="0.01" min="0.01" placeholder="Precio S/" required value="<?= h($_POST['precio']??'') ?>">
  <input name="stock" type="number" min="0" placeholder="Stock" required value="<?= h($_POST['stock']??'') ?>">
  <input name="imagen" type="file" accept="image/jpeg,image/png,image/webp">
  <button class="btn">Guardar</button>
 </form>
</details>

<div class="chips">
 <a class="chip <?= $marca===''?'on':'' ?>" href="<?= h($url('')) ?>">Todos</a>
 <?php foreach($marcas as $m): ?><a class="chip <?= $marca===$m?'on':'' ?>" href="<?= h($url($m)) ?>"><?= h($m) ?></a><?php endforeach; ?>
 <form method="get"><input type="hidden" name="marca" value="<?= h($marca) ?>"><input type="hidden" name="q" value="<?= h($_GET['q']??'') ?>">
  <select name="orden" onchange="this.form.submit()" aria-label="Ordenar">
   <option value="precio" <?= $orden==='precio'?'selected':'' ?>>Ordenar por: Precio</option>
   <option value="marca" <?= $orden==='marca'?'selected':'' ?>>Ordenar por: Marca</option>
   <option value="stock" <?= $orden==='stock'?'selected':'' ?>>Ordenar por: Stock</option>
  </select></form>
</div>
<section class="grid">
<?php foreach($lista as $p) tarjeta($p,true); ?>
</section>
<?php if(!$lista): ?><p class="pag-sub" style="margin-top:16px">No hay celulares que coincidan con tu búsqueda.</p><?php endif; ?>
<?php require '../includes/footer.php'; ?>
