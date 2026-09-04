<?php $msgs = ['Wah, berani eh nak masuk bilik admin?', 'Eh eh, nak ke mana tu?', 'Pintu salah, dik. Kedai kat depan sana.', 'Admin saja boleh masuk. Yang lain sila beratur.', 'Tahniah! Anda jumpa pintu rahsia. Tapi tetap tak boleh masuk.']; $m = $msgs[array_rand($msgs)]; ?>
<div class="deny">
<div class="tape">DILARANG MASUK · ADMIN SAHAJA · DILARANG MASUK · ADMIN SAHAJA ·&nbsp;</div>
<svg class="guard" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 4 5v6c0 5 3.5 9.3 8 11 4.5-1.7 8-6 8-11V5z"/><path d="M9.5 12l2 2 3.5-4"/></svg>
<div class="code403">403</div>
<h1><?= e($m) ?></h1>
<p class="muted">Halaman ini khas untuk admin WebGroceries sahaja.<br>Jangan memandai-memandai.</p>
<p class="fakelog">Percubaan anda telah direkod pada <?= date('d/m/Y h:i A') ?>. <small>(bergurau je — tak rakam apa-apa pun)</small></p>
<p class="quota" id="denQ"></p>
<div class="row" style="justify-content:center">
<a class="btn big" href="/">Saya Silap Jalan</a>
<a class="btn ghost" href="/login">Saya Sebenarnya Admin</a>
</div>
<div class="tape">DILARANG MASUK · ADMIN SAHAJA · DILARANG MASUK · ADMIN SAHAJA ·&nbsp;</div>
</div>
<script>
(function(){var q=['“Hampir dapat!” — tiada siapa, pernah.','Tip: butang belah kiri tu jalan keluar.','Admin sedang minum kopi. Jangan kacau.','Cubaan ke-1 gagal. Cubaan ke-2 pun akan gagal.'];document.getElementById('denQ').textContent=q[Math.floor(Math.random()*q.length)];})();
</script>
