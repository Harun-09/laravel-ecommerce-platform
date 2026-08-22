import { useState } from 'react';
import { Link, router } from '@inertiajs/react';

const primaryNavigation = [
    { label: 'Shop All', href: route('products.index') },
    { label: 'Bulk Orders', href: route('products.bulk') },
    { label: 'MOQ Pricing', href: route('products.moq') },
    { label: 'Request a Quote', href: route('rfq.create') },
];

const utilityLinks = [
    { label: 'Top Deals', href: route('products.index', { featured: 1, sort: 'popular' }) },
    { label: 'Bulk Orders', href: route('products.bulk') },
    { label: 'MOQ Pricing', href: route('products.moq') },
    { label: 'Discover', href: route('products.index', { sort: 'latest' }) },
    { label: 'Request a Quote', href: route('rfq.create') },
    { label: 'Help', href: route('rfq.create') },
];

const footerGroups = [
    {
        title: 'Buy smarter',
        links: [
            { label: 'Product catalog', href: route('products.index') },
            { label: 'Bulk orders', href: route('products.bulk') },
            { label: 'MOQ pricing', href: route('products.moq') },
        ],
    },
    {
        title: 'For business',
        links: [
            { label: 'Request a quote', href: route('rfq.create') },
            { label: 'Become a supplier', href: route('supplier.apply') },
            { label: 'Browse products', href: route('products.index') },
        ],
    },
    {
        title: 'Need help?',
        links: [
            { label: 'Talk to our team', href: route('rfq.create') },
            { label: 'Supplier application', href: route('supplier.apply') },
            { label: 'Marketplace', href: route('products.index') },
        ],
    },
];

