<h1><?= e(t('Your cart')) ?></h1>
<?php if (!$items): ?><div class="empty"><?= e(t('Your cart is empty.')) ?> <a href="/shop"><?= e(t('Continue shopping →')) ?></a></div>
<?php else: ?><div class="cartwrap"><div id="cartList">
<?php foreach ($items as $i): $eff = \App\Models\Product::effectivePrice($i); ?>
<div class="citem" data-id="<?= (int)$i['product_id'] ?>">
<?= pimg($i['image'], $i['name'], 'loading="lazy"') ?>
<div><b><?= e($i['name']) ?></b><span class="muted"><?= money($eff) ?> / <?= e($i['unit']) ?></span>
<div class="qty sm"><button data-dec>−</button><input value="<?= (int)$i['quantity'] ?>" data-q inputmode="numeric" pattern="[0-9]*"><button data-inc>+</button></div></div>
<div class="cline" data-line><?= money($eff * (int)$i['quantity']) ?></div>
<button class="link" data-rm><?= e(t('Remove')) ?></button></div>
<?php endforeach; ?></div>
<aside class="summary"><h3><?= e(t('Summary')) ?></h3>
<div class="srow"><span><?= e(t('Subtotal')) ?></span><b id="subT"><?= money($subtotal) ?></b></div>
<div class="srow"><span><?= e(t('Discount')) ?></span><b id="discT">−<?= money($discount) ?></b></div>
<form id="couponF" class="row"><input name="code" placeholder="<?= e(t('Coupon (try FRESH10)')) ?>" value="<?= e($_SESSION['coupon'] ?? '') ?>"><button class="btn ghost"><?= e(t('Apply')) ?></button></form>
<p class="muted"><?= e(t('Delivery calculated at checkout. Free standard over RM 80.')) ?></p>
<div class="row"><a class="btn ghost" href="/shop"><?= e(t('Continue shopping')) ?></a><a class="btn big" href="/checkout"><?= e(t('Checkout')) ?></a></div>
</aside></div><?php endif; ?>
