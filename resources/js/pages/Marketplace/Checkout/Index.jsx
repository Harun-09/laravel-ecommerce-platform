import { Head, Link, router } from '@inertiajs/react';
import { useMemo, useState } from 'react';
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

function CheckoutLineItem({ item, currency }) {
    const product = item.product || {};
    return (
        <div className="flex items-center gap-3 rounded-lg border border-[#e5e7eb] bg-[#f8fafc] p-3.5">
            <img src={product.primary_image_url || fallbackImage} alt={product.name} className="h-14 w-14 rounded-lg object-cover" onError={(e) => { e.currentTarget.src = fallbackImage; }} />
            <div className="min-w-0 flex-1">
                <p className="truncate text-sm font-bold text-slate-900">{product.name}</p>
                <p className="text-xs text-[#64748b]">{product.supplier?.company_name || 'Supplier'} &middot; Qty {item.quantity}</p>
            </div>
            <div className="text-right">
                <p className="text-sm font-bold text-slate-900">{formatMoney(item.line_total, currency)}</p>
                <p className="text-[11px] text-[#64748b]">{formatMoney(item.unit_price, currency)} each</p>
            </div>
        </div>
    );
}

function GatewayCard({ gateway, selectedGateway, onSelect }) {
    const checked = gateway.key === selectedGateway;
    return (
        <label className={`flex cursor-pointer items-start gap-3 rounded-lg border p-4 transition ${checked ? 'border-[#0046be] bg-blue-50 shadow-sm' : 'border-[#e5e7eb] bg-white hover:border-[#0046be]/50'}`}>
            <input type="radio" name="gateway" value={gateway.key} checked={checked} onChange={() => onSelect(gateway.key)} className="mt-1 h-4 w-4 accent-[#0046be]" required />
            <div className="min-w-0 flex-1">
                <div className="flex items-center justify-between gap-3">
                    <p className="font-bold text-slate-900">{gateway.label}</p>
                    <span className={`rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-[0.14em] ${gateway.accent === 'amber' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-[#0046be]'}`}>{gateway.accent}</span>
                </div>
                <p className="mt-1 text-sm text-[#64748b] leading-5">{gateway.description}</p>
            </div>
        </label>
    );
}

