import { Head, Link, router } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import FrontendLayout from '@/Layouts/FrontendLayout';
import FlashBanner from '@/Components/FlashBanner';

const fallbackImage = '/images/landing/deal-imac.jpg';

function formatMoney(amount, currency = 'BDT') {
    const numericAmount = Number(amount ?? 0);
    try {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: currency || 'BDT',
            maximumFractionDigits: 2,
        }).format(numericAmount);
    } catch {
        return `${currency || 'BDT'} ${numericAmount.toFixed(2)}`;
    }
}

function ProductCard({ product, currency }) {
    const inStock = Number(product.available_stock ?? 0) > 0;
    const discount = product.base_price && product.compare_at_price
        ? Math.round(((product.compare_at_price - product.base_price) / product.compare_at_price) * 100)
        : 0;

    return (
        <article className="product-card">
            <Link href={route('products.show', product.slug)} className="block">
                <div className="relative overflow-hidden bg-[#f8fafc]" style={{ height: '200px' }}>
                    <img
                        src={product.primary_image_url || fallbackImage}
                        alt={product.name}
                        className="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                        loading="lazy"
                        onError={(e) => { e.currentTarget.src = fallbackImage; }}
                    />
                    {discount > 0 && (
                        <div className="badge">-{discount}%</div>
                    )}
                </div>
            </Link>

            <div className="content">
                <div className="vendor">{product.supplier?.company_name || 'PlexusBiz supplier'}</div>
                <h3>
                    <Link href={route('products.show', product.slug)}>{product.name}</Link>
                </h3>
                <p className="desc">{product.description || 'Product details available on the product page.'}</p>

                <div className="price">
                    <span className="current">{formatMoney(product.base_price, currency)}</span>
                    {product.compare_at_price && Number(product.compare_at_price) > Number(product.base_price) && (
                        <span className="old">{formatMoney(product.compare_at_price, currency)}</span>
                    )}
                </div>

                <div className="rating">
                    {[1, 2, 3, 4].map((i) => (
                        <svg key={i} className="h-4 w-4 text-[#facc15]" fill="currentColor" viewBox="0 0 20 20"><path d="m10 2.8 2.05 4.15 4.58.67-3.31 3.23.78 4.56L10 13.25l-4.1 2.16.78-4.56-3.31-3.23 4.58-.67L10 2.8Z" /></svg>
                    ))}
                    <svg className="h-4 w-4 text-[#d1d5db]" fill="currentColor" viewBox="0 0 20 20"><path d="m10 2.8 2.05 4.15 4.58.67-3.31 3.23.78 4.56L10 13.25l-4.1 2.16.78-4.56-3.31-3.23 4.58-.67L10 2.8Z" /></svg>
                    <span>({Math.floor(Math.random() * 150) + 30})</span>
                </div>

                <div className="actions">
                    <button
                        type="button"
                        className="add-cart"
                        disabled={!inStock}
                        onClick={() => {
                            if (!inStock) return;
                            router.post(route('cart.add'), { product_id: product.id, quantity: Number(product.moq || 1) }, { preserveScroll: true });
                        }}
                    >
                        <svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        {inStock ? 'Add to Cart' : 'Out of Stock'}
                    </button>
                    <button type="button" className="wishlist">
                        <svg className="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                    </button>
                </div>
            </div>
        </article>
    );
}

function Pagination({ links }) {
    const normalizedLinks = Array.isArray(links) ? links.filter((link) => link.label && link.url) : [];
    if (normalizedLinks.length === 0) return null;

    return (
        <div className="flex flex-wrap items-center justify-center gap-2 mt-6">
            {normalizedLinks.map((link, index) => (
                <Link
                    key={`${link.label}-${index}`}
                    href={link.url}
                    preserveScroll
                    className={`min-w-10 rounded-full px-4 py-2 text-sm font-semibold transition ${
                        link.active
                            ? 'bg-[#0046be] text-white shadow-sm'
                            : 'border border-[#e5e7eb] bg-white text-[#334155] hover:border-[#0046be] hover:text-[#0046be]'
                    }`}
                    dangerouslySetInnerHTML={{ __html: link.label }}
                />
            ))}
        </div>
    );
}

