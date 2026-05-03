/* ═══════════════════════════════════════════════════════════
   علامة | ALAMAH — Main JavaScript
   E-Commerce System: Cart, Products, WhatsApp Checkout
   Dynamic API-driven version
   ═══════════════════════════════════════════════════════════ */

/* ── SAR SVG Helper ── */
const SAR_SVG = `<span class="sar-icon"><img src="image/Saudi_Riyal_Symbol.svg" alt="ر.س"></span>`;

/* ── Product Data (loaded from API) ── */
let PRODUCTS = [];
let CATEGORIES = [{ key: 'all', label: 'الكل' }];
let _dataLoaded = false;

/* ── Load data from API ── */
async function loadProductData() {
  if (_dataLoaded) return;
  try {
    const res = await fetch('api/products.php');
    const data = await res.json();
    PRODUCTS = data.products || [];
    CATEGORIES = data.categories || [{ key: 'all', label: 'الكل' }];
    _dataLoaded = true;
  } catch (e) {
    console.warn('API unavailable, using empty data', e);
  }
}

/* WA_NUMBER is injected from DB via footer.php */

/* ── Cart State ── */
let cart = JSON.parse(localStorage.getItem('alamah_cart') || '[]');
let pendingProduct = null; // for custom field modal

/* ═══════════════════════════════════════════════
   DOM READY
   ═══════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', async () => {

  /* ── Load product data from API ── */
  await loadProductData();

  /* ── Page Loader ── */
  const loader = document.querySelector('.page-loader');
  if (loader) {
    window.addEventListener('load', () => { setTimeout(() => { loader.classList.add('hidden'); document.body.style.overflow = ''; }, 600); });
    setTimeout(() => { loader.classList.add('hidden'); document.body.style.overflow = ''; }, 3000);
  }
  document.body.style.overflow = 'hidden';

  /* ── Navbar Scroll ── */
  const navbar = document.querySelector('.navbar-alamah');
  const handleNavScroll = () => { navbar && (window.scrollY > 60 ? navbar.classList.add('scrolled') : navbar.classList.remove('scrolled')); };
  window.addEventListener('scroll', handleNavScroll, { passive: true });
  handleNavScroll();

  /* ── Hamburger & Offcanvas ── */
  const toggler = document.querySelector('.navbar-toggler');
  const hamburger = document.querySelector('.hamburger-icon');
  const offcanvasEl = document.getElementById('navbarOffcanvas');
  if (toggler && hamburger) {
    toggler.addEventListener('click', () => hamburger.classList.toggle('active'));
  }
  if (offcanvasEl && hamburger) {
    offcanvasEl.addEventListener('hidden.bs.offcanvas', () => {
      hamburger.classList.remove('active');
    });
  }

  /* ── Hero Slider (only on index) ── */
  initHeroSlider();

  /* ── Scroll Reveal ── */
  const revealElements = document.querySelectorAll('.reveal');
  const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => { if (entry.isIntersecting) { entry.target.classList.add('revealed'); revealObserver.unobserve(entry.target); } });
  }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
  revealElements.forEach(el => revealObserver.observe(el));

  /* ── Back to Top ── */
  const backToTop = document.querySelector('.back-to-top');
  if (backToTop) {
    window.addEventListener('scroll', () => { backToTop.classList.toggle('visible', window.scrollY > 500); }, { passive: true });
    backToTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
  }

  /* ── Smooth Anchor Scrolling ── */
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const id = this.getAttribute('href');
      if (id === '#') return;
      const target = document.querySelector(id);
      if (target) {
        e.preventDefault();
        const navH = navbar ? navbar.offsetHeight : 0;
        window.scrollTo({ top: target.getBoundingClientRect().top + window.scrollY - navH - 20, behavior: 'smooth' });
      }
    });
  });

  /* ── Counter Animation ── */
  const statNumbers = document.querySelectorAll('.craft-stat-number');
  const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => { if (entry.isIntersecting) { animateCounter(entry.target); counterObserver.unobserve(entry.target); } });
  }, { threshold: 0.5 });
  statNumbers.forEach(el => counterObserver.observe(el));

  /* ── Initialize Products ── */
  renderBestsellers();
  renderProductsPage();
  initCategoryTabs();
  renderProductDetail();
  renderWishlistPage();

  /* ── Initialize Cart UI ── */
  updateCartUI();
  initCartEvents();
  initCheckoutEvents();
  initCustomFieldEvents();

  /* ── Wishlist & Account ── */
  updateWishlistBadge();
  loadWishlistFromAPI();
  loadCartFromAPI();
});


