import React, { useEffect } from 'react';
import { Head } from '@inertiajs/react';

export default function Cart() {
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
        <h1 className="ex-style-32">Shopping Cart</h1>
        <div className="ex-style-33">
            <div className="card ex-style-34">
                <table className="ex-style-35">
                    <thead><tr className="ex-style-36"><th className="ex-style-37">Product</th><th className="ex-style-38">Price</th><th className="ex-style-38">Quantity</th><th className="ex-style-39">Total</th><th className="ex-style-40"></th></tr></thead>
                    <tbody>
                        <tr className="ex-style-41">
                            <td className="ex-style-42"><div className="ex-style-43"><img src="/frontend/images/products/dummy/dummy_121.webp" alt="Samsung Galaxy S24" className="ex-style-44" /><div><h4 className="ex-style-45">Samsung Galaxy S24 Ultra 5G</h4><p className="ex-style-46">Color: Titanium Black | 256GB</p></div></div></td>
                            <td className="ex-style-47">৳89,999</td>
                            <td className="ex-style-48"><div className="ex-style-49"><button className="ex-style-50">-</button><span className="ex-style-51">1</span><button className="ex-style-50">+</button></div></td>
                            <td className="ex-style-52">৳89,999</td>
                            <td className="ex-style-53"><button className="ex-style-54"><i className="fas fa-trash-alt"></i></button></td>
                        </tr>
                        <tr className="ex-style-41">
                            <td className="ex-style-42"><div className="ex-style-43"><img src="/frontend/images/products/dummy/dummy_188.webp" alt="Cotton T-Shirt" className="ex-style-44" /><div><h4 className="ex-style-45">Premium Cotton T-Shirt</h4><p className="ex-style-46">Size: M | Color: Navy Blue</p></div></div></td>
                            <td className="ex-style-47">৳799</td>
                            <td className="ex-style-48"><div className="ex-style-49"><button className="ex-style-50">-</button><span className="ex-style-51">2</span><button className="ex-style-50">+</button></div></td>
                            <td className="ex-style-52">৳1,598</td>
                            <td className="ex-style-53"><button className="ex-style-54"><i className="fas fa-trash-alt"></i></button></td>
                        </tr>
                    </tbody>
                </table>
                <div className="ex-style-55"><a href="products.html" className="ex-style-56"><i className="fas fa-arrow-left"></i> Continue Shopping</a></div>
            </div>
            <div>
                <div className="card ex-style-57">
                    <h3 className="ex-style-58">Order Summary</h3>
                    <div className="ex-style-59"><form className="ex-style-60"><input type="text" placeholder="Enter coupon code" className="ex-style-61" /><button type="button" className="btn btn-outline ex-style-62">Apply</button></form></div>
                    <div className="ex-style-63">
                        <div className="ex-style-64"><span className="ex-style-65">Subtotal</span><span className="ex-style-18">৳91,597</span></div>
                        <div className="ex-style-64"><span className="ex-style-65">Shipping</span><span className="ex-style-65">Calculated at checkout</span></div>
                        <div className="ex-style-66"><span className="ex-style-67">Total</span><span className="ex-style-68">৳91,597</span></div>
                    </div>
                    <a href="checkout.html" className="btn btn-primary ex-style-69">Proceed to Checkout <i className="fas fa-arrow-right ex-style-70"></i></a>
                    <div className="ex-style-71"><p className="ex-style-46"><i className="fas fa-lock"></i> Secure checkout powered by SSL</p></div>
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
