<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Văn Phòng Phẩm</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
  <div class="logo">Văn Phòng Phẩm</div>
  <div class="search-bar">
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
    <input type="text" id="searchInput" placeholder="Tìm kiếm sản phẩm..." autocomplete="off">
  </div>
  <div class="header-actions">
    <div class="nav-tabs">
      <button class="nav-tab active" onclick="showPage('products')">🏠 Sản phẩm</button>
      <button class="nav-tab" onclick="showPage('orders')">📋 Đơn hàng</button>
    </div>
    <button class="icon-btn" onclick="openCart()">
      🛒 Giỏ hàng
      <span class="badge" id="cartBadge" style="display:none">0</span>
    </button>
  </div>
</header>

<main>
  <div class="success-banner" id="successBanner">
    <span class="icon">✅</span>
    <p id="successMsg">Đặt hàng thành công! Đơn hàng của bạn đang được xử lý.</p>
  </div>

  <div class="products-page" id="productsPage">
    <div class="hero">
      <div class="hero-text">
        <div class="hero-badge">✨ Mới về</div>
        <h1>Văn phòng phẩm<br>chất lượng cao</h1>
        <p>Dụng cụ học tập và làm việc được tuyển chọn kỹ lưỡng, phù hợp với mọi nhu cầu của bạn.</p>
      </div>
    </div>

    <div class="filter-chips" id="filterChips">
      <button class="chip active" onclick="filterProducts('all', this)">Tất cả</button>
      <button class="chip" onclick="filterProducts('but', this)">🖊️ Bút</button>
      <button class="chip" onclick="filterProducts('giay', this)">📄 Giấy</button>
      <button class="chip" onclick="filterProducts('so', this)">📓 Sổ tay</button>
      <button class="chip" onclick="filterProducts('van-phong', this)">📐 Văn phòng</button>
      <button class="chip" onclick="filterProducts('muc', this)">🖨️ Mực</button>
    </div>

    <div class="section-header">
      <h2 class="section-title" id="sectionTitle">Tất cả sản phẩm</h2>
      <span class="section-sub" id="productCount">0 sản phẩm</span>
    </div>

    <div class="product-grid" id="productGrid"></div>
  </div>

  <div class="orders-page" id="ordersPage">
    <div class="section-header" style="margin-bottom:1.5rem">
      <h2 class="section-title">Danh sách đơn hàng</h2>
      <span class="section-sub" id="orderCount">0 đơn hàng</span>
    </div>
    <div id="ordersList"></div>
  </div>
</main>

<div class="overlay" id="productOverlay" onclick="closeProductModal(event)">
  <div class="product-modal" id="productModal">
    <button class="modal-close" onclick="closeProductModal()">✕</button>
    <div class="modal-product-img" id="modalImg"></div>
    <div class="modal-category" id="modalCat"></div>
    <h2 class="modal-name" id="modalName"></h2>
    <div class="modal-price" id="modalPrice"></div>
    <p class="modal-desc" id="modalDesc"></p>
    <div class="modal-meta" id="modalMeta"></div>
    <div class="qty-row">
      <div class="qty-ctrl">
        <button onclick="changeModalQty(-1)">−</button>
        <span id="modalQty">1</span>
        <button onclick="changeModalQty(1)">+</button>
      </div>
      <button class="btn-primary" onclick="addToCartFromModal()">🛒 Thêm vào giỏ hàng</button>
    </div>
  </div>
</div>

<div class="cart-backdrop" id="cartBackdrop" onclick="closeCart()"></div>
<div class="cart-panel" id="cartPanel">
  <div class="cart-header">
    <h2 class="cart-title">Giỏ hàng</h2>
    <button class="modal-close" onclick="closeCart()">✕</button>
  </div>
  <div class="cart-body" id="cartBody"></div>
  <div class="cart-footer">
    <div class="cart-total">
      <span class="cart-total-label">Tổng cộng</span>
      <span class="cart-total-value" id="cartTotal">0₫</span>
    </div>
    <button class="btn-checkout" id="checkoutBtn" onclick="checkout()">Đặt hàng ngay →</button>
  </div>
</div>

<div class="toast" id="toast">
  <span id="toastIcon">✅</span>
  <span id="toastMsg">Đã thêm vào giỏ hàng</span>
</div>

<script src="script.js"></script>
</body>
</html>