function SearchIcon() {
    return (
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" className="h-5 w-5 shrink-0 text-[#64748b]">
            <circle cx="10.75" cy="10.75" r="5.75" stroke="currentColor" strokeWidth="1.8" />
            <path d="m15.25 15.25 4.25 4.25" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" />
        </svg>
    );
}

function CartIcon() {
    return (
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" className="h-5 w-5">
            <path d="M3.75 4.75h1.7l1.52 8.05a1.8 1.8 0 0 0 1.77 1.47h7.73a1.8 1.8 0 0 0 1.75-1.34l1.15-4.68H7.05" stroke="currentColor" strokeWidth="1.8" strokeLinejoin="round" />
            <circle cx="9.2" cy="18.3" r="1.15" fill="currentColor" />
            <circle cx="16.7" cy="18.3" r="1.15" fill="currentColor" />
        </svg>
    );
}

function MenuIcon({ open }) {
    return open ? (
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" className="h-5 w-5">
            <path d="m6 6 12 12M18 6 6 18" stroke="currentColor" strokeWidth="1.9" strokeLinecap="round" />
        </svg>
    ) : (
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" className="h-5 w-5">
            <path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" strokeWidth="1.9" strokeLinecap="round" />
        </svg>
    );
}

function Header({ auth, canLogin, cartCount }) {
    const [isMenuOpen, setIsMenuOpen] = useState(false);
    const [search, setSearch] = useState('');
    const isAuthenticated = Boolean(auth?.user);

    const submitSearch = (event) => {
        event.preventDefault();
        setIsMenuOpen(false);
        router.get(
            route('products.index'),
            { search: search.trim() || undefined },
            { preserveScroll: false, preserveState: false },
        );
    };

    return (
        <header className="sticky top-0 z-[1100] text-white" style={{ background: 'linear-gradient(180deg, #0046be 0%, #0042b4 72%, #003a9f 100%)', boxShadow: '0 8px 20px rgba(2, 23, 62, 0.2)' }}>
            {/* ── Tier 1: Top bar ── */}
            <div className="hidden lg:block" style={{ background: '#003087', padding: '7px 0', fontSize: '12px' }}>
                <div className="site-container flex items-center justify-between gap-3.5">
                    <div className="flex items-center gap-4 min-w-0">
                        <Link href={route('supplier.apply')} className="text-white font-medium opacity-90 hover:opacity-100 hover:underline whitespace-nowrap">
                            Sell with PlexusBiz
                        </Link>
                    </div>
                    <div className="flex items-center gap-3.5 flex-shrink-0">
                        <Link href={route('rfq.create')} className="text-white font-medium opacity-90 hover:opacity-100 hover:underline whitespace-nowrap">
                            Order Status
                        </Link>
                        <Link href={route('rfq.create')} className="text-white font-medium opacity-90 hover:opacity-100 hover:underline whitespace-nowrap">
                            Help
                        </Link>
                    </div>
                </div>
            </div>

            {/* ── Tier 2: Main header ── */}
            <div className="site-container" style={{ padding: '13px 0' }}>
                <div className="grid items-center gap-4 lg:grid-cols-[auto_minmax(360px,1fr)_auto]">
                    {/* Logo + Menu */}
                    <div className="flex items-center gap-3.5">
                        <Link href={route('home')} className="text-white no-underline flex items-center gap-3">
                            <span className="grid h-10 w-10 place-items-center overflow-hidden rounded-xl bg-white/10 border border-white/20">
                                <img src="/images/project-logo.png" alt="PlexusBiz" className="h-8 w-8 object-contain" />
                            </span>
                            <span className="hidden sm:block">
                                <span className="block text-base font-black tracking-[-0.045em]">PlexusBiz</span>
                                <span className="block text-[10px] font-bold uppercase tracking-[0.18em] text-white/60">Marketplace</span>
                            </span>
                        </Link>
                        <Link
                            href={route('products.index')}
                            className="inline-flex items-center gap-1.5 text-white font-bold text-lg px-2.5 py-2 rounded-lg border border-white/28"
                            style={{ background: 'rgba(2, 26, 79, 0.34)' }}
                        >
                            <MenuIcon />
                            <span className="hidden md:inline">Menu</span>
                        </Link>
                    </div>

                    {/* Search */}
                    <form className="hidden lg:block" onSubmit={submitSearch} role="search">
                        <div className="relative w-full">
                            <input
                                type="search"
                                value={search}
                                onChange={(event) => setSearch(event.target.value)}
                                placeholder="Search PlexusBiz products..."
                                className="w-full text-[#0f172a] text-[15px] rounded-lg border border-[rgba(2,23,62,0.12)] shadow-[0_1px_3px_rgba(15,23,42,0.08)] transition-all duration-200 focus:border-[#3b82f6] focus:shadow-[0_0_0_3px_rgba(59,130,246,0.2)] outline-none"
                                style={{ padding: '14px 52px 14px 16px' }}
                            />
                            <button
                                type="submit"
                                className="absolute right-1.5 top-1.5 w-9 h-9 rounded-md flex items-center justify-center text-[#0f172a] transition-colors hover:bg-[#e2e8f0] bg-transparent border-none cursor-pointer"
                            >
                                <SearchIcon />
                            </button>
                        </div>
                    </form>

                    {/* Actions */}
                    <div className="ml-auto hidden items-center gap-1 lg:flex">
                        <Link href={route('products.index')} className="inline-flex items-center gap-1.5 text-white text-sm font-semibold px-2.5 py-2 rounded-lg transition-colors hover:bg-white/14">
                            <svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" /></svg>
                            <span>Gift Ideas</span>
                        </Link>

                        {isAuthenticated ? (
                            <Link href={route('dashboard')} className="inline-flex items-center gap-1.5 text-white text-sm font-semibold px-2.5 py-2 rounded-lg transition-colors hover:bg-white/14">
                                <svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                <span>Account</span>
                            </Link>
                        ) : canLogin ? (
                            <Link href={route('login')} className="inline-flex items-center gap-1.5 text-white text-sm font-semibold px-2.5 py-2 rounded-lg transition-colors hover:bg-white/14">
                                <svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                <span>Sign in</span>
                            </Link>
                        ) : null}

                        <Link href={route('cart.index')} className="relative inline-flex items-center gap-1.5 text-white text-sm font-semibold px-2.5 py-2 rounded-lg transition-colors hover:bg-white/14">
                            <CartIcon />
                            <span>Cart</span>
                            {Number(cartCount) > 0 ? (
                                <span className="absolute -right-1 -top-1 min-w-[20px] h-5 rounded-full flex items-center justify-center px-1.5 text-[10px] font-bold" style={{ background: '#ffe000', color: '#fff' }}>
                                    {cartCount}
                                </span>
                            ) : null}
                        </Link>
                    </div>

                    {/* Mobile actions */}
                    <div className="ml-auto flex items-center gap-2 lg:hidden">
                        <Link href={route('cart.index')} aria-label="Open cart" className="relative grid h-10 w-10 place-items-center rounded-lg bg-white/10 text-white border border-white/20">
                            <CartIcon />
                            {Number(cartCount) > 0 ? (
                                <span className="absolute -right-1 -top-1 grid min-w-[20px] place-items-center rounded-full px-1.5 py-0.5 text-[10px] font-bold" style={{ background: '#ffe000', color: '#fff' }}>
                                    {cartCount}
                                </span>
                            ) : null}
                        </Link>
                        <button
                            type="button"
                            aria-label={isMenuOpen ? 'Close navigation' : 'Open navigation'}
                            aria-expanded={isMenuOpen}
                            onClick={() => setIsMenuOpen((current) => !current)}
                            className="grid h-10 w-10 place-items-center rounded-lg bg-white/10 text-white border border-white/20"
                        >
                            <MenuIcon open={isMenuOpen} />
                        </button>
                    </div>
                </div>
            </div>

            {/* ── Tier 3: Utility nav ── */}
            <div className="hidden lg:block" style={{ borderTop: '1px solid rgba(255,255,255,0.2)', borderBottom: '1px solid rgba(255,255,255,0.16)', padding: '8px 0', background: 'rgba(0,32,95,0.24)' }}>
                <div className="site-container flex items-center justify-between gap-5">
                    <div className="flex items-center flex-wrap min-w-0" style={{ gap: '18px' }}>
                        {utilityLinks.map((item) => (
                            <Link key={item.label} href={item.href} className="text-white text-sm font-medium opacity-[0.92] hover:opacity-100 hover:underline whitespace-nowrap" style={{ color: '#fff' }}>
                                {item.label}
                            </Link>
                        ))}
                    </div>
                </div>
            </div>

            {/* ── Mobile menu ── */}
            {isMenuOpen ? (
                <div className="border-t border-white/20 bg-white px-4 py-4 shadow-xl lg:hidden">
                    <form className="mb-4" onSubmit={submitSearch} role="search">
                        <div className="flex h-11 items-center rounded-lg border border-[#e5e7eb] bg-[#f8fafc] px-3 focus-within:border-[#0046be] focus-within:bg-white">
                            <SearchIcon />
                            <input id="storefront-mobile-search" type="search" value={search} onChange={(event) => setSearch(event.target.value)} placeholder="Search products" className="h-full min-w-0 flex-1 border-0 bg-transparent px-3 text-sm text-slate-900 outline-none placeholder:text-[#9ca3af]" />
                            <button type="submit" className="text-xs font-bold text-[#0046be]">Search</button>
                        </div>
                    </form>
                    <nav className="grid gap-1" aria-label="Mobile navigation">
                        {primaryNavigation.map((item) => (
                            <Link key={item.label} href={item.href} onClick={() => setIsMenuOpen(false)} className="rounded-lg px-3 py-3 text-sm font-semibold text-slate-700 transition hover:bg-blue-50 hover:text-[#0046be]">
                                {item.label}
                            </Link>
                        ))}
                        <Link href={isAuthenticated ? route('dashboard') : route('login')} onClick={() => setIsMenuOpen(false)} className="rounded-lg px-3 py-3 text-sm font-semibold text-slate-700 transition hover:bg-blue-50 hover:text-[#0046be]">
                            {isAuthenticated ? 'My account' : 'Sign in'}
                        </Link>
                    </nav>
                </div>
            ) : null}
        </header>
    );
}

function Footer() {
    return (
        <footer className="site-footer">
            <div className="site-container">
                <div className="footer-grid">
                    <div className="max-w-sm">
                        <Link href={route('home')} className="inline-flex items-center gap-3 text-white no-underline">
                            <span className="grid h-10 w-10 place-items-center overflow-hidden rounded-xl bg-white">
                                <img src="/images/project-logo.png" alt="PlexusBiz" className="h-8 w-8 object-contain" />
                            </span>
                            <span className="text-lg font-black tracking-[-0.045em]">PlexusBiz</span>
                        </Link>
                        <p className="mt-5 text-sm leading-7 text-[#9ca3af]">
                            A focused marketplace for teams that need clear product information, bulk purchasing options, and a direct path to a quote.
                        </p>
                        <Link href={route('rfq.create')} className="mt-6 inline-flex items-center gap-2 text-sm font-bold text-blue-300 transition hover:text-white">
                            Start a sourcing request <span aria-hidden="true">&rarr;</span>
                        </Link>
                    </div>

                    {footerGroups.map((group) => (
                        <div key={group.title}>
                            <h2 className="text-xs font-bold uppercase tracking-[0.18em] text-white">{group.title}</h2>
                            <ul className="mt-4 space-y-3 list-none p-0">
                                {group.links.map((item) => (
                                    <li key={item.label}>
                                        <Link href={item.href} className="text-sm text-[#9ca3af] transition hover:text-white">
                                            {item.label}
                                        </Link>
                                    </li>
                                ))}
                            </ul>
                        </div>
                    ))}
                </div>
                <div className="footer-bottom flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p>&copy; {new Date().getFullYear()} PlexusBiz Automate. Built for purposeful buying.</p>
                    <p>Catalog &middot; MOQ pricing &middot; RFQ support</p>
                </div>
            </div>
        </footer>
    );
}

export default function FrontendLayout({ children, auth = {}, canLogin = true, cartCount = 0 }) {
    return (
        <div className="min-h-screen bg-[#f4f7fb] text-slate-900">
            <Header auth={auth} canLogin={canLogin} cartCount={cartCount} />
            {children}
            <Footer />
        </div>
    );
}