/* ═══════════════════════════════════════════════
   HERO SLIDER
   ═══════════════════════════════════════════════ */
function initHeroSlider() {
  const slides = document.querySelectorAll('.hero-slide');
  const dots = document.querySelectorAll('.hero-dot');
  const progressBar = document.querySelector('.hero-progress-bar');
  if (slides.length === 0) return;
  let current = 0, interval, pStart = null, pRaf = null;
  const DUR = 5000;

  function go(i) {
    slides.forEach(s => s.classList.remove('active'));
    dots.forEach(d => d.classList.remove('active'));
    slides[i].classList.add('active');
    dots[i].classList.add('active');
    current = i;
    pStart = performance.now();
    if (pRaf) cancelAnimationFrame(pRaf);
    animP();
  }
  function animP() {
    const p = Math.min((performance.now() - pStart) / DUR, 1);
    if (progressBar) progressBar.style.width = `${p * 100}%`;
    if (p < 1) pRaf = requestAnimationFrame(animP);
  }
  function start() { clearInterval(interval); interval = setInterval(() => go((current + 1) % slides.length), DUR); pStart = performance.now(); if (pRaf) cancelAnimationFrame(pRaf); animP(); }
  dots.forEach((d, i) => d.addEventListener('click', () => { go(i); start(); }));
  start();

  const hero = document.querySelector('.hero-section');
  if (hero) {
    hero.addEventListener('mouseenter', () => { clearInterval(interval); if (pRaf) cancelAnimationFrame(pRaf); });
    hero.addEventListener('mouseleave', start);
  }
}


/* ═══════════════════════════════════════════════
   PRODUCT CARD HTML BUILDER
   ═══════════════════════════════════════════════ */
function buildProductCardHTML(p, extraClass = '') {
  const badgeHTML = p.badge ? `<span class="product-badge"${p.badgeColor ? ` style="background:${p.badgeColor};color:#fff;"` : ''}>${p.badge}</span>` : '';
  const isWished = getWishlist().includes(p.id);
  return `
    <div class="product-card h-100 ${extraClass}" data-category="${p.category}" data-product-id="${p.id}">
      <a href="product.php?id=${p.id}" class="product-card-image-link">
        <div class="product-card-image">
          <img src="${p.image}" alt="${p.name}">${badgeHTML}
          <button class="product-heart-btn ${isWished ? 'active' : ''}" data-product-id="${p.id}" onclick="event.preventDefault();event.stopPropagation();toggleWishlist(${p.id},this)" aria-label="المفضلة">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="${isWished ? 'currentColor' : 'none'}" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
          </button>
        </div>
      </a>
      <div class="product-card-body">
        <a href="product.php?id=${p.id}" class="product-card-title-link"><h3 class="product-card-title">${p.name}</h3></a>
        <div class="product-card-price">
          <span>يبدأ من:</span> ${p.price} ${SAR_SVG}
        </div>
        <div class="product-card-meta">
          <svg viewBox="0 0 24 24"><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.2 3.2.8-1.3-4.5-2.7V7z"/></svg>
          مدة التنفيذ: ${p.time}
        </div>
        <button class="btn-product-order" onclick="handleAddToCart(${p.id}, this)">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" style="margin-left:5px;"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>
          أضف للسلة
        </button>
      </div>
    </div>`;
}

/* ── Wishlist ── */
function getWishlist() {
  return JSON.parse(localStorage.getItem('alamah_wishlist') || '[]').map(Number);
}

function toggleWishlist(productId, btn) {
  productId = Number(productId);
  let list = getWishlist();
  const idx = list.indexOf(productId);
  if (idx > -1) { list.splice(idx, 1); } else { list.push(productId); }
  localStorage.setItem('alamah_wishlist', JSON.stringify(list));
  if (btn) {
    const isActive = list.includes(productId);
    btn.classList.toggle('active', isActive);
    const svg = btn.querySelector('svg');
    svg.setAttribute('fill', isActive ? 'currentColor' : 'none');
    btn.style.transform = 'scale(1.3)';
    setTimeout(() => btn.style.transform = '', 300);
  }
  // Update all heart buttons for same product across the page
  document.querySelectorAll(`.product-heart-btn[data-product-id="${productId}"]`).forEach(b => {
    if (b !== btn) {
      const isActive = list.includes(productId);
      b.classList.toggle('active', isActive);
      const svg = b.querySelector('svg');
      if (svg) svg.setAttribute('fill', isActive ? 'currentColor' : 'none');
    }
  });
  // Update wishlist badge
  updateWishlistBadge();
  // Sync to backend API
  fetch('api/wishlist.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'toggle', product_id: productId })
  }).catch(() => {});
}

