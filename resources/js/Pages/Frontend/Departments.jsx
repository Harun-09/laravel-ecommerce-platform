import React, { useEffect } from 'react';
import { Head } from '@inertiajs/react';

export default function Departments() {
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
                <div className="ex-style-85"><div><h1 className="ex-style-86">All Products</h1><p className="ex-style-87">Showing 128 products</p></div><div className="ex-style-88"><span className="ex-style-89">Sort by:</span><select className="ex-style-90"><option>Newest</option><option>Price: Low to High</option><option>Price: High to Low</option><option>Most Popular</option><option>Best Rating</option></select></div></div>
                <div className="grid grid-4">

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/generated/premium_noise_cancelling_headphones.jpg" alt="Premium Noise-Cancelling Headphones" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Electronics Store</div>
                            <h3><a href="product.html">Premium Noise-Cancelling Headphones</a></h3>
                            <p className="desc">Premium quality product curated for you.</p>
                            <div className="price"><span className="current">৳8,999</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <span>(21)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(100)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(100,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/generated/classic_analog_watch.jpg" alt="Classic Analog Watch" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Store</div>
                            <h3><a href="product.html">Classic Analog Watch</a></h3>
                            <p className="desc">Premium quality product curated for you.</p>
                            <div className="price"><span className="current">৳4,500</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <span>(228)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(101)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(101,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/generated/ultraslim_business_laptop.jpg" alt="UltraSlim Business Laptop" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Electronics Store</div>
                            <h3><a href="product.html">UltraSlim Business Laptop</a></h3>
                            <p className="desc">Premium quality product curated for you.</p>
                            <div className="price"><span className="current">৳75,000</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <span>(281)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(102)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(102,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/generated/smart_watch_series_8.jpg" alt="Smart Watch Series 8" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Electronics Store</div>
                            <h3><a href="product.html">Smart Watch Series 8</a></h3>
                            <p className="desc">Premium quality product curated for you.</p>
                            <div className="price"><span className="current">৳35,000</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <span>(487)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(103)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(103,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/generated/smartphone_13_pro_max.jpg" alt="Smartphone 13 Pro Max" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Electronics Store</div>
                            <h3><a href="product.html">Smartphone 13 Pro Max</a></h3>
                            <p className="desc">Premium quality product curated for you.</p>
                            <div className="price"><span className="current">৳95,000</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <span>(402)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(104)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(104,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/generated/dslr_camera_4k.jpg" alt="DSLR Camera 4K" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Electronics Store</div>
                            <h3><a href="product.html">DSLR Camera 4K</a></h3>
                            <p className="desc">Premium quality product curated for you.</p>
                            <div className="price"><span className="current">৳45,000</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <span>(268)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(105)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(105,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/generated/red_running_sneakers.jpg" alt="Red Running Sneakers" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Store</div>
                            <h3><a href="product.html">Red Running Sneakers</a></h3>
                            <p className="desc">Premium quality product curated for you.</p>
                            <div className="price"><span className="current">৳2,500</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <span>(461)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(106)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(106,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/generated/classic_white_sneakers.jpg" alt="Classic White Sneakers" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Store</div>
                            <h3><a href="product.html">Classic White Sneakers</a></h3>
                            <p className="desc">Premium quality product curated for you.</p>
                            <div className="price"><span className="current">৳1,800</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <span>(136)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(107)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(107,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/generated/modern_living_room_sofa.jpg" alt="Modern Living Room Sofa" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Home & Living Store</div>
                            <h3><a href="product.html">Modern Living Room Sofa</a></h3>
                            <p className="desc">Premium quality product curated for you.</p>
                            <div className="price"><span className="current">৳25,000</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <span>(350)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(108)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(108,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/generated/luxury_fragrance_100ml.jpg" alt="Luxury Fragrance 100ml" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Beauty & Health Store</div>
                            <h3><a href="product.html">Luxury Fragrance 100ml</a></h3>
                            <p className="desc">Premium quality product curated for you.</p>
                            <div className="price"><span className="current">৳3,500</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <span>(105)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(109)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(109,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/generated/essential_white_t_shirt.jpg" alt="Essential White T-Shirt" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Store</div>
                            <h3><a href="product.html">Essential White T-Shirt</a></h3>
                            <p className="desc">Premium quality product curated for you.</p>
                            <div className="price"><span className="current">৳600</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <span>(86)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(110)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(110,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/generated/wireless_earbuds_pro.jpg" alt="Wireless Earbuds Pro" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Electronics Store</div>
                            <h3><a href="product.html">Wireless Earbuds Pro</a></h3>
                            <p className="desc">Premium quality product curated for you.</p>
                            <div className="price"><span className="current">৳5,500</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <span>(355)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(111)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(111,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/generated/designer_leather_handbag.jpg" alt="Designer Leather Handbag" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Store</div>
                            <h3><a href="product.html">Designer Leather Handbag</a></h3>
                            <p className="desc">Premium quality product curated for you.</p>
                            <div className="price"><span className="current">৳8,000</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <span>(33)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(112)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(112,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/generated/men_s_leather_jacket.jpg" alt="Men's Leather Jacket" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Store</div>
                            <h3><a href="product.html">Men's Leather Jacket</a></h3>
                            <p className="desc">Premium quality product curated for you.</p>
                            <div className="price"><span className="current">৳4,500</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <span>(53)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(113)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(113,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/generated/minimalist_kitchen_set.jpg" alt="Minimalist Kitchen Set" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Home & Living Store</div>
                            <h3><a href="product.html">Minimalist Kitchen Set</a></h3>
                            <p className="desc">Premium quality product curated for you.</p>
                            <div className="price"><span className="current">৳15,000</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <span>(379)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(114)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(114,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/generated/aesthetic_room_decor.jpg" alt="Aesthetic Room Decor" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Home & Living Store</div>
                            <h3><a href="product.html">Aesthetic Room Decor</a></h3>
                            <p className="desc">Premium quality product curated for you.</p>
                            <div className="price"><span className="current">৳1,200</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <span>(136)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(115)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(115,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/generated/organic_skincare_routine.jpg" alt="Organic Skincare Routine" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Beauty & Health Store</div>
                            <h3><a href="product.html">Organic Skincare Routine</a></h3>
                            <p className="desc">Premium quality product curated for you.</p>
                            <div className="price"><span className="current">৳2,200</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <span>(308)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(118)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(118,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/generated/minimalist_wrist_watch.jpg" alt="Minimalist Wrist Watch" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Store</div>
                            <h3><a href="product.html">Minimalist Wrist Watch</a></h3>
                            <p className="desc">Premium quality product curated for you.</p>
                            <div className="price"><span className="current">৳3,000</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <span>(335)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(119)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(119,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card"><a href="product.html"><img src="/frontend/images/products/dummy/dummy_148.webp" alt="Samsung Galaxy S24" /></a><div className="content"><div className="vendor">TechStore BD</div><h3><a href="product.html">Samsung Galaxy S24 Ultra 5G</a></h3><div className="price"><span className="current">৳89,999</span><span className="old">৳1,19,999</span></div><div className="rating"><i className="fas fa-star"></i><i className="fas fa-star"></i><i className="fas fa-star"></i><i className="fas fa-star"></i><i className="fas fa-star-half-alt"></i><span>(42)</span></div><div className="actions"><button type="button" className="add-cart"><i className="fas fa-shopping-cart"></i> Add to Cart</button><button type="button" className="wishlist"><i className="fas fa-heart"></i></button></div></div></div>
                    <div className="product-card"><a href="product.html"><img src="/frontend/images/products/dummy/dummy_47.webp" alt="Cotton T-Shirt" /></a><div className="content"><div className="vendor">Fashion Hub</div><h3><a href="product.html">Premium Cotton T-Shirt</a></h3><div className="price"><span className="current">৳799</span></div><div className="rating"><i className="fas fa-star"></i><i className="fas fa-star"></i><i className="fas fa-star"></i><i className="fas fa-star"></i><i className="far fa-star"></i><span>(28)</span></div><div className="actions"><button type="button" className="add-cart"><i className="fas fa-shopping-cart"></i> Add to Cart</button><button type="button" className="wishlist"><i className="fas fa-heart"></i></button></div></div></div>
                    <div className="product-card"><div className="badge">-30%</div><a href="product.html"><img src="/frontend/images/products/dummy/dummy_58.webp" alt="Rice Cooker" /></a><div className="content"><div className="vendor">Home Appliances</div><h3><a href="product.html">Smart Rice Cooker 1.8L</a></h3><div className="price"><span className="current">৳3,399</span><span className="old">৳4,999</span></div><div className="rating"><i className="fas fa-star"></i><i className="fas fa-star"></i><i className="fas fa-star"></i><i className="fas fa-star"></i><i className="fas fa-star"></i><span>(67)</span></div><div className="actions"><button type="button" className="add-cart"><i className="fas fa-shopping-cart"></i> Add to Cart</button><button type="button" className="wishlist"><i className="fas fa-heart"></i></button></div></div></div>
                    <div className="product-card"><a href="product.html"><img src="/frontend/images/products/dummy/dummy_99.webp" alt="Atomic Habits" /></a><div className="content"><div className="vendor">Book Corner</div><h3><a href="product.html">Atomic Habits by James Clear</a></h3><div className="price"><span className="current">৳450</span></div><div className="rating"><i className="fas fa-star"></i><i className="fas fa-star"></i><i className="fas fa-star"></i><i className="fas fa-star"></i><i className="fas fa-star-half-alt"></i><span>(15)</span></div><div className="actions"><button type="button" className="add-cart"><i className="fas fa-shopping-cart"></i> Add to Cart</button><button type="button" className="wishlist"><i className="fas fa-heart"></i></button></div></div></div>
                    <div className="product-card"><div className="badge">-25%</div><a href="product.html"><img src="/frontend/images/products/dummy/dummy_112.webp" alt="MacBook Pro" /></a><div className="content"><div className="vendor">TechStore BD</div><h3><a href="product.html">Apple MacBook Pro M3</a></h3><div className="price"><span className="current">৳1,89,999</span><span className="old">৳2,49,999</span></div><div className="rating"><i className="fas fa-star"></i><i className="fas fa-star"></i><i className="fas fa-star"></i><i className="fas fa-star"></i><i className="fas fa-star"></i><span>(120)</span></div><div className="actions"><button type="button" className="add-cart"><i className="fas fa-shopping-cart"></i> Add to Cart</button><button type="button" className="wishlist"><i className="fas fa-heart"></i></button></div></div></div>
                    <div className="product-card"><a href="product.html"><img src="/frontend/images/products/dummy/dummy_168.webp" alt="Denim Jeans" /></a><div className="content"><div className="vendor">Fashion Hub</div><h3><a href="product.html">Premium Denim Jeans</a></h3><div className="price"><span className="current">৳1,399</span></div><div className="rating"><i className="fas fa-star"></i><i className="fas fa-star"></i><i className="fas fa-star"></i><i className="fas fa-star"></i><i className="far fa-star"></i><span>(34)</span></div><div className="actions"><button type="button" className="add-cart"><i className="fas fa-shopping-cart"></i> Add to Cart</button><button type="button" className="wishlist"><i className="fas fa-heart"></i></button></div></div></div>
                    <div className="product-card"><a href="product.html"><img src="/frontend/images/products/dummy/dummy_50.webp" alt="Yoga Mat" /></a><div className="content"><div className="vendor">FitnessPro</div><h3><a href="product.html">Premium Yoga Mat Non-Slip</a></h3><div className="price"><span className="current">৳1,599</span></div><div className="rating"><i className="fas fa-star"></i><i className="fas fa-star"></i><i className="fas fa-star"></i><i className="fas fa-star"></i><i className="fas fa-star"></i><span>(245)</span></div><div className="actions"><button type="button" className="add-cart"><i className="fas fa-shopping-cart"></i> Add to Cart</button><button type="button" className="wishlist"><i className="fas fa-heart"></i></button></div></div></div>
                    <div className="product-card"><a href="product.html"><img src="/frontend/images/products/dummy/dummy_119.webp" alt="Vitamin C Serum" /></a><div className="content"><div className="vendor">Beauty Plus</div><h3><a href="product.html">Vitamin C Brightening Serum</a></h3><div className="price"><span className="current">৳1,299</span></div><div className="rating"><i className="fas fa-star"></i><i className="fas fa-star"></i><i className="fas fa-star"></i><i className="fas fa-star"></i><i className="fas fa-star-half-alt"></i><span>(56)</span></div><div className="actions"><button type="button" className="add-cart"><i className="fas fa-shopping-cart"></i> Add to Cart</button><button type="button" className="wishlist"><i className="fas fa-heart"></i></button></div></div></div>
                </div>
                <div className="ex-style-91"><div className="pagination"><div className="page-item active"><span className="page-link">1</span></div><div className="page-item"><a className="page-link" href="#">2</a></div><div className="page-item"><a className="page-link" href="#">3</a></div><div className="page-item"><a className="page-link" href="#">Next</a></div></div></div>
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
