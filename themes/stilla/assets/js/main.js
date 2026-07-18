// ---------- NAV: hide on scroll down, show on scroll up + border on scroll ----------
const nav = document.getElementById('siteNav');
let lastY = window.scrollY;
window.addEventListener('scroll', () => {
	const y = window.scrollY;
	nav.classList.toggle('is-scrolled', y > 10);
	if (y > lastY && y > 400) nav.classList.add('is-hidden');
	else nav.classList.remove('is-hidden');
	lastY = y;
}, { passive: true });

// ---------- SEARCH TOGGLE ----------
const searchToggle = document.getElementById('searchToggle');
const navSearch = document.getElementById('navSearch');
searchToggle?.addEventListener('click', () => {
	const open = navSearch.hasAttribute('hidden');
	if (open) navSearch.removeAttribute('hidden'); else navSearch.setAttribute('hidden', '');
	searchToggle.setAttribute('aria-expanded', String(open));
});

// ---------- MOBILE MENU ----------
const burger = document.getElementById('navBurger');
const menu = document.querySelector('.nav__menu');
burger?.addEventListener('click', () => {
	const open = menu.classList.toggle('is-open');
	burger.setAttribute('aria-expanded', String(open));
});

// ---------- REVEAL ON SCROLL ----------
const io = new IntersectionObserver((entries) => {
	entries.forEach((e) => {
		if (e.isIntersecting) { e.target.classList.add('is-visible'); io.unobserve(e.target); }
	});
}, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
document.querySelectorAll('.reveal').forEach((el) => io.observe(el));

// ---------- PARALLAX ----------
const parallaxEls = document.querySelectorAll('[data-parallax]');
window.addEventListener('scroll', () => {
	const vh = window.innerHeight;
	parallaxEls.forEach((el) => {
		const r = el.getBoundingClientRect();
		const offset = (r.top + r.height / 2 - vh / 2) * parseFloat(el.dataset.parallax);
		el.style.transform = `translateY(${offset * -1}px)`;
	});
}, { passive: true });

// ---------- TILT (split media) ----------
document.querySelectorAll('[data-tilt]').forEach((el) => {
	el.addEventListener('mousemove', (e) => {
		const r = el.getBoundingClientRect();
		const px = (e.clientX - r.left) / r.width - 0.5;
		const py = (e.clientY - r.top) / r.height - 0.5;
		el.style.transform = `perspective(800px) rotateY(${px * 8}deg) rotateX(${-py * 8}deg)`;
	});
	el.addEventListener('mouseleave', () => { el.style.transform = ''; });
});

// ---------- CAROUSEL ENGINE ----------
function initCarousel(root) {
	const track = root.querySelector('.carousel__track');
	const dotsWrap = root.querySelector('[data-dots]');
	const prev = root.parentElement.querySelector('.cbtn--prev');
	const next = root.parentElement.querySelector('.cbtn--next');
	if (!track) return;

	const cards = Array.from(track.children);
	let index = 0;
	let perView = computePerView();

	function computePerView() {
		const w = window.innerWidth;
		if (w <= 520) return 1.25;
		if (w <= 820) return 2;
		if (w <= 1024) return 3;
		return 5;
	}
	function step() {
		const gap = parseFloat(getComputedStyle(track).gap) || 0;
		return cards[0].getBoundingClientRect().width + gap;
	}
	function maxIndex() {
		return Math.max(0, cards.length - Math.floor(perView));
	}
	function render(animate = true) {
		const i = Math.min(index, maxIndex());
		track.style.transition = animate ? '' : 'none';
		track.style.transform = `translateX(${-i * step()}px)`;
		if (dotsWrap) {
			[...dotsWrap.children].forEach((d, di) => d.classList.toggle('is-active', di === i));
		}
		if (prev) prev.disabled = i <= 0;
		if (next) next.disabled = i >= maxIndex();
	}
	function buildDots() {
		if (!dotsWrap) return;
		dotsWrap.innerHTML = '';
		const n = maxIndex() + 1;
		for (let i = 0; i < n; i++) {
			const b = document.createElement('button');
			if (i === 0) b.classList.add('is-active');
			b.addEventListener('click', () => { index = i; render(); });
			dotsWrap.appendChild(b);
		}
	}
	prev?.addEventListener('click', () => { index = Math.max(0, index - 1); render(); });
	next?.addEventListener('click', () => { index = Math.min(maxIndex(), index + 1); render(); });

	// DRAG
	let startX = 0, dragX = 0, dragging = false;
	track.addEventListener('pointerdown', (e) => {
		dragging = true; startX = e.clientX; dragX = 0;
		track.classList.add('is-dragging');
	});
	track.addEventListener('pointermove', (e) => {
		if (!dragging) return;
		dragX = e.clientX - startX;
		const i = Math.min(index, maxIndex());
		track.style.transform = `translateX(${-i * step() + dragX}px)`;
	});
	function endDrag() {
		if (!dragging) return;
		dragging = false;
		track.classList.remove('is-dragging');
		if (Math.abs(dragX) > 60) {
			if (dragX < 0) index = Math.min(maxIndex(), index + 1);
			else index = Math.max(0, index - 1);
		}
		dragX = 0;
		render();
	}
	track.addEventListener('pointerup', endDrag);
	track.addEventListener('pointercancel', endDrag);
	track.addEventListener('pointerleave', endDrag);

	// CLICK → navigate. Pointer capture can swallow the native anchor click on
	// some browsers, so navigate explicitly when the gesture wasn't a drag.
	track.querySelectorAll('a.card').forEach((a) => {
		a.addEventListener('click', (e) => {
			if (dragging || Math.abs(dragX) > 8) { e.preventDefault(); return; }
			e.preventDefault();
			if (a.getAttribute('href')) window.location.href = a.getAttribute('href');
		});
	});

	window.addEventListener('resize', () => { perView = computePerView(); buildDots(); render(false); });
	buildDots();
	render(false);
}
document.querySelectorAll('[data-carousel]').forEach(initCarousel);

// ---------- LANG TOGGLE (demo) ----------
document.getElementById('langToggle')?.addEventListener('click', (e) => {
	e.target.textContent = e.target.textContent === 'EN' ? 'SE' : 'EN';
});

// ---------- PRODUCT GALLERY: thumbnail -> stage swap ----------
const stageImg = document.getElementById('stageImg');
if (stageImg) {
	document.querySelectorAll('.pthumb').forEach((t) => {
		t.addEventListener('click', () => {
			document.querySelectorAll('.pthumb').forEach((x) => x.classList.remove('is-active'));
			t.classList.add('is-active');
			stageImg.style.opacity = '0';
			const full = t.dataset.full;
			const pre = new Image();
			pre.onload = () => { stageImg.src = full; stageImg.style.opacity = '1'; };
			pre.src = full;
		});
	});
}

// ---------- CATALOG FILTERS (search, price, category, sort) ----------
const grid = document.getElementById('prodGrid');
if (grid) {
	const cards   = Array.from(grid.querySelectorAll('.pcard'));
	const search  = document.getElementById('searchInput');
	const minR    = document.getElementById('priceMin');
	const maxR    = document.getElementById('priceMax');
	const minL    = document.getElementById('priceMinLbl');
	const maxL    = document.getElementById('priceMaxLbl');
	const typeC   = Array.from(document.querySelectorAll('.f-type'));
	const famC    = Array.from(document.querySelectorAll('.f-family'));
	const stockC  = document.getElementById('inStockOnly');
	const sortS   = document.getElementById('sortSelect');
	const count   = document.getElementById('resultCount');
	const empty   = document.getElementById('emptyMsg');
	const clearB  = document.getElementById('clearFilters');

	function apply() {
		const q    = (search.value || '').trim().toLowerCase();
		const tMin = parseInt(minR.value, 10);
		const tMax = parseInt(maxR.value, 10);
		const types  = typeC.filter(c => c.checked).map(c => c.value);
		const fams   = famC.filter(c => c.checked).map(c => c.value);
		const stock  = stockC && stockC.checked;

		let visible = cards.filter(c => {
			const name  = c.dataset.name || '';
			const price = parseFloat(c.dataset.price) || 0;
			const stk   = parseInt(c.dataset.stock, 10) || 0;
			if (q && !name.includes(q)) return false;
			if (types.length && !types.includes(c.dataset.type)) return false;
			if (fams.length && !fams.includes(c.dataset.family)) return false;
			if (price < tMin || price > tMax) return false;
			if (stock && stk <= 0) return false;
			return true;
		});

		// sort
		const s = sortS.value;
		if (s === 'price-asc')  visible.sort((a,b) => (a.dataset.price)-(b.dataset.price));
		if (s === 'price-desc') visible.sort((a,b) => (b.dataset.price)-(a.dataset.price));
		if (s === 'name-asc')   visible.sort((a,b) => a.querySelector('h3').textContent.localeCompare(b.querySelector('h3').textContent));

		cards.forEach(c => { c.style.display = 'none'; });
		visible.forEach(c => { c.style.display = ''; grid.appendChild(c); });
		count.textContent = visible.length + ' products';
		empty.hidden = visible.length > 0;
	}
	function syncLabels() {
		let lo = parseInt(minR.value, 10), hi = parseInt(maxR.value, 10);
		if (lo > hi) { [lo, hi] = [hi, lo]; }
		minL.textContent = '$' + lo; maxL.textContent = '$' + hi;
	}
	minR.addEventListener('input', () => { if (+minR.value > +maxR.value) minR.value = maxR.value; syncLabels(); apply(); });
	maxR.addEventListener('input', () => { if (+maxR.value < +minR.value) maxR.value = minR.value; syncLabels(); apply(); });
	search.addEventListener('input', apply);
	typeC.forEach(c => c.addEventListener('change', apply));
	famC.forEach(c => c.addEventListener('change', apply));
	if (stockC) stockC.addEventListener('change', apply);
	sortS.addEventListener('change', apply);
	clearB.addEventListener('click', () => {
		search.value = ''; typeC.forEach(c => c.checked = false); famC.forEach(c => c.checked = false);
		if (stockC) stockC.checked = false; minR.value = minR.min; maxR.value = maxR.max; syncLabels(); apply();
	});
	syncLabels();
}

// ---------- STRIPE BUY NOW ----------
const buyBtn = document.getElementById('buyNow');
if (buyBtn) {
	const status = document.getElementById('buyStatus');
	const qtyInput = document.getElementById('buyQty');
	const qMinus = document.getElementById('qtyMinus');
	const qPlus = document.getElementById('qtyPlus');
	if (qMinus && qtyInput) qMinus.addEventListener('click', () => { qtyInput.value = Math.max(1, (parseInt(qtyInput.value,10)||1) - 1); });
	if (qPlus && qtyInput) qPlus.addEventListener('click', () => { qtyInput.value = (parseInt(qtyInput.value,10)||1) + 1; });
	if (qtyInput) qtyInput.addEventListener('change', () => { qtyInput.value = Math.max(1, parseInt(qtyInput.value,10)||1); });
	buyBtn.addEventListener('click', async () => {
		if ( ! stillaAjax.isLoggedIn ) { window.location.href = stillaAjax.loginUrl; return; }
		status.textContent = 'Redirecting to secure checkout…'; status.className = 'buy-status';
		const fd = new FormData();
		fd.append('action', 'stilla_checkout');
		fd.append('slug', buyBtn.dataset.slug);
		fd.append('qty', qtyInput ? qtyInput.value : 1);
		try {
			const res = await fetch(stillaAjax.url, { method: 'POST', body: fd });
			const data = await res.json();
			if (data.success && data.data.url) { window.location.href = data.data.url; }
			else { status.textContent = data.data.message || 'Checkout failed.'; status.className = 'buy-status is-err'; }
		} catch (e) {
			status.textContent = 'Network error — please try again.'; status.className = 'buy-status is-err';
		}
	});
}

// ---------- CART ----------
const cartToggle = document.getElementById('cartToggle');
const cartDrawer = document.getElementById('cartDrawer');
if (cartToggle && cartDrawer) {
	const drawerItems = document.getElementById('drawerItems');
	const drawerCount = document.getElementById('drawerCount');
	const drawerTotal = document.getElementById('drawerTotal');
	const navCount = document.getElementById('cartCount');

	const fmt = (n) => '$' + Number(n).toLocaleString();

	async function renderCart() {
		const res = await fetch(stillaAjax.url, { method: 'POST', body: new URLSearchParams({ action: 'stilla_cart_get' }) });
		const data = await res.json();
		const items = data.data.items || [];
		if (!items.length) {
			drawerItems.innerHTML = '<p class="drawer__empty">Your cart is empty.</p>';
		} else {
			drawerItems.innerHTML = items.map(it => `
				<div class="ditem" data-slug="${it.slug}">
					<div class="ditem__name">${it.name}</div>
					<div class="ditem__row">
						<span>${fmt(it.price)} × ${it.qty}</span>
						<button class="ditem__x" data-remove="${it.slug}">Remove</button>
					</div>
				</div>`).join('');
		}
		navCount.textContent = data.data.count;
		drawerCount.textContent = '(' + data.data.count + ')';
		drawerTotal.textContent = fmt(data.data.total);
	}
	function openCart() { cartDrawer.removeAttribute('hidden'); document.body.style.overflow = 'hidden'; renderCart(); }
	function closeCart() { cartDrawer.setAttribute('hidden', ''); document.body.style.overflow = ''; }
	cartToggle.addEventListener('click', openCart);
	cartDrawer.querySelectorAll('[data-close-cart]').forEach(el => el.addEventListener('click', closeCart));
	document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !cartDrawer.hasAttribute('hidden')) closeCart(); });

	drawerItems.addEventListener('click', async (e) => {
		const slug = e.target.dataset.remove;
		if (!slug) return;
		await fetch(stillaAjax.url, { method: 'POST', body: new URLSearchParams({ action: 'stilla_cart_remove', slug }) });
		renderCart();
	});

	async function addToCart(slug, qty = 1) {
		if ( ! stillaAjax.isLoggedIn ) { window.location.href = stillaAjax.loginUrl; return; }
		await fetch(stillaAjax.url, { method: 'POST', body: new URLSearchParams({ action: 'stilla_cart_add', slug, qty }) });
		openCart();
	}
	// Catalog "+ Cart" buttons
	document.querySelectorAll('.pcard__add').forEach(b => b.addEventListener('click', (e) => {
		e.preventDefault(); e.stopPropagation();
		addToCart(b.dataset.slug);
	}));
	// Product page "Add to cart"
	document.getElementById('addToCart')?.addEventListener('click', () => addToCart(document.getElementById('addToCart').dataset.slug));

	// Checkout (drawer + cart page)
	async function checkout() {
		if ( ! stillaAjax.isLoggedIn ) { window.location.href = stillaAjax.loginUrl; return; }
		const res = await fetch(stillaAjax.url, { method: 'POST', body: new URLSearchParams({ action: 'stilla_checkout_cart' }) });
		const data = await res.json();
		if (data.success && data.data.url) window.location.href = data.data.url;
		else alert(data.data.message || 'Checkout failed.');
	}
	document.getElementById('drawerCheckout')?.addEventListener('click', checkout);
	document.getElementById('cartCheckout')?.addEventListener('click', checkout);

	// Full cart page qty + remove
	const cartItems = document.getElementById('cartItems');
	if (cartItems) {
		cartItems.addEventListener('click', async (e) => {
			const li = e.target.closest('.citem'); if (!li) return;
			const slug = li.dataset.slug;
			if (e.target.classList.contains('qplus')) {
				const inp = li.querySelector('.qinput'); inp.value = +inp.value + 1;
				await fetch(stillaAjax.url, { method: 'POST', body: new URLSearchParams({ action: 'stilla_cart_qty', slug, qty: inp.value }) });
				location.reload();
			} else if (e.target.classList.contains('qminus')) {
				const inp = li.querySelector('.qinput'); inp.value = Math.max(1, +inp.value - 1);
				await fetch(stillaAjax.url, { method: 'POST', body: new URLSearchParams({ action: 'stilla_cart_qty', slug, qty: inp.value }) });
				location.reload();
			} else if (e.target.classList.contains('citem__remove')) {
				await fetch(stillaAjax.url, { method: 'POST', body: new URLSearchParams({ action: 'stilla_cart_remove', slug }) });
				location.reload();
			}
		});
		cartItems.addEventListener('change', async (e) => {
			if (!e.target.classList.contains('qinput')) return;
			const slug = e.target.closest('.citem').dataset.slug;
			await fetch(stillaAjax.url, { method: 'POST', body: new URLSearchParams({ action: 'stilla_cart_qty', slug, qty: e.target.value }) });
			location.reload();
		});
	}
}

