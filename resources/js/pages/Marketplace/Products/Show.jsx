import { Head, Link, router, useForm } from '@inertiajs/react';
import { useMemo, useState, Fragment } from 'react';
import { Dialog, Transition } from '@headlessui/react';
import FrontendLayout from '@/Layouts/FrontendLayout';
import FlashBanner from '@/Components/FlashBanner';

const fallbackImage = '/images/landing/deal-imac.jpg';

function formatMoney(amount, currency = 'BDT') {
    const numericAmount = Number(amount ?? 0);
    try {
        return new Intl.NumberFormat('en-US', { style: 'currency', currency: currency || 'BDT', maximumFractionDigits: 2 }).format(numericAmount);
    } catch {
        return `${currency || 'BDT'} ${numericAmount.toFixed(2)}`;
    }
}

function resolveTierPrice(quantity, tiers, fallbackPrice) {
    const sortedTiers = Array.isArray(tiers) ? [...tiers].sort((a, b) => Number(a.min_quantity) - Number(b.min_quantity)) : [];
    const tier = sortedTiers.filter((t) => Number(quantity) >= Number(t.min_quantity)).pop();
    return { tier, price: Number(tier?.unit_price ?? fallbackPrice ?? 0) };
}

function SuggestionCard({ product, currency }) {
    return (
        <article className="product-card">
            <Link href={route('products.show', product.slug || 'slug')} className="block">
                <div className="relative overflow-hidden bg-[#f8fafc]" style={{ height: '200px' }}>
                    <img src={product.primary_image_url || fallbackImage} alt={product.name} className="w-full h-full object-cover" loading="lazy" onError={(e) => { e.currentTarget.src = fallbackImage; }} />
                </div>
            </Link>
            <div className="content">
                <div className="vendor">{product.supplier?.company_name || 'Supplier'}</div>
                <h3><Link href={route('products.show', product.slug || 'slug')}>{product.name}</Link></h3>
                <div className="price">
                    <span className="current">{formatMoney(product.base_price, currency)}</span>
                </div>
                <div className="rating">
                    {[1, 2, 3, 4].map((i) => <svg key={i} className="h-4 w-4 text-[#facc15]" fill="currentColor" viewBox="0 0 20 20"><path d="m10 2.8 2.05 4.15 4.58.67-3.31 3.23.78 4.56L10 13.25l-4.1 2.16.78-4.56-3.31-3.23 4.58-.67L10 2.8Z" /></svg>)}
                    <svg className="h-4 w-4 text-[#d1d5db]" fill="currentColor" viewBox="0 0 20 20"><path d="m10 2.8 2.05 4.15 4.58.67-3.31 3.23.78 4.56L10 13.25l-4.1 2.16.78-4.56-3.31-3.23 4.58-.67L10 2.8Z" /></svg>
                </div>
                <div className="actions">
                    <button type="button" className="add-cart" onClick={() => { router.post(route('cart.add'), { product_id: product.id, quantity: Number(product.moq || 1) }, { preserveScroll: true }); }}>Add to Cart</button>
                    <button type="button" className="wishlist"><svg className="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg></button>
                </div>
            </div>
        </article>
    );
}

