<?php function pcard(array $p): void { $eff = \App\Models\Product::effectivePrice($p); $off = $p['discount_price'] ? round(100*(1-$p['discount_price']/$p['price'])) : 0; ?>
<article class="pcard" style="--i:0">
<a href="/product/<?= e($p['slug']) ?>"><?= pimg($p['image'] ?? '', $p['name'] ?? '', 'loading="lazy"') ?></a>
<?php if ($off): ?><span class="badge">-<?= $off ?>%</span><?php endif; ?>
<button class="wish" data-wish="<?= (int)$p['id'] ?>" aria-label="wishlist">♡</button>
<div class="pbody"><span class="cat"><?= e($p['cat_name'] ?? $p['brand'] ?? '') ?></span>
<a class="pname" href="/product/<?= e($p['slug']) ?>"><?= e($p['name']) ?></a>
<div class="price"><?php if ($p['discount_price']): ?><s><?= money((float)$p['price']) ?></s><?php endif; ?><b><?= money($eff) ?></b><span class="unit">/ <?= e($p['unit']) ?></span></div>
<div class="stock <?= (int)$p['stock_quantity'] === 0 ? 'out' : ((int)$p['stock_quantity'] <= ($p['low_stock_threshold'] ?? 10) ? 'low' : '') ?>"><?= (int)$p['stock_quantity'] === 0 ? 'Out of stock' : ((int)$p['stock_quantity'] <= ($p['low_stock_threshold'] ?? 10) ? 'Only ' . (int)$p['stock_quantity'] . ' left' : 'In stock') ?></div>
<button class="btn add" data-add="<?= (int)$p['id'] ?>" <?= (int)$p['stock_quantity'] === 0 ? 'disabled' : '' ?>>Add to cart</button>
</div></article><?php } ?>
