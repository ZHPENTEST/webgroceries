// iOS 26 motion: spring stagger, scroll reveals, fly-to-cart, hero drift
(function(){
  if(!matchMedia('(prefers-reduced-motion:no-preference)').matches) return;
  var i, g, groups = document.querySelectorAll('.pgrid,.catgrid,.bento,.olist,.reviews,.addrPick,.fgrid,.stats,.agrid,.oGrid');
  groups.forEach(function(gr){ [...gr.children].forEach(function(c,idx){ c.classList.add('rv'); c.style.setProperty('--i', Math.min(idx,8)); }); });
  document.querySelectorAll('.hero-t > *, .tile, .rev, .stat, .ccard, .auth, .citem, .orow').forEach(function(el){ if(!el.classList.contains('rv')) el.classList.add('rv'); });
  var io = new IntersectionObserver(function(es){ es.forEach(function(en){ if(en.isIntersecting){ en.target.classList.add('in'); io.unobserve(en.target); } }); }, {threshold:.1, rootMargin:'0px 0px -30px 0px'});
  document.querySelectorAll('.rv').forEach(function(el){ io.observe(el); });
  // Fly-to-cart droplet with springy tab bounce
  window.flyToCart = function(fromEl){
    try{
      var wide = innerWidth > 900;
      var c = document.querySelector(wide ? '.links a[href="/cart"]' : '.bnav a[href="/cart"]') || document.querySelector('a[href="/cart"]');
      if(!fromEl || !c) return;
      var a = fromEl.getBoundingClientRect(), b = c.getBoundingClientRect();
      var d = document.createElement('div'); d.className = 'flydot';
      d.style.left = (a.left + a.width/2) + 'px'; d.style.top = (a.top + a.height/2) + 'px';
      document.body.appendChild(d);
      requestAnimationFrame(function(){ requestAnimationFrame(function(){
        d.style.transform = 'translate(' + (b.left+b.width/2-(a.left+a.width/2)) + 'px,' + (b.top+b.height/2-(a.top+a.height/2)) + 'px) scale(.25)';
        d.style.opacity = '0';
      }); });
      setTimeout(function(){ d.remove(); }, 720);
      c.animate([{transform:'scale(1)'},{transform:'scale(1.4)'},{transform:'scale(1)'}],{duration:480,easing:'cubic-bezier(.34,1.65,.5,1)'});
    }catch(e){}
  };
  // Gentle hero drift on scroll (transform-only, rAF-throttled)
  var hv = document.querySelector('.hero-v img'), tick = false;
  if(hv){ addEventListener('scroll', function(){
    if(tick) return; tick = true;
    requestAnimationFrame(function(){ hv.style.transform = 'translateY(' + Math.min(scrollY * .05, 36) + 'px)'; tick = false; });
  }, {passive:true}); }
})();
