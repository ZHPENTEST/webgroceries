<?php $steps = ['pending','confirmed','processing','packed','out_for_delivery','delivered']; $idx = array_search($o['status'], $steps); ?>
<?php if ($placed ?? false): ?><div class="alert ok">Order successfully placed. Thank you!</div><?php endif; ?>
<h1>Order <?= e($o['order_number']) ?></h1>
<div class="row no-print" style="margin-bottom:1rem">
<button class="btn ghost" onclick="window.print()">Print invoice</button>
<?php if ($o['status'] === 'pending'): ?><form method="post" action="/orders/<?= (int)$o['id'] ?>/cancel" onsubmit="return confirm('Cancel this order?')"><?= csrf_field() ?><button class="btn ghost">Cancel order</button></form><?php endif; ?>
<form method="post" action="/orders/<?= (int)$o['id'] ?>/reorder"><?= csrf_field() ?><button class="btn">Order again</button></form>
</div>
<div class="timeline"><?php foreach ($steps as $i => $s): ?><span class="<?= $o['status'] === 'cancelled' ? 'x' : ($i <= $idx ? 'done' : '') ?>"><?= e($s) ?></span><?php endforeach; ?><?php if ($o['status'] === 'cancelled'): ?><span class="x">cancelled</span><?php endif; ?></div>
<div class="oGrid"><div class="card"><h3>Items</h3><?php foreach ($items as $i): ?><div class="srow"><span><?= e($i['product_name']) ?> × <?= (int)$i['quantity'] ?></span><b><?= money((float)$i['line_total']) ?></b></div><?php endforeach; ?>
<div class="srow"><span>Subtotal</span><b><?= money((float)$o['subtotal']) ?></b></div><div class="srow"><span>Discount</span><b>−<?= money((float)$o['discount']) ?></b></div><div class="srow"><span>Delivery (<?= e($o['delivery_method']) ?>)</span><b><?= money((float)$o['delivery_fee']) ?></b></div><div class="srow tot"><span>Total</span><b><?= money((float)$o['total']) ?></b></div></div>
<div class="card"><h3>Delivery</h3><p><?= e($o['recipient']) ?> · <?= e($o['phone']) ?><br><?= e($o['address_line']) ?>, <?= e($o['city']) ?> <?= e($o['postcode']) ?></p><p>Payment: <?= e($o['payment_method']) ?> (<?= e($o['payment_status']) ?>)</p><p>Placed: <?= e($o['created_at']) ?></p></div></div>
