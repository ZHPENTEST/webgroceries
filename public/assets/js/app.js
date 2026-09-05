function toast(m){const t=document.createElement('div');t.className='toast';t.textContent=m;document.getElementById('toasts').appendChild(t);setTimeout(()=>t.remove(),2800);}
const csrf=document.querySelector('meta[name=csrf]')?.content||'';
async function post(url,data){const r=await fetch(url,{method:'POST',headers:{'X-CSRF':csrf},body:new URLSearchParams({...data,csrf})});return r.json();}
document.querySelectorAll('.malert').forEach(a=>setTimeout(()=>{a.classList.add('out');setTimeout(()=>a.remove(),320);},5000));
// Show/hide password toggle
document.querySelectorAll('[data-showpw]').forEach(b=>b.addEventListener('click',()=>{const i=b.closest('.pw-wrap').querySelector('input');i.type=i.type==='password'?'text':'password';b.textContent=i.type==='password'?'Show':'Hide';}));
// Simple dropdown menu toggle
const menuBtn=document.getElementById('menuBtn'),mmenu=document.getElementById('mmenu');
menuBtn?.addEventListener('click',e=>{e.stopPropagation();const o=document.body.classList.toggle('mopen');menuBtn.setAttribute('aria-expanded',o);});
document.addEventListener('click',e=>{if(!e.target.closest('#menuBtn,#mmenu'))document.body.classList.remove('mopen');});
mmenu?.querySelectorAll('a').forEach(a=>a.addEventListener('click',()=>document.body.classList.remove('mopen')));
addEventListener('keydown',e=>{if(e.key==='Escape')document.body.classList.remove('mopen');});
