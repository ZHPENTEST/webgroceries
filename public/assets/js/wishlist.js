document.querySelectorAll('[data-wish]').forEach(b=>b.addEventListener('click',async()=>{
  const r=await fetch('/wishlist',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:new URLSearchParams({id:b.dataset.wish,csrf,ajax:1})});
  if(r.redirected){location.href='/login';return;}b.textContent=b.textContent==='♡'?'♥':'♡';b.animate([{transform:'scale(.6)'},{transform:'scale(1.35)'},{transform:'scale(1)'}],{duration:420,easing:'cubic-bezier(.34,1.65,.5,1)'});toast('Wishlist updated');}));
