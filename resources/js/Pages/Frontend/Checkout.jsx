import React, { useEffect } from 'react';
import { Head } from '@inertiajs/react';

export default function Checkout() {
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

    <div className="container section">
        <h1 className="ex-style-32">Checkout</h1>
        <div className="ex-style-92">
            <div>
                <div className="card ex-style-93">
                    <h3 className="ex-style-58"><i className="fas fa-truck ex-style-94"></i>Shipping Information</h3>
                    <div className="ex-style-95"><div className="form-group"><label>Full Name *</label><input type="text" className="form-control" value="Ahmed Rahman" required /></div><div className="form-group"><label>Phone Number *</label><input type="tel" className="form-control" value="01701885707" required /></div></div>
                    <div className="form-group"><label>Email Address</label><input type="email" className="form-control" value="ahmed@email.com" /></div>
                    <div className="form-group"><label>Address *</label><textarea className="form-control" rows="2" required>House 12, Road 5, Gulshan-1</textarea></div>
                    <div className="ex-style-95"><div className="form-group"><label>City *</label><select className="form-control" required><option>Dhaka</option><option>Chittagong</option><option>Sylhet</option></select></div><div className="form-group"><label>Postal Code</label><input type="text" className="form-control" value="1212" /></div></div>
                </div>
                <div className="card ex-style-93">
                    <h3 className="ex-style-58"><i className="fas fa-shipping-fast ex-style-94"></i>Shipping Method</h3>
                    <div className="ex-style-96"><i className="fas fa-map-marker-alt ex-style-97"></i><span className="ex-style-98">Delivery Zone:</span><span className="ex-style-99">Dhaka Metro</span></div>
                    <label className="ex-style-100"><div className="ex-style-88"><input type="radio" name="shipping_method" checked /><div><p className="ex-style-23">Standard Delivery</p><p className="ex-style-46">Est. delivery: 3-5 days</p></div></div><div className="ex-style-53"><p className="ex-style-101">FREE</p></div></label>
                    <label className="ex-style-102"><div className="ex-style-88"><input type="radio" name="shipping_method" /><div><p className="ex-style-23">Express Delivery</p><p className="ex-style-46">Est. delivery: 1-2 days</p></div></div><div className="ex-style-53"><p className="ex-style-101">৳150</p></div></label>
                </div>
                <div className="card ex-style-93">
                    <h3 className="ex-style-58"><i className="fas fa-credit-card ex-style-94"></i>Payment Method</h3>
                    <div className="ex-style-103">
                        <label className="ex-style-104"><input type="radio" name="payment_method" checked /><i className="fas fa-money-bill-wave ex-style-105"></i><div><p className="ex-style-23">Cash on Delivery</p><p className="ex-style-46">Pay when you receive your order</p></div></label>
                        <label className="ex-style-106"><input type="radio" name="payment_method" /><i className="fas fa-credit-card ex-style-107"></i><div><p className="ex-style-23">Stripe (Card Payment)</p><p className="ex-style-46">Visa, MasterCard, and international cards</p></div></label>
                        <label className="ex-style-106"><input type="radio" name="payment_method" /><i className="fas fa-credit-card ex-style-108"></i><div><p className="ex-style-23">SSLCOMMERZ (Online Payment)</p><p className="ex-style-46">bKash, Nagad, Visa, MasterCard & more</p></div></label>
                    </div>
                </div>
                <div className="card ex-style-34">
                    <h3 className="ex-style-58"><i className="fas fa-sticky-note ex-style-94"></i>Order Notes (Optional)</h3>
                    <textarea className="form-control" rows="3" placeholder="Any special instructions for delivery..."></textarea>
                </div>
            </div>
            <div>
                <div className="card ex-style-57">
                    <h3 className="ex-style-58">Order Summary</h3>
                    <div className="ex-style-109">
                        <div className="ex-style-110"><img src="/frontend/images/products/dummy/dummy_2.webp" alt="" className="ex-style-111" /><div className="ex-style-84"><p className="ex-style-112">Samsung Galaxy S24 Ultra</p><p className="ex-style-46">Qty: 1</p></div><p className="ex-style-23">৳89,999</p></div>
                        <div className="ex-style-110"><img src="/frontend/images/products/dummy/dummy_156.webp" alt="" className="ex-style-111" /><div className="ex-style-84"><p className="ex-style-112">Premium Cotton T-Shirt</p><p className="ex-style-46">Qty: 2</p></div><p className="ex-style-23">৳1,598</p></div>
                    </div>
                    <div className="ex-style-113">
                        <div className="ex-style-114"><span className="ex-style-65">Subtotal</span><span>৳91,597</span></div>
                        <div className="ex-style-114"><span className="ex-style-65">Shipping</span><span>FREE</span></div>
                        <div className="ex-style-66"><span className="ex-style-67">Total</span><span className="ex-style-68">৳91,597</span></div>
                    </div>
                    <button type="submit" className="btn btn-primary ex-style-69"><i className="fas fa-lock ex-style-115"></i> Place Order</button>
                    <p className="ex-style-116">By placing this order, you agree to our <a href="#" className="ex-style-117">Terms & Conditions</a></p>
                </div>
            </div>
        </div>
    </div>

    <footer className="footer">
        <div className="container">
            <div className="footer-grid">
                <div><a href="index.html" className="logo ex-style-8">Nova<span className="ex-style-9">Mart</span></a><p className="ex-style-10">Bangladesh's leading multi-vendor NovaMart platform. Shop with confidence and enjoy the best deals on thousands of products.</p><div className="social-links"><a href="#"><i className="fab fa-facebook-f"></i></a><a href="#"><i className="fab fa-instagram"></i></a><a href="#"><i className="fab fa-youtube"></i></a><a href="#"><i className="fab fa-twitter"></i></a></div></div>
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
