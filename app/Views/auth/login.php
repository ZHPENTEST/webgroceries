<div class="auth"><h1>Login</h1><p class="muted">Demo admin: admin@webgroceries.test / password</p>
<form method="post" action="/login"><?= csrf_field() ?><label>Email<input name="email" type="email" required autocomplete="email" value="<?= old('email') ?>"></label><label>Password<input name="password" type="password" required autocomplete="current-password"></label><button class="btn big">Login</button></form>
<p>No account? <a href="/register">Create one</a></p></div>
