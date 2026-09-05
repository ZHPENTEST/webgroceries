<h1>Settings</h1>
<div class="card"><h3>QR Bayaran Tetap (DuitNow / Transfer)</h3>
<?php if ($qr): ?><img class="qrbox-img" src="<?= e($qr) ?>" alt="Payment QR"><?php else: ?><p class="muted">Belum dimuat naik — pelanggan transfer akan diminta pilih Cash.</p><?php endif; ?>
<form method="post" action="/admin/settings/qr" enctype="multipart/form-data" class="row"><?= csrf_field() ?><input type="file" name="qr" accept="image/jpeg,image/png,image/webp" required><button class="btn">Upload QR</button></form>
<p class="muted">Muat naik gambar QR anda — ia dipaparkan di checkout dan halaman order untuk bayaran transfer.</p></div>
<div class="card"><h3>Google Maps API Key (pilihan)</h3>
<p class="muted">Kosongkan untuk guna peta percuma (OpenStreetMap). Isi key untuk guna Google Maps sebenar.</p>
<form method="post" action="/admin/settings/mapkey" class="row"><?= csrf_field() ?><input name="mapkey" value="<?= e($mapKey ?? '') ?>" placeholder="AIza..."><button class="btn">Save key</button></form></div>
<div class="card"><h3>WhatsApp Kedai</h3>
<p class="muted">Nombor untuk terima order via WhatsApp. Pelanggan tekan satu butang, order lengkap dihantar ke nombor ini. Percuma (wa.me).</p>
<form method="post" action="/admin/settings/whatsapp" class="row"><?= csrf_field() ?><input name="whatsapp" value="<?= e($wa ?? '') ?>" inputmode="tel" placeholder="cth. 0123456789"><button class="btn">Save</button></form></div>
