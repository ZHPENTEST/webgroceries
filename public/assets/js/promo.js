// Promo slider: auto-rotate, dots, pause on touch
(function(){
  var s=document.getElementById('pslider'); if(!s) return;
  var track=s.querySelector('.ptrack'), n=track.children.length, dots=s.querySelector('.pdots'), i=0, t;
  for(var k=0;k<n;k++){var b=document.createElement('button');b.setAttribute('aria-label','Slide '+(k+1));(function(k){b.addEventListener('click',function(){go(k);restart();});})(k);dots.appendChild(b);}
  function go(j){i=(j+n)%n;track.style.transform='translateX(-'+(i*100)+'%)';[...dots.children].forEach(function(d,x){d.classList.toggle('on',x===i);});}
  function restart(){clearInterval(t);t=setInterval(function(){go(i+1);},5000);}
  s.addEventListener('pointerdown',function(){clearInterval(t);},{once:true});
  go(0); restart();
})();
