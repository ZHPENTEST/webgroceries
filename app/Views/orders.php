<h1>My orders</h1>
<?php if (!$orders): ?><div class="empty">No orders yet. <a href="/shop">Start shopping →</a></div><?php endif; ?>
<div class="olist"><?php foreach ($orders as $o): ?><a class="orow" href="/orders/<?= (int)$o['id'] ?>"><b><?= e($o['order_number']) ?></b><span><?= e($o['created_at']) ?></span><span class="pill <?= e($o['status']) ?>"><?= e($o['status']) ?></span><b><?= money((float)$o['total']) ?></b></a><?php endforeach; ?></div>