function updateWishlistBadge() {
  const list = getWishlist();
  document.querySelectorAll('.wishlist-badge').forEach(b => {
    b.textContent = list.length;
    b.classList.toggle('show', list.length > 0);
  });
}

function refreshAllHeartButtons() {
  const list = getWishlist();
  document.querySelectorAll('.product-heart-btn').forEach(btn => {
    const pid = Number(btn.dataset.productId);
    if (!pid) return;
    const isActive = list.includes(pid);
    btn.classList.toggle('active', isActive);
    const svg = btn.querySelector('svg');
    if (svg) svg.setAttribute('fill', isActive ? 'currentColor' : 'none');
  });
}

// Load wishlist from API on login
function loadWishlistFromAPI() {
  fetch('api/wishlist.php')
    .then(r => r.json())
    .then(data => {
      if (data.ok && data.product_ids) {
        const ids = data.product_ids.map(Number);
        // Merge with localStorage (keep local items too)
        const local = getWishlist();
        const merged = [...new Set([...ids, ...local])];
        localStorage.setItem('alamah_wishlist', JSON.stringify(merged));
        updateWishlistBadge();
        refreshAllHeartButtons();
      }
    })
    .catch(() => {});
}


/* ── Wishlist Page ── */
function renderWishlistPage() {
  const grid = document.getElementById('wishlistGrid');
  const emptyState = document.getElementById('wishlistEmpty');
  if (!grid) return;

  const list = getWishlist();
  if (list.length === 0) {
    grid.innerHTML = '';
    if (emptyState) emptyState.style.display = 'block';
    return;
  }
  if (emptyState) emptyState.style.display = 'none';

  const wishProducts = PRODUCTS.filter(p => list.includes(p.id));
  if (wishProducts.length === 0) {
    grid.innerHTML = '';
    if (emptyState) emptyState.style.display = 'block';
    return;
  }

  grid.innerHTML = wishProducts.map(p => `
    <div class="col-6 col-md-4 col-lg-3 reveal">
      ${buildProductCardHTML(p)}
    </div>
  `).join('');

  // Observe reveals
  grid.querySelectorAll('.reveal').forEach(el => {
    const obs = new IntersectionObserver((entries) => {
      entries.forEach(entry => { if (entry.isIntersecting) { entry.target.classList.add('revealed'); obs.unobserve(entry.target); } });
    }, { threshold: 0.1 });
    obs.observe(el);
  });
}

/* ═══════════════════════════════════════════════
   BESTSELLERS (index page)
   ═══════════════════════════════════════════════ */
function renderBestsellers() {
  const container = document.getElementById('bestsellerSlider');
  if (!container) return;
  const best = PRODUCTS.slice(0, 4);
  container.innerHTML = best.map((p, i) => `
    <div class="col-6 col-md-4 col-lg-3 reveal reveal-delay-${i + 1}">
      ${buildProductCardHTML(p)}
    </div>
  `).join('');
  // Re-observe reveals
  document.querySelectorAll('#bestsellerSlider .reveal').forEach(el => {
    const obs = new IntersectionObserver((entries) => {
      entries.forEach(entry => { if (entry.isIntersecting) { entry.target.classList.add('revealed'); obs.unobserve(entry.target); } });
    }, { threshold: 0.1 });
    obs.observe(el);
  });
}


/* ═══════════════════════════════════════════════
   PRODUCTS PAGE
   ═══════════════════════════════════════════════ */
function renderProductsPage() {
  const grid = document.getElementById('productsGrid');
  if (!grid) return;
  const params = new URLSearchParams(window.location.search);
  const activeCat = params.get('cat') || 'all';
  grid.innerHTML = PRODUCTS.map(p => buildProductCardHTML(p, 'fade-in')).join('');
  // Apply filter
  filterProducts(activeCat);
}

function initCategoryTabs() {
  const tabsContainer = document.getElementById('tabsContainer');
  if (!tabsContainer) return;
  const params = new URLSearchParams(window.location.search);
  const activeCat = params.get('cat') || 'all';

  tabsContainer.innerHTML = CATEGORIES.map(c =>
    `<button class="tab-btn${c.key === activeCat ? ' active' : ''}" data-cat="${c.key}">${c.label}</button>`
  ).join('');

  tabsContainer.addEventListener('click', (e) => {
    if (!e.target.classList.contains('tab-btn')) return;
    tabsContainer.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    e.target.classList.add('active');
    filterProducts(e.target.dataset.cat);
    // Update URL without reload
    const url = new URL(window.location);
    if (e.target.dataset.cat === 'all') url.searchParams.delete('cat');
    else url.searchParams.set('cat', e.target.dataset.cat);
    history.replaceState(null, '', url);
  });
}

