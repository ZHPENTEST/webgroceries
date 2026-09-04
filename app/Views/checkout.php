<h1>Checkout</h1>
<div class="steps"><span class="on">1 Info</span><span class="on">2 Address</span><span class="on">3 Delivery</span><span class="on">4 Payment</span><span>5 Done</span></div>
<form class="coWrap" method="post" action="/checkout/place" id="coForm"><?= csrf_field() ?>
<div class="coMain">
<fieldset><legend>Contact</legend><div class="row"><label>Full name<input name="name" required autocomplete="name" value="<?= e(\App\Core\Auth::user()['name'] ?? '') ?>"></label><label>Phone<input name="phone" required inputmode="tel" autocomplete="tel"></label></div></fieldset>
<fieldset><legend>Delivery address</legend>
<?php if ($addrs): ?><div class="addrPick"><label class="radio"><input type="radio" name="addr_pick" value="new" checked> New address</label><?php foreach ($addrs as $i => $a): ?><label class="radio"><input type="radio" name="addr_pick" value="<?= (int)$i ?>" data-fill='<?= json_encode($a, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>'> <?= e($a['label']) ?> — <?= e($a['line1']) ?>, <?= e($a['city']) ?></label><?php endforeach; ?></div><?php endif; ?>
<label>Street<input name="line1" required autocomplete="street-address"></label><div class="row"><label>City<input name="city" required autocomplete="address-level2"></label><label>Postcode<input name="postcode" required inputmode="numeric" autocomplete="postal-code"></label></div></fieldset>
<fieldset><legend>Delivery method</legend>
<?php foreach ($fees as $k => $v): ?><label class="radio"><input type="radio" name="delivery" value="<?= $k ?>" <?= $k === 'standard' ? 'checked' : '' ?>> <?= ucfirst($k) ?> — <?= money($v) ?></label><?php endforeach; ?>
<label>Scheduled slot (optional)<select name="slot"><option value="">No preference</option><?php foreach ($slots as $s): ?><option><?= e($s) ?></option><?php endforeach; ?></select></label></fieldset>
<fieldset><legend>Payment</legend>
<label class="radio"><input type="radio" name="payment" value="cod" checked> Cash on Delivery</label>
<label class="radio"><input type="radio" name="payment" value="mock_online"> Mock Online Payment (test only, no real charge)</label></fieldset>
</div>
<aside class="summary"><h3>Order summary</h3>
<?php foreach ($items as $i): ?><div class="srow"><span><?= e($i['name']) ?> × <?= (int)$i['quantity'] ?></span><b><?= money(\App\Models\Product::effectivePrice($i) * (int)$i['quantity']) ?></b></div><?php endforeach; ?>
<div class="srow"><span>Subtotal</span><b><?= money($subtotal) ?></b></div>
<div class="srow"><span>Discount</span><b>−<?= money($discount) ?></b></div>
<p class="muted">Free standard shipping over <?= money($free_over) ?>.</p>
<button class="btn big" id="placeBtn">Place order</button></aside>
</form>
