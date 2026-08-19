import { Head, Link, router } from '@inertiajs/react';
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

function LineItem({ item, currency }) {
    const product = item.product || {};
    const currentQuantity = Number(item.quantity || 1);
    const moq = Number(product.moq || 1);
    const availableStock = Number(product.available_stock || 0);
    const maxQuantity = Math.max(moq, availableStock);
    const canIncrease = availableStock === 0 ? false : currentQuantity < maxQuantity;
    const canDecrease = currentQuantity > moq;

    const updateQuantity = (nextQuantity) => {
        router.post(route('cart.update'), { item_id: item.id, quantity: nextQuantity }, { preserveScroll: true });
    };

    return (
        <article className="card">
            <div className="flex flex-col md:flex-row">
                <Link href={route('products.show', product.slug)} className="block bg-[#f8fafc] md:w-[170px] flex-shrink-0">
                    <img src={product.primary_image_url || fallbackImage} alt={product.name} className="w-full h-40 md:h-full object-cover" onError={(e) => { e.currentTarget.src = fallbackImage; }} />
                </Link>

                <div className="flex-1 p-5">
                    <div className="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p className="text-xs font-bold uppercase tracking-[0.14em] text-[#64748b]">{product.supplier?.company_name || 'Supplier'}</p>
                            <h3 className="text-lg font-bold text-slate-900 mt-1">
                                <Link href={route('products.show', product.slug)} className="hover:text-[#0046be] transition-colors">{product.name}</Link>
                            </h3>
                            <p className="text-sm text-[#64748b] mt-1">SKU {product.sku || 'N/A'} &middot; MOQ {moq} &middot; Stock {availableStock}</p>
                        </div>
                        <div className="text-right">
                            <p className="text-xs font-bold uppercase tracking-[0.14em] text-[#64748b]">Unit price</p>
                            <p className="text-xl font-bold text-[#0046be]">{formatMoney(item.unit_price, currency)}</p>
                        </div>
                    </div>

                    <div className="flex flex-wrap items-center justify-between gap-4 mt-4 pt-4 border-t border-[#f1f5f9]">
                        <div className="flex items-center gap-3">
                            <p className="text-xs font-bold uppercase tracking-[0.14em] text-[#64748b]">Qty:</p>
                            <div className="flex items-center rounded-lg border border-[#e5e7eb] overflow-hidden">
                                <button type="button" disabled={!canDecrease} onClick={() => updateQuantity(currentQuantity - 1)} className="w-9 h-9 flex items-center justify-center text-sm font-bold text-[#334155] bg-[#f9fafb] hover:bg-[#f3f4f6] transition-colors border-none cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">&minus;</button>
                                <span className="min-w-12 text-center text-sm font-bold text-slate-900 border-x border-[#e5e7eb] bg-white h-9 flex items-center">{currentQuantity}</span>
                                <button type="button" disabled={!canIncrease} onClick={() => updateQuantity(currentQuantity + 1)} className="w-9 h-9 flex items-center justify-center text-sm font-bold text-[#334155] bg-[#f9fafb] hover:bg-[#f3f4f6] transition-colors border-none cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">+</button>
                            </div>
                            {currentQuantity > availableStock && (
                                <p className="text-xs font-bold text-rose-600">Only {availableStock} left</p>
                            )}
                        </div>

                        <div className="flex items-center gap-4">
                            <div className="text-right">
                                <p className="text-xs font-bold uppercase tracking-[0.14em] text-[#64748b]">Total</p>
                                <p className="text-lg font-bold text-slate-900">{formatMoney(item.line_total, currency)}</p>
                            </div>
                            <button
                                type="button"
                                onClick={() => router.post(route('cart.remove'), { item_id: item.id }, { preserveScroll: true })}
                                className="rounded-full border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-700 hover:bg-rose-100 transition cursor-pointer"
                            >
                                Remove
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </article>
    );
}

function SuggestionCard({ product, currency }) {
    const inStock = Number(product.available_stock ?? 0) > 0;
    return (
        <article className="product-card">
            <Link href={route('products.show', product.slug)} className="block">
                <div className="overflow-hidden bg-[#f8fafc]" style={{ height: '200px' }}>
                    <img src={product.primary_image_url || fallbackImage} alt={product.name} className="w-full h-full object-cover" loading="lazy" onError={(e) => { e.currentTarget.src = fallbackImage; }} />
                </div>
            </Link>
            <div className="content">
                <div className="vendor">{product.supplier?.company_name || 'Supplier'}</div>
                <h3><Link href={route('products.show', product.slug)}>{product.name}</Link></h3>
                <div className="price">
                    <span className="current">{formatMoney(product.base_price, currency)}</span>
                </div>
                <div className="actions">
                    <button type="button" className="add-cart" disabled={!inStock} onClick={() => { if (!inStock) return; router.post(route('cart.add'), { product_id: product.id, quantity: Number(product.moq || 1) }, { preserveScroll: true }); }}>
                        {inStock ? 'Add to Cart' : 'Out of Stock'}
                    </button>
                    <button type="button" className="wishlist"><svg className="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg></button>
                </div>
            </div>
        </article>
    );
}

export default function Index({ auth, flash, errors, cart, suggestions, currency }) {
    const summary = cart?.summary || {};
    const items = Array.isArray(cart?.items) ? cart.items : [];
    const suggestedProducts = Array.isArray(suggestions) ? suggestions : [];
    const validationMessage = Object.values(errors || {}).find(Boolean);
    const hasInsufficientStock = items.some((item) => Number(item.quantity || 1) > Number(item.product?.available_stock || 0));

    return (
        <FrontendLayout auth={auth} canLogin={true} cartCount={cart?.summary?.items_count || 0}>
            <Head title="Shopping Cart" />

            <div className="market-page">
                <main className="site-container section">
                    {/* Simple Header */}
                    <div className="mb-6">
                        <h1 className="text-2xl font-bold text-slate-900">Shopping Cart</h1>
                        <p className="text-sm text-[#64748b] mt-1">{items.length} item{items.length !== 1 ? 's' : ''} in your cart</p>
                    </div>

                    <FlashBanner message={flash?.success} />
                    <FlashBanner message={flash?.error} type="error" />
                    <FlashBanner message={validationMessage} type="error" />

                    {items.length > 0 ? (
                        <div className="flex flex-col lg:flex-row gap-7">
                            {/* Cart Items */}
                            <div className="flex-1 min-w-0 space-y-4">
                                {items.map((item) => (
                                    <LineItem key={item.id} item={item} currency={currency} />
                                ))}
                            </div>

                            {/* Order Summary */}
                            <aside className="w-full lg:w-[360px] flex-shrink-0">
                                <div className="card p-5 sticky top-24">
                                    <p className="text-sm font-bold text-slate-900 mb-4">Order Summary</p>

                                    <dl className="space-y-2 text-sm">
                                        {[
                                            ['Subtotal', summary.subtotal],
                                            ['Shipping', summary.shipping_total],
                                            ['Tax', summary.tax_total],
                                            ['Discount', summary.discount_total],
                                        ].map(([label, value]) => (
                                            <div key={label} className="flex items-center justify-between py-2">
                                                <dt className="text-[#64748b]">{label}</dt>
                                                <dd className="font-bold text-slate-900">{formatMoney(value || 0, currency)}</dd>
                                            </div>
                                        ))}
                                    </dl>

                                    <div className="mt-4 py-4 border-t border-[#e5e7eb]">
                                        <div className="flex items-center justify-between">
                                            <span className="text-base font-bold text-slate-900">Grand Total</span>
                                            <span className="text-xl font-bold text-[#0046be]">{formatMoney(summary.grand_total || 0, currency)}</span>
                                        </div>
                                    </div>

                                    {hasInsufficientStock ? (
                                        <button type="button" disabled className="btn w-full mt-4 bg-slate-300 text-white cursor-not-allowed">Adjust stock before checkout</button>
                                    ) : (
                                        <Link href={route('checkout.index')} className="btn btn-primary w-full mt-4">Proceed to Checkout</Link>
                                    )}

                                    <Link href={route('products.index')} className="block text-center mt-3 text-sm text-[#64748b] hover:text-[#0046be]">Continue Shopping</Link>
                                </div>
                            </aside>
                        </div>
                    ) : (
                        <div className="card p-12 text-center">
                            <svg className="h-16 w-16 mx-auto text-[#d1d5db] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                            <h2 className="text-2xl font-bold text-slate-900 mb-2">Your cart is empty</h2>
                            <p className="text-[#64748b] mb-6 max-w-md mx-auto">Start adding products to your cart and they will appear here.</p>
                            <Link href={route('products.index')} className="btn btn-primary">Browse Products</Link>
                        </div>
                    )}

                    {/* Suggested Products */}
                    {suggestedProducts.length > 0 && (
                        <section className="section mt-8">
                            <div className="section-title">
                                <h2>You May Also Like</h2>
                                <Link href={route('products.index')}>View Catalog <svg className="inline h-4 w-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5l7 7-7 7" /></svg></Link>
                            </div>
                            <div className="grid-4">
                                {suggestedProducts.slice(0, 4).map((product) => (
                                    <SuggestionCard key={product.id} product={product} currency={currency} />
                                ))}
                            </div>
                        </section>
                    )}
                </main>
            </div>
        </FrontendLayout>
    );
}
