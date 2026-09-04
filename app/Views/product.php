<?php $eff = \App\Models\Product::effectivePrice($p); ?>
<div class="pdetail">
<div><?= pimg($p['image'] ?? '', $p['name'] ?? '', 'class="pimg" fetchpriority="high"') ?></div>
<div><span class="cat"><?= e($p['cat_name']) ?> · <?= e($p['brand'] ?? '') ?></span>
<h1><?= e($p['name']) ?></h1>
<div class="price big"><?php if ($p['discount_price']): ?><s><?= money((float)$p['price']) ?></s><?php endif; ?><b><?= money($eff) ?></b></div>
<p><?= e($p['description'] ?? '') ?></p>
<p class="stock"><?= (int)$p['stock_quantity'] > 0 ? 'In stock (' . (int)$p['stock_quantity'] . ' ' . e($p['unit']) . ')' : 'Out of stock' ?></p>
<div class="buyrow"><div class="qty"><button onclick="qstep(-1)">−</button><input id="qty" value="1" type="number" min="1" max="99" inputmode="numeric"><button onclick="qstep(1)">+</button></div>
<button class="btn big" data-add="<?= (int)$p['id'] ?>" data-qty-src="#qty" <?= (int)$p['stock_quantity'] === 0 ? 'disabled' : '' ?>>Add to cart</button>
<button class="btn ghost" data-wish="<?= (int)$p['id'] ?>">♡ Wishlist</button></div>
<ul class="spec"><li>Category: <?= e($p['cat_name']) ?></li><li>Unit: <?= e($p['unit']) ?></li><li>SKU: WG-<?= (int)$p['id'] ?></li></ul></div>
</div>
<h2 class="st">Related products</h2>
<div class="pgrid"><?php require dirname(__FILE__) . '/components.php'; foreach ($related as $r) pcard($r); ?></div>
<h2 class="st">Ratings &amp; reviews <?= $agg['n'] ? '(' . (int)$agg['n'] . ' · ' . number_format((float)$agg['a'], 1) . '★)' : '' ?></h2>
<div class="card">
<?php if (!$reviews): ?><p class="muted">No reviews yet — be the first.</p><?php endif; ?>
<?php foreach ($reviews as $rv): ?><div class="rev"><b><?= e($rv['uname']) ?> · <?= str_repeat('★', (int)$rv['rating']) . str_repeat('☆', 5 - (int)$rv['rating']) ?></b><?php if ($rv['title']): ?><br><b><?= e($rv['title']) ?></b><?php endif; ?><p><?= e($rv['body']) ?></p><small class="muted"><?= e($rv['created_at']) ?></small></div><?php endforeach; ?>
<?php if (\App\Core\Auth::check() && $can): ?>
<h3><?= $mine ? 'Update your review' : 'Write a review' ?></h3>
<form method="post" action="/product/<?= e($p['slug']) ?>/review"><?= csrf_field() ?>
<div class="row"><label>Rating<select name="rating"><?php for ($s = 5; $s >= 1; $s--): ?><option value="<?= $s ?>" <?= $mine && (int)$mine['rating'] === $s ? 'selected' : '' ?>><?= $s ?> ★</option><?php endfor; ?></select></label><label>Title<input name="title" maxlength="120" value="<?= e($mine['title'] ?? '') ?>"></label></div>
<label>Review<textarea name="body" rows="3" required minlength="3" maxlength="1000"><?= e($mine['body'] ?? '') ?></textarea></label>
<button class="btn">Submit review</button></form>
<?php elseif (!\App\Core\Auth::check()): ?><p><a href="/login">Login</a> to write a review.</p>
<?php else: ?><p class="muted">Only verified buyers can review — items you ordered will unlock reviews here.</p><?php endif; ?>
</div>
<?php if ($recent): ?><h2 class="st">Recently viewed</h2><div class="pgrid"><?php foreach ($recent as $r) pcard($r); ?></div><?php endif; ?>
<script>function qstep(d){const i=document.getElementById('qty');i.value=Math.max(1,Math.min(99,(+i.value||1)+d));}</script>
