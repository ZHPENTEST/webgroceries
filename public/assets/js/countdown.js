// Flash-sale countdown: reads ISO end from closest [data-ends]
(function(){
  function pad(n){return String(n).padStart(2,'0');}
  function tick(){
    document.querySelectorAll('[data-ends]').forEach(function(box){
      var end=new Date(box.dataset.ends).getTime(), now=Date.now(), d=Math.max(0,end-now);
      var el=box.querySelector('[data-cd]'); if(!el) return;
      var h=Math.floor(d/36e5), m=Math.floor(d%36e5/6e4), s=Math.floor(d%6e4/1e3);
      el.textContent = d<=0 ? 'ENDED' : pad(h+Math.floor(0)) + ':' + pad(m) + ':' + pad(s);
      if(d>864e5) el.textContent = Math.floor(d/864e5) + 'd ' + el.textContent;
    });
  }
  tick(); setInterval(tick,1000);
})();
