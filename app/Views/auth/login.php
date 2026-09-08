<div class="auth">
<a class="logo" href="/"><span class="logo-dot"></span>WebGroceries</a>
<div><h1><?= e(t('Welcome back')) ?></h1><p class="muted"><?= e(t('Login to track orders and checkout faster.')) ?></p></div>
<form method="post" action="/login"><?= csrf_field() ?>
<label><?= e(t('Email')) ?><input name="email" type="email" required autocomplete="email" value="<?= old('email') ?>"></label>
<label><?= e(t('Password')) ?><span class="pw-wrap"><input name="password" type="password" required autocomplete="current-password"><button type="button" data-showpw>Show</button></span></label>
<button class="btn big"><?= e(t('Login')) ?></button></form>
<p><?= e(t('No account?')) ?> <a href="/register"><?= e(t('Create one')) ?></a></p></div>
