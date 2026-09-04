</main>
<footer><div class="wrap fgrid">
<div><b>WebGroceries</b><p>Fresh, fast and trustworthy grocery delivery.</p></div>
<div><b>Shop</b><br><a href="/shop">All products</a><br><a href="/category/fresh-produce">Fresh produce</a><br><a href="/category/dairy">Dairy</a></div>
<div><b>Account</b><br><a href="/account">Profile</a><br><a href="/orders">Orders</a><br><a href="/cart">Cart</a></div>
<div><b>Stay fresh</b><form class="search" onsubmit="event.preventDefault();toast('You are subscribed')"><input placeholder="Email address"><button>Join</button></form></div>
</div><div class="wrap tiny">© 2026 WebGroceries · Demo storefront · Prices in RM</div></footer>
<div id="toasts"></div>
<?php $cur = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH); $bu = $u ?? \App\Core\Auth::user(); ?>
<nav class="bnav" aria-label="Mobile">
<a href="/" class="<?= $cur === '/' ? 'on' : '' ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/></svg>Home</a>
<a href="/shop" class="<?= str_starts_with($cur, '/shop') || str_starts_with($cur, '/category') || str_starts_with($cur, '/product') ? 'on' : '' ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>Shop</a>
<a href="/cart" class="<?= str_starts_with($cur, '/cart') || str_starts_with($cur, '/checkout') ? 'on' : '' ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="20" r="1.5"/><circle cx="17" cy="20" r="1.5"/><path d="M2 3h3l2.6 12.5H18L21 7H6"/></svg>Cart<b class="bn-badge" id="bnCount"><?= ($cartN ?? 0) ? (int)($cartN ?? 0) : '' ?></b></a>
<a href="<?= $bu ? '/account' : '/login' ?>" class="<?= str_starts_with($cur, '/account') || str_starts_with($cur, '/orders') || str_starts_with($cur, '/login') ? 'on' : '' ?>"><?php if ($bu && !empty($bu['avatar'])): ?><img class="avatar" style="width:24px;height:24px;border-width:1px" src="<?= e($bu['avatar']) ?>" alt=""><?php else: ?><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c1.5-4 5-5.5 8-5.5s6.5 1.5 8 5.5"/></svg><?php endif; ?><?= $bu ? 'Saya' : 'Login' ?></a>
</nav>
<script src="<?= asset('/assets/js/app.js') ?>"></script>
<script src="<?= asset('/assets/js/motion.js') ?>"></script>
<script src="<?= asset('/assets/js/cart.js') ?>"></script>
<script src="<?= asset('/assets/js/search.js') ?>"></script>
<script src="<?= asset('/assets/js/wishlist.js') ?>"></script>
<script src="<?= asset('/assets/js/checkout.js') ?>"></script>
<script src="<?= asset('/assets/js/countdown.js') ?>"></script>
<script src="<?= asset('/assets/js/promo.js') ?>"></script>
<script src="<?= asset('/assets/js/filters.js') ?>"></script>
<script src="<?= asset('/assets/js/modal.js') ?>"></script>
<script src="<?= asset('/assets/js/notifications.js') ?>"></script>
<script src="<?= asset('/assets/js/admin.js') ?>"></script>
</body></html>
