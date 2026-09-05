<div class="auth">
<a class="logo" href="/"><span class="logo-dot"></span>WebGroceries</a>
<div><h1>Create account</h1><p class="muted">Join for faster checkout and order tracking.</p></div>
<form method="post" action="/register"><?= csrf_field() ?>
<label>Name<input name="name" required autocomplete="name" value="<?= old('name') ?>"></label>
<label>Email<input name="email" type="email" required autocomplete="email" value="<?= old('email') ?>"></label>
<label>Password (8+ chars)<span class="pw-wrap"><input name="password" type="password" required minlength="8" autocomplete="new-password"><button type="button" data-showpw>Show</button></span></label>
<button class="btn big">Sign up</button></form>
<p>Have an account? <a href="/login">Login</a></p></div>
