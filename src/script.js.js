const PRODUCTS = [
  { id:1,  name:'Bút bi Thiên Long TL-027', cat:'but',       catLabel:'Bút',        emoji:'🖊️', price:5000,  stock:150, brand:'Thiên Long', origin:'Việt Nam', desc:'Bút bi cao cấp với mực chảy đều, ngòi 0.7mm, viết mượt mà trên mọi loại giấy. Thích hợp cho học sinh và văn phòng.', unit:'Cái' },
  { id:2,  name:'Bút mực Parker Vector',    cat:'but',       catLabel:'Bút',        emoji:'🖋️', price:285000,stock:30,  brand:'Parker',     origin:'Anh',      desc:'Bút máy cao cấp với thân nhôm bóng, ngòi thép không gỉ, viết thanh mảnh và chuyên nghiệp.', unit:'Cái' },
  { id:3,  name:'Bút chì 2B Staedtler',     cat:'but',       catLabel:'Bút',        emoji:'✏️', price:12000, stock:200, brand:'Staedtler',  origin:'Đức',      desc:'Bút chì chuyên nghiệp với độ cứng 2B, phù hợp cho vẽ phác thảo và viết tay nhẹ nhàng.', unit:'Cái' },
  { id:4,  name:'Giấy A4 Double A 80gsm',   cat:'giay',      catLabel:'Giấy',       emoji:'📄', price:95000, stock:500, brand:'Double A',   origin:'Thái Lan', desc:'Giấy in A4 cao cấp 80gsm, trắng sáng, phù hợp với máy in laser và inkjet, 500 tờ/ream.', unit:'Ream' },
  { id:5,  name:'Giấy note tự dính Post-it', cat:'giay',     catLabel:'Giấy',       emoji:'📝', price:45000, stock:80,  brand:'3M',         origin:'Mỹ',       desc:'Giấy ghi chú tự dính đa màu sắc, kích thước 76x76mm, 100 tờ/gói. Dán và bóc dễ dàng không để lại vết.', unit:'Gói' },
  { id:6,  name:'Sổ tay Leuchtturm1917 A5',  cat:'so',       catLabel:'Sổ tay',     emoji:'📓', price:320000,stock:15,  brand:'Leuchtturm',  origin:'Đức',     desc:'Sổ tay chấm bi (dotted) A5 cao cấp với giấy dày 80gsm, bìa cứng, gáy dán keo chắc chắn, 249 trang.', unit:'Cuốn' },
  { id:7,  name:'Sổ vòng Oxford Campus',     cat:'so',       catLabel:'Sổ tay',     emoji:'📒', price:75000, stock:60,  brand:'Oxford',      origin:'Pháp',    desc:'Sổ vòng Oxford Campus B5 với 160 trang kẻ ngang, bìa cứng nhiều màu sắc trẻ trung. Phù hợp học sinh.', unit:'Cuốn' },
  { id:8,  name:'Bộ compa Maped Comfort',    cat:'van-phong', catLabel:'Văn phòng',  emoji:'📐', price:125000,stock:25,  brand:'Maped',      origin:'Pháp',     desc:'Bộ compa cao cấp 8 chi tiết bằng kim loại, đầu kim cứng, vẽ đường tròn chính xác đến 0.5mm.', unit:'Bộ' },
  { id:9,  name:'Thước kẻ Thiên Long 30cm',  cat:'van-phong', catLabel:'Văn phòng', emoji:'📏', price:8000,  stock:300, brand:'Thiên Long', origin:'Việt Nam', desc:'Thước nhựa trong suốt 30cm, chia vạch rõ ràng, cạnh thước thẳng và chắc chắn.', unit:'Cái' },
  { id:10, name:'Kéo văn phòng 3M Scotch',   cat:'van-phong', catLabel:'Văn phòng', emoji:'✂️', price:55000, stock:45,  brand:'3M',         origin:'Mỹ',       desc:'Kéo văn phòng cán nhựa ergonomic, lưỡi thép không gỉ, cắt gọn các loại giấy và vải.', unit:'Cái' },
  { id:11, name:'Mực in Brother TN-2385',    cat:'muc',       catLabel:'Mực',        emoji:'🖨️', price:450000,stock:20,  brand:'Brother',    origin:'Nhật Bản', desc:'Hộp mực toner chính hãng Brother dùng cho máy in laser HL-L2321D và các dòng tương thích, 1.200 trang.', unit:'Hộp' },
  { id:12, name:'Mực in HP 678 màu đen',     cat:'muc',       catLabel:'Mực',        emoji:'🖫',  price:185000,stock:35, brand:'HP',         origin:'Mỹ',       desc:'Hộp mực HP 678 Black dùng cho máy in phun HP Deskjet 2515/3515, in đậm sắc nét.', unit:'Hộp' },
  { id:13, name:'Bộ bút màu Faber-Castell 48', cat:'but',    catLabel:'Bút',        emoji:'🎨', price:195000,stock:22,  brand:'Faber-Castell', origin:'Đức', desc:'Hộp 48 màu bút chì màu Faber-Castell Grip, lõi màu đậm, không gãy, vỏ hình lục giác chống lăn.', unit:'Hộp' },
  { id:14, name:'Giấy bìa màu Origami A4',   cat:'giay',      catLabel:'Giấy',       emoji:'🎴', price:28000, stock:120, brand:'Origami',    origin:'Việt Nam', desc:'Giấy bìa màu nhiều màu sắc A4, 180gsm, gói 50 tờ 10 màu khác nhau, phù hợp thủ công và in ấn.', unit:'Gói' },
  { id:15, name:'Ghim bấm Kangaro HD-10',    cat:'van-phong', catLabel:'Văn phòng', emoji:'📌', price:35000, stock:80,  brand:'Kangaro',    origin:'Ấn Độ',    desc:'Máy ghim Kangaro HD-10 ghim được tối đa 20 tờ A4 80gsm, thiết kế nhỏ gọn phù hợp trên bàn làm việc.', unit:'Cái' },
  { id:16, name:'Sổ họp bìa da A4',          cat:'so',        catLabel:'Sổ tay',     emoji:'📋', price:185000,stock:18,  brand:'Klong',      origin:'Thái Lan', desc:'Sổ họp bìa da PU sang trọng khổ A4, có kẹp giấy và bút, 80 tờ giấy trắng kẻ ngang 70gsm.', unit:'Cuốn' },
  { id:17, name:'Băng keo trong 3M Scotch',  cat:'van-phong', catLabel:'Văn phòng', emoji:'🔖', price:22000, stock:200, brand:'3M',         origin:'Mỹ',       desc:'Băng keo trong suốt 3M Scotch 18mm x 25m, dán chắc, không vàng, dễ xé tay không cần dụng cụ.', unit:'Cuộn' },
  { id:18, name:'Mực in Canon PG-745',       cat:'muc',       catLabel:'Mực',        emoji:'🖨️', price:165000,stock:40,  brand:'Canon',      origin:'Nhật Bản', desc:'Hộp mực Canon PG-745 Black dùng cho máy in Canon PIXMA MG2570S/MG3070S, in sắc nét 180 trang.', unit:'Hộp' },
];