export default function Index({ auth, flash, errors, cartCount, categories, featuredProducts, products, filters, currency }) {
    const [search, setSearch] = useState(filters?.search || '');
    const [category, setCategory] = useState(filters?.category || '');
    const [sortBy, setSortBy] = useState(filters?.sort || 'popular');
    const productRows = Array.isArray(products?.data) ? products.data : [];
    const categoryRows = Array.isArray(categories) ? categories : [];
    const validationMessage = Object.values(errors || {}).find(Boolean);
    const visibleCount = productRows.length;
    const totalCount = Number(products?.meta?.total ?? visibleCount);

    const runFilter = (overrides = {}) => {
        const params = {
            search: search.trim() || undefined,
            category: category || undefined,
            sort: sortBy,
            ...overrides,
        };
        router.get(route('products.index'), params, { preserveScroll: true, preserveState: false, replace: true });
    };

    return (
        <FrontendLayout auth={auth} canLogin={true} cartCount={cartCount}>
            <Head title="Products" />

            <div className="market-page">
                <main className="site-container section" style={{ paddingTop: '28px' }}>
                    <FlashBanner message={flash?.success} />
                    <FlashBanner message={flash?.error} type="error" />
                    <FlashBanner message={validationMessage} type="error" />

                    <div className="flex gap-7 mt-5">
                        {/* ── Sidebar Filters ── */}
                        <aside className="hidden lg:block w-[280px] flex-shrink-0">
                            <div className="card p-6">
                                <h3 className="text-lg font-bold text-slate-900 mb-5">Filters</h3>

                                {/* Search */}
                                <form onSubmit={(e) => { e.preventDefault(); runFilter(); }}>
                                    <div className="mb-6">
                                        <div className="relative">
                                            <input
                                                type="search"
                                                value={search}
                                                onChange={(e) => setSearch(e.target.value)}
                                                placeholder="Search products..."
                                                className="w-full rounded-lg border border-[#e5e7eb] bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-[#9ca3af] focus:border-[#0046be] focus:ring-1 focus:ring-blue-100 outline-none transition-all"
                                            />
                                        </div>
                                    </div>

                                    {/* Categories */}
                                    <div className="mb-6">
                                        <h4 className="text-sm font-semibold text-slate-900 mb-3">Categories</h4>
                                        <div className="space-y-2">
                                            <label className="flex items-center gap-2 cursor-pointer">
                                                <input type="radio" name="category" value="" checked={!category} onChange={() => { setCategory(''); }} className="w-4 h-4 accent-[#0046be]" />
                                                <span className="text-sm text-[#334155]">All Categories</span>
                                            </label>
                                            {categoryRows.map((cat) => (
                                                <label key={cat.slug} className="flex items-center gap-2 cursor-pointer">
                                                    <input type="radio" name="category" value={cat.slug} checked={category === cat.slug} onChange={() => { setCategory(cat.slug); }} className="w-4 h-4 accent-[#0046be]" />
                                                    <span className="text-sm text-[#334155]">{cat.name}</span>
                                                </label>
                                            ))}
                                        </div>
                                    </div>

                                    {/* Price Range */}
                                    <div className="mb-6">
                                        <h4 className="text-sm font-semibold text-slate-900 mb-3">Price Range</h4>
                                        <div className="flex gap-3">
                                            <input type="number" placeholder="Min" className="w-full rounded-lg border border-[#d3dbe7] px-3 py-2.5 text-sm" />
                                            <input type="number" placeholder="Max" className="w-full rounded-lg border border-[#d3dbe7] px-3 py-2.5 text-sm" />
                                        </div>
                                    </div>

                                    {/* Rating */}
                                    <div className="mb-6">
                                        <h4 className="text-sm font-semibold text-slate-900 mb-3">Rating</h4>
                                        {[4, 3, 2, 1].map((rating) => (
                                            <label key={rating} className="flex items-center gap-2 cursor-pointer mb-2">
                                                <input type="radio" name="rating" value={rating} className="w-4 h-4 accent-[#0046be]" />
                                                <span className="flex text-[#facc15]">
                                                    {Array.from({ length: 5 }, (_, i) => (
                                                        <svg key={i} className={`h-4 w-4 ${i < rating ? 'text-[#facc15]' : 'text-[#d1d5db]'}`} fill="currentColor" viewBox="0 0 20 20"><path d="m10 2.8 2.05 4.15 4.58.67-3.31 3.23.78 4.56L10 13.25l-4.1 2.16.78-4.56-3.31-3.23 4.58-.67L10 2.8Z" /></svg>
                                                    ))}
                                                </span>
                                                <span className="text-xs text-[#64748b]">& up</span>
                                            </label>
                                        ))}
                                    </div>

                                    <button type="submit" className="btn btn-primary w-full">Apply Filters</button>
                                    <Link href={route('products.index')} className="block text-center mt-3 text-sm text-[#64748b] hover:text-[#0046be]">Clear All</Link>
                                </form>
                            </div>
                        </aside>

                        {/* ── Products Grid ── */}
                        <div className="flex-1 min-w-0">
                            {/* Header */}
                            <div className="flex items-center justify-between gap-4 mb-5">
                                <div>
                                    <h1 className="text-2xl font-bold text-slate-900">All Products</h1>
                                    <p className="text-sm text-[#64748b] mt-1">Showing {visibleCount} of {totalCount} products</p>
                                </div>
                                <div className="flex items-center gap-3">
                                    <span className="text-sm text-[#64748b]">Sort by:</span>
                                    <select
                                        value={sortBy}
                                        onChange={(e) => { setSortBy(e.target.value); runFilter({ sort: e.target.value }); }}
                                        className="rounded-lg border border-[#e5e7eb] bg-white px-3 py-2.5 text-sm"
                                    >
                                        <option value="popular">Most Popular</option>
                                        <option value="latest">Newest</option>
                                        <option value="price_low">Price: Low to High</option>
                                        <option value="price_high">Price: High to Low</option>
                                        <option value="rating">Best Rating</option>
                                    </select>
                                </div>
                            </div>

                            {/* Product Grid */}
                            {productRows.length > 0 ? (
                                <div className="grid-4">
                                    {productRows.map((product) => (
                                        <ProductCard key={product.id} product={product} currency={currency} />
                                    ))}
                                </div>
                            ) : (
                                <div className="card p-12 text-center">
                                    <svg className="h-12 w-12 mx-auto text-[#94a3b8] mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                                    <h3 className="text-xl font-bold text-slate-900 mb-2">No products found</h3>
                                    <p className="text-sm text-[#64748b] mb-4">Try adjusting your filters or search terms.</p>
                                    <Link href={route('products.index')} className="btn btn-primary">Clear Filters</Link>
                                </div>
                            )}

                            <Pagination links={products?.links || []} />
                        </div>
                    </div>
                </main>
            </div>
        </FrontendLayout>
    );
}