export default function Show({ auth, flash, errors, cartCount, currency, defaultQuantity, product, relatedProducts, supplierProducts, isPurchasable }) {
    const gallery = Array.isArray(product.gallery) && product.gallery.length > 0
        ? product.gallery
        : [{ url: product.primary_image_url || fallbackImage, alt: product.name, is_primary: true }];

    const [selectedImage, setSelectedImage] = useState(gallery[0]?.url || product.primary_image_url || fallbackImage);
    const [quantity, setQuantity] = useState(Math.max(1, Number(defaultQuantity || product.moq || 1)));
    const [activeTab, setActiveTab] = useState('overview');
    const [aiQuery, setAiQuery] = useState('');
    const [aiResponse, setAiResponse] = useState('');
    const [isThinking, setIsThinking] = useState(false);
    const validationMessage = Object.values(errors || {}).find(Boolean);
    const isB2B = !!auth?.user;
    const [isRfqModalOpen, setIsRfqModalOpen] = useState(false);

    const rfqForm = useForm({
        contact_name: auth?.user?.name || '',
        company_name: auth?.user?.company_name || auth?.user?.name || '',
        email: auth?.user?.email || '',
        product_id: product.id,
        product_name: product.name,
        quantity: quantity,
        target_price: '',
        message: `I would like to request a quote for this product.`,
    });

    const submitRfq = (e) => {
        e.preventDefault();
        rfqForm.post(route('rfq.store'), { preserveScroll: true, onSuccess: () => setIsRfqModalOpen(false) });
    };

    const availableStock = Number(product.available_stock ?? 0);
    const minimumOrder = isB2B ? Number(product.moq ?? 1) : 1;
    const canPurchase = Boolean(isPurchasable) && availableStock >= minimumOrder;
    const safeQuantity = useMemo(() => {
        if (!canPurchase) return Math.max(1, minimumOrder);
        return Math.min(Math.max(1, quantity), Math.max(minimumOrder, availableStock));
    }, [availableStock, canPurchase, minimumOrder, quantity]);

    const pricing = useMemo(
        () => resolveTierPrice(safeQuantity, isB2B ? (product.pricing_tiers || []) : [], Number(product.base_price ?? 0)),
        [product.pricing_tiers, product.base_price, safeQuantity, isB2B],
    );

    const unitPrice = pricing.price;
    const lineTotal = unitPrice * safeQuantity;
    const savings = Number(product.base_price ?? 0) - unitPrice;
    const discount = product.compare_at_price ? Math.round(((product.compare_at_price - product.base_price) / product.compare_at_price) * 100) : 0;

    let stockStatusText = 'Out of stock';
    let stockStatusColor = 'text-red-600';
    if (canPurchase) {
        if (availableStock <= minimumOrder + 10) {
            stockStatusText = `Only ${availableStock} left`;
            stockStatusColor = 'text-orange-600';
        } else {
            stockStatusText = 'In stock';
            stockStatusColor = 'text-[#0f766e]';
        }
    }

    const addToCart = () => {
        if (!canPurchase) return;
        router.post(route('cart.add'), { product_id: product.id, quantity: safeQuantity }, { preserveScroll: true });
    };

    const buyNow = () => {
        if (!canPurchase) return;
        router.post(route('cart.add'), { product_id: product.id, quantity: safeQuantity }, {
            preserveScroll: true,
            onSuccess: () => router.visit(route('checkout.index')),
        });
    };

    const askAi = (question) => {
        if (!question) return;
        setAiQuery(question);
        setIsThinking(true);
        setAiResponse('');
        setTimeout(() => {
            setIsThinking(false);
            setAiResponse(`Based on the product details, ${product.name} is available in the ${product.category?.name || 'catalog'} category with a minimum order of ${minimumOrder} units and is currently ${canPurchase ? 'in stock' : 'out of stock'}.`);
        }, 1500);
    };

    return (
        <FrontendLayout auth={auth} canLogin={true} cartCount={cartCount}>
            <Head title={product.name} />

            <div className="market-page">
                <main className="site-container pdp" style={{ paddingTop: '28px' }}>
                    {/* Breadcrumb */}
                    <nav className="pdp-breadcrumb">
                        <Link href={route('home')}>Home</Link>
                        <span>/</span>
                        <Link href={route('products.index')}>Products</Link>
                        <span>/</span>
                        <span className="text-[#0f172a] font-semibold">{product.category?.name || 'Category'}</span>
                    </nav>

                    <FlashBanner message={flash?.success} />
                    <FlashBanner message={flash?.error} type="error" />
                    <FlashBanner message={validationMessage} type="error" />

                    {/* Main Grid: Content + Buy Box */}
                    <div className="pdp-grid mt-4">
                        {/* Left: Gallery + Info */}
                        <div className="pdp-main">
                            {/* Gallery */}
                            <section className="pdp-gallery">
                                <div className="pdp-thumbs">
                                    {gallery.map((img, idx) => (
                                        <button
                                            key={idx}
                                            type="button"
                                            className={`pdp-thumb ${selectedImage === img.url ? 'active' : ''}`}
                                            onClick={() => setSelectedImage(img.url)}
                                        >
                                            <img src={img.url} alt={img.alt || product.name} onError={(e) => { e.currentTarget.src = fallbackImage; }} />
                                        </button>
                                    ))}
                                </div>
                                <div className="pdp-hero">
                                    {discount > 0 && <div className="badge">-{discount}%</div>}
                                    <img src={selectedImage} alt={product.name} onError={(e) => { e.currentTarget.src = fallbackImage; }} />
                                </div>
                            </section>

                            {/* Info */}
                            <section className="pdp-info">
                                <div className="store-row">
                                    <Link href="#">Shop all {product.brand?.name || ''} products</Link>
                                    <span className="text-[#d1d5db]">|</span>
                                    <span>Sold by {product.supplier?.company_name || 'Verified Supplier'}</span>
                                    <button type="button" className="inline-flex items-center border border-[#1d4ed8] bg-white text-[#1d4ed8] rounded-full px-2.5 py-1 text-xs font-bold cursor-pointer hover:bg-[#1d4ed8] hover:text-white transition-colors">Follow</button>
                                </div>

                                <h1>{product.name}</h1>

                                <div className="meta">
                                    <div className="flex text-[#facc15]">
                                        {[1, 2, 3].map((i) => <svg key={i} className="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="m10 2.8 2.05 4.15 4.58.67-3.31 3.23.78 4.56L10 13.25l-4.1 2.16.78-4.56-3.31-3.23 4.58-.67L10 2.8Z" /></svg>)}
                                        <svg className="h-4 w-4 text-[#cbd5e1]" fill="currentColor" viewBox="0 0 20 20"><path d="m10 2.8 2.05 4.15 4.58.67-3.31 3.23.78 4.56L10 13.25l-4.1 2.16.78-4.56-3.31-3.23 4.58-.67L10 2.8Z" /></svg>
                                        <svg className="h-4 w-4 text-[#cbd5e1]" fill="currentColor" viewBox="0 0 20 20"><path d="m10 2.8 2.05 4.15 4.58.67-3.31 3.23.78 4.56L10 13.25l-4.1 2.16.78-4.56-3.31-3.23 4.58-.67L10 2.8Z" /></svg>
                                    </div>
                                    <span className="text-sm font-semibold text-slate-800">3.0</span>
                                    <button type="button" className="text-[#1d4ed8] underline text-sm bg-transparent border-none cursor-pointer">0 reviews</button>
                                    <span className="text-[#d1d5db]">|</span>
                                    <span className="text-sm text-[#64748b]">0 answered questions</span>
                                </div>

                                <p className="text-sm text-[#475569]">SKU: <strong>{product.sku || 'N/A'}</strong></p>
                                <p className="text-[#334155]">{product.description || 'No detailed description available.'}</p>

                                <div className="promo-tags">
                                    <span className="promo-tag">Secure checkout</span>
                                    <span className="promo-tag">Fast shipping</span>
                                    <span className="promo-tag">Authentic product</span>
                                </div>

                                {/* Highlights */}
                                <div className="mt-4">
                                    <h2 className="text-lg font-bold text-slate-900 mb-3">Highlights</h2>
                                    <ul className="space-y-2">
                                        {['Premium quality material and build', 'Fast delivery options available', 'Secure and encrypted checkout', 'Trusted verified supplier', '100% genuine product guarantee'].map((h, i) => (
                                            <li key={i} className="flex items-start gap-2 text-sm text-[#334155]">
                                                <svg className="h-5 w-5 text-[#0046be] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7" /></svg>
                                                {h}
                                            </li>
                                        ))}
                                    </ul>
                                </div>

                                {/* Specs Grid */}
                                <div className="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    {[
                                        ['Brand', product.brand?.name || 'Generic'],
                                        ['Category', product.category?.name || 'N/A'],
                                        ['SKU', product.sku || 'N/A'],
                                        ['MOQ', `${minimumOrder} units`],
                                    ].map(([label, value]) => (
                                        <div key={label} className="rounded-xl bg-[#f8fafc] px-3 py-2.5">
                                            <p className="text-[10px] font-bold uppercase tracking-[0.18em] text-[#64748b]">{label}</p>
                                            <p className="mt-1 font-bold text-slate-900 text-sm">{value}</p>
                                        </div>
                                    ))}
                                </div>
                            </section>

                            {/* AI Assistant */}
                            <div className="rounded-xl border border-blue-100 bg-blue-50/60 p-6 mt-6">
                                <div className="flex items-center gap-2 mb-4">
                                    <svg className="h-5 w-5 text-[#0046be]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                    <h2 className="text-lg font-bold text-slate-900">Ask AI Assistant</h2>
                                </div>
                                <div className="flex flex-wrap gap-2 mb-4">
                                    {['What are the key features?', 'Is there a warranty?', 'How fast is shipping?'].map((q, i) => (
                                        <button key={i} onClick={() => askAi(q)} className="rounded-full border border-blue-200 bg-white px-3 py-1.5 text-xs font-medium text-[#334155] hover:border-[#0046be] hover:text-[#0046be] transition cursor-pointer">{q}</button>
                                    ))}
                                </div>
                                <div className="flex gap-2">
                                    <input type="text" value={aiQuery} onChange={(e) => setAiQuery(e.target.value)} onKeyDown={(e) => e.key === 'Enter' && askAi(aiQuery)} placeholder="Ask something else..." className="flex-1 rounded-lg border border-[#e5e7eb] px-3 py-2 text-sm focus:border-[#0046be] outline-none" />
                                    <button onClick={() => askAi(aiQuery)} className="rounded-lg bg-[#0046be] px-4 py-2 text-sm font-bold text-white hover:bg-[#00318a] transition cursor-pointer">Ask</button>
                                </div>
                                {isThinking && <div className="mt-4 p-4 rounded-xl bg-white border border-blue-100 text-sm text-[#64748b] italic">Thinking...</div>}
                                {aiResponse && !isThinking && (
                                    <div className="mt-4 p-4 rounded-xl bg-white border border-blue-200 text-sm text-[#334155] leading-relaxed">
                                        <p className="font-bold text-[#0046be] text-xs mb-1 uppercase tracking-wide">AI Answer</p>
                                        {aiResponse}
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* Right: Buy Box */}
                        <aside className="space-y-4">
                            <div className="buy-box">
                                <p className="text-sm font-semibold text-[#64748b] uppercase tracking-wide mb-3">Order Summary</p>

                                {isB2B && savings > 0 ? (
                                    <>
                                        <span className="text-sm text-[#64748b] line-through">Normal Price: {formatMoney(product.base_price, currency)}</span>
                                        <div className="flex items-end gap-2 mt-1">
                                            <span className="price-big">{formatMoney(unitPrice, currency)}</span>
                                            <span className="text-sm text-[#64748b] mb-1">/ unit</span>
                                        </div>
                                        <div className="mt-2 inline-flex items-center bg-[#fee2e2] text-[#be123c] rounded-full px-2.5 py-1 text-xs font-bold">Save {formatMoney(savings, currency)}/unit</div>
                                    </>
                                ) : (
                                    <div className="flex items-end gap-2">
                                        <span className="price-big">{formatMoney(unitPrice, currency)}</span>
                                        <span className="text-sm text-[#64748b] mb-1">/ unit</span>
                                    </div>
                                )}

                                <p className={`mt-3 text-sm font-semibold ${stockStatusColor}`}>{stockStatusText} ({availableStock})</p>

                                {/* Policies */}
                                <div className="policy-list mt-4 mb-4">
                                    <div className="policy-item">
                                        <span className="policy-icon"><svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg></span>
                                        <div>
                                            <strong className="text-sm font-bold text-slate-900 block">Free shipping</strong>
                                            <small className="text-xs text-[#64748b]">Delivery: 5-7 business days</small>
                                        </div>
                                    </div>
                                    <div className="policy-item">
                                        <span className="policy-icon"><svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg></span>
                                        <div>
                                            <strong className="text-sm font-bold text-slate-900 block">Return & refund</strong>
                                            <small className="text-xs text-[#64748b]">7-day easy return on eligible products.</small>
                                        </div>
                                    </div>
                                    <div className="policy-item">
                                        <span className="policy-icon"><svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg></span>
                                        <div>
                                            <strong className="text-sm font-bold text-slate-900 block">Security & Privacy</strong>
                                            <small className="text-xs text-[#64748b]">Protected checkout and encrypted payment.</small>
                                        </div>
                                    </div>
                                </div>

                                {/* Quantity */}
                                <div className="mb-4">
                                    <label className="text-sm font-medium text-[#334155] flex justify-between mb-2">
                                        <span>Quantity</span>
                                        <span className="text-xs text-[#64748b]">Min: {minimumOrder}</span>
                                    </label>
                                    <div className="qty-selector">
                                        <button type="button" disabled={!canPurchase || safeQuantity <= minimumOrder} onClick={() => setQuantity((c) => Math.max(minimumOrder, c - 1))}>&minus;</button>
                                        <input type="number" min={minimumOrder} max={Math.max(minimumOrder, availableStock)} value={safeQuantity} disabled={!canPurchase} onChange={(e) => { const val = Number(e.target.value || minimumOrder); setQuantity(Math.min(Math.max(minimumOrder, val), availableStock)); }} />
                                        <button type="button" disabled={!canPurchase || safeQuantity >= Math.max(minimumOrder, availableStock)} onClick={() => setQuantity((c) => Math.min(availableStock, c + 1))}>+</button>
                                    </div>
                                </div>

                                {/* Total */}
                                <div className="flex justify-between items-center py-3 border-t border-[#e5e7eb] mb-4">
                                    <span className="text-sm font-medium text-[#64748b]">Total Price</span>
                                    <span className="text-lg font-bold text-slate-900">{formatMoney(lineTotal, currency)}</span>
                                </div>

                                {/* Actions */}
                                <div className="space-y-2">
                                    <button onClick={addToCart} disabled={!canPurchase} className="btn btn-primary w-full">{canPurchase ? 'Add to Cart' : 'Out of Stock'}</button>
                                    <button onClick={buyNow} disabled={!canPurchase} className="btn btn-secondary w-full">{canPurchase ? 'Buy Now' : 'Unavailable'}</button>
                                    {isB2B && safeQuantity >= minimumOrder && (
                                        <button onClick={() => { rfqForm.setData('quantity', safeQuantity); setIsRfqModalOpen(true); }} className="btn btn-outline w-full">Request a Quote</button>
                                    )}
                                </div>

                                {/* Assist Actions */}
                                <div className="grid grid-cols-2 divide-x divide-[#e5e7eb] border-t border-[#e5e7eb] mt-4">
                                    <button className="flex items-center justify-center gap-2 py-3 text-sm font-medium text-[#64748b] hover:text-[#0046be] hover:bg-blue-50/50 transition bg-transparent border-none cursor-pointer">
                                        <svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                                        Save
                                    </button>
                                    <button className="flex items-center justify-center gap-2 py-3 text-sm font-medium text-[#64748b] hover:text-[#0046be] hover:bg-blue-50/50 transition bg-transparent border-none cursor-pointer">
                                        <svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                                        Compare
                                    </button>
                                </div>
                            </div>

                            {/* Share */}
                            <div className="flex items-center justify-center gap-4 py-2">
                                <span className="text-sm font-semibold text-[#64748b]">Share:</span>
                                <button className="text-[#94a3b8] hover:text-[#0046be] transition bg-transparent border-none cursor-pointer"><svg className="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" /></svg></button>
                                <button className="text-[#94a3b8] hover:text-[#0046be] transition bg-transparent border-none cursor-pointer"><svg className="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z" /></svg></button>
                            </div>

                            {/* Seller Box */}
                            <div className="rounded-xl border border-[#dbe4f3] bg-white p-4">
                                <p className="text-sm font-bold text-slate-900">Sold by {product.supplier?.company_name || 'Verified Supplier'}</p>
                                <p className="text-xs text-[#64748b] mt-1">Ships from Dhaka, Bangladesh</p>
                            </div>
                        </aside>
                    </div>

                    {/* Tabs */}
                    <div className="tabs mt-10">
                        <div className="tab-head">
                            {['overview', 'specifications', 'reviews', 'faq'].map((tab) => (
                                <button key={tab} onClick={() => setActiveTab(tab)} className={`tab-btn ${activeTab === tab ? 'active' : ''}`}>{tab}</button>
                            ))}
                        </div>

                        <div className="tab-pane active">
                            {activeTab === 'overview' && (
                                <div>
                                    <h3 className="text-2xl font-bold text-slate-900 mb-4">Product Overview</h3>
                                    <p className="text-[#334155] leading-relaxed mb-4">{product.description || 'No detailed description available for this product.'}</p>
                                    <ul className="space-y-2">
                                        {['Premium quality material and build', 'Fast delivery options available', 'Secure and encrypted checkout', 'Trusted verified supplier'].map((h, i) => (
                                            <li key={i} className="flex items-start gap-2 text-sm text-[#334155]">
                                                <svg className="h-5 w-5 text-[#0046be] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7" /></svg>
                                                {h}
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                            )}
                            {activeTab === 'specifications' && (
                                <div>
                                    <h3 className="text-2xl font-bold text-slate-900 mb-4">Specifications</h3>
                                    <div className="grid grid-cols-1 sm:grid-cols-2 divide-y sm:divide-y-0 sm:divide-x divide-[#e5e7eb]">
                                        {[
                                            ['Brand', product.brand?.name || 'Generic'],
                                            ['Category', product.category?.name || 'N/A'],
                                            ['SKU', product.sku || 'N/A'],
                                            ['Weight', '1.2 kg (Approx)'],
                                            ['Total Sold', `${Math.floor(Math.random() * 500) + 50} Units`],
                                            ['Condition', 'Brand New'],
                                        ].map(([label, value]) => (
                                            <div key={label} className="flex justify-between px-4 py-3 hover:bg-[#f8fafc] transition">
                                                <span className="text-sm font-medium text-[#64748b]">{label}</span>
                                                <span className="text-sm font-semibold text-slate-900">{value}</span>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            )}
                            {activeTab === 'reviews' && (
                                <div>
                                    <h3 className="text-2xl font-bold text-slate-900 mb-4">Customer Reviews</h3>
                                    <p className="text-sm text-[#64748b]">No reviews yet. Be the first to review this product.</p>
                                </div>
                            )}
                            {activeTab === 'faq' && (
                                <div>
                                    <h3 className="text-2xl font-bold text-slate-900 mb-4">Frequently Asked Questions</h3>
                                    <p className="text-sm text-[#64748b]">No questions yet. Ask a question about this product.</p>
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Related Products */}
                    {relatedProducts && relatedProducts.length > 0 && (
                        <section className="section">
                            <div className="section-title">
                                <h2>Similar Products</h2>
                                <Link href={route('products.index')}>View All <svg className="inline h-4 w-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5l7 7-7 7" /></svg></Link>
                            </div>
                            <div className="grid-4">
                                {relatedProducts.slice(0, 4).map((item) => (
                                    <SuggestionCard key={item.id} product={item} currency={currency} />
                                ))}
                            </div>
                        </section>
                    )}
                </main>
            </div>

            {/* RFQ Modal */}
            <Transition appear show={isRfqModalOpen} as={Fragment}>
                <Dialog as="div" className="relative z-50" onClose={() => setIsRfqModalOpen(false)}>
                    <Transition.Child as={Fragment} enter="ease-out duration-300" enterFrom="opacity-0" enterTo="opacity-100" leave="ease-in duration-200" leaveFrom="opacity-100" leaveTo="opacity-0">
                        <div className="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" />
                    </Transition.Child>
                    <div className="fixed inset-0 overflow-y-auto">
                        <div className="flex min-h-full items-center justify-center p-4">
                            <Transition.Child as={Fragment} enter="ease-out duration-300" enterFrom="opacity-0 scale-95" enterTo="opacity-100 scale-100" leave="ease-in duration-200" leaveFrom="opacity-100 scale-100" leaveTo="opacity-0 scale-95">
                                <Dialog.Panel className="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
                                    <Dialog.Title as="h3" className="text-lg font-bold text-slate-900">Request a Custom Quote</Dialog.Title>
                                    <form onSubmit={submitRfq} className="mt-4 space-y-4">
                                        <div>
                                            <label className="block text-sm font-medium text-[#334155]">Target Price / Unit</label>
                                            <input type="number" min="0.01" step="0.01" className="mt-1 block w-full rounded-lg border border-[#e5e7eb] px-3 py-2.5 text-sm" value={rfqForm.data.target_price} onChange={(e) => rfqForm.setData('target_price', e.target.value)} />
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-[#334155]">Quantity</label>
                                            <input type="number" min={minimumOrder} className="mt-1 block w-full rounded-lg border border-[#e5e7eb] px-3 py-2.5 text-sm" value={rfqForm.data.quantity} onChange={(e) => rfqForm.setData('quantity', e.target.value)} />
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-[#334155]">Message</label>
                                            <textarea rows={4} className="mt-1 block w-full rounded-lg border border-[#e5e7eb] px-3 py-2.5 text-sm" value={rfqForm.data.message} onChange={(e) => rfqForm.setData('message', e.target.value)} />
                                        </div>
                                        <div className="mt-6 flex justify-end gap-3">
                                            <button type="button" onClick={() => setIsRfqModalOpen(false)} className="btn btn-outline">Cancel</button>
                                            <button type="submit" disabled={rfqForm.processing} className="btn btn-primary">{rfqForm.processing ? 'Submitting...' : 'Submit Request'}</button>
                                        </div>
                                    </form>
                                </Dialog.Panel>
                            </Transition.Child>
                        </div>
                    </div>
                </Dialog>
            </Transition>
        </FrontendLayout>
    );
}
