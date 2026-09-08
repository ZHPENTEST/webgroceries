<h1>Delivery zones</h1>
<p class="muted">Padanan prefix poskod terpanjang menang. Prefix kosong = caj fallback.</p>
<div class="card"><form method="post" action="/admin/zones/save" class="row"><?= csrf_field() ?><input type="hidden" name="id"><input name="name" placeholder="Nama zon" required><input name="prefix" placeholder="Prefix poskod (cth. 50)" inputmode="numeric"><input name="fee" type="number" step="0.01" placeholder="Caj RM" required><button class="btn">Save</button></form></div>
<div class="tbl"><table><tr><th>Zon</th><th>Prefix</th><th>Caj</th><th>Status</th><th></th></tr>
<?php foreach ($items as $z): ?><tr><td><?= e($z['name']) ?></td><td class="mono"><?= e($z['prefix'] === '' ? '(semua)' : $z['prefix']) ?></td><td><?= money((float)$z['fee']) ?></td><td><?= e($z['status']) ?></td>
<td><form style="display:inline" method="post" action="/admin/zones/<?= (int)$z['id'] ?>/delete" onsubmit="return confirm('Delete?')"><?= csrf_field() ?><button class="link">Delete</button></form></td></tr><?php endforeach; ?></table></div>
