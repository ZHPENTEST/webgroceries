<h1>Backups</h1>
<p class="muted">Backup automatik: cron harian <span class="mono">php cron/backup.php</span> (simpan 7 terkini). Manual:</p>
<div class="card"><form method="post" action="/admin/backups/run"><?= csrf_field() ?><button class="btn">Backup sekarang</button></form></div>
<?php if (!$items): ?><div class="empty">No backups yet.</div><?php endif; ?>
<div class="tbl"><table><tr><th>Fail</th><th>Saiz</th><th>Tarikh</th><th></th></tr>
<?php foreach ($items as $b): ?><tr><td class="mono"><?= e($b['name']) ?></td><td><?= e($b['size']) ?></td><td><?= e($b['date']) ?></td>
<td style="white-space:nowrap"><a class="btn ghost" href="/admin/backups/<?= e($b['name']) ?>/download">Muat turun</a>
<form style="display:inline" method="post" action="/admin/backups/<?= e($b['name']) ?>/delete" onsubmit="return confirm('Delete?')"><?= csrf_field() ?><button class="link">Delete</button></form></td></tr><?php endforeach; ?></table></div>