function filterProducts(cat) {
  const grid = document.getElementById('productsGrid');
  if (!grid) return;
  const cards = grid.querySelectorAll('.product-card');
  cards.forEach(card => {
    if (cat === 'all' || card.dataset.category === cat) {
      card.classList.remove('hidden-card');
      card.classList.add('fade-in');
    } else {
      card.classList.add('hidden-card');
      card.classList.remove('fade-in');
    }
  });
  // Update title
  const title = document.getElementById('productsPageTitle');
  if (title) {
    const catObj = CATEGORIES.find(c => c.key === cat);
    title.textContent = cat === 'all' ? 'جميع المنتجات' : catObj ? catObj.label : 'المنتجات';
  }
}


/* ═══════════════════════════════════════════════
   ADD TO CART HANDLER
   ═══════════════════════════════════════════════ */
function handleAddToCart(productId, btnEl) {
  const product = PRODUCTS.find(p => p.id === productId);
  if (!product) return;

  if (product.customFields && product.customFields.length > 0) {
    // Show custom field modal
    pendingProduct = { ...product };
    showCustomFieldModal(product);
  } else {
    addToCart(product, {});
    flashButton(btnEl);
  }
}

function addToCart(product, customData) {
  const existing = cart.find(item => item.id === product.id && JSON.stringify(item.customData) === JSON.stringify(customData));
  if (existing) {
    existing.qty += 1;
  } else {
    cart.push({ id: product.id, name: product.name, price: product.price, image: product.image, qty: 1, customData, customFields: product.customFields || [] });
  }
  saveCart();
  updateCartUI();
}

function removeFromCart(index) {
  cart.splice(index, 1);
  saveCart();
  updateCartUI();
}

function updateQty(index, delta) {
  cart[index].qty += delta;
  if (cart[index].qty <= 0) cart.splice(index, 1);
  saveCart();
  updateCartUI();
}

function saveCart() {
  localStorage.setItem('alamah_cart', JSON.stringify(cart));
  // Sync to backend for abandoned cart tracking
  const total = cart.reduce((s, i) => s + i.price * i.qty, 0);
  const items = cart.map(i => ({ id: i.id, name: i.name, price: i.price, image: i.image, qty: i.qty, customData: i.customData || {} }));
  fetch('api/cart_sync.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ items, total })
  }).catch(() => {}); // silently fail
}

function loadCartFromAPI() {
  fetch('api/cart_sync.php')
    .then(r => r.json())
    .then(data => {
      if (data.ok && data.items && data.items.length > 0) {
        // Merge DB cart with local cart
        const localCart = JSON.parse(localStorage.getItem('alamah_cart') || '[]');
        const dbItems = data.items;
        // If local cart is empty, use DB cart
        if (localCart.length === 0 && dbItems.length > 0) {
          // Enrich DB items with product data from PRODUCTS
          cart = dbItems.map(dbItem => {
            const product = PRODUCTS.find(p => p.id === dbItem.id);
            return {
              id: dbItem.id,
              name: dbItem.name || (product ? product.name : ''),
              price: dbItem.price || (product ? product.price : 0),
              image: dbItem.image || (product ? product.image : ''),
              qty: dbItem.qty || 1,
              customData: dbItem.customData || {},
              customFields: product ? (product.customFields || []) : []
            };
          });
          localStorage.setItem('alamah_cart', JSON.stringify(cart));
          updateCartUI();
        }
      }
    })
    .catch(() => {});
}

function flashButton(btn) {
  if (!btn) return;
  const orig = btn.textContent;
  btn.classList.add('added');
  btn.innerHTML = '✓ تمت الإضافة';
  setTimeout(() => {
    btn.classList.remove('added');
    btn.innerHTML = `<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" style="margin-left:5px;"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg> أضف للسلة`;
  }, 1200);
  // Bounce badge
  document.querySelectorAll('.cart-badge:not(.wishlist-badge)').forEach(b => { b.classList.add('bounce'); setTimeout(() => b.classList.remove('bounce'), 500); });
}


/* ═══════════════════════════════════════════════
   CART UI
   ═══════════════════════════════════════════════ */