export default function Index({ auth, flash, errors, cart, buyer, isB2C, csrfToken, currency, defaultGateway, gateways }) {
    const summary = cart?.summary || {};
    const items = Array.isArray(cart?.items) ? cart.items : [];
    const gatewayOptions = Array.isArray(gateways) ? gateways : [];
    const validationMessage = Object.values(errors || {}).find(Boolean);
    const [selectedGateway, setSelectedGateway] = useState(defaultGateway || 'stripe');
    const [paymentTerm, setPaymentTerm] = useState('cash');
    const [shippingMethod, setShippingMethod] = useState(cart?.shipping_method || 'weight_based');

    const selectedGatewayInfo = useMemo(
        () => gatewayOptions.find((g) => g.key === selectedGateway) || gatewayOptions[0] || null,
        [gatewayOptions, selectedGateway],
    );

    const handleShippingChange = (method) => {
        setShippingMethod(method);
        router.post(route('cart.update'), { shipping_method: method }, { preserveScroll: true, preserveState: true });
    };

    return (
        <FrontendLayout auth={auth} canLogin={true} cartCount={cart?.summary?.items_count || 0}>
            <Head title="Checkout" />

            <div className="market-page">
                <main className="site-container section">
                    {/* Simple Header */}
                    <div className="mb-6">
                        <h1 className="text-2xl font-bold text-slate-900">Checkout</h1>
                        <p className="text-sm text-[#64748b] mt-1">{items.length} item{items.length !== 1 ? 's' : ''} &middot; Order total {formatMoney(summary.grand_total || 0, currency)}</p>
                    </div>

                    <FlashBanner message={flash?.success} />
                    <FlashBanner message={flash?.error} type="error" />
                    <FlashBanner message={validationMessage} type="error" />

                    {items.length > 0 ? (
                        <div className="flex flex-col xl:flex-row gap-7">
                            <form method="post" action={route('checkout.process')} className="flex-1 min-w-0 space-y-6">
                                <input type="hidden" name="_token" value={csrfToken} />
                                <input type="hidden" name="shipping_method" value={isB2C ? 'standard' : shippingMethod} />

                                {/* Shipping */}
                                <div className="card p-5">
                                    <h2 className="text-lg font-bold text-slate-900 mb-4">Shipping Method</h2>
                                    {isB2C ? (
                                        <div className="rounded-lg border border-[#0046be] bg-blue-50 p-4 text-[#0046be]">
                                            <p className="font-bold">Standard Shipping</p>
                                            <p className="mt-1 text-sm text-[#0046be]/80">Fixed shipping rate for retail orders.</p>
                                        </div>
                                    ) : (
                                        <div className="grid gap-3 sm:grid-cols-2">
                                            {[
                                                { value: 'weight_based', label: 'Weight-based', desc: '$2.00 per kg' },
                                                { value: 'own_logistics', label: 'Own Logistics', desc: 'Pick up or arrange your own. Free.' },
                                            ].map((opt) => (
                                                <label key={opt.value} className={`flex cursor-pointer flex-col gap-1 rounded-lg border p-4 transition ${shippingMethod === opt.value ? 'border-[#0046be] bg-blue-50 shadow-sm' : 'border-[#e5e7eb] bg-white hover:border-[#0046be]/50'}`}>
                                                    <input type="radio" name="b2b_shipping" value={opt.value} checked={shippingMethod === opt.value} onChange={(e) => handleShippingChange(e.target.value)} className="sr-only" />
                                                    <span className="font-bold">{opt.label}</span>
                                                    <span className="text-xs text-[#64748b]">{opt.desc}</span>
                                                </label>
                                            ))}
                                        </div>
                                    )}
                                </div>

                                {/* Payment Terms */}
                                <div className="card p-5">
                                    <h2 className="text-lg font-bold text-slate-900 mb-4">Payment Terms</h2>
                                    <div className="grid gap-3 sm:grid-cols-3">
                                        {['cash', 'net30', 'net60'].map((term) => (
                                            <label key={term} className={`flex cursor-pointer flex-col items-center gap-1 rounded-lg border p-4 text-center transition ${paymentTerm === term ? 'border-[#0046be] bg-blue-50 shadow-sm' : 'border-[#e5e7eb] bg-white hover:border-[#0046be]/50'}`}>
                                                <input type="radio" name="payment_term" value={term} checked={paymentTerm === term} onChange={(e) => setPaymentTerm(e.target.value)} className="sr-only" required />
                                                <span className="font-bold capitalize">{term.replace('net', 'Net ')}</span>
                                                {term !== 'cash' && <span className="text-[10px] text-[#64748b]">Requires Credit Line</span>}
                                            </label>
                                        ))}
                                    </div>
                                </div>

                                {/* Payment Gateway */}
                                {paymentTerm === 'cash' && (
                                    <div className="card p-5">
                                        <div className="flex items-center justify-between mb-4">
                                            <h2 className="text-lg font-bold text-slate-900">Payment Gateway</h2>
                                            <span className="rounded-full border border-[#e5e7eb] bg-[#f8fafc] px-2.5 py-0.5 text-xs font-bold text-[#64748b]">{gatewayOptions.length} options</span>
                                        </div>
                                        {gatewayOptions.length > 0 ? (
                                            <div className="space-y-3">
                                                {gatewayOptions.map((gateway) => (
                                                    <GatewayCard key={gateway.key} gateway={gateway} selectedGateway={selectedGateway} onSelect={setSelectedGateway} />
                                                ))}
                                            </div>
                                        ) : (
                                            <div className="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm font-semibold text-amber-800">No payment gateway available. Please contact support.</div>
                                        )}
                                    </div>
                                )}

                                {/* Review Items */}
                                <div className="card p-5">
                                    <div className="flex items-center justify-between mb-4">
                                        <h2 className="text-lg font-bold text-slate-900">Review Items</h2>
                                        <Link href={route('cart.index')} className="text-sm font-semibold text-[#0046be] hover:underline">Back to cart</Link>
                                    </div>
                                    <div className="space-y-3">
                                        {items.map((item) => (
                                            <CheckoutLineItem key={item.id} item={item} currency={currency} />
                                        ))}
                                    </div>
                                </div>

                                <button type="submit" className="btn w-full py-4 text-base font-bold" style={{ background: '#ff8a00', color: 'white', borderRadius: '8px' }}>Place order and continue</button>
                            </form>

                            {/* Order Summary Sidebar */}
                            <aside className="w-full xl:w-[390px] flex-shrink-0">
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

                                    <div className="mt-4 rounded-lg border border-[#e5e7eb] bg-[#f8fafc] p-4 text-sm text-[#64748b] leading-relaxed">
                                        <p className="font-bold text-slate-900 mb-1">What happens next</p>
                                        <p>Your order will be confirmed and you will be redirected to the selected payment gateway.</p>
                                    </div>
                                </div>
                            </aside>
                        </div>
                    ) : (
                        <div className="card p-12 text-center">
                            <svg className="h-16 w-16 mx-auto text-[#d1d5db] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                            <h2 className="text-2xl font-bold text-slate-900 mb-2">Nothing to check out</h2>
                            <p className="text-[#64748b] mb-6 max-w-md mx-auto">Your cart is empty. Add some products first.</p>
                            <Link href={route('products.index')} className="btn btn-primary">Browse Products</Link>
                        </div>
                    )}
                </main>
            </div>
        </FrontendLayout>
    );
}
