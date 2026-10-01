(function(){
  const f=n=>'S/ '+n.toLocaleString('es-PE',{minimumFractionDigits:2,maximumFractionDigits:2});
  const esc=s=>String(s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

  // Tema claro / oscuro
  const bt=document.getElementById('tema');
  if(bt) bt.addEventListener('click',()=>{
    const r=document.documentElement, n=r.getAttribute('data-tema')==='claro'?'oscuro':'claro';
    r.setAttribute('data-tema',n); try{localStorage.setItem('tema',n)}catch(e){}
  });

  // Confirmar acciones destructivas
  document.querySelectorAll('form[data-confirmar]').forEach(fm=>fm.addEventListener('submit',e=>{
    if(!confirm(fm.dataset.confirmar)) e.preventDefault();
  }));

  // Búsqueda en vivo sobre elementos [data-buscar]
  const bus=document.getElementById('buscar');
  const filtrar=(inp,scope)=>{
    const t=inp.value.trim().toLowerCase();
    document.querySelectorAll((scope||'')+'[data-buscar]').forEach(el=>{el.style.display=el.dataset.buscar.includes(t)?'':'none'});
  };
  if(bus && document.querySelector('[data-buscar]') && !document.getElementById('cotizador')){
    bus.addEventListener('input',()=>filtrar(bus));
    bus.form.addEventListener('submit',e=>{ if(document.querySelector('.grid')===null) e.preventDefault(); });
    if(bus.value) filtrar(bus);
  }

  // Cotizador
  const fm=document.getElementById('cotizador'); if(!fm) return;
  const cat=document.getElementById('catalogo'), car=document.getElementById('carrito');
  const prods={}; cat.querySelectorAll('tr[data-id]').forEach(tr=>prods[tr.dataset.id]={id:tr.dataset.id,n:tr.dataset.nombre,p:parseFloat(tr.dataset.precio),s:parseInt(tr.dataset.stock)});
  const cart={}; let pre={}; try{pre=JSON.parse(fm.dataset.precarga)||{}}catch(e){}
  Object.keys(pre).forEach(id=>{ if(prods[id]) cart[id]=Math.min(Math.max(1,parseInt(pre[id])||1),prods[id].s); });

  function pintar(){
    const ids=Object.keys(cart); let sub=0;
    car.innerHTML=ids.length?ids.map(id=>{const p=prods[id],q=cart[id],t=p.p*q; sub+=t;
      return `<tr><td>${esc(p.n)}</td><td><input class="qty" type="number" name="cant[${id}]" min="1" max="${p.s}" value="${q}" data-id="${id}" aria-label="Cantidad de ${esc(p.n)}"></td><td>${f(p.p)}</td><td class="der">${f(t)}</td><td><button type="button" class="ib rj quitar" data-id="${id}" aria-label="Quitar ${esc(p.n)}"><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/></svg></button></td></tr>`;}).join('')
      :'<tr><td colspan="5" class="vacio">Todavía no agregaste productos. Usa «Agregar» en la lista.</td></tr>';
    const d=parseFloat(document.getElementById('desc').value)||0, dto=sub*d/100, igv=(sub-dto)*0.18;
    document.getElementById('sub').textContent=f(sub);
    document.getElementById('igv').textContent=f(igv);
    document.getElementById('tot').textContent=f(sub-dto+igv);
    document.getElementById('generar').disabled=!ids.length;
  }
  cat.addEventListener('click',e=>{const b=e.target.closest('.agregar'); if(!b) return;
    const id=b.closest('tr').dataset.id; cart[id]=Math.min((cart[id]||0)+1,prods[id].s); pintar();});
  car.addEventListener('click',e=>{const b=e.target.closest('.quitar'); if(!b) return; delete cart[b.dataset.id]; pintar();});
  car.addEventListener('change',e=>{const i=e.target.closest('.qty'); if(!i) return;
    const m=prods[i.dataset.id].s; cart[i.dataset.id]=Math.min(Math.max(1,parseInt(i.value)||1),m); pintar();});
  document.getElementById('desc').addEventListener('change',pintar);
  document.getElementById('limpiar').addEventListener('click',()=>{Object.keys(cart).forEach(k=>delete cart[k]); document.getElementById('desc').value='0'; pintar();});

  // Filtro de productos y datos del cliente
  const bp=document.getElementById('buscar-prod');
  bp.addEventListener('input',()=>{const t=bp.value.trim().toLowerCase(); cat.querySelectorAll('tr[data-id]').forEach(tr=>tr.style.display=tr.dataset.buscar.includes(t)?'':'none');});
  const sel=document.getElementById('cliente'), info=document.getElementById('cli-info');
  const ic=p=>`<svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">${p}</svg>`;
  function cliente(){const o=sel.selectedOptions[0]; info.innerHTML=(o&&o.value)?
    `${o.dataset.tel?`<span>${ic('<path d="M5 3h4l2 5-2.5 1.5a11 11 0 0 0 6 6L16 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 5a2 2 0 0 1 2-2z"/>')}${esc(o.dataset.tel)}</span>`:''}${o.dataset.mail?`<span>${ic('<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>')}${esc(o.dataset.mail)}</span>`:''}`:'';}
  sel.addEventListener('change',cliente); cliente(); pintar();
})();
