<h1>Categories</h1>
<div class="card"><form method="post" action="/admin/categories/save" class="row"><?= csrf_field() ?><input type="hidden" name="id"><input name="name" placeholder="New category name" required><input name="description" placeholder="Description"><button class="btn">Save</button></form></div>
<div class="tbl"><table><tr><th>Name</th><th>Slug</th><th>Products</th><th>Status</th><th></th></tr>
<?php foreach ($items as $c): ?><tr><td><?= e($c['name']) ?></td><td class="mono"><?= e($c['slug']) ?></td><td><?= (int)$c['n'] ?></td><td><?= e($c['status']) ?></td>
<td><form style="display:inline" method="post" action="/admin/categories/<?= (int)$c['id'] ?>/delete" onsubmit="return confirm('Delete?')"><?= csrf_field() ?><button class="link">Delete</button></form></td></tr><?php endforeach; ?></table></div>