let cart = [];
let orders = [];
let currentProduct = null;
let modalQty = 1;
let searchQuery = '';
let activeFilter = 'all';

function fmt(n) { return n.toLocaleString('vi-VN') + '₫'; }

function renderProducts() {
  let list = PRODUCTS;
  if (activeFilter !== 'all') list = list.filter(p => p.cat === activeFilter);
  if (searchQuery) {
    const q = searchQuery.toLowerCase();
    list = list.filter(p => p.name.toLowerCase().includes(q) || p.catLabel.toLowerCase().includes(q) || p.brand.toLowerCase().includes(q));
  }
  const grid = document.getElementById('productGrid');
  document.getElementById('productCount').textContent = list.length + ' sản phẩm';
  if (list.length === 0) {
    grid.innerHTML = '<div class="empty-state" style="grid-column:1/-1"><div class="emoji">🔍</div><h3>Không tìm thấy sản phẩm</h3><p>Thử từ khóa khác hoặc xóa bộ lọc</p></div>';
    return;
  }
  grid.innerHTML = list.map(p => `
    <div class="product-card" onclick="openProduct(${p.id})">
      <div class="product-img" style="background:${getBg(p.cat)}">
        ${p.emoji}
        <span class="product-stock-badge ${p.stock > 50 ? 'in-stock' : 'low-stock'}">${p.stock > 50 ? 'Còn hàng' : 'Sắp hết'}</span>
      </div>
      <div class="product-info">
        <div class="product-category">${p.catLabel}</div>
        <div class="product-name">${p.name}</div>
        <div class="product-price-row">
          <span class="product-price">${fmt(p.price)}</span>
          <button class="add-btn" onclick="event.stopPropagation(); quickAdd(${p.id})" title="Thêm vào giỏ">+</button>
        </div>
      </div>
    </div>
  `).join('');
}

