@extends('layouts.admin')

@section('title', 'POS')

@section('content')
    <div class="row g-4">

        <!-- Left: Products -->
        <div class="col-lg-8">

            <!-- Search -->
            <div class="card mb-3">
                <div class="card-body py-3">
                    <div class="row g-2">

                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" placeholder="Search product by name or SKU..."
                                    id="searchInput">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <select class="form-select" id="categoryFilter">
                                <option value="">All categories</option>
                            </select>
                        </div>

                        <!-- CUSTOMER DROPDOWN -->
                        <div class="col-md-4">
                            <select class="form-select" id="customerSelect">
                                <option value="">Select Customer</option>
                            </select>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Product Grid -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-grid me-2"></i>Products</span>
                    <span class="text-muted small">Click to add to cart</span>
                </div>
                <div class="card-body">
                    <div class="row g-3" id="productGrid">
                        <div class="col-12 text-center text-muted py-4">Loading products...</div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right: Cart -->
        <div class="col-lg-4">
            <div class="card cart-sticky">

                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                    <span><i class="bi bi-cart3 me-2"></i>Cart</span>
                    <span class="badge bg-white text-primary" id="cartBadge">0 items</span>
                </div>

                <div class="card-body p-0">

                    <!-- CUSTOMER DISPLAY -->
                    <div id="selectedCustomerBox" class="alert alert-info m-2 py-2 px-2" style="display:none;">
                        <strong>Customer:</strong>
                        <span id="selectedCustomerName"></span>
                        (<span id="selectedCustomerMobile"></span>)
                    </div>

                    <!-- Cart Items -->
                    <div class="p-3" style="max-height:320px; overflow-y:auto;" id="cartItemsContainer">
                        <div class="text-center text-muted py-4">Cart is empty</div>
                    </div>

                    <!-- Totals -->
                    <div class="border-top p-3 bg-light" id="totalsSection" style="display:none">

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal</span>
                            <span id="subtotalDisplay">$ 0.00</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2 text-danger" id="itemDiscountRow"
                            style="display:none">
                            <span>Item Discounts</span>
                            <span id="itemDiscountDisplay">- $ 0.00</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Invoice Discount</span>
                            <div class="d-flex align-items-center gap-1">
                                <select class="form-select form-select-sm" id="invoiceDiscountType" style="width:65px;">
                                    <option value="">None</option>
                                    <option value="fixed">$</option>
                                    <option value="percent">%</option>
                                </select>
                                <input type="number" class="form-control form-control-sm" id="invoiceDiscountValue"
                                    style="width:70px;" value="0" min="0">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mb-2 text-danger" id="invoiceDiscountRow"
                            style="display:none">
                            <span></span>
                            <span id="invoiceDiscountDisplay">- $ 0.00</span>
                        </div>

                        <hr class="my-2">

                        <div class="d-flex justify-content-between fs-5 fw-bold">
                            <span>Grand Total</span>
                            <span class="text-success" id="grandTotalDisplay">$ 0.00</span>
                        </div>

                    </div>

                    <!-- Invoice Info -->
                    <div class="border-top p-3">

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small text-muted mb-1">Invoice No</label>
                                <input type="text" class="form-control form-control-sm" id="invoiceNoInput" readonly>
                            </div>

                            <div class="col-6">
                                <label class="form-label small text-muted mb-1">Date</label>
                                <input type="date" class="form-control form-control-sm" id="invoiceDateInput">
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button class="btn btn-success btn-lg" id="finalizeBtn" disabled>
                                <i class="bi bi-check-circle me-2"></i>Finalize Invoice
                            </button>

                            <div class="row g-2">
                                <div class="col-6">
                                    <button class="btn btn-outline-primary w-100" id="saveDraftBtn" disabled>
                                        <i class="bi bi-save me-1"></i>Save Draft
                                    </button>
                                </div>

                                <div class="col-6">
                                    <button class="btn btn-outline-secondary w-100" id="clearCartBtn">
                                        <i class="bi bi-x-lg me-1"></i>Clear
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script>

            let productsUrl = '{{ url("/api/v1/products") }}';
            let categoriesUrl = '{{ url("/api/v1/categories") }}';
            let customersUrl = '{{ url("/api/v1/customers") }}';
            let invoicesUrl = '{{ url("/api/v1/invoices") }}';

            let allProducts = [];
            let allCategories = [];
            let cart = [];

            function getToken() { return localStorage.getItem('token') || ''; }
            function authHeaders() {
                const token = getToken();
                return token ? { headers: { Authorization: 'Bearer ' + token } } : {};
            }
            function formatMoney(a) {
                return '$ ' + (parseFloat(a || 0)).toFixed(2);
            }
            async function loadCategories() {

                try {

                    let r = await axios.get(categoriesUrl, authHeaders());

                    allCategories = r.data.data || [];

                    let s = document.getElementById('categoryFilter');

                    s.innerHTML = '<option value="">All categories</option>';

                    allCategories.forEach(c => {
                        s.innerHTML += '<option value="' + c.id + '">' + c.name + '</option>';
                    });

                } catch (e) {

                    console.error('Category load failed', e);

                }

            }
            async function loadProducts() {

                try {

                    let r = await axios.get(productsUrl, authHeaders());

                    allProducts = r.data.data || [];

                    renderProducts(allProducts);

                } catch (e) {

                    document.getElementById('productGrid').innerHTML =
                        '<div class="col-12 text-center text-danger py-4">Failed to load products</div>';

                }

            }

            function renderProducts(products) {

                let grid = document.getElementById('productGrid');

                if (!products.length) {
                    grid.innerHTML = '<div class="col-12 text-center text-muted py-4">No products found</div>';
                    return;
                }

                grid.innerHTML = '';

                products.forEach(p => {

                    let card = `
                                                                                                                                                                        <div class="col-md-4">
                                                                                                                                                                            <div class="card h-100 product-card" style="cursor:pointer"
                                                                                                                                                                                onclick="addToCart(${p.id})">

                                                                                                                                                                                <div class="card-body text-center">

                                                                                                                                                                                   <h6 class="fw-semibold mb-1">${p.product_name || ''}</h6>

                                                                                                                                                                                    <div class="text-muted small mb-2">
                                                                                                                                                                                        ${p.category ? p.category.name : ''}
                                                                                                                                                                                    </div>

                                                                                                                                                                                    <div class="fw-bold text-primary">
                                                                                                                                                                                       $ ${parseFloat(p.price || 0).toFixed(2)}
                                                                                                                                                                                    </div>

                                                                                                                                                                                    <div class="small text-muted mt-1">
                                                                                                                                                                                     Stock: ${p.stock_qty ?? p.stock ?? 0}
                                                                                                                                                                                    </div>

                                                                                                                                                                                </div>
                                                                                                                                                                            </div>
                                                                                                                                                                        </div>
                                                                                                                                                                        `;

                    grid.insertAdjacentHTML('beforeend', card);

                });

            }



            async function loadCustomers() {
                try {
                    let r = await axios.get(customersUrl, authHeaders());
                    let customers = r.data.data || [];

                    let s = document.getElementById('customerSelect');
                    s.innerHTML = '<option value="">Select Customer</option>';

                    customers.forEach(c => {
                        s.innerHTML += '<option value="' + c.id + '" data-mobile="' + (c.mobile || '') + '">' + c.name + '</option>';
                    });

                } catch (e) {
                    console.error('Customer load failed', e);
                }
            }

            const customerSelect = document.getElementById('customerSelect');
            if (customerSelect) {
                customerSelect.addEventListener('change', function () {

                    let name = this.options[this.selectedIndex].text;
                    let mobile = this.options[this.selectedIndex].getAttribute('data-mobile');

                    if (this.value) {
                        document.getElementById('selectedCustomerBox').style.display = 'block';
                        document.getElementById('selectedCustomerName').innerText = name;
                        document.getElementById('selectedCustomerMobile').innerText = mobile;
                    } else {
                        document.getElementById('selectedCustomerBox').style.display = 'none';
                    }

                });
            }

            function buildInvoicePayload(status) {

                let invoiceNo = document.getElementById('invoiceNoInput').value;
                let customerId = document.getElementById('customerSelect').value || null;
                let invoiceDate = document.getElementById('invoiceDateInput').value;

                let discountType = document.getElementById('invoiceDiscountType').value || null;
                let discountValue = parseFloat(document.getElementById('invoiceDiscountValue').value) || 0;

                let subtotal = 0;
                let discountAmount = 0;
                let items = [];

                cart.forEach(item => {

                    let quantity = parseFloat(item.quantity) || 0;
                    let unitPrice = parseFloat(item.unit_price) || 0;
                    let lineTotal = parseFloat(item.line_total) || (quantity * unitPrice);

                    subtotal += quantity * unitPrice;

                    items.push({
                        product_id: item.product_id,
                        quantity: quantity,
                        unit_price: unitPrice,
                        discount_type: item.discount_type || null,
                        discount_value: parseFloat(item.discount_value) || 0,
                        discount_amount: parseFloat(item.discount_amount) || 0,
                        line_total: lineTotal
                    });

                    discountAmount += parseFloat(item.discount_amount) || 0;
                });

                let invoiceDiscountAmount = 0;

                if (discountType === 'percent') {
                    invoiceDiscountAmount = subtotal * (discountValue / 100);
                } else if (discountType === 'fixed') {
                    invoiceDiscountAmount = discountValue;
                }

                let grandTotal = subtotal - discountAmount - invoiceDiscountAmount;

                return {
                    invoice_no: invoiceNo,
                    customer_id: customerId,
                    invoice_date: invoiceDate,
                    items: items,
                    subtotal: subtotal,
                    discount_type: discountType,
                    discount_value: discountValue,
                    discount_amount: invoiceDiscountAmount,
                    grand_total: grandTotal,
                    status: status
                };
            }
            function addToCart(productId) {

                let p = allProducts.find(x => x.id == productId);
                if (!p) return;

                let existing = cart.find(x => x.product_id == productId);

                if (existing) {
                    existing.quantity++;
                    existing.line_total = existing.quantity * existing.unit_price;
                } else {

                    cart.push({
                        product_id: p.id,
                        product_name: p.product_name,
                        quantity: 1,
                        unit_price: parseFloat(p.price),
                        discount_type: null,
                        discount_value: 0,
                        discount_amount: 0,
                        line_total: parseFloat(p.price)
                    });

                }

                renderCart();
            }

            function renderCart() {

                let container = document.getElementById('cartItemsContainer');

                if (!cart.length) {
                    container.innerHTML = '<div class="text-center text-muted py-4">Cart is empty</div>';
                    document.getElementById('totalsSection').style.display = 'none';
                    document.getElementById('cartBadge').innerText = '0 items';
                    return;
                }

                container.innerHTML = '';

                cart.forEach((item, index) => {

                    container.innerHTML += `
                                                                                                                                                                <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">

                                                                                                                                                                    <div>
                                                                                                                                                                        <div class="fw-semibold">${item.product_name}</div>
                                                                                                                                                                        <div class="small text-muted">
                                                                                                                                                                            ${item.quantity} × ${formatMoney(item.unit_price)}
                                                                                                                                                                        </div>
                                                                                                                                                                    </div>

                                                                                                                                                                    <div class="text-end">
                                                                                                                                                                        <div class="fw-bold">${formatMoney(item.line_total)}</div>
                                                                                                                                                                        <button class="btn btn-sm btn-outline-danger mt-1"
                                                                                                                                                                                onclick="removeCartItem(${index})">
                                                                                                                                                                                <i class="bi bi-x"></i>
                                                                                                                                                                        </button>
                                                                                                                                                                    </div>

                                                                                                                                                                </div>`;
                });

                document.getElementById('cartBadge').innerText = cart.reduce((t, i) => t + i.quantity, 0) + ' items';
                updateTotals();
            }

            function removeCartItem(index) {
                cart.splice(index, 1);
                renderCart();
            }

            function updateTotals() {

                let subtotal = 0;

                cart.forEach(i => {
                    subtotal += i.line_total;
                });

                let discountType = document.getElementById('invoiceDiscountType').value;
                let discountValue = parseFloat(document.getElementById('invoiceDiscountValue').value) || 0;

                let invoiceDiscountAmount = 0;

                if (discountType === 'percent') {
                    invoiceDiscountAmount = subtotal * (discountValue / 100);
                }

                if (discountType === 'fixed') {
                    invoiceDiscountAmount = discountValue;
                }

                let grandTotal = subtotal - invoiceDiscountAmount;

                document.getElementById('subtotalDisplay').innerText = formatMoney(subtotal);
                document.getElementById('grandTotalDisplay').innerText = formatMoney(grandTotal);

                document.getElementById('totalsSection').style.display = 'block';

                document.getElementById('finalizeBtn').disabled = false;
                document.getElementById('saveDraftBtn').disabled = false;
            }

            document.getElementById('invoiceDiscountType').addEventListener('change', updateTotals);
            document.getElementById('invoiceDiscountValue').addEventListener('input', updateTotals);

            document.getElementById('clearCartBtn').addEventListener('click', function () {
                cart = [];
                renderCart();
            });

            /* INIT INVOICE DATE */
            document.getElementById('invoiceDateInput').value = new Date().toISOString().split('T')[0];

            /* GENERATE INVOICE NUMBER */
            document.getElementById('invoiceNoInput').value = 'INV-' + Date.now();
            document.getElementById('finalizeBtn').addEventListener('click', async function () {

                try {

                    let payload = buildInvoicePayload('finalized');

                    let r = await axios.post(invoicesUrl, payload, authHeaders());

                    alert('Invoice created successfully');

                    cart = [];
                    renderCart();
                    updateTotals();
                    document.getElementById('invoiceNoInput').value = 'INV-' + Date.now();

                } catch (e) {

                    console.error('Invoice create failed', e);
                    alert('Failed to create invoice');

                }

            });

            document.getElementById('saveDraftBtn').addEventListener('click', async function () {

                try {

                    let payload = buildInvoicePayload('draft');

                    let r = await axios.post(invoicesUrl, payload, authHeaders());

                    alert('Draft saved successfully');

                    cart = [];
                    renderCart();
                    updateTotals();
                    document.getElementById('invoiceNoInput').value = 'INV-' + Date.now();
                    
                } catch (e) {

                    console.error('Draft save failed', e);
                    alert('Failed to save draft');

                }

            });

            /* SEARCH PRODUCTS */
            document.getElementById('searchInput').addEventListener('input', function () {

                let q = this.value.toLowerCase();

                let filtered = allProducts.filter(p => {
                    return (
                        (p.product_name && p.product_name.toLowerCase().includes(q)) ||
                        (p.sku && p.sku.toLowerCase().includes(q))
                    );
                });

                renderProducts(filtered);

            });
            /* CATEGORY FILTER */
            document.getElementById('categoryFilter').addEventListener('change', function () {

                let categoryId = this.value;

                if (!categoryId) {
                    renderProducts(allProducts);
                    return;
                }

                let filtered = allProducts.filter(p => p.category_id == categoryId);

                renderProducts(filtered);

            });
            loadCategories();
            loadCustomers();
            loadProducts();
        </script>
    @endpush

@endsection