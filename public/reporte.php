<?php $titulo='Reportes'; require '../includes/header.php';
$cot=leer('cotizaciones'); $prod=[]; $cli=[];
foreach($cot as $c){
  $cli[$c['cliente']]=($cli[$c['cliente']]??0)+$c['total'];
  foreach($c['items'] as $i){ $prod[$i['producto']]['u']=($prod[$i['producto']]['u']??0)+$i['cantidad']; $prod[$i['producto']]['m']=($prod[$i['producto']]['m']??0)+$i['total']; }
}
uasort($prod,fn($a,$b)=>$b['u']<=>$a['u']); arsort($cli);
$suma=array_sum(array_column($cot,'total')); $max=$prod?max(array_column($prod,'u')):1; ?>
<h2 class="pag">Reportes</h2>
<p class="pag-sub">Resumen de lo cotizado hasta hoy.</p>
<section class="cards">
 <div class="kpi"><small>Cotizaciones</small><b><?= count($cot) ?></b></div>
 <div class="kpi"><small>Total cotizado</small><b><?= soles($suma) ?></b></div>
 <div class="kpi"><small>Promedio por cotización</small><b><?= soles($cot?$suma/count($cot):0) ?></b></div>
</section>
<section class="panel claro">
 <h3>Celulares más cotizados</h3>
 <div class="tab-scroll"><table><tr><th>Producto</th><th>Unidades</th><th></th><th class="der">Monto (sin IGV)</th></tr>
 <?php foreach($prod as $n=>$d): ?><tr><td><?= h($n) ?></td><td><?= $d['u'] ?></td><td><div class="barra"><i style="width:<?= round($d['u']/$max*100) ?>%"></i></div></td><td class="der"><?= soles($d['m']) ?></td></tr><?php endforeach; ?></table></div>
 <?php if(!$prod): ?><p class="vacio">Todavía no hay datos. Genera una cotización para ver el reporte.</p><?php endif; ?>
</section>
<section class="panel claro">
 <h3>Clientes por monto</h3>
 <div class="tab-scroll"><table><tr><th>Cliente</th><th class="der">Total</th></tr>
 <?php foreach($cli as $n=>$t): ?><tr><td><?= h($n) ?></td><td class="der"><?= soles($t) ?></td></tr><?php endforeach; ?></table></div>
 <?php if(!$cli): ?><p class="vacio">Sin clientes cotizados por ahora.</p><?php endif; ?>
</section>
<?php require '../includes/footer.php'; ?>
