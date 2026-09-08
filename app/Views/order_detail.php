<?php $steps = ['pending','confirmed','processing','packed','out_for_delivery','delivered']; $idx = array_search($o['status'], $steps); ?>
<?php if ($placed ?? false): ?><div class="alert ok"><?= e(t('Order successfully placed. Thank you!')) ?></div><?php endif; ?>
<h1>Order <?= e($o['order_number']) ?></h1>
<div class="row no-print" style="margin-bottom:1rem">
<button class="btn ghost" onclick="window.print()"><?= e(t('Print invoice')) ?></button>
<?php if ($waLink ?? null): ?><a class="btn" target="_blank" rel="noopener" href="<?= e($waLink) ?>">Hantar order via WhatsApp</a><?php endif; ?>
<?php if ($o['status'] === 'pending'): ?><form method="post" action="/orders/<?= (int)$o['id'] ?>/cancel" onsubmit="return confirm('Cancel this order?')"><?= csrf_field() ?><button class="btn ghost"><?= e(t('Cancel order')) ?></button></form><?php endif; ?>
<form method="post" action="/orders/<?= (int)$o['id'] ?>/reorder"><?= csrf_field() ?><button class="btn"><?= e(t('Order again')) ?></button></form>
</div>
<div class="timeline"><?php foreach ($steps as $i => $s): ?><span class="<?= $o['status'] === 'cancelled' ? 'x' : ($i <= $idx ? 'done' : '') ?>"><?= e($s) ?></span><?php endforeach; ?><?php if ($o['status'] === 'cancelled'): ?><span class="x">cancelled</span><?php endif; ?></div>
<div class="oGrid"><div class="card"><h3><?= e(t('Items')) ?></h3><?php foreach ($items as $i): ?><div class="srow"><span><?= e($i['product_name']) ?> × <?= (int)$i['quantity'] ?></span><b><?= money((float)$i['line_total']) ?></b></div><?php endforeach; ?>
<div class="srow"><span><?= e(t('Subtotal')) ?></span><b><?= money((float)$o['subtotal']) ?></b></div><div class="srow"><span><?= e(t('Discount')) ?></span><b>−<?= money((float)$o['discount']) ?></b></div><?php if ((int)$o['points_used']): ?><div class="srow"><span>Mata digunakan</span><b>−<?= number_format((int)$o['points_used']) ?> pts</b></div><?php endif; ?><div class="srow"><span><?= e(t('Delivery')) ?> (<?= e($o['delivery_method']) ?>)</span><b><?= money((float)$o['delivery_fee']) ?></b></div><div class="srow tot"><span><?= e(t('Total')) ?></span><b><?= money((float)$o['total']) ?></b></div><?php if ((int)$o['points_earned']): ?><div class="srow"><span>Mata diperoleh</span><b>+<?= number_format((int)$o['points_earned']) ?> pts</b></div><?php endif; ?></div>
<div class="card"><h3><?= e(t('Delivery')) ?></h3><p><?= e($o['recipient']) ?> · <?= e($o['phone']) ?><br><?= e($o['address_line']) ?>, <?= e($o['city']) ?> <?= e($o['postcode']) ?></p><p><?= e(t('Payment:')) ?> <?= e($o['payment_method']) ?> (<?= e($o['payment_status']) ?><?php if (($pay['status'] ?? '') === 'claimed'): ?> · menunggu pengesahan admin<?php endif; ?>)</p>
<?php if ($o['payment_method'] === 'cod' && (int)$o['needs_change']): ?><p class="pill">Perlukan duit baki — bayar dengan <?= money((float)$o['cash_tendered']) ?>, rider sediakan kecil</p><?php endif; ?>
<?php if ($o['payment_method'] === 'transfer' && ($pay['status'] ?? '') === 'pending'): ?>
<?php if ($qr): ?><img class="qrbox-img" src="<?= e($qr) ?>" alt="Payment QR"><?php endif; ?>
<form method="post" action="/orders/<?= (int)$o['id'] ?>/claim-paid" onsubmit="return confirm('Sahkan anda sudah buat bayaran?')"><?= csrf_field() ?><button class="btn big">Saya Dah Bayar</button></form>
<?php elseif ($o['payment_method'] === 'transfer' && ($pay['status'] ?? '') === 'claimed'): ?><div class="alert ok">Bayaran dihantar — menunggu pengesahan admin.</div><?php endif; ?>
<p><?= e(t('Placed:')) ?> <?= e($o['created_at']) ?></p>
<?php if (!empty($o['latitude']) && !empty($o['longitude'])): ?><p><a class="btn ghost" target="_blank" rel="noopener" href="https://www.google.com/maps?q=<?= e($o['latitude']) ?>,<?= e($o['longitude']) ?>">Lihat pin di Google Maps</a></p><?php endif; ?></div></div>