function updateCartUI() {
  const totalItems = cart.reduce((s, i) => s + i.qty, 0);
  const totalPrice = cart.reduce((s, i) => s + i.price * i.qty, 0);

  // Badges
  document.querySelectorAll('.cart-badge:not(.wishlist-badge)').forEach(b => {
    b.textContent = totalItems;
    b.classList.toggle('show', totalItems > 0);
  });

  // Count label
  const countLabel = document.getElementById('cartCountLabel');
  if (countLabel) countLabel.textContent = totalItems > 0 ? `(${totalItems} منتج)` : '';

  // Cart body
  const body = document.getElementById('cartDrawerBody');
  const footer = document.getElementById('cartDrawerFooter');
  if (!body) return;

  if (cart.length === 0) {
    body.innerHTML = `<div class="cart-empty"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg><p>السلة فارغة</p></div>`;
    if (footer) footer.style.display = 'none';
    return;
  }

  body.innerHTML = cart.map((item, i) => {
    const customHTML = item.customData && Object.keys(item.customData).length > 0
      ? `<div class="cart-item-custom">` + Object.entries(item.customData).map(([k, v]) => `✍️ ${k}: ${v}`).join('<br>') + `</div>`
      : '';
    return `
      <div class="cart-item">
        <div class="cart-item-img"><img src="${item.image}" alt="${item.name}"></div>
        <div class="cart-item-info">
          <div class="cart-item-name">${item.name}</div>
          ${customHTML}
          <div class="cart-item-price">${item.price * item.qty} ${SAR_SVG}</div>
          <div class="cart-item-actions">
            <button class="qty-btn" onclick="updateQty(${i}, -1)">−</button>
            <span class="cart-item-qty">${item.qty}</span>
            <button class="qty-btn" onclick="updateQty(${i}, 1)">+</button>
            <button class="cart-item-remove" onclick="removeFromCart(${i})">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
            </button>
          </div>
        </div>
      </div>`;
  }).join('');

  if (footer) {
    footer.style.display = 'block';
    const totalEl = document.getElementById('cartTotalValue');
    if (totalEl) totalEl.innerHTML = `${totalPrice} ${SAR_SVG}`;
  }
}

function initCartEvents() {
  const overlay = document.getElementById('cartOverlay');
  const drawer = document.getElementById('cartDrawer');
  const closeBtn = document.getElementById('cartCloseBtn');

  function openCart() { overlay?.classList.add('open'); drawer?.classList.add('open'); document.body.style.overflow = 'hidden'; }
  function closeCart() { overlay?.classList.remove('open'); drawer?.classList.remove('open'); document.body.style.overflow = ''; }

  document.getElementById('cartBtnMobile')?.addEventListener('click', openCart);
  document.getElementById('cartBtnDesktop')?.addEventListener('click', openCart);
  closeBtn?.addEventListener('click', closeCart);
  overlay?.addEventListener('click', closeCart);

  // Checkout button
  document.getElementById('btnCheckoutWa')?.addEventListener('click', () => {
    if (cart.length === 0) return;
    closeCart();
    setTimeout(() => showCheckoutModal(), 300);
  });
}


/* ═══════════════════════════════════════════════
   CHECKOUT MODAL
   ═══════════════════════════════════════════════ */
function showCheckoutModal() {
  const modal = document.getElementById('checkoutModal');
  if (!modal) return;

  // Build order summary preview
  const preview = document.getElementById('orderSummaryPreview');
  const total = cart.reduce((s, i) => s + i.price * i.qty, 0);
  if (preview) {
    preview.innerHTML = `<strong>ملخص الطلب:</strong><br>` +
      cart.map((item, i) => {
        let line = `${i + 1}. ${item.name} × ${item.qty} — ${item.price * item.qty} ر.س`;
        if (item.customData && Object.keys(item.customData).length > 0) {
          line += '<br><small style="color:var(--alamah-gold);">' + Object.entries(item.customData).map(([k, v]) => `  ✍️ ${k}: ${v}`).join('<br>') + '</small>';
        }
        return line;
      }).join('<br>') +
      `<br><hr style="border-color:rgba(0,0,0,0.08);margin:0.5rem 0;"><strong>المجموع: ${total} ر.س</strong>`;
  }

  modal.classList.add('open');
}

function initCheckoutEvents() {
  const modal = document.getElementById('checkoutModal');
  const closeBtn = document.getElementById('checkoutModalClose');
  const confirmBtn = document.getElementById('btnConfirmWa');

  closeBtn?.addEventListener('click', () => modal?.classList.remove('open'));
  modal?.addEventListener('click', (e) => { if (e.target === modal) modal.classList.remove('open'); });

  confirmBtn?.addEventListener('click', () => {
    const name = document.getElementById('customerName')?.value.trim();
    const phone = document.getElementById('customerPhone')?.value.trim();
    if (!name || !phone) {
      alert('يرجى إدخال الاسم ورقم الجوال');
      return;
    }
    const waNum = confirmBtn.getAttribute('data-wa') || WA_NUMBER;
    sendWhatsAppOrder(name, phone, waNum);
    modal?.classList.remove('open');
  });
}

