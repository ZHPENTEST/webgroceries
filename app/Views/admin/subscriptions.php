<h1>Subscriptions</h1>
<p class="muted">Jana order manual atau biar cron mingguan jalan. Cron: <span class="mono">php /path/cron/weekly.php</span></p>
<?php if (!$items): ?><div class="empty">No subscriptions yet.</div><?php endif; ?>
<div class="tbl"><table><tr><th>ID</th><th>Customer</th><th>Box</th><th>Hari</th><th>Next</th><th>Status</th><th></th></tr>
<?php foreach ($items as $s): ?><tr><td><?= (int)$s['id'] ?></td><td><?= e($s['uname']) ?><br><small><?= e($s['line1']) ?>, <?= e($s['city']) ?></small></td><td><?= e($s['pname']) ?> × <?= (int)$s['qty'] ?></td><td><?= ['Ahd','Isn','Sel','Rab','Kha','Jum','Sab'][(int)$s['day_of_week']] ?></td><td><?= e($s['next_run']) ?></td><td><?= e($s['status']) ?></td>
<td><?php if ($s['status'] === 'active'): ?><form style="display:inline" method="post" action="/admin/subscriptions/<?= (int)$s['id'] ?>/generate"><?= csrf_field() ?><button class="btn ghost">Jana order</button></form><?php endif; ?></td></tr><?php endforeach; ?></table></div>
