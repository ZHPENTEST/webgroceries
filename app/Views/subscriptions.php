<h1>Kotak langganan mingguan</h1>
<p class="muted">Pilih kotak, kami hantar setiap minggu. Jeda atau batal bila-bila masa.</p>
<div class="pgrid"><?php require dirname(__FILE__) . '/components.php'; foreach ($boxes as $p) pcard($p); ?></div>
<h2 class="st">Mulakan langganan</h2>
<div class="card"><form method="post" action="/langganan/subscribe"><?= csrf_field() ?>
<div class="row"><label>Kotak<select name="box_id"><?php foreach ($boxes as $b): ?><option value="<?= (int)$b['id'] ?>"><?= e($b['name']) ?> — <?= money((float)$b['price']) ?></option><?php endforeach; ?></select></label><label>Kuantiti<input name="qty" type="number" value="1" min="1" max="4"></label></div>
<div class="row"><label>Hari hantar<select name="day"><?php foreach (['Ahad','Isnin','Selasa','Rabu','Khamis','Jumaat','Sabtu'] as $i => $d): ?><option value="<?= $i ?>"><?= $d ?></option><?php endforeach; ?></select></label><label>Bayaran<select name="payment"><option value="cod">Cash</option><option value="transfer">Transfer</option></select></label></div>
<div class="row"><label>Nama penerima<input name="recipient" required></label><label>Telefon<input name="phone" required inputmode="tel"></label></div>
<label>Alamat jalan<input name="line1" required></label>
<div class="row"><label>Bandar<input name="city" required></label><label>Poskod<input name="postcode" required inputmode="numeric"></label></div>
<button class="btn big">Aktifkan langganan</button></form></div>
<?php if ($mine): ?><h2 class="st">Langganan saya</h2>
<div class="olist"><?php foreach ($mine as $s): ?><div class="orow"><b><?= e($s['pname']) ?> × <?= (int)$s['qty'] ?></b><span>Seterusnya: <?= e($s['next_run']) ?></span><span class="pill"><?= e($s['status']) ?></span><span style="display:flex;gap:.4rem">
<?php if ($s['status'] === 'active'): ?><form method="post" action="/langganan/<?= (int)$s['id'] ?>/paused"><?= csrf_field() ?><button class="btn ghost">Jeda</button></form><?php endif; ?>
<?php if ($s['status'] === 'paused'): ?><form method="post" action="/langganan/<?= (int)$s['id'] ?>/active"><?= csrf_field() ?><button class="btn">Sambung</button></form><?php endif; ?>
<?php if ($s['status'] !== 'cancelled'): ?><form method="post" action="/langganan/<?= (int)$s['id'] ?>/cancelled" onsubmit="return confirm('Batal langganan?')"><?= csrf_field() ?><button class="link">Batal</button></form><?php endif; ?>
</span></div><?php endforeach; ?></div><?php endif; ?>
