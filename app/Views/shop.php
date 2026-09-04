<?php require dirname(__FILE__) . '/components.php'; ?>
<h1><?= e($heading ?? 'Shop all groceries') ?></h1>
<p class="muted"><?= (int)$total ?> items · page <?= (int)$page ?> of <?= (int)$pages ?></p>
<div class="catchips"><a href="/shop" class="<?= empty($f['cat']) ? 'on' : '' ?>">Semua</a><?php foreach ($cats as $c): ?><a href="/category/<?= e($c['slug']) ?>" class="<?= ($f['cat'] ?? '') === $c['slug'] ? 'on' : '' ?>"><?= e($c['name']) ?></a><?php endforeach; ?></div>
<div class="shopwrap">
<aside><details class="filters" open><summary>Tapisan &amp; susunan</summary><form method="get" action="/shop" id="filterForm">
<label>Search<input name="q" value="<?= e($f['q'] ?? '') ?>"></label>
<label>Category<select name="cat"><option value="">All</option><?php foreach ($cats as $c): ?><option value="<?= e($c['slug']) ?>" <?= ($f['cat'] ?? '') === $c['slug'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select></label>
<div class="row"><label>Min<input name="min" type="number" step="0.01" value="<?= e($f['min'] ?? '') ?>"></label><label>Max<input name="max" type="number" step="0.01" value="<?= e($f['max'] ?? '') ?>"></label></div>
<label>Sort<select name="sort"><option value="new">Newest</option><option value="price_asc" <?= ($f['sort'] ?? '') === 'price_asc' ? 'selected' : '' ?>>Price low→high</option><option value="price_desc" <?= ($f['sort'] ?? '') === 'price_desc' ? 'selected' : '' ?>>Price high→low</option><option value="name" <?= ($f['sort'] ?? '') === 'name' ? 'selected' : '' ?>>Name</option></select></label>
<button class="btn">Apply</button></form></details></aside>
<div><div class="pgrid" id="grid"><?php foreach ($items as $p) pcard($p); ?></div>
<?php if (!$items): ?><div class="empty">No products found. Try another search.</div><?php endif; ?>
<div class="pager"><?php for ($i = 1; $i <= $pages; $i++): ?><a class="<?= $i === $page ? 'on' : '' ?>" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a><?php endfor; ?></div></div>
</div>
