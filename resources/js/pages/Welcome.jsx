import { Head, Link } from '@inertiajs/react';
import { useState, useEffect, useCallback } from 'react';
import FrontendLayout from '@/Layouts/FrontendLayout';

const storefrontAsset = (path) => `/images/ecommerce/${path}`;
const storeAsset = (path) => `/images/store/${path}`;

const heroSlides = [
    { bg: 'linear-gradient(120deg, #77dede 0%, #7ccff8 100%)', image: '/images/banners/hero-user-1.jpeg', alt: 'Winter Collection 2026', href: '/products' },
    { bg: 'linear-gradient(120deg, #f8faff 0%, #e2e8f8 100%)', image: '/images/banners/hero-user-2.jpg', alt: 'New Arrivals', href: '/products' },
    { bg: 'linear-gradient(120deg, #5f82e8 0%, #7054ce 100%)', image: '/images/banners/hero-user-3.jpg', alt: 'Free Shipping', href: '/products' },
    { bg: 'linear-gradient(120deg, #e9f2ff 0%, #d9e6ff 100%)', image: '/images/banners/hero-user-4.jpg', alt: 'Best Deals', href: '/products' },
    { bg: 'linear-gradient(120deg, #77dede 0%, #7ccff8 100%)', image: '/images/banners/hero-user-5.png', alt: 'Mobile Offers', href: '/products' },
];

const categories = [
    { name: 'Electronics', slug: 'electronics', image: '/images/categories/electronics.png' },
    { name: 'Fashion', slug: 'fashion', image: '/images/categories/fashion.png' },
    { name: 'Home & Living', slug: 'home-living', image: '/images/categories/home-living.png' },
    { name: 'Beauty & Health', slug: 'beauty-health', image: '/images/categories/beauty-health.png' },
    { name: 'Sports & Outdoors', slug: 'sports-outdoors', image: '/images/categories/sports-outdoors.png' },
    { name: 'Books & Stationery', slug: 'books-stationery', image: '/images/categories/books-stationery.png' },
    { name: 'Groceries', slug: 'groceries', image: '/images/categories/groceries.png' },
];

const showcaseGroups = [
    {
        title: 'Your Go-to Destination for Electronics!',
        viewAll: 'electronics',
        items: [
            { title: 'Electronics & Appliances', subtitle: 'Official Warranty | EMI with 33 Banks', image: storefrontAsset('products/product_001.jpg'), href: '/products?category=electronics' },
            { title: 'Official Smartphones', subtitle: 'Display Insurance | Fast Delivery', image: storefrontAsset('products/product_003.jpg'), href: '/products?category=electronics' },
            { title: 'Gadgets & Accessories', subtitle: 'Brand Warranty | Same-day Delivery', image: storefrontAsset('products/product_005.jpg'), href: '/products?category=electronics' },
            { title: 'Kitchen Appliances', subtitle: 'Top Brands | Best Prices', image: storefrontAsset('products/product_006.jpg'), href: '/products?category=kitchen-dining' },
            { title: 'Lifestyle Essentials', subtitle: 'Free Delivery | Same-day Delivery', image: storefrontAsset('products/product_001.jpg'), href: '/products?category=fashion' },
        ],
    },
    {
        title: 'Upgrade Your Home & Lifestyle Today!',
        viewAll: 'home-living',
        items: [
            { title: 'Laptops & Computers', subtitle: 'Official Warranty | Fast Delivery', image: storefrontAsset('products/product_003.jpg'), href: '/products?category=electronics' },
            { title: 'Refrigerators & Freezers', subtitle: 'Top Brands | Best Prices', image: storefrontAsset('products/product_005.jpg'), href: '/products?category=home-living' },
            { title: 'Kitchen Essentials', subtitle: 'Free Delivery | Easy EMI', image: storefrontAsset('products/product_006.jpg'), href: '/products?category=kitchen-dining' },
            { title: 'Furniture & Living', subtitle: 'Durable Build | Smart Design', image: storefrontAsset('products/product_001.jpg'), href: '/products?category=home-living' },
            { title: 'Beauty & Skincare', subtitle: 'Authentic Products | Fast Delivery', image: storefrontAsset('products/product_003.jpg'), href: '/products?category=beauty-health' },
        ],
    },
];

