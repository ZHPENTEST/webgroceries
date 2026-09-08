<h1><?= e(t('Checkout')) ?></h1>
<div class="steps"><span class="on">1 <?= e(t('Contact')) ?></span><span class="on">2 <?= e(t('Delivery address')) ?></span><span class="on">3 <?= e(t('Delivery method')) ?></span><span class="on">4 <?= e(t('Payment')) ?></span><span>5 <?= e(t('Done')) ?></span></div>
<form class="coWrap" method="post" action="/checkout/place" id="coForm"><?= csrf_field() ?>
<div class="coMain">
<fieldset><legend><?= e(t('Contact')) ?></legend><div class="row"><label><?= e(t('Full name')) ?><input name="name" required autocomplete="name" value="<?= e(\App\Core\Auth::user()['name'] ?? '') ?>"></label><label><?= e(t('Phone')) ?><input name="phone" required inputmode="tel" autocomplete="tel"></label></div></fieldset>
<fieldset><legend><?= e(t('Delivery address')) ?></legend>
<p class="muted">Gerakkan pin ke rumah anda — alamat auto-isi. Laju dan tepat untuk rider.</p>
<div id="mapPicker" data-key="<?= e($mapKey ?? '') ?>"></div>
<div class="row"><button type="button" class="btn ghost" id="locateBtn">Guna lokasi semasa</button><small id="pinRead" class="muted"></small></div>
<input type="hidden" name="lat" id="f_lat"><input type="hidden" name="lng" id="f_lng">
<?php if ($addrs): ?><div class="addrPick"><label class="radio"><input type="radio" name="addr_pick" value="new" checked> <?= e(t('New address')) ?></label><?php foreach ($addrs as $i => $a): ?><label class="radio"><input type="radio" name="addr_pick" value="<?= (int)$i ?>" data-fill='<?= json_encode($a, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>'> <?= e($a['label']) ?> — <?= e($a['line1']) ?>, <?= e($a['city']) ?></label><?php endforeach; ?></div><?php endif; ?>
<label><?= e(t('Street')) ?><input name="line1" required autocomplete="street-address"></label><div class="row"><label><?= e(t('City')) ?><input name="city" required autocomplete="address-level2"></label><label><?= e(t('Postcode')) ?><input name="postcode" required inputmode="numeric" autocomplete="postal-code"></label></div></fieldset>
<fieldset><legend><?= e(t('Delivery method')) ?></legend>
<?php foreach ($fees as $k => $v): ?><label class="radio"><input type="radio" name="delivery" value="<?= $k ?>" <?= $k === 'standard' ? 'checked' : '' ?>> <?= e(t(ucfirst($k))) ?> — <?= money($v) ?></label><?php endforeach; ?>
<label><?= e(t('Scheduled slot (optional)')) ?><select name="slot"><option value=""><?= e(t('No preference')) ?></option><?php foreach ($slots as $s): ?><option><?= e($s) ?></option><?php endforeach; ?></select></label></fieldset>
<fieldset><legend><?= e(t('Payment')) ?></legend>
<label class="radio"><input type="radio" name="payment" value="cod" checked> <?= e(t('Cash on Delivery')) ?></label>
<div class="cashbox" id="cashBox">
<label class="radio"><input type="checkbox" name="change" value="1"> Saya perlukan duit baki (rider sediakan duit kecil)</label>
<label>Bayar dengan (RM)<input name="cash" type="number" step="0.01" min="0" inputmode="decimal" placeholder="cth. 50.00"></label>
</div>
<label class="radio"><input type="radio" name="payment" value="transfer"> <?= e(t('Bank Transfer (DuitNow / QR)')) ?></label>
<div class="qrbox" id="transferBox" hidden>
<?php if ($qr): ?><img src="<?= e($qr) ?>" alt="Payment QR"><p>Scan QR di atas untuk bayar <?= money($subtotal - $discount) ?>, kemudian tekan <b>Place order</b> dan butang <b>Saya Dah Bayar</b>.</p>
<?php else: ?><p class="muted">QR belum dimuat naik — pilih Cash buat sementara.</p><?php endif; ?>
</div>
<label class="radio"><input type="radio" name="payment" value="mock_online"> <?= e(t('Mock Online Payment (test only, no real charge)')) ?></label></fieldset>
</div>
<aside class="summary"><h3><?= e(t('Order summary')) ?></h3>
<?php foreach ($items as $i): ?><div class="srow"><span><?= e($i['name']) ?> × <?= (int)$i['quantity'] ?></span><b><?= money(\App\Models\Product::effectivePrice($i) * (int)$i['quantity']) ?></b></div><?php endforeach; ?>
<div class="srow"><span><?= e(t('Subtotal')) ?></span><b><?= money($subtotal) ?></b></div>
<div class="srow"><span><?= e(t('Discount')) ?></span><b>−<?= money($discount) ?></b></div>
<?php if (($points ?? 0) >= 100): ?><label class="radio"><input type="checkbox" name="use_points" value="1"> Guna <?= number_format($points) ?> mata (bernilai <?= money(floor($points / 100)) ?>)</label><?php elseif (($points ?? 0) > 0): ?><p class="muted">Mata anda: <?= (int)$points ?> (100 mata = RM 1)</p><?php endif; ?>
<div class="srow"><span><?= e(t('Delivery')) ?> <small id="zoneName"></small></span><b id="feeEst">—</b></div>
<p class="muted"><?= e(t('Free standard shipping over')) ?> <?= money($free_over) ?>.</p>
<button class="btn big" id="placeBtn"><?= e(t('Place order')) ?></button></aside>
</form>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="<?= asset('/assets/js/maps.js') ?>"></script>
