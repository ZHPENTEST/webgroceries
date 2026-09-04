<h1>Reviews</h1>
<?php if (!$items): ?><div class="empty">No reviews yet.</div><?php endif; ?>
<div class="tbl"><table><tr><th>Product</th><th>User</th><th>Rating</th><th>Review</th><th></th></tr>
<?php foreach ($items as $r): ?><tr><td><?= e($r['pname']) ?></td><td><?= e($r['uname']) ?></td><td><?= str_repeat('★', (int)$r['rating']) ?></td><td><b><?= e($r['title'] ?? '') ?></b><br><small><?= e(mb_substr($r['body'], 0, 120)) ?></small></td>
<td><form style="display:inline" method="post" action="/admin/reviews/<?= (int)$r['id'] ?>/delete" onsubmit="return confirm('Delete?')"><?= csrf_field() ?><button class="link">Delete</button></form></td></tr><?php endforeach; ?></table></div>