function sendWhatsAppOrder(name, phone, waNum) {
  const total = cart.reduce((s, i) => s + i.price * i.qty, 0);
  let msg = `🛍️ *طلب جديد من متجر علامة*\n`;
  msg += `━━━━━━━━━━━━━━━━━\n`;
  msg += `👤 *الاسم:* ${name}\n`;
  msg += `📱 *الجوال:* ${phone}\n`;
  msg += `━━━━━━━━━━━━━━━━━\n`;
  msg += `📦 *المنتجات:*\n`;

  cart.forEach((item, i) => {
    msg += `\n${i + 1}️⃣ *${item.name}* × ${item.qty}\n`;
    if (item.customData && Object.keys(item.customData).length > 0) {
      Object.entries(item.customData).forEach(([k, v]) => { msg += `   ✍️ ${k}: ${v}\n`; });
    }
    msg += `   💰 ${item.price * item.qty} ر.س\n`;
  });

  msg += `━━━━━━━━━━━━━━━━━\n`;
  msg += `💰 *المجموع الكلي: ${total} ر.س*\n`;
  msg += `━━━━━━━━━━━━━━━━━\n`;
  msg += `شكرًا لاختياركم علامة ✨`;

  const url = `https://wa.me/${waNum}?text=${encodeURIComponent(msg)}`;
  window.open(url, '_blank');

  // Clear cart after sending
  cart = [];
  saveCart();
  updateCartUI();
  // Reset form
  const nameEl = document.getElementById('customerName');
  const phoneEl = document.getElementById('customerPhone');
  if (nameEl) nameEl.value = '';
  if (phoneEl) phoneEl.value = '';
}


/* ═══════════════════════════════════════════════
   CUSTOM FIELD MODAL
   ═══════════════════════════════════════════════ */
function showCustomFieldModal(product) {
  const modal = document.getElementById('customFieldModal');
  const body = document.getElementById('customFieldBody');
  if (!modal || !body) return;

  body.innerHTML = `<p style="font-size:0.9rem;color:var(--alamah-gray);margin-bottom:1rem;display:flex;align-items:center;gap:0.4rem;"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg> ${product.name}</p>` +
    product.customFields.map((f, i) => `
      <label for="cf_${i}">${f.label} ${f.required ? '*' : ''}</label>
      ${f.type === 'textarea'
        ? `<textarea id="cf_${i}" rows="3" placeholder="أدخل ${f.label}" ${f.required ? 'required' : ''}></textarea>`
        : `<input type="text" id="cf_${i}" placeholder="أدخل ${f.label}" ${f.required ? 'required' : ''}>`}
    `).join('');

  modal.classList.add('open');
}

function initCustomFieldEvents() {
  const modal = document.getElementById('customFieldModal');
  const closeBtn = document.getElementById('customFieldModalClose');
  const confirmBtn = document.getElementById('btnCustomFieldConfirm');

  closeBtn?.addEventListener('click', () => { modal?.classList.remove('open'); pendingProduct = null; });
  modal?.addEventListener('click', (e) => { if (e.target === modal) { modal.classList.remove('open'); pendingProduct = null; } });

  confirmBtn?.addEventListener('click', () => {
    if (!pendingProduct) return;
    const customData = {};
    let valid = true;

    pendingProduct.customFields.forEach((f, i) => {
      const el = document.getElementById(`cf_${i}`);
      const val = el?.value.trim() || '';
      if (f.required && !val) { valid = false; el?.focus(); }
      if (val) customData[f.label] = val;
    });

    if (!valid) { alert('يرجى تعبئة الحقول المطلوبة'); return; }

    addToCart(pendingProduct, customData);
    modal?.classList.remove('open');

    // Flash all matching buttons
    document.querySelectorAll(`[data-product-id="${pendingProduct.id}"] .btn-product-order`).forEach(btn => flashButton(btn));
    pendingProduct = null;
  });
}


/* ═══════════════════════════════════════════════
   COUNTER ANIMATION
   ═══════════════════════════════════════════════ */
function animateCounter(el) {
  const text = el.textContent;
  const match = text.match(/(\d+)/);
  if (!match) return;
  const target = parseInt(match[1]);
  const duration = 1800;
  const start = performance.now();
  function update(now) {
    const elapsed = now - start;
    const progress = Math.min(elapsed / duration, 1);
    const eased = 1 - Math.pow(1 - progress, 3);
    el.innerHTML = text.replace(match[0], Math.round(target * eased));
    if (progress < 1) requestAnimationFrame(update);
  }
  requestAnimationFrame(update);
}


