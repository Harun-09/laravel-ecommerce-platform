import React, { useEffect } from 'react';
import { Head } from '@inertiajs/react';

export default function OrderStatus() {
    useEffect(() => {
        // Init scripts
    }, []);

    return (
        


<>
    <header className="header">
        <div className="header-top"><div className="container header-top-row"><div className="header-top-links"><a href="outlet.html">Best Buy Outlet</a><a href="business.html">Best Buy Business</a></div><div className="header-top-right"><details className="pref-switcher"><summary className="pref-trigger"><span className="pref-current">EN</span><span className="pref-current">BDT</span><i className="fas fa-angle-down"></i></summary><div className="pref-dropdown-menu"><p className="pref-section-title">Change Language</p><button className="pref-option active"><span className="pref-radio"><i className="fas fa-dot-circle"></i></span><span>English<small>- EN</small></span></button><button className="pref-option"><span className="pref-radio"><i className="far fa-circle"></i></span><span>Bangla<small>- BN</small></span></button><div className="pref-divider"></div><p className="pref-section-title">Change Currency</p><button className="pref-option active"><span className="pref-radio"><i className="fas fa-dot-circle"></i></span><span>৳ - BDT<small>- Bangladeshi Taka</small></span></button><button className="pref-option"><span className="pref-radio"><i className="far fa-circle"></i></span><span>$ - USD<small>- US Dollar</small></span></button></div></details><a href="tel:+8801701885707"><i className="fas fa-phone"></i> +8801701885707</a><a href="order-status.html">Order Status</a><a href="help.html">Help</a></div></div></div>
        <div className="header-main"><div className="container header-main-row"><div className="header-main-left"><a href="index.html" className="logo">Nova<span>Mart</span></a><a href="gift-ideas.html" className="menu-launcher"><i className="fas fa-bars"></i> Menu</a></div><form className="search-box"><input type="text" name="q" placeholder="Search NovaMart" autocomplete="off" /><button type="submit"><i className="fas fa-search"></i></button><div className="search-suggestions" hidden></div></form><div className="header-actions"><a href="#"><i className="fas fa-gift"></i><span>Gift Ideas</span></a><a href="login.html"><i className="fas fa-user"></i><span>Sign in</span></a><a href="wishlist.html"><i className="fas fa-heart"></i><span>Saved Items</span></a><a href="cart.html" className="cart-badge"><i className="fas fa-shopping-cart"></i><span id="cart-count">0</span><small>Cart</small></a></div></div></div>
        <div className="header-utility"><div className="container header-utility-row"><div className="header-utility-links"><a href="top-deals.html">Top Deals</a><a href="deal-of-the-day.html">Deal of the Day</a><a href="discover.html">Discover</a><a href="memberships.html">My NovaMart Memberships</a><a href="credit-cards.html">Credit Cards</a><a href="gift-cards.html">Gift Cards</a></div><div className="header-utility-links"><a href="recently-viewed.html">Recently Viewed</a></div></div></div>
    </header>

    {/*  Navigation  */}
    <nav className="nav">
        <div className="container">
            <ul className="nav-menu">
                <li><a href="products.html"><i className="fas fa-th-large"></i> Shop All Departments</a></li>
                <li><a href="products.html">Electronics</a></li>
                <li><a href="products.html">Fashion</a></li>
                <li><a href="products.html">Home & Living</a></li>
                <li><a href="products.html">Beauty</a></li>
                <li><a href="products.html">Sports</a></li>
                <li><a href="products.html">Groceries</a></li>
            </ul>
        </div>
    </nav>

    <div className="container section static-page-hero static-page-hero--about">
        <div className="static-page-hero__panel">
            <p className="static-page-hero__eyebrow">About NovaMart</p>
            <h1>Order Status</h1>
            <p>Connecting buyers with thousands of verified sellers across the country since 2024.</p>
        </div>
    </div>

    <div className="container section static-page-body">
        <div className="about-layout">
            <div className="about-content card">
                <h2>Our Story</h2>
                <p>NovaMart was founded with a simple mission: to create a trusted, accessible, and enjoyable online shopping experience for everyone in Bangladesh. We believe that e-commerce should be simple, secure, and accessible to all.</p>
                <p>Starting as a small team of passionate entrepreneurs, we've grown into one of Bangladesh's leading multi-vendor platforms, connecting thousands of sellers with millions of customers across the country.</p>
                <h3>Our Mission</h3>
                <p>To empower businesses of all sizes by providing them with the tools, technology, and platform they need to reach customers across Bangladesh and beyond.</p>
                <h3>Our Values</h3>
                <ul>
                    <li><strong>Trust:</strong> Every transaction on NovaMart is backed by our secure payment system and buyer protection program.</li>
                    <li><strong>Quality:</strong> We work with verified sellers to ensure that every product meets our quality standards.</li>
                    <li><strong>Innovation:</strong> We continuously improve our platform to provide the best shopping experience.</li>
                    <li><strong>Community:</strong> We support local businesses and help them grow through our platform.</li>
                </ul>
            </div>
            <div className="about-side">
                <div className="about-side__card card ex-style-1">
                    <h3 className="ex-style-2">Our Numbers</h3>
                    <div className="ex-style-3">
                        <div className="ex-style-4"><span className="ex-style-5">Active Sellers</span><strong>5,000+</strong></div>
                        <div className="ex-style-4"><span className="ex-style-5">Products Listed</span><strong>50,000+</strong></div>
                        <div className="ex-style-4"><span className="ex-style-5">Happy Customers</span><strong>200,000+</strong></div>
                        <div className="ex-style-4"><span className="ex-style-5">Districts Covered</span><strong>64</strong></div>
                    </div>
                </div>
                <div className="about-side__cta card ex-style-1">
                    <h3 className="ex-style-2">Start Selling Today</h3>
                    <p className="ex-style-6">Join thousands of sellers who are growing their business on NovaMart.</p>
                    <a href="register.html" className="btn btn-primary ex-style-7">Become a Seller</a>
                </div>
            </div>
        </div>
    </div>

    <footer className="footer">
        <div className="container">
            <div className="footer-grid">
                <div><a href="index.html" className="logo ex-style-8">Nova<span className="ex-style-9">Mart</span></a><p className="ex-style-10">Bangladesh's leading multi-vendor e-commerce platform. Shop with confidence and enjoy the best deals on thousands of products.</p><div className="social-links"><a href="#"><i className="fab fa-facebook-f"></i></a><a href="#"><i className="fab fa-instagram"></i></a><a href="#"><i className="fab fa-youtube"></i></a><a href="#"><i className="fab fa-twitter"></i></a></div></div>
                <div><h4>Quick Links</h4><ul><li><a href="about.html">About Us</a></li><li><a href="contact.html">Contact Us</a></li><li><a href="#">Terms & Conditions</a></li><li><a href="#">Privacy Policy</a></li><li><a href="#">Return Policy</a></li></ul></div>
                <div><h4>My Account</h4><ul><li><a href="login.html">Login</a></li><li><a href="register.html">Register</a></li><li><a href="products.html">Browse Products</a></li><li><a href="cart.html">Shopping Cart</a></li></ul></div>
                <div><h4>Contact Info</h4><ul><li><i className="fas fa-map-marker-alt"></i> Gulshan, Dhaka, Bangladesh</li><li><i className="fas fa-phone"></i> +8801701885707</li><li><i className="fas fa-envelope"></i> info@novamart.com</li><li><i className="fas fa-clock"></i> Sat-Thu: 9:00 AM - 9:00 PM</li></ul></div>
            </div>
            <div className="footer-bottom"><p>&copy; 2025 NovaMart. All Rights Reserved.</p></div>
        </div>
    </footer>

    <div id="toast-container" className="toast-container" aria-live="polite" aria-atomic="true"></div>

    
</>

    );
}