function formatProductPrice(product) {
    if (product?.price) return product.price;
    if (product?.base_price !== undefined) {
        return `BDT ${Number(product.base_price || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    }
    return 'Pricing on request';
}

function ProductCard({ product, index }) {
    const title = product?.title || product?.name || 'Business product';
    const category = typeof product?.category === 'string' ? product.category : product?.category?.name || 'Product';
    const image = product?.image || product?.primary_image_url || storefrontAsset(`products/product_00${(index % 6) + 1}.jpg`);
    const href = product?.slug ? route('products.show', product.slug) : route('products.index');

    return (
        <article className="product-card">
            <Link href={href} className="block">
                <img src={image} alt={title} loading="lazy" onError={(e) => { e.currentTarget.src = storefrontAsset(`products/product_00${(index % 6) + 1}.jpg`); }} />
            </Link>
            <div className="content">
                <div className="flex items-center justify-between gap-2">
                    <div className="vendor">{category}</div>
                </div>
                <h3>
                    <Link href={href}>{title}</Link>
                </h3>
                {product?.short || product?.description ? (
                    <p className="desc">{product.short || product.description}</p>
                ) : null}
                <div className="price">
                    <span className="current">{formatProductPrice(product)}</span>
                </div>
                <div className="rating">
                    {[1, 2, 3, 4].map((i) => (
                        <svg key={i} className="h-4 w-4 text-[#facc15]" fill="currentColor" viewBox="0 0 20 20"><path d="m10 2.8 2.05 4.15 4.58.67-3.31 3.23.78 4.56L10 13.25l-4.1 2.16.78-4.56-3.31-3.23 4.58-.67L10 2.8Z" /></svg>
                    ))}
                    <svg className="h-4 w-4 text-[#d1d5db]" fill="currentColor" viewBox="0 0 20 20"><path d="m10 2.8 2.05 4.15 4.58.67-3.31 3.23.78 4.56L10 13.25l-4.1 2.16.78-4.56-3.31-3.23 4.58-.67L10 2.8Z" /></svg>
                    <span>({Math.floor(Math.random() * 200) + 50})</span>
                </div>
                <div className="actions">
                    <button type="button" className="add-cart" onClick={(e) => { e.preventDefault(); e.stopPropagation(); }}>
                        <svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        Add to Cart
                    </button>
                    <button type="button" className="wishlist" onClick={(e) => { e.preventDefault(); e.stopPropagation(); }}>
                        <svg className="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                    </button>
                </div>
            </div>
        </article>
    );
}

function HeroBanner() {
    const [active, setActive] = useState(0);

    const next = useCallback(() => setActive((c) => (c + 1) % heroSlides.length), []);
    const prev = useCallback(() => setActive((c) => (c - 1 + heroSlides.length) % heroSlides.length), []);

    useEffect(() => {
        const timer = setInterval(next, 5000);
        return () => clearInterval(timer);
    }, [next]);

    return (
        <div className="hero-banner" style={{ background: heroSlides[active].bg, transition: 'background 0.6s ease' }}>
            {heroSlides.map((slide, idx) => (
                <div
                    key={idx}
                    className={`hero-slide ${idx === active ? 'active' : ''}`}
                    style={{ '--hero-slide-bg': 'transparent', background: 'transparent' }}
                >
                    <Link href={slide.href} className="block w-full h-full">
                        <div className="hero-slide-content">
                            <div className="hero-slide-media">
                                <img src={slide.image} alt={slide.alt} draggable={false} loading={idx === 0 ? 'eager' : 'lazy'} />
                            </div>
                        </div>
                    </Link>
                </div>
            ))}

            <button type="button" className="hero-nav prev" onClick={prev} aria-label="Previous banner">
                <svg className="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 19l-7-7 7-7" /></svg>
            </button>
            <button type="button" className="hero-nav next" onClick={next} aria-label="Next banner">
                <svg className="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5l7 7-7 7" /></svg>
            </button>

            <div className="hero-dots">
                {heroSlides.map((_, idx) => (
                    <button key={idx} type="button" className={`hero-dot ${idx === active ? 'active' : ''}`} onClick={() => setActive(idx)} aria-label={`Go to banner ${idx + 1}`} />
                ))}
            </div>
        </div>
    );
}

function ShowcaseSection({ group }) {
    return (
        <section className="section" style={{ paddingTop: '16px', paddingBottom: '10px' }}>
            <div className="site-container">
                <div className="section-title">
                    <h2>{group.title}</h2>
                    <Link href={route('products.index', { category: group.viewAll, sort: 'popular' })}>
                        View All <svg className="inline h-4 w-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5l7 7-7 7" /></svg>
                    </Link>
                </div>
                <div className="showcase-grid">
                    {group.items.map((item, idx) => (
                        <Link key={idx} href={item.href} className="showcase-card">
                            <img src={item.image} alt={item.title} loading="lazy" onError={(e) => { e.currentTarget.src = storefrontAsset('products/product_001.jpg'); }} />
                            <div className="showcase-card-body">
                                <span className="showcase-card-subtitle">{item.subtitle}</span>
                                <span className="showcase-card-title">{item.title}</span>
                            </div>
                        </Link>
                    ))}
                </div>
            </div>
        </section>
    );
}

export default function Welcome({ auth = {}, canLogin, canRegister, featuredProducts = [], dbCategories = [] }) {
    const visibleProducts = featuredProducts.length > 0 ? featuredProducts.slice(0, 10) : [];
    const displayCategories = dbCategories.length > 0 ? dbCategories : categories;

    return (
        <FrontendLayout auth={auth} canLogin={canLogin}>
            <Head title="PlexusBiz | Business marketplace" />

            <main className="overflow-hidden">
                {/* Hero Banner - Full Width with gradient bleed */}
                <div className="w-full" style={{ background: 'linear-gradient(135deg, #77dede 0%, #7ccff8 50%, #b8e6ff 100%)' }}>
                    <HeroBanner />
                </div>

                {/* Category Grid */}
                <section className="section" style={{ paddingTop: '28px', paddingBottom: '8px', background: '#fff' }}>
                    <div className="site-container">
                        <div className="section-title">
                            <h2>Shop By Category</h2>
                            <Link href={route('products.index')}>View All <svg className="inline h-4 w-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5l7 7-7 7" /></svg></Link>
                        </div>
                        <div className="category-grid">
                            {displayCategories.map((cat) => {
                                const name = cat.name;
                                const image = cat.image_url || cat.image || `/images/categories/${cat.slug || 'electronics'}.png`;
                                const href = cat.slug ? route('products.index', { category: cat.slug }) : route('products.index');
                                return (
                                    <Link key={cat.slug || name} href={href} className="category-item">
                                        <img
                                            src={image}
                                            alt={name}
                                            loading="lazy"
                                            onError={(e) => { e.currentTarget.src = `/images/categories/electronics.png`; }}
                                        />
                                        <span>{name}</span>
                                    </Link>
                                );
                            })}
                        </div>
                    </div>
                </section>

                {/* Showcase Groups */}
                {showcaseGroups.map((group, idx) => (
                    <ShowcaseSection key={idx} group={group} />
                ))}

                {/* Services Strip */}
                <section style={{ background: '#f3f4f6', padding: '40px 0' }}>
                    <div className="site-container">
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            {[
                                { icon: 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4', title: 'Fast Delivery', desc: 'Quick and reliable shipping to your doorstep' },
                                { icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', title: 'Secure Payment', desc: '100% secure payment processing' },
                                { icon: 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15', title: 'Easy Returns', desc: 'Hassle-free return policy within 7 days' },
                                { icon: 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z', title: '24/7 Support', desc: 'Round the clock customer support' },
                            ].map((s) => (
                                <div key={s.title} className="flex gap-4 bg-white rounded-xl p-6 shadow-[0_1px_3px_rgba(0,0,0,0.1)]">
                                    <div className="flex-shrink-0 w-14 h-14 rounded-xl bg-blue-50 flex items-center justify-center text-[#0046be]">
                                        <svg className="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="1.7" strokeLinecap="round" strokeLinejoin="round"><path d={s.icon} /></svg>
                                    </div>
                                    <div>
                                        <h3 className="text-sm font-bold text-slate-900">{s.title}</h3>
                                        <p className="mt-1 text-sm text-[#64748b] leading-5">{s.desc}</p>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </section>

                {/* Featured Products */}
                {visibleProducts.length > 0 && (
                    <section className="section">
                        <div className="site-container">
                            <div className="section-title">
                                <h2>
                                    <svg className="inline h-5 w-5 mr-1.5 text-[#f59e0b]" fill="currentColor" viewBox="0 0 20 20"><path d="m10 2.8 2.05 4.15 4.58.67-3.31 3.23.78 4.56L10 13.25l-4.1 2.16.78-4.56-3.31-3.23 4.58-.67L10 2.8Z" /></svg>
                                    Featured Products
                                </h2>
                                <Link href={route('products.index', { featured: 1 })}>View All <svg className="inline h-4 w-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5l7 7-7 7" /></svg></Link>
                            </div>
                            <div className="grid-5">
                                {visibleProducts.map((product, index) => (
                                    <ProductCard key={product.slug || product.id || index} product={product} index={index} />
                                ))}
                            </div>
                        </div>
                    </section>
                )}

                {/* CTA Banner */}
                <section className="section">
                    <div className="site-container">
                        <div className="relative overflow-hidden rounded-2xl bg-[#0f172a] px-6 py-10 sm:px-10 lg:px-14 lg:py-14">
                            <div className="absolute -right-12 -top-20 h-64 w-64 rounded-full bg-[#0046be]/35 blur-3xl" />
                            <div className="absolute -bottom-28 left-1/3 h-56 w-56 rounded-full bg-[#7c3aed]/20 blur-3xl" />
                            <div className="relative grid gap-7 lg:grid-cols-[1fr_auto] lg:items-center">
                                <div className="max-w-2xl">
                                    <p className="text-[11px] font-bold uppercase tracking-[0.18em] text-blue-300">Need a recommendation?</p>
                                    <h2 className="mt-3 text-3xl font-extrabold tracking-[-0.05em] text-white sm:text-4xl">Tell us what you need. We will help shape the next step.</h2>
                                    <p className="mt-4 text-base leading-7 text-slate-300">A request for quotation gives your order requirements the space they need.</p>
                                </div>
                                <Link href={route('rfq.create')} className="market-button-light justify-center">Open an RFQ <svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5l7 7-7 7" /></svg></Link>
                            </div>
                        </div>
                    </div>
                </section>
            </main>
        </FrontendLayout>
    );
}
