<?php $u = \App\Core\Auth::user(); $csrf = \App\Core\Csrf::token();
try { $cartN = (int)(\App\Models\Cart::totals()['count'] ?? 0); } catch (\Throwable $e) { $cartN = 0; } ?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<meta name="theme-color" content="#FF2EA6">
<title><?= e($title ?? 'WebGroceries') ?></title>
<meta name="description" content="WebGroceries — fresh groceries delivered fast. Produce, dairy, meat, pantry and more.">
<link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= asset('/assets/css/app.css') ?>">
<meta name="csrf" content="<?= e($csrf) ?>">
</head><body>
<header class="nav"><div class="wrap nav-in">
<a class="logo" href="/"><span class="logo-dot"></span>WebGroceries</a>
<form class="search" action="/shop" method="get"><input name="q" placeholder="Search milk, salmon, rice…" value="<?= e($_GET['q'] ?? '') ?>"><button>Search</button></form>
<nav class="links"><a href="/">Home</a><a href="/shop">Shop</a><a href="/cart">Cart <b id="cartCount"><?= $cartN ? '(' . $cartN . ')' : '' ?></b></a>
<?php if ($u): ?><a href="/orders">Orders</a><a class="ava-link" href="/account" title="<?= e($u['name']) ?>"><?php if (!empty($u['avatar'])): ?><img class="avatar" src="<?= e($u['avatar']) ?>" alt="<?= e($u['name']) ?>"><?php else: $ini = implode('', array_map(fn($w) => strtoupper($w[0] ?? ''), array_slice(explode(' ', $u['name']), 0, 2))); ?><span class="avatar avatar-init"><?= e($ini) ?></span><?php endif; ?></a><?php if ($u['role']==='admin'): ?><a href="/admin">Admin</a><?php endif; ?><form class="loform" method="post" action="/logout" onsubmit="return confirm('Log out of WebGroceries?')"><?= csrf_field() ?><button class="btn-logout">Logout</button></form>
<?php else: ?><a href="/login">Login</a><a class="btn" href="/register">Sign up</a><?php endif; ?></nav>
<button class="burger" id="menuBtn" aria-expanded="false"><i><span></span><span></span><span></span></i>Menu</button>
</div>
<div class="mdrop" id="mmenu">
<a href="/">Home</a>
<a href="/shop">Shop</a>
<a href="/cart">My Cart<?= $cartN ? ' (' . $cartN . ')' : '' ?></a>
<a href="/orders">My Orders</a>
<a href="/account">My Account</a>
<?php if ($u && $u['role'] === 'admin'): ?><a href="/admin">Admin</a><?php endif; ?>
<?php if ($u): ?><form method="post" action="/logout"><?= csrf_field() ?><button>Logout</button></form>
<?php else: ?><a href="/login">Login</a><?php endif; ?>
</div></header>
<main class="wrap">
<div class="malerts">
<?php if ($m = flash('error')): ?><div class="malert err" role="alert"><span class="ma-ic">!</span><div><b>Action needed</b><p><?= e($m) ?></p></div><button class="ma-x" aria-label="Dismiss" onclick="this.closest('.malert').remove()">×</button><i class="ma-bar"></i></div><?php endif; ?>
<?php if ($m = flash('ok')): ?><div class="malert ok" role="status"><span class="ma-ic">✓</span><div><b>Success</b><p><?= e($m) ?></p></div><button class="ma-x" aria-label="Dismiss" onclick="this.closest('.malert').remove()">×</button><i class="ma-bar"></i></div><?php endif; ?>
</div>
