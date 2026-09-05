<h1>Settings</h1>
<div class="card"><h3>QR Bayaran Tetap (DuitNow / Transfer)</h3>
<?php if ($qr): ?><img class="qrbox-img" src="<?= e($qr) ?>" alt="Payment QR"><?php else: ?><p class="muted">Belum dimuat naik — pelanggan transfer akan diminta pilih Cash.</p><?php endif; ?>
<form method="post" action="/admin/settings/qr" enctype="multipart/form-data" class="row"><?= csrf_field() ?><input type="file" name="qr" accept="image/jpeg,image/png,image/webp" required><button class="btn">Upload QR</button></form>
<p class="muted">Muat naik gambar QR anda — ia dipaparkan di checkout dan halaman order untuk bayaran transfer.</p></div>
