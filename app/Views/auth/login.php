<div class="auth">
<a class="logo" href="/"><span class="logo-dot"></span>WebGroceries</a>
<div><h1>Welcome back</h1><p class="muted">Login to track orders and checkout faster.</p></div>
<form method="post" action="/login"><?= csrf_field() ?>
<label>Email<input name="email" type="email" required autocomplete="email" value="<?= old('email') ?>"></label>
<label>Password<span class="pw-wrap"><input name="password" type="password" required autocomplete="current-password"><button type="button" data-showpw>Show</button></span></label>
<button class="btn big">Login</button></form>
<p>No account? <a href="/register">Create one</a></p></div>
