<h1>Products</h1>
<form class="row" method="get"><input name="q" placeholder="Search…" value="<?= e($q) ?>"><button class="btn">Search</button></form>
<div class="card"><h3>Add / edit product</h3>
<form method="post" action="/admin/products/save" enctype="multipart/form-data" class="aform"><?= csrf_field() ?>
<input type="hidden" name="id" id="f_id"><input type="hidden" name="existing_image" id="f_img">
<div class="row"><label>Name<input name="name" id="f_name" required></label><label>Brand<input name="brand" id="f_brand"></label></div>
<div class="row"><label>Category<select name="category_id"><?php foreach ($cats as $c): ?><option value="<?= (int)$c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?></select></label><label>Unit<input name="unit" value="pc"></label><label>Status<select name="status"><option value="active">active</option><option value="inactive">inactive</option></select></label></div>
<div class="row"><label>Price<input name="price" type="number" step="0.01" required></label><label>Discount<input name="discount_price" type="number" step="0.01"></label><label>Stock<input name="stock_quantity" type="number" value="50"></label></div>
<label>Description<textarea name="description" rows="2"></textarea></label>
<div class="row"><label>Image (JPG/PNG/WebP ≤2MB)<input type="file" name="image" accept="image/jpeg,image/png,image/webp"></label><label><input type="checkbox" name="is_featured" value="1"> Featured</label><label><input type="checkbox" name="is_bestseller" value="1"> Bestseller</label></div>
<button class="btn">Save product</button></form></div>
<div class="tbl"><table><tr><th>ID</th><th>Name</th><th>Price</th><th>Stock</th><th>Status</th><th></th></tr>
<?php foreach ($items as $p): ?><tr><td><?= (int)$p['id'] ?></td><td><?= e($p['name']) ?><br><small><?= e($p['cat']) ?></small></td><td><?= money((float)($p['discount_price'] ?? $p['price'])) ?></td><td><span data-stock="<?= (int)$p['id'] ?>"><?= (int)$p['stock_quantity'] ?></span> <button class="btn ghost" style="padding:.3rem .6rem;min-height:36px" data-sd="-1" data-sp="<?= (int)$p['id'] ?>">−</button><button class="btn ghost" style="padding:.3rem .6rem;min-height:36px" data-sd="1" data-sp="<?= (int)$p['id'] ?>">+</button></td><td><?= e($p['status']) ?></td>
<td><button class="btn ghost" data-edit='<?= json_encode($p, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>'>Edit</button>
<form style="display:inline" method="post" action="/admin/products/<?= (int)$p['id'] ?>/delete" onsubmit="return confirm('Delete?')"><?= csrf_field() ?><button class="link">Delete</button></form></td></tr><?php endforeach; ?></table></div>