function getBg(cat) {
  const map = { but:'#F5EFE6', giay:'#EBF4F0', so:'#F0EBF5', 'van-phong':'#EBF0F5', muc:'#F5EBF0' };
  return map[cat] || '#F5F5F0';
}

function filterProducts(cat, el) {
  activeFilter = cat;
  document.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
  el.classList.add('active');
  const labels = { all:'Tất cả sản phẩm', but:'Bút', giay:'Giấy', so:'Sổ tay', 'van-phong':'Văn phòng', muc:'Mực in' };
  document.getElementById('sectionTitle').textContent = labels[cat];
  renderProducts();
}

document.getElementById('searchInput').addEventListener('input', function() {
  searchQuery = this.value.trim();
  renderProducts();
  if (searchQuery) {
    document.getElementById('sectionTitle').textContent = 'Kết quả tìm kiếm: "' + searchQuery + '"';
  } else {
    const labels = { all:'Tất cả sản phẩm', but:'Bút', giay:'Giấy', so:'Sổ tay', 'van-phong':'Văn phòng', muc:'Mực in' };
    document.getElementById('sectionTitle').textContent = labels[activeFilter];
  }
});

function openProduct(id) {
  currentProduct = PRODUCTS.find(p => p.id === id);
  modalQty = 1;
  document.getElementById('modalImg').textContent = currentProduct.emoji;
  document.getElementById('modalCat').textContent = currentProduct.catLabel;
  document.getElementById('modalName').textContent = currentProduct.name;
  document.getElementById('modalPrice').textContent = fmt(currentProduct.price);
  document.getElementById('modalDesc').textContent = currentProduct.desc;
  document.getElementById('modalQty').textContent = 1;
  document.getElementById('modalMeta').innerHTML = `
    <div class="meta-item"><div class="meta-label">Thương hiệu</div><div class="meta-value">${currentProduct.brand}</div></div>
    <div class="meta-item"><div class="meta-label">Xuất xứ</div><div class="meta-value">${currentProduct.origin}</div></div>
    <div class="meta-item"><div class="meta-label">Tồn kho</div><div class="meta-value">${currentProduct.stock} ${currentProduct.unit}</div></div>
    <div class="meta-item"><div class="meta-label">Đơn vị</div><div class="meta-value">${currentProduct.unit}</div></div>
  `;
  document.getElementById('productOverlay').classList.add('open');
}

function closeProductModal(e) {
  if (!e || e.target === document.getElementById('productOverlay')) {
    document.getElementById('productOverlay').classList.remove('open');
  }
}

function changeModalQty(d) {
  modalQty = Math.max(1, modalQty + d);
  document.getElementById('modalQty').textContent = modalQty;
}

function addToCartFromModal() {
  if (!currentProduct) return;
  addToCart(currentProduct, modalQty);
  document.getElementById('productOverlay').classList.remove('open');
}

function quickAdd(id) {
  const p = PRODUCTS.find(x => x.id === id);
  addToCart(p, 1);
}

function addToCart(product, qty) {
  const existing = cart.find(i => i.id === product.id);
  if (existing) {
    existing.qty += qty;
  } else {
    cart.push({ ...product, qty });
  }
  updateCartBadge();
  renderCart();
  showToast('✅', `Đã thêm "${product.name}" vào giỏ hàng`);
}

function removeFromCart(id) {
  cart = cart.filter(i => i.id !== id);
  updateCartBadge();
  renderCart();
}

function changeCartQty(id, d) {
  const item = cart.find(i => i.id === id);
  if (!item) return;
  item.qty = Math.max(1, item.qty + d);
  updateCartBadge();
  renderCart();
}

