<h1>Customers</h1>
<p class="muted">Urus mata loyalti dan akses pengguna. Akaun yang di-ban terus ditendang keluar.</p>
<div class="tbl"><table><tr><th>Name</th><th>Orders / Spent</th><th>Points</th><th>Status</th><th>Actions</th></tr>
<?php foreach ($items as $c): ?><tr>
<td><b><?= e($c['name']) ?></b><br><small><?= e($c['email']) ?> · <?= e($c['phone'] ?? '—') ?></small><br><small class="muted">Sejak <?= e($c['created_at']) ?></small></td>
<td><?= (int)$c['orders'] ?> order<br><b><?= money((float)$c['spent']) ?></b></td>
<td><b><?= number_format((int)$c['points']) ?></b>
<form style="margin-top:.4rem" class="row" method="post" action="/admin/customers/<?= (int)$c['id'] ?>/points"><?= csrf_field() ?><input name="delta" type="number" placeholder="+/-" style="max-width:90px"><button class="btn ghost" style="min-height:36px;padding:.3rem .7rem">OK</button></form></td>
<td><span class="pill <?= $c['status'] === 'active' ? 'delivered' : 'cancelled' ?>"><?= e($c['status']) ?></span></td>
<td style="white-space:nowrap"><?php if ($c['status'] === 'active'): ?><form style="display:inline" method="post" action="/admin/customers/<?= (int)$c['id'] ?>/status" onsubmit="return confirm('Ban pengguna ini?')"><?= csrf_field() ?><input type="hidden" name="status" value="suspended"><button class="btn-logout" style="min-height:36px;padding:.3rem .8rem;font-size:.85rem">Ban</button></form>
<?php else: ?><form style="display:inline" method="post" action="/admin/customers/<?= (int)$c['id'] ?>/status"><?= csrf_field() ?><input type="hidden" name="status" value="active"><button class="btn ghost" style="min-height:36px;padding:.3rem .8rem">Unban</button></form><?php endif; ?></td>
</tr><?php endforeach; ?></table></div>
