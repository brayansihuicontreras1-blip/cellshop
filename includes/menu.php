<?php $act=basename($_SERVER['SCRIPT_NAME']);
$links=[
 'index.php'=>['Inicio','home',['index.php']],
 'celulares.php'=>['Celulares','phone',['celulares.php']],
 'clientes.php'=>['Clientes','users',['clientes.php']],
 'detalle_cotizacion.php'=>['Cotizaciones','doc',['detalle_cotizacion.php','cotizacion.php']],
 'reporte.php'=>['Reportes','chart',['reporte.php']],
]; 
$logo='<span class="marca-ico"><svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round"><rect x="7" y="2.5" width="10" height="19" rx="2.6"/><path d="M10.5 18h3"/></svg></span>'; ?>
<aside class="menu"><a class="logo" href="index.php"><?= $logo ?>CellShop</a><nav aria-label="Principal">
<?php foreach($links as $f=>[$t,$i,$grp]): ?><a href="<?= $f ?>" class="<?= in_array($act,$grp)?'activo':'' ?>"><?= ico($i) ?><span><?= $t ?></span></a><?php endforeach; ?>
</nav></aside>
<nav class="inferior" aria-label="Principal móvil">
<?php foreach($links as $f=>[$t,$i,$grp]): ?><a href="<?= $f ?>" class="<?= in_array($act,$grp)?'activo':'' ?>"><?= ico($i) ?><span><?= $t ?></span></a><?php endforeach; ?>
</nav>
