<?php require dirname(__FILE__) . '/components.php'; ?>
<section class="hero">
<div class="hero-t"><h1>Fresh groceries <?= pimg('/assets/images/products/hero-salad.jpg', 'fresh vegetables') ?> delivered in <em>hours</em>, <?= pimg('/assets/images/products/fresh-strawberries-250g.jpg', 'fresh fruit') ?> not days.</h1>
<p>Produce, dairy, meat and pantry staples — picked fresh, priced fairly, tracked to your door.</p>
<a class="btn big" href="/shop">Shop fresh now</a>
<div class="trust"><span>★ 4.8 from 6,214 orders</span><span>Free shipping over RM 80</span><span>COD + online payment</span></div></div>
<div class="hero-v"><?= pimg('/assets/images/products/hero-market.jpg', 'Grocery basket', 'fetchpriority="high"') ?><div class="float-card"><b>Today's deal</b><span>Strawberries 250g — RM 9.90</span><a href="/shop">Grab it →</a></div></div>
</section>
<h2 class="st">Shop by category</h2>
<div class="catgrid"><?php foreach ($cats as $c): ?><a class="ccard" href="/category/<?= e($c['slug']) ?>"><b><?= e($c['name']) ?></b><span><?= e($c['description'] ?? '') ?></span></a><?php endforeach; ?></div>
<h2 class="st">Featured picks</h2>
<?php if (!$featured): ?><div class="empty">No products found.</div><?php endif; ?>
<div class="pgrid"><?php foreach ($featured as $p) pcard($p); ?></div>
<section class="bento">
<div class="tile big"><b>Best sellers, restocked daily</b><p>Salmon, milk, eggs and rice — the staples households reorder weekly.</p><a href="/shop?sort=new">Browse all →</a></div>
<div class="tile"><b>RM 4.90 delivery</b><p>Free standard shipping over RM 80.</p></div>
<div class="tile"><b>Freshness promise</b><p>Chilled chain from farm to doorstep.</p></div>
<div class="tile"><b>Special offers</b><p>Up to 25% off this week.</p><a href="/shop">See deals →</a></div>
<div class="tile"><b>Express 2-hour</b><p> Klang Valley pilot zones.</p></div>
</section>
<h2 class="st">Best sellers</h2>
<div class="pgrid"><?php foreach ($best as $p) pcard($p); ?></div>
<h2 class="st">Special offers</h2>
<div class="flash" data-ends="<?= e($flashEnds) ?>"><b>Flash sale ends in</b><span class="cd" data-cd>--:--:--</span><span>Extra deals auto-applied weekly. Use code FRESH10.</span></div>
<div class="pgrid"><?php foreach ($deals as $p) pcard($p); ?></div>
<div class="pslider" id="pslider"><div class="ptrack">
<div class="pslide"><b>Free shipping over RM 80</b><span>Standard delivery, auto-applied.</span></div>
<div class="pslide"><b>FRESH10 — 10% off RM 50+</b><span>Apply the coupon in your cart.</span></div>
<div class="pslide"><b>Express 2-hour delivery</b><span>Pilot zones in Klang Valley.</span></div>
</div><div class="pdots"></div></div>
<section class="why"><div><h2>Why WebGroceries</h2><ul><li><b>Picked like you would</b> — trained fresh-produce pickers, no bruised apples.</li><li><b>Honest pricing</b> — server-calculated totals, no tampered checkouts.</li><li><b>Tracked delivery</b> — pending → delivered timeline on every order.</li></ul></div>
<div class="reviews"><?php foreach ($reviews as $r): ?><div class="rev"><b><?= e($r['n']) ?> · <?= str_repeat('★', $r['r']) ?></b><p><?= e($r['t']) ?></p></div><?php endforeach; ?></div></section>
