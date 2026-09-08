document.querySelectorAll('[data-fill]').forEach(r=>r.addEventListener('change',()=>{if(!r.checked||r.value==='new')return;const a=JSON.parse(r.dataset.fill);const f=document.getElementById('coForm');f.line1.value=a.line1||'';f.city.value=a.city||'';f.postcode.value=a.postcode||'';f.phone.value=a.phone||'';toast('Address filled');}));
document.getElementById('coForm')?.addEventListener('submit',e=>{const b=document.getElementById('placeBtn');b.disabled=true;b.textContent='Placing order…';});
// Zone fee estimate from postcode
(function(){
  const pc=document.querySelector('input[name=postcode]'), fee=document.getElementById('feeEst'), zn=document.getElementById('zoneName');
  if(!pc||!fee) return; let t;
  pc.addEventListener('input',()=>{clearTimeout(t);t=setTimeout(async()=>{
    const digits=pc.value.replace(/\D/g,''); if(digits.length<2){fee.textContent='—';if(zn)zn.textContent='';return;}
    try{const r=await fetch('/api/cart/zone?postcode='+encodeURIComponent(digits));const j=await r.json();
    if(j.ok){fee.textContent='RM '+Number(j.standard).toFixed(2)+'+';if(zn)zn.textContent='('+j.zone+')';}}catch(e){}
  },500);});
})();
(function(){
  const t=document.getElementById('transferBox'), c=document.getElementById('cashBox');
  function sync(){const v=document.querySelector('input[name=payment]:checked')?.value;if(t)t.hidden=v!=='transfer';if(c)c.style.display=v==='cod'?'':'none';}
  document.querySelectorAll('input[name=payment]').forEach(r=>r.addEventListener('change',sync)); sync();
})();
