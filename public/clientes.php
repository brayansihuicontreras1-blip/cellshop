<?php $titulo='Clientes'; require '../includes/header.php';
$cli=leer('clientes'); $err=''; $edit=null; $eid=(int)($_GET['editar']??0);
foreach($cli as $c) if($c['id']===$eid) $edit=$c;
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(isset($_POST['eliminar'])){
    db()->prepare('DELETE FROM clientes WHERE id=?')->execute([(int)$_POST['eliminar']]); header('Location: clientes.php'); exit;
  }
  $n=trim($_POST['nombre']??''); $d=trim($_POST['dni']??''); $t=trim($_POST['telefono']??''); $e=trim($_POST['email']??''); $id=(int)($_POST['id']??0);
  if($n===''||!preg_match('/^\d{8}$/',$d)) $err='Nombre obligatorio y DNI de 8 dígitos.';
  elseif($e!==''&&!filter_var($e,FILTER_VALIDATE_EMAIL)) $err='Email no válido.';
  else{
    try{
      if($id) db()->prepare('UPDATE clientes SET nombre=?,dni=?,telefono=?,email=? WHERE id=?')->execute([$n,$d,$t,$e,$id]);
      else db()->prepare('INSERT INTO clientes (nombre,dni,telefono,email) VALUES (?,?,?,?)')->execute([$n,$d,$t,$e]);
      header('Location: clientes.php'); exit;
    }catch(PDOException $x){ $err=$x->getCode()==='23000'?'Ya existe un cliente con ese DNI.':'No se pudo guardar el cliente.'; }
  }
  $edit=['id'=>$id,'nombre'=>$n,'dni'=>$d,'telefono'=>$t,'email'=>$e];
}
$por=8; $pag=max(1,(int)($_GET['p']??1)); $tot=max(1,(int)ceil(count($cli)/$por)); $pag=min($pag,$tot);
$vista=array_slice($cli,($pag-1)*$por,$por);
?>
<section class="panel claro">
 <div class="cab"><div><h2>Clientes</h2><p class="pag-sub">Gestiona la información de tus clientes.</p></div>
  <a class="btn" href="clientes.php?nuevo=1#form"><?= ico('plus') ?> Nuevo cliente</a></div>
 <?php if($err): ?><p class="error"><?= h($err) ?></p><?php endif; ?>
 <?php if($edit||isset($_GET['nuevo'])||$err): ?>
 <form method="post" class="form-nuevo fila" id="form">
  <input type="hidden" name="id" value="<?= (int)($edit['id']??0) ?>">
  <input name="nombre" placeholder="Nombre completo" required value="<?= h($edit['nombre']??'') ?>">
  <input name="dni" placeholder="DNI (8 dígitos)" maxlength="8" inputmode="numeric" required value="<?= h($edit['dni']??'') ?>">
  <input name="telefono" placeholder="Teléfono" value="<?= h($edit['telefono']??'') ?>">
  <input name="email" type="email" placeholder="Correo" value="<?= h($edit['email']??'') ?>">
  <button class="btn"><?= !empty($edit['id'])?'Guardar cambios':'Agregar cliente' ?></button>
  <a class="btn suave" href="clientes.php">Cancelar</a>
 </form>
 <?php endif; ?>
 <div class="tab-scroll"><table>
  <tr><th>N°</th><th>Nombre</th><th>DNI</th><th>Teléfono</th><th>Correo</th><th>Acciones</th></tr>
  <?php foreach($vista as $c): ?>
  <tr data-buscar="<?= h(strtolower($c['nombre'].' '.$c['dni'].' '.$c['email'])) ?>">
   <td><?= str_pad($c['id'],3,'0',STR_PAD_LEFT) ?></td><td><?= h($c['nombre']) ?></td><td><?= h($c['dni']) ?></td><td><?= h($c['telefono']) ?></td><td><?= h($c['email']) ?></td>
   <td><div class="acc">
    <a class="ib az" href="clientes.php?editar=<?= $c['id'] ?>#form" title="Editar" aria-label="Editar <?= h($c['nombre']) ?>"><?= ico('edit') ?></a>
    <form method="post" data-confirmar="¿Eliminar a <?= h($c['nombre']) ?>?"><input type="hidden" name="eliminar" value="<?= $c['id'] ?>"><button class="ib rj" title="Eliminar" aria-label="Eliminar <?= h($c['nombre']) ?>"><?= ico('trash') ?></button></form>
   </div></td>
  </tr>
  <?php endforeach; ?>
 </table></div>
 <?php if(!$cli): ?><p class="vacio">Aún no hay clientes. Usa «Nuevo cliente» para agregar el primero.</p><?php endif; ?>
 <?php if($tot>1): ?><div class="paginas">
  <?php if($pag>1): ?><a href="?p=<?= $pag-1 ?>">Anterior</a><?php endif; ?>
  <?php for($i=1;$i<=$tot;$i++): ?><?= $i===$pag?'<span class="on">'.$i.'</span>':'<a href="?p='.$i.'">'.$i.'</a>' ?><?php endfor; ?>
  <?php if($pag<$tot): ?><a href="?p=<?= $pag+1 ?>">Siguiente</a><?php endif; ?>
 </div><?php endif; ?>
</section>
<?php require '../includes/footer.php'; ?>
