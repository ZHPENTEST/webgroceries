document.querySelectorAll('[data-fill]').forEach(r=>r.addEventListener('change',()=>{if(!r.checked||r.value==='new')return;const a=JSON.parse(r.dataset.fill);const f=document.getElementById('coForm');f.line1.value=a.line1||'';f.city.value=a.city||'';f.postcode.value=a.postcode||'';f.phone.value=a.phone||'';toast('Address filled');}));
document.getElementById('coForm')?.addEventListener('submit',e=>{const b=document.getElementById('placeBtn');b.disabled=true;b.textContent='Placing order…';});
// Show QR for transfer, cash-change box for COD
(function(){
  const t=document.getElementById('transferBox'), c=document.getElementById('cashBox');
  function sync(){const v=document.querySelector('input[name=payment]:checked')?.value;if(t)t.hidden=v!=='transfer';if(c)c.style.display=v==='cod'?'':'none';}
  document.querySelectorAll('input[name=payment]').forEach(r=>r.addEventListener('change',sync)); sync();
})();