// ---------- QUOTE MODAL + AJAX SUBMIT ----------
const quoteModal = document.getElementById('quoteModal');
if (quoteModal) {
	const form = document.getElementById('quoteForm');
	const msg = document.getElementById('quoteMsg');
	const open = () => {
		if ( ! stillaAjax.isLoggedIn ) { window.location.href = stillaAjax.loginUrl; return; }
		quoteModal.removeAttribute('hidden'); document.body.style.overflow = 'hidden';
	};
	const close = () => { quoteModal.setAttribute('hidden', ''); document.body.style.overflow = ''; msg.textContent = ''; msg.className = 'qform__msg'; };
	document.getElementById('openQuote')?.addEventListener('click', open);
	quoteModal.querySelectorAll('[data-close]').forEach((el) => el.addEventListener('click', close));
	document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !quoteModal.hasAttribute('hidden')) close(); });
	form.addEventListener('submit', async (e) => {
		e.preventDefault();
		msg.textContent = 'Sending…'; msg.className = 'qform__msg';
		const fd = new FormData(form);
		fd.append('action', 'stilla_quote');
		try {
			const res = await fetch(stillaAjax.url, { method: 'POST', body: fd });
			const data = await res.json();
			if (data.success) { msg.textContent = data.data.message; msg.className = 'qform__msg is-ok'; form.reset(); setTimeout(close, 2600); }
			else if (data.data && data.data.code === 'login') { window.location.href = stillaAjax.loginUrl; }
			else { msg.textContent = data.data.message || 'Something went wrong.'; msg.className = 'qform__msg is-err'; }
		} catch (err) {
			msg.textContent = 'Network error — please try again.'; msg.className = 'qform__msg is-err';
		}
	});
}

// ---------- REVIEW STAR RATINGS ----------
document.querySelectorAll('.review[data-rating]').forEach((el) => {
	const rating = Math.max(0, Math.min(5, parseInt(el.dataset.rating, 10) || 0));
	const wrap = el.querySelector('.review__stars');
	if (!wrap) return;
	let html = '';
	for (let i = 1; i <= 5; i++) html += i <= rating ? '★' : '☆';
	wrap.textContent = html;
});

// ---------- FORGOT PASSWORD TOGGLE (swap views) ----------
const forgotToggle = document.getElementById('forgotToggle');
const forgotBack = document.getElementById('forgotBack');
const loginCard = document.getElementById('loginCard');
const resetCard = document.getElementById('resetCard');
if (forgotToggle && loginCard && resetCard) {
	forgotToggle.addEventListener('click', (e) => { e.preventDefault(); loginCard.hidden = true; resetCard.hidden = false; resetCard.querySelector('input')?.focus(); });
	if (forgotBack) forgotBack.addEventListener('click', (e) => { e.preventDefault(); resetCard.hidden = true; loginCard.hidden = false; loginCard.querySelector('input')?.focus(); });
}

