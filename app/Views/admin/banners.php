<h1>Banners</h1>
<p class="muted">Banner aktif dipaparkan di slider homepage.</p>
<div class="card"><form method="post" action="/admin/banners/save" class="row"><?= csrf_field() ?><input name="title" placeholder="Tajuk" required><input name="subtitle" placeholder="Subtajuk"><input name="link" placeholder="Link (cth. /shop)"><select name="theme"><option value="pink">pink</option><option value="purple">purple</option><option value="cyan">cyan</option><option value="lime">lime</option></select><input name="sort" type="number" value="0" style="max-width:90px"><button class="btn">Tambah</button></form></div>
<div class="tbl"><table><tr><th>Tajuk</th><th>Tema</th><th>Status</th><th></th></tr>
<?php foreach ($items as $b): ?><tr><td><b><?= e($b['title']) ?></b><br><small><?= e($b['subtitle'] ?? '') ?></small></td><td><?= e($b['theme']) ?></td><td><?= e($b['status']) ?></td>
<td style="white-space:nowrap"><form style="display:inline" method="post" action="/admin/banners/<?= (int)$b['id'] ?>/toggle"><?= csrf_field() ?><button class="btn ghost"><?= $b['status'] === 'active' ? 'Off' : 'On' ?></button></form>
<form style="display:inline" method="post" action="/admin/banners/<?= (int)$b['id'] ?>/delete" onsubmit="return confirm('Delete?')"><?= csrf_field() ?><button class="link">Delete</button></form></td></tr><?php endforeach; ?></table></div>
