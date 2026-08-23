import React, { useEffect, useState } from 'react';
import products from './data/beauty.json';
import { Head } from '@inertiajs/react';

export default function CategoryBeauty() {
    const [visibleCount, setVisibleCount] = useState(10);

    const handleLoadMore = (e) => {
        e.preventDefault();
        setVisibleCount(prev => prev + 10);
    };

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
        <div className="ex-style-72">
            <aside className="ex-style-73">
                <div className="card ex-style-34">
                    <h3 className="ex-style-58">Filters</h3>
                    <form>
                        <div className="ex-style-19"><h4 className="ex-style-74">Categories</h4>
                            <div className="ex-style-75"><label className="ex-style-76"><input type="radio" name="category" className="ex-style-77" /><span className="ex-style-78">Electronics</span></label></div>
                            <div className="ex-style-75"><label className="ex-style-76"><input type="radio" name="category" className="ex-style-77" /><span className="ex-style-78">Fashion</span></label></div>
                            <div className="ex-style-75"><label className="ex-style-76"><input type="radio" name="category" className="ex-style-77" /><span className="ex-style-78">Home & Living</span></label></div>
                            <div className="ex-style-75"><label className="ex-style-76"><input type="radio" name="category" className="ex-style-77" /><span className="ex-style-78">Beauty & Health</span></label></div>
                            <div className="ex-style-75"><label className="ex-style-76"><input type="radio" name="category" className="ex-style-77" /><span className="ex-style-78">Sports & Outdoors</span></label></div>
                            <div className="ex-style-75"><label className="ex-style-76"><input type="radio" name="category" className="ex-style-77" /><span className="ex-style-78">Groceries</span></label></div>
                        </div>
                        <div className="ex-style-19"><h4 className="ex-style-74">Price Range</h4><div className="ex-style-79"><input type="number" placeholder="Min" className="ex-style-80" /><input type="number" placeholder="Max" className="ex-style-80" /></div></div>
                        <div className="ex-style-19"><h4 className="ex-style-74">Rating</h4>
                            <div className="ex-style-75"><label className="ex-style-76"><input type="radio" name="rating" className="ex-style-77" /><span className="ex-style-81"><i className="fas fa-star"></i><i className="fas fa-star"></i><i className="fas fa-star"></i><i className="fas fa-star"></i><i className="fas fa-star"></i></span><span className="ex-style-46">& up</span></label></div>
                            <div className="ex-style-75"><label className="ex-style-76"><input type="radio" name="rating" className="ex-style-77" /><span className="ex-style-81"><i className="fas fa-star"></i><i className="fas fa-star"></i><i className="fas fa-star"></i><i className="fas fa-star"></i><i className="far fa-star"></i></span><span className="ex-style-46">& up</span></label></div>
                        </div>
                        <button type="submit" className="btn btn-primary ex-style-82">Apply Filters</button>
                        <a href="#" className="ex-style-83">Clear All</a>
                    </form>
                </div>
            </aside>
            <div className="ex-style-84">
                <div className="ex-style-85"><div><h1 className="ex-style-86">All Products</h1><p className="ex-style-87">Showing {products.length} products</p></div><div className="ex-style-88"><span className="ex-style-89">Sort by:</span><select className="ex-style-90"><option>Newest</option><option>Price: Low to High</option><option>Price: High to Low</option><option>Most Popular</option><option>Best Rating</option></select></div></div>
                <div className="grid grid-4">
                    {products.slice(0, visibleCount).map((product) => (
                        <div key={product.id} className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                            <a href="product.html"><img src={product.image} alt={product.name} loading="lazy" /></a>
                            {product.oldPrice && <div className="badge">-{Math.round((product.oldPrice - product.price) / product.oldPrice * 100)}%</div>}
                            <div className="content">
                                <div className="vendor">{product.vendor}</div>
                                <h3><a href="product.html">{product.name}</a></h3>
                                <p className="desc">Premium quality product curated for you.</p>
                                <div className="price">
                                    <span className="current">৳{product.price}</span>
                                    {product.oldPrice && <span className="old">৳{product.oldPrice}</span>}
                                </div>
                                <div className="rating">
                                    <i className="fas fa-star ex-style-81"></i>
                                    <i className="fas fa-star ex-style-81"></i>
                                    <i className="fas fa-star ex-style-81"></i>
                                    <i className="fas fa-star ex-style-81"></i>
                                    <i className="fas fa-star ex-style-81"></i>
                                    <span>({product.reviews})</span>
                                </div>
                                <div className="actions">
                                    <button type="button" className="add-cart" onClick={(e) => { e.preventDefault(); e.stopPropagation(); window.addToCart(product.id); }}><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                    <button type="button" className="wishlist" onClick={(e) => { e.preventDefault(); e.stopPropagation(); window.toggleWishlist(product.id, e.currentTarget); }}><i className="fas fa-heart"></i></button>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
                {visibleCount < products.length && (
                    <div style={{ textAlign: 'center', marginTop: '30px' }}>
                        <button onClick={handleLoadMore} className="btn btn-primary" style={{ padding: '10px 30px', fontSize: '16px', borderRadius: '5px' }}>
                            Load More
                        </button>
                    </div>
                )}
                <div style={{display: 'none'}} className="ex-style-91"><div className="pagination"><div className="page-item active"><span className="page-link">1</span></div><div className="page-item"><a className="page-link" href="#">2</a></div><div className="page-item"><a className="page-link" href="#">3</a></div><div className="page-item"><a className="page-link" href="#">Next</a></div></div></div>
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
