document.addEventListener('click',async e=>{
  const add=e.target.closest('[data-add]');
  if(add){if(add.disabled)return;const q=document.querySelector(add.dataset.qtySrc)?.value||1;
    const r=await post('/api/cart/add',{id:add.dataset.add,qty:q});
    r.ok?(toast('Product added to cart'),updCount(r.count),window.flyToCart&&flyToCart(add)):toast(r.error||'Error');return;}
  const row=e.target.closest('.citem');if(!row)return;const id=row.dataset.id;
  if(e.target.closest('[data-inc]')||e.target.closest('[data-dec]')){
    const inp=row.querySelector('[data-q]');let q=+inp.value+(e.target.closest('[data-inc]')?1:-1);
    const r=await post('/api/cart/qty',{id,q});r.ok?location.reload():toast(r.error||'Error');}
  if(e.target.closest('[data-rm]')){const r=await post('/api/cart/remove',{id});r.ok?location.reload():toast('Error');}
});
async function updCount(n){const h=document.getElementById('cartCount');if(h)h.textContent=n?'('+n+')':'';const b=document.getElementById('bnCount');if(b)b.textContent=n?n:'';}
document.getElementById('couponF')?.addEventListener('submit',async e=>{e.preventDefault();const code=e.target.code.value;const r=await post('/api/cart/coupon',{code});r.ok?location.reload():toast('Coupon error');});