/* ═══════════════════════════════════════════════
   PRODUCT DETAIL PAGE
   ═══════════════════════════════════════════════ */
function renderProductDetail() {
  const container = document.getElementById('productDetailContent');
  if (!container) return;

  const params = new URLSearchParams(window.location.search);
  const productId = parseInt(params.get('id'));
  const product = PRODUCTS.find(p => p.id === productId);

  if (!product) {
    container.innerHTML = `
      <div class="col-12 text-center" style="padding:4rem 0;">
        <svg viewBox="0 0 24 24" width="60" height="60" fill="none" stroke="var(--alamah-gray)" stroke-width="1.5" style="opacity:0.4;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <h3 style="color:var(--alamah-navy);margin-top:1rem;">المنتج غير موجود</h3>
        <p style="color:var(--alamah-gray);">لم نتمكن من العثور على المنتج المطلوب</p>
        <a href="products.php" class="btn-product-order" style="display:inline-block;margin-top:1rem;text-decoration:none;">تصفح المنتجات</a>
      </div>`;
    return;
  }

  // Update page title and breadcrumb
  document.title = `${product.name} | علامة ALAMAH`;
  const breadcrumb = document.getElementById('breadcrumbProductName');
  if (breadcrumb) breadcrumb.textContent = product.name;

  // Custom fields HTML
  const customFieldsHTML = product.customFields && product.customFields.length > 0
    ? product.customFields.map((f, i) => `
        <div class="pd-custom-field">
          <label for="pd_cf_${i}">${f.label} ${f.required ? '<span style="color:var(--alamah-red);">*</span>' : ''}</label>
          ${f.type === 'textarea'
            ? `<textarea id="pd_cf_${i}" rows="3" placeholder="أدخل ${f.label}" ${f.required ? 'required' : ''}></textarea>`
            : `<input type="text" id="pd_cf_${i}" placeholder="أدخل ${f.label}" ${f.required ? 'required' : ''}>`}
        </div>`).join('')
    : '';

  const badgeHTML = product.badge ? `<span class="pd-badge" ${product.badgeColor ? `style="background:${product.badgeColor};"` : ''}>${product.badge}</span>` : '';

  container.innerHTML = `
    <!-- Product Image -->
    <div class="col-lg-6">
      <div class="pd-image-wrapper">
        <img src="${product.image}" alt="${product.name}" class="pd-main-image">
        ${badgeHTML}
      </div>
    </div>

    <!-- Product Info -->
    <div class="col-lg-6">
      <div class="pd-info">
        <span class="pd-category-tag">${product.catLabel}</span>
        <h1 class="pd-title">${product.name}</h1>

        <div class="pd-price-box">
        <span class="pd-price-note">يبدأ من</span>
          <span class="pd-price">${product.price} ${SAR_SVG}</span>
        </div>

        ${product.description ? `<div class="pd-description" style="margin:1rem 0;padding:1rem;background:#FAFAFA;border-radius:10px;font-size:0.92rem;line-height:1.8;color:var(--alamah-navy);">
          <h4 style="font-size:0.9rem;font-weight:700;margin-bottom:0.5rem;color:var(--alamah-navy);display:flex;align-items:center;gap:0.4rem;">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="var(--alamah-gold)" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            وصف المنتج
          </h4>
          <p style="margin:0;white-space:pre-line;">${product.description}</p>
        </div>` : ''}

        <div class="pd-meta-row">
          <div class="pd-meta-item">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <span>مدة التنفيذ: <strong>${product.time}</strong></span>
          </div>
          <div class="pd-meta-item">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
            <span>الفئة: <strong>${product.catLabel}</strong></span>
          </div>
        </div>

        ${customFieldsHTML ? `<div class="pd-custom-fields">${customFieldsHTML}</div>` : ''}

        <div class="pd-qty-row">
          <label>الكمية:</label>
          <div class="pd-qty-control">
            <button type="button" class="pd-qty-btn" id="pdQtyMinus">−</button>
            <span class="pd-qty-value" id="pdQtyValue">1</span>
            <button type="button" class="pd-qty-btn" id="pdQtyPlus">+</button>
          </div>
        </div>

        <div class="pd-actions">
          <button class="pd-add-to-cart" id="pdAddToCart">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>
            أضف للسلة
          </button>
          <a href="https://wa.me/${WA_NUMBER}?text=${encodeURIComponent('مرحبًا، أرغب بالاستفسار عن: ' + product.name)}" target="_blank" class="pd-whatsapp-btn">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.263.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 2C6.477 2 2 6.477 2 12c0 1.89.525 3.66 1.438 5.168L2 22l4.832-1.438A9.955 9.955 0 0 0 12 22c5.523 0 10-4.477 10-10S17.523 2 12 2zm0 18a8 8 0 0 1-4.243-1.214l-.252-.151-2.734.734.734-2.734-.164-.265A7.96 7.96 0 0 1 4 12a8 8 0 1 1 16 0 8 8 0 0 1-8 8z"/></svg>
            استفسر عبر واتساب
          </a>
        </div>

        <div class="pd-features">
          <div class="pd-feature"><svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="var(--alamah-gold)" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg> جودة مضمونة</div>
          <div class="pd-feature"><svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="var(--alamah-gold)" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg> شحن لجميع المناطق</div>
          <div class="pd-feature"><svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="var(--alamah-gold)" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg> تخصيص كامل حسب الطلب</div>
        </div>
      </div>
    </div>`;

  // Qty controls
  let qty = 1;
  const qtyVal = document.getElementById('pdQtyValue');
  document.getElementById('pdQtyMinus')?.addEventListener('click', () => { if (qty > 1) { qty--; qtyVal.textContent = qty; } });
  document.getElementById('pdQtyPlus')?.addEventListener('click', () => { qty++; qtyVal.textContent = qty; });

  // Add to cart
  document.getElementById('pdAddToCart')?.addEventListener('click', function() {
    const customData = {};
    let valid = true;
    if (product.customFields && product.customFields.length > 0) {
      product.customFields.forEach((f, i) => {
        const el = document.getElementById(`pd_cf_${i}`);
        const val = el?.value.trim() || '';
        if (f.required && !val) { valid = false; el?.focus(); }
        if (val) customData[f.label] = val;
      });
      if (!valid) { alert('يرجى تعبئة الحقول المطلوبة'); return; }
    }
    // Add qty times
    for (let i = 0; i < qty; i++) addToCart(product, customData);
    flashButton(this);
  });

  // Related products
  renderRelatedProducts(product);
}

