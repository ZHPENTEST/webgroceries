<h1>Your cart</h1>
<?php if (!$items): ?><div class="empty">Your cart is empty. <a href="/shop">Continue shopping →</a></div>
<?php else: ?><div class="cartwrap"><div id="cartList">
<?php foreach ($items as $i): $eff = \App\Models\Product::effectivePrice($i); ?>
<div class="citem" data-id="<?= (int)$i['product_id'] ?>">
<img src="<?= e($i['image']) ?>" alt="<?= e($i['name']) ?>">
<div><b><?= e($i['name']) ?></b><span class="muted"><?= money($eff) ?> / <?= e($i['unit']) ?></span>
<div class="qty sm"><button data-dec>−</button><input value="<?= (int)$i['quantity'] ?>" data-q inputmode="numeric" pattern="[0-9]*"><button data-inc>+</button></div></div>
<div class="cline" data-line><?= money($eff * (int)$i['quantity']) ?></div>
<button class="link" data-rm>Remove</button></div>
<?php endforeach; ?></div>
<aside class="summary"><h3>Summary</h3>
<div class="srow"><span>Subtotal</span><b id="subT"><?= money($subtotal) ?></b></div>
<div class="srow"><span>Discount</span><b id="discT">−<?= money($discount) ?></b></div>
<form id="couponF" class="row"><input name="code" placeholder="Coupon (try FRESH10)" value="<?= e($_SESSION['coupon'] ?? '') ?>"><button class="btn ghost">Apply</button></form>
<p class="muted">Delivery calculated at checkout. Free standard over RM 80.</p>
<div class="row"><a class="btn ghost" href="/shop">Continue shopping</a><a class="btn big" href="/checkout">Checkout</a></div>
</aside></div><?php endif; ?>