function renderCart() {
  const body = document.getElementById('cartBody');
  const total = cart.reduce((s, i) => s + i.price * i.qty, 0);
  document.getElementById('cartTotal').textContent = fmt(total);
  document.getElementById('checkoutBtn').disabled = cart.length === 0;
  if (cart.length === 0) {
    body.innerHTML = '<div class="empty-state"><div class="emoji">🛒</div><h3>Giỏ hàng trống</h3><p>Thêm sản phẩm vào giỏ để tiếp tục</p></div>';
    return;
  }
  body.innerHTML = cart.map(i => `
    <div class="cart-item">
      <div class="cart-item-img">${i.emoji}</div>
      <div class="cart-item-info">
        <div class="cart-item-name">${i.name}</div>
        <div class="cart-item-price">${fmt(i.price * i.qty)}</div>
      </div>
      <div class="cart-qty">
        <button onclick="changeCartQty(${i.id},-1)">−</button>
        <span>${i.qty}</span>
        <button onclick="changeCartQty(${i.id},1)">+</button>
      </div>
      <button class="remove-btn" onclick="removeFromCart(${i.id})" title="Xóa">✕</button>
    </div>
  `).join('');
}

function updateCartBadge() {
  const total = cart.reduce((s, i) => s + i.qty, 0);
  const badge = document.getElementById('cartBadge');
  badge.style.display = total ? 'flex' : 'none';
  badge.textContent = total;
}

function openCart() {
  document.getElementById('cartPanel').classList.add('open');
  document.getElementById('cartBackdrop').classList.add('open');
}

function closeCart() {
  document.getElementById('cartPanel').classList.remove('open');
  document.getElementById('cartBackdrop').classList.remove('open');
}

function checkout() {
  if (cart.length === 0) return;
  const total = cart.reduce((s, i) => s + i.price * i.qty, 0);
  const now = new Date();
  const order = {
    id: 'DH' + String(orders.length + 1).padStart(4, '0'),
    date: now.toLocaleDateString('vi-VN', { day:'2-digit', month:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit' }),
    items: cart.map(i => ({ name: i.name, qty: i.qty, price: i.price, emoji: i.emoji })),
    total,
    status: 'pending'
  };
  orders.unshift(order);
  cart = [];
  updateCartBadge();
  renderCart();
  closeCart();
  renderOrders();
  showSuccess(`Đặt hàng ${order.id} thành công! Tổng: ${fmt(total)}`);
}

function renderOrders() {
  document.getElementById('orderCount').textContent = orders.length + ' đơn hàng';
  const list = document.getElementById('ordersList');
  if (orders.length === 0) {
    list.innerHTML = '<div class="empty-state"><div class="emoji">📋</div><h3>Chưa có đơn hàng nào</h3><p>Hãy thêm sản phẩm vào giỏ và đặt hàng</p></div>';
    return;
  }
  const statusMap = { pending: ['status-pending', '⏳ Chờ xử lý'], processing: ['status-processing', '🔄 Đang xử lý'], delivered: ['status-delivered', '✅ Đã giao'] };
  list.innerHTML = orders.map(o => {
    const [cls, label] = statusMap[o.status];
    return `
      <div class="order-card">
        <div class="order-header">
          <div>
            <div class="order-id">#${o.id}</div>
            <div class="order-date">${o.date}</div>
          </div>
          <span class="order-status ${cls}">${label}</span>
        </div>
        <div class="order-items">
          ${o.items.map(i => `<span class="order-item-chip">${i.emoji} ${i.name} x${i.qty}</span>`).join('')}
        </div>
        <div class="order-footer">
          <span style="font-size:0.82rem;color:var(--ink-muted)">${o.items.reduce((s,i)=>s+i.qty,0)} sản phẩm</span>
          <span class="order-total">${fmt(o.total)}</span>
        </div>
      </div>
    `;
  }).join('');
}

function showPage(page) {
  document.querySelectorAll('.nav-tab').forEach((t,i) => t.classList.toggle('active', (i===0&&page==='products')||(i===1&&page==='orders')));
  document.getElementById('productsPage').classList.toggle('hidden', page !== 'products');
  document.getElementById('ordersPage').classList.toggle('active', page === 'orders');
  if (page === 'orders') renderOrders();
}

function showToast(icon, msg) {
  const t = document.getElementById('toast');
  document.getElementById('toastIcon').textContent = icon;
  document.getElementById('toastMsg').textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 2800);
}

function showSuccess(msg) {
  const b = document.getElementById('successBanner');
  document.getElementById('successMsg').textContent = msg;
  b.classList.add('show');
  setTimeout(() => b.classList.remove('show'), 4000);
}

// Khởi tạo ban đầu
renderProducts();
renderCart();