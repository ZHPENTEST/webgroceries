<div class="auth"><h1>Create account</h1>
<form method="post" action="/register"><?= csrf_field() ?><label>Name<input name="name" required autocomplete="name" value="<?= old('name') ?>"></label><label>Email<input name="email" type="email" required autocomplete="email" value="<?= old('email') ?>"></label><label>Password (8+ chars)<input name="password" type="password" required minlength="8" autocomplete="new-password"></label><button class="btn big">Sign up</button></form>
<p>Have an account? <a href="/login">Login</a></p></div>
