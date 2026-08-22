import React, { useEffect } from 'react';
import { Head } from '@inertiajs/react';

export default function Contact() {
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

    <div className="container section static-page-hero static-page-hero--contact">
        <div className="static-page-hero__panel">
            <p className="static-page-hero__eyebrow">Get In Touch</p>
            <h1>Contact Us</h1>
            <p>Have a question or need help? Our team is here for you 24/7.</p>
        </div>
    </div>

    <div className="container section static-page-body">
        <div className="contact-layout">
            <div className="contact-left">
                <div className="contact-content card">
                    <h2>We'd Love to Hear From You</h2>
                    <p>Whether you have a question about products, orders, or anything else, our team is ready to answer all your questions.</p>
                    <div className="ex-style-135">
                        <div className="ex-style-136"><h3 className="ex-style-137">Call Us</h3><p className="ex-style-138">Available 24/7</p><a href="tel:+8801701885707" className="ex-style-139">+8801701885707</a></div>
                        <div className="ex-style-136"><h3 className="ex-style-137">Email Us</h3><p className="ex-style-138">Response within 24 hours</p><a href="mailto:info@novamart.com" className="ex-style-139">info@novamart.com</a></div>
                        <div className="ex-style-136"><h3 className="ex-style-137">Visit Us</h3><p className="ex-style-138">Gulshan, Dhaka</p><strong className="ex-style-140">Bangladesh</strong></div>
                    </div>
                </div>
            </div>
            <div className="contact-form-card card">
                <h3 className="ex-style-141">Send a Message</h3>
                <p className="ex-style-142">Fill out the form below and we'll get back to you as soon as possible.</p>
                <form>
                    <div className="ex-style-95">
                        <div className="form-group"><label>First Name *</label><input type="text" className="form-control" placeholder="Your first name" required /></div>
                        <div className="form-group"><label>Last Name *</label><input type="text" className="form-control" placeholder="Your last name" required /></div>
                    </div>
                    <div className="form-group"><label>Email *</label><input type="email" className="form-control" placeholder="your@email.com" required /></div>
                    <div className="form-group"><label>Subject *</label><select className="form-control" required><option value="">Select a subject</option><option>General Inquiry</option><option>Order Support</option><option>Return Request</option><option>Vendor Inquiry</option><option>Technical Support</option></select></div>
                    <div className="form-group"><label>Message *</label><textarea className="form-control" rows="5" placeholder="Write your message here..." required></textarea></div>
                    <button type="submit" className="btn btn-primary contact-submit ex-style-143">Send Message</button>
                </form>
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