function renderRelatedProducts(currentProduct) {
  const grid = document.getElementById('relatedProductsGrid');
  if (!grid) return;
  const related = PRODUCTS.filter(p => p.category === currentProduct.category && p.id !== currentProduct.id).slice(0, 4);
  const fallback = related.length > 0 ? related : PRODUCTS.filter(p => p.id !== currentProduct.id).slice(0, 4);
  grid.innerHTML = fallback.map(p => `<div class="col-6 col-md-4 col-lg-3">${buildProductCardHTML(p)}</div>`).join('');
}


/* ═══════════════════════════════════════════════
   CONTACT FORM HANDLER
   ═══════════════════════════════════════════════ */
function handleContactForm(e) {
  e.preventDefault();
  const name = document.getElementById('cfName')?.value.trim();
  const phone = document.getElementById('cfPhone')?.value.trim();
  const email = document.getElementById('cfEmail')?.value.trim();
  const subject = document.getElementById('cfSubject')?.value;
  const message = document.getElementById('cfMessage')?.value.trim();

  if (!name || !phone || !subject || !message) {
    alert('يرجى تعبئة جميع الحقول المطلوبة');
    return false;
  }

  let msg = `📩 *رسالة جديدة من موقع علامة*\n`;
  msg += `━━━━━━━━━━━━━━━━━\n`;
  msg += `👤 *الاسم:* ${name}\n`;
  msg += `📱 *الجوال:* ${phone}\n`;
  if (email) msg += `📧 *البريد:* ${email}\n`;
  msg += `📌 *الموضوع:* ${subject}\n`;
  msg += `━━━━━━━━━━━━━━━━━\n`;
  msg += `💬 *الرسالة:*\n${message}\n`;
  msg += `━━━━━━━━━━━━━━━━━`;

  const url = `https://wa.me/${WA_NUMBER}?text=${encodeURIComponent(msg)}`;
  window.open(url, '_blank');

  // Visual feedback
  const btn = document.querySelector('.contact-submit-btn');
  if (btn) {
    btn.classList.add('sent');
    btn.innerHTML = '✓ تم الإرسال بنجاح';
    setTimeout(() => {
      btn.classList.remove('sent');
      btn.innerHTML = `<svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg> إرسال الرسالة`;
    }, 2500);
  }

  document.getElementById('contactForm')?.reset();
  return false;
}
