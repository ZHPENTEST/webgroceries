document.querySelectorAll('[data-edit]').forEach(b=>b.addEventListener('click',()=>{const p=JSON.parse(b.dataset.edit);for(const k of['id','name','brand'])document.getElementById('f_'+k).value=p[k]??'';document.querySelector('[name=price]').value=p.price;document.querySelector('[name=stock_quantity]').value=p.stock_quantity;document.querySelector('[name=description]').value=p.description||'';document.getElementById('f_img').value=p.image||'';window.scrollTo({top:0,behavior:'smooth'});}));
// Responsive tables: label each cell from its column header (mobile cards)
document.querySelectorAll('.tbl table').forEach(t=>{
  const rows=[...t.querySelectorAll('tr')]; if(!rows.length) return;
  const heads=[...rows[0].children].map(h=>h.textContent.trim());
  rows.slice(1).forEach(tr=>{[...tr.children].forEach((td,i)=>{if(heads[i])td.setAttribute('data-label',heads[i]);});});
});
// Inline stock stepper
const acsrf=document.querySelector('meta[name=csrf]')?.content||'';
document.querySelectorAll('[data-sp]').forEach(b=>b.addEventListener('click',async()=>{
  const r=await fetch('/admin/products/'+b.dataset.sp+'/stock',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:new URLSearchParams({delta:b.dataset.sd,csrf:acsrf})});
  const j=await r.json();
  if(j.ok){document.querySelector('[data-stock="'+b.dataset.sp+'"]').textContent=j.qty;}
}));
// Sales bar chart (vanilla canvas)
(function(){
  const cv=document.getElementById('salesChart'); if(!cv) return;
  const data=JSON.parse(cv.dataset.chart||'[]'); if(!data.length){cv.outerHTML='<p class="muted">No sales yet.</p>';return;}
  const dpr=devicePixelRatio||1, W=cv.clientWidth||600, H=180;
  cv.width=W*dpr; cv.height=H*dpr; cv.style.width='100%';
  const x=cv.getContext('2d'); x.scale(dpr,dpr);
  const max=Math.max(...data.map(d=>+d.t),1), bw=W/data.length;
  data.forEach((d,i)=>{
    const h=Math.max(4,(+d.t/max)*(H-34)), px=i*bw+6, py=H-20-h;
    const g=x.createLinearGradient(0,py,0,py+h); g.addColorStop(0,'#FF5FBE'); g.addColorStop(1,'#8B2FF7');
    x.fillStyle=g;
    x.beginPath(); x.roundRect(px,py,bw-12,h,5); x.fill();
    x.fillStyle='#C6AFDF'; x.font='10px Outfit,sans-serif'; x.textAlign='center';
    x.fillText(String(d.d).slice(5), i*bw+bw/2, H-6);
  });
})();
