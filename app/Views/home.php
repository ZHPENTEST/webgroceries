<?php require dirname(__FILE__) . '/components.php'; ?>
<section class="hero">
<div class="hero-t"><h1><?= e(t('Fresh groceries')) ?> <?= pimg('/assets/images/products/hero-salad.jpg', 'fresh vegetables') ?> <?= e(t('delivered in')) ?> <em><?= e(t('hours')) ?></em>, <?= pimg('/assets/images/products/fresh-strawberries-250g.jpg', 'fresh fruit') ?> <?= e(t('not days.')) ?></h1>
<p><?= e(t('Produce, dairy, meat and pantry staples — picked fresh, priced fairly, tracked to your door.')) ?></p>
<a class="btn big" href="/shop"><?= e(t('Shop fresh now')) ?></a>
<div class="trust"><span><?= e(t('★ 4.8 from 6,214 orders')) ?></span><span><?= e(t('Free shipping over RM 80')) ?></span><span><?= e(t('COD + online payment')) ?></span></div></div>
<div class="hero-v"><?= pimg('/assets/images/products/hero-market.jpg', 'Grocery basket', 'fetchpriority="high"') ?><div class="float-card"><b><?= e(t("Today's deal")) ?></b><span><?= e(t('Strawberries 250g — RM 9.90')) ?></span><a href="/shop"><?= e(t('Grab it →')) ?></a></div></div>
</section>
<h2 class="st"><?= e(t('Shop by category')) ?></h2>
<div class="catgrid"><?php foreach ($cats as $c): ?><a class="ccard" href="/category/<?= e($c['slug']) ?>"><b><?= e($c['name']) ?></b><span><?= e($c['description'] ?? '') ?></span></a><?php endforeach; ?></div>
<h2 class="st"><?= e(t('Featured picks')) ?></h2>
<?php if (!$featured): ?><div class="empty"><?= e(t('No products found.')) ?></div><?php endif; ?>
<div class="pgrid"><?php foreach ($featured as $p) pcard($p); ?></div>
<section class="bento">
<div class="tile big"><b><?= e(t('Best sellers, restocked daily')) ?></b><p><?= e(t('Salmon, milk, eggs and rice — the staples households reorder weekly.')) ?></p><a href="/shop?sort=new"><?= e(t('Browse all →')) ?></a></div>
<div class="tile"><b><?= e(t('RM 4.90 delivery')) ?></b><p><?= e(t('Free standard shipping over RM 80.')) ?></p></div>
<div class="tile"><b><?= e(t('Freshness promise')) ?></b><p><?= e(t('Chilled chain from farm to doorstep.')) ?></p></div>
<div class="tile"><b><?= e(t('Special offers')) ?></b><p><?= e(t('Up to 25% off this week.')) ?></p><a href="/shop"><?= e(t('See deals →')) ?></a></div>
<div class="tile"><b><?= e(t('Express 2-hour')) ?></b><p><?= e(t('Klang Valley pilot zones.')) ?></p></div>
</section>
<h2 class="st"><?= e(t('Best sellers')) ?></h2>
<div class="pgrid"><?php foreach ($best as $p) pcard($p); ?></div>
<h2 class="st"><?= e(t('Special offers')) ?></h2>
<div class="flash" data-ends="<?= e($flashEnds) ?>"><b><?= e(t('Flash sale ends in')) ?></b><span class="cd" data-cd>--:--:--</span><span><?= e(t('Extra deals auto-applied weekly. Use code FRESH10.')) ?></span></div>
<div class="pgrid"><?php foreach ($deals as $p) pcard($p); ?></div>
<div class="pslider" id="pslider"><div class="ptrack">
<?php foreach ($banners as $bn): ?><div class="pslide th-<?= e($bn['theme']) ?>"><?php if (!empty($bn['link'])): ?><a href="<?= e($bn['link']) ?>"><?php endif; ?><b><?= e($bn['title']) ?></b><span><?= e($bn['subtitle'] ?? '') ?></span><?php if (!empty($bn['link'])): ?></a><?php endif; ?></div><?php endforeach; ?>
</div><div class="pdots"></div></div>
<section class="why"><div><h2><?= e(t('Why WebGroceries')) ?></h2><ul><li><b><?= e(t('Picked like you would')) ?></b> — <?= e(t('trained fresh-produce pickers, no bruised apples.')) ?></li><li><b><?= e(t('Honest pricing')) ?></b> — <?= e(t('server-calculated totals, no tampered checkouts.')) ?></li><li><b><?= e(t('Tracked delivery')) ?></b> — <?= e(t('pending → delivered timeline on every order.')) ?></li></ul></div>
<div class="reviews"><?php foreach ($reviews as $r): ?><div class="rev"><b><?= e($r['n']) ?> · <?= str_repeat('★', $r['r']) ?></b><p><?= e($r['t']) ?></p></div><?php endforeach; ?></div></section>
