import React, { useEffect } from 'react';
import { Head } from '@inertiajs/react';

export default function Product() {
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

    <div className="container section pdp">
        <nav className="pdp-breadcrumb"><a href="index.html">Home</a><span>/</span><a href="products.html">Products</a><span>/</span><a href="#">Electronics</a></nav>

        <div className="pdp-grid">
            <div className="pdp-main">
                <section className="card pdp-gallery">
                    <div className="thumbs">
                        <button type="button" className="thumb is-active"><img src="/frontend/images/products/dummy/dummy_9.webp" alt="Samsung Galaxy S24" /></button>
                        <button type="button" className="thumb"><img src="/frontend/images/products/dummy/dummy_102.webp" alt="Samsung Galaxy A55" /></button>
                        <button type="button" className="thumb"><img src="/frontend/images/products/dummy/dummy_21.webp" alt="OnePlus Nord 4" /></button>
                        <button type="button" className="thumb"><img src="/frontend/images/products/dummy/dummy_66.webp" alt="Xiaomi 14" /></button>
                    </div>
                    <div className="hero">
                        <span className="badge">-25%</span>
                        <img src="/frontend/images/products/dummy/dummy_191.webp" alt="Samsung Galaxy S24 Ultra" id="main-image" />
                    </div>
                </section>

                <section className="pdp-info">
                    <div className="store-row">
                        <a href="#">Shop all Samsung products</a><span>|</span><span>Sold by TechStore BD</span>
                        <button type="button" className="store-follow-btn">Follow</button>
                        <small>1,234 followers</small>
                    </div>
                    <h1>Samsung Galaxy S24 Ultra 5G 256GB Titanium Black</h1>
                    <div className="meta">
                        <div><i className="fas fa-star ex-style-155"></i><i className="fas fa-star ex-style-155"></i><i className="fas fa-star ex-style-155"></i><i className="fas fa-star ex-style-155"></i><i className="fas fa-star-half-alt ex-style-155"></i></div>
                        <span>4.5</span>
                        <button type="button" className="link-btn">42 reviews</button>
                        <button type="button" className="link-btn">8 questions</button>
                        <span>6 answered</span>
                    </div>
                    <p className="sku-line">SKU: <strong>SM-S928B-BLK-256</strong></p>
                    <p className="ex-style-175">Experience the future of mobile technology with Samsung Galaxy S24 Ultra. Featuring a revolutionary AI-powered camera system, S Pen integration, and the powerful Snapdragon 8 Gen 3 processor.</p>
                    <div className="promo-tags">
                        <span className="promo-tag">Secure checkout</span>
                        <span className="promo-tag">Fast shipping</span>
                        <span className="promo-tag">Authentic product</span>
                        <span className="promo-tag">Official warranty</span>
                    </div>

                    <div className="variants">
                        <div className="variant-group color-group">
                            <p>Color: <strong id="selected-color-node">Titanium Black</strong></p>
                            <div className="variant-options">
                                <button type="button" className="variant-btn sel ex-style-176"><span className="swatch-dot" aria-hidden="true"></span><span className="swatch-label">Titanium Black</span></button>
                                <button type="button" className="variant-btn ex-style-177"><span className="swatch-dot" aria-hidden="true"></span><span className="swatch-label">Titanium Gray</span></button>
                                <button type="button" className="variant-btn ex-style-178"><span className="swatch-dot" aria-hidden="true"></span><span className="swatch-label">Titanium Yellow</span></button>
                                <button type="button" className="variant-btn ex-style-179"><span className="swatch-dot" aria-hidden="true"></span><span className="swatch-label">Titanium Violet</span></button>
                            </div>
                        </div>
                        <div className="variant-group">
                            <p>Storage</p>
                            <div className="variant-options">
                                <button type="button" className="variant-btn">256GB</button>
                                <button type="button" className="variant-btn sel">512GB</button>
                                <button type="button" className="variant-btn">1TB</button>
                            </div>
                        </div>
                    </div>

                    <div className="quick-spec-grid">
                        <div className="quick-spec-item"><span>Brand</span><strong>Samsung</strong></div>
                        <div className="quick-spec-item"><span>Category</span><strong>Electronics</strong></div>
                        <div className="quick-spec-item"><span>SKU</span><strong>SM-S928B</strong></div>
                        <div className="quick-spec-item"><span>Weight</span><strong>232g</strong></div>
                        <div className="quick-spec-item"><span>Views</span><strong>12,456</strong></div>
                        <div className="quick-spec-item"><span>Sold</span><strong>312</strong></div>
                    </div>
                </section>
                <section className="card ai-assistant">
                    <p className="ai-kicker"><i className="fas fa-robot" aria-hidden="true"></i> Ask AI Assistant</p>
                    <div className="ai-suggestions">
                        <button type="button" className="ai-chip">What are the key features of this product?</button>
                        <button type="button" className="ai-chip">Is this product in stock and how fast is delivery?</button>
                        <button type="button" className="ai-chip">What should I check before buying this product?</button>
                    </div>
                    <div className="ai-ask-row">
                        <input type="text" id="ai-question-input" placeholder="Ask something else about this product" />
                        <button type="button" id="ai-ask-btn">Ask</button>
                    </div>
                    <div className="ai-answer hidden" id="ai-answer-box">
                        <p className="ai-answer-title">AI Answer</p>
                        <p id="ai-answer-text"></p>
                    </div>
                </section>
            </div>

            <aside className="card buy">
                <div><p className="price" id="price-node">৳89,999</p><p className="old" id="old-price-node">৳1,19,999</p><p className="save" id="save-node">Save ৳30,000</p></div>
                <div className="finance-box"><p><strong>৳7,500</strong>/month estimated payment (12 months)</p><p>or 4 interest-free payments of <strong>৳22,500</strong></p></div>
                <p className="stock">In stock (45)</p>
                <div className="policy-list">
                    <div className="policy-item"><span className="policy-icon"><i className="fas fa-truck"></i></span><span className="policy-copy"><strong>Free shipping</strong><small>Delivery: Jul 18 - Jul 22</small></span><span className="policy-arrow"><i className="fas fa-chevron-right"></i></span></div>
                    <a href="#" className="policy-item"><span className="policy-icon"><i className="fas fa-rotate-left"></i></span><span className="policy-copy"><strong>Return & refund policy</strong><small>7-day easy return on eligible products.</small></span><span className="policy-arrow"><i className="fas fa-chevron-right"></i></span></a>
                    <a href="#" className="policy-item"><span className="policy-icon"><i className="fas fa-shield-alt"></i></span><span className="policy-copy"><strong>Security & Privacy</strong><small>Protected checkout and encrypted payment.</small></span><span className="policy-arrow"><i className="fas fa-chevron-right"></i></span></a>
                </div>
                <div className="qty"><button>-</button><input type="number" value="1" min="1" /><button>+</button></div>
                <button type="button" className="btn btn-primary" id="add-btn">Add to Cart</button>
                <button type="button" className="btn btn-secondary" id="buy-btn">Buy Now</button>
                <button type="button" className="wish-btn"><i className="fas fa-heart"></i> Save</button>
                <div className="assist-actions">
                    <button type="button" className="assist-btn"><i className="far fa-clone"></i> Compare</button>
                    <button type="button" className="assist-btn"><i className="far fa-bell"></i> Price alert</button>
                    <button type="button" className="assist-btn"><i className="far fa-flag"></i> Report listing</button>
                </div>
                <div className="social-row">
                    <button type="button" className="social-btn"><i className="fas fa-share-alt"></i><span>Share</span></button>
                    <button type="button" className="social-btn"><i className="far fa-heart"></i><span>156</span></button>
                </div>
                <div className="seller-box">
                    <p className="seller-title">Sold by TechStore BD</p>
                    <p className="seller-rating"><i className="fas fa-star"></i> 4.8 (1,234 reviews)</p>
                    <p className="seller-meta">5,678 orders completed</p>
                    <p className="seller-meta">Ships from Dhaka, Bangladesh</p>
                </div>
            </aside>
        </div>

        <section className="card tabs">
            <div className="tab-head">
                <button type="button" className="tab-btn is-active">Description</button>
                <button type="button" className="tab-btn">Specifications</button>
                <button type="button" className="tab-btn">Q&amp;A (8)</button>
                <button type="button" className="tab-btn">Price History</button>
                <button type="button" className="tab-btn">Reviews (42)</button>
            </div>
            <div className="tab-body">
                <div className="tab-pane is-active">
                    <div className="desc-rich">
                        <section className="desc-block">
                            <h3>Product Overview</h3>
                            <p>Samsung Galaxy S24 Ultra is the ultimate smartphone for those who demand the best. Featuring a revolutionary AI-powered camera system with 200MP main sensor, S Pen integration, and the powerful Snapdragon 8 Gen 3 processor.</p>
                            <p>The 6.8-inch Dynamic AMOLED 2X display delivers stunning visuals with 120Hz adaptive refresh rate and 2600 nits peak brightness. Built with titanium frame for durability and premium feel.</p>
                        </section>
                        <section className="desc-block">
                            <h3>Key Highlights</h3>
                            <ul className="desc-bullets">
                                <li>200MP AI-powered camera system with Nightography</li>
                                <li>Snapdragon 8 Gen 3 processor for unmatched performance</li>
                                <li>6.8" Dynamic AMOLED 2X display, 120Hz</li>
                                <li>S Pen with Air Actions support</li>
                                <li>5000mAh battery with 45W super fast charging</li>
                                <li>Titanium frame, IP68 water resistance</li>
                            </ul>
                        </section>
                        <section className="desc-block">
                            <h3>Complete Product Details</h3>
                            <div className="desc-fact-grid">
                                <p>Product Name</p><strong>Samsung Galaxy S24 Ultra 5G</strong>
                                <p>Brand</p><strong>Samsung</strong>
                                <p>Category</p><strong>Electronics</strong>
                                <p>SKU</p><strong>SM-S928B-BLK-256</strong>
                                <p>Current Price</p><strong>৳89,999</strong>
                                <p>Regular Price</p><strong>৳1,19,999</strong>
                                <p>Discount</p><strong>25% OFF</strong>
                                <p>Availability</p><strong>In stock (45)</strong>
                                <p>Rating</p><strong>4.5 / 5</strong>
                                <p>Total Reviews</p><strong>42</strong>
                                <p>Total Views</p><strong>12,456</strong>
                                <p>Total Sold</p><strong>312</strong>
                                <p>Weight</p><strong>232g</strong>
                                <p>Delivery Window</p><strong>Jul 18 - Jul 22</strong>
                                <p>Seller</p><strong>TechStore BD</strong>
                                <p>Ships From</p><strong>Dhaka, Bangladesh</strong>
                            </div>
                        </section>
                        <section className="desc-block">
                            <h3>Shipping, Return, and Support</h3>
                            <ul className="desc-bullets">
                                <li>Estimated delivery window: Jul 18 - Jul 22.</li>
                                <li>Return and refund: 7-day return on eligible products as per store policy.</li>
                                <li>Checkout: secure payment flow with account-level order tracking.</li>
                                <li>Seller support: TechStore BD and support team handle product queries.</li>
                            </ul>
                        </section>
                    </div>
                </div>
                <div className="tab-pane"><div className="spec-grid"><p>Brand</p><strong>Samsung</strong><p>Model</p><strong>Galaxy S24 Ultra</strong><p>Processor</p><strong>Snapdragon 8 Gen 3</strong><p>RAM</p><strong>12GB</strong><p>Storage</p><strong>256GB</strong><p>Display</p><strong>6.8" Dynamic AMOLED 2X</strong><p>Camera</p><strong>200MP + 50MP + 12MP + 10MP</strong><p>Battery</p><strong>5000mAh</strong><p>OS</p><strong>Android 14, One UI 6.1</strong><p>Water Resistance</p><strong>IP68</strong></div></div>
                
                <div className="tab-pane" data-pane="qa">
                    <div className="qa-list">
                        <article className="qa-item">
                            <p className="qa-question"><strong>Q:</strong> Does it come with a charger?</p>
                            <p className="qa-meta">Asked by John D. on 10 Jul 2025</p>
                            <p className="qa-answer"><strong>A:</strong> No, Samsung no longer includes chargers in the box.</p>
                            <p className="qa-meta">Answered 11 Jul 2025</p>
                        </article>
                    </div>
                    <div className="qa-form-wrap">
                        <h3>Ask a Question</h3>
                        <form className="qa-form">
                            <div className="form-group">
                                <label htmlFor="question-input">Your Question</label>
                                <textarea id="question-input" name="question" className="form-control" rows="3" required></textarea>
                            </div>
                            <button type="submit" className="btn btn-primary">Submit Question</button>
                        </form>
                    </div>
                </div>
                <div className="tab-pane" data-pane="price-history">
                    <div className="price-history-wrap">
                        <table className="price-history-table">
                            <thead>
                                <tr><th>Date</th><th>Price</th><th>Compare Price</th><th>Changed By</th></tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>15 Jul 2025, 10:00 AM</td>
                                    <td>৳1,19,999 <span className="arrow">&rarr;</span> ৳89,999</td>
                                    <td>N/A <span className="arrow">&rarr;</span> ৳1,19,999</td>
                                    <td>Vendor</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div className="tab-pane">
                    <div className="review"><p><strong>Ahmed R.</strong> <span>15 Jul 2025</span> <span className="review-badge">Verified Purchase</span></p><p>Amazing phone! The camera quality is outstanding and the S Pen is very responsive.</p></div>
                    <div className="review"><p><strong>Sara K.</strong> <span>12 Jul 2025</span> <span className="review-badge">Verified Purchase</span></p><p>Best Android phone I've ever used. Battery life is incredible and the display is gorgeous.</p></div>
                    <div className="review"><p><strong>Karim U.</strong> <span>10 Jul 2025</span></p><p>Worth every penny. The AI features are game-changing.</p></div>
                </div>
            </div>
        </section>

        <section className="pdp-section">
            <div className="section-title-inline"><h2>Similar Products</h2><a href="products.html">Shop All Products</a></div>
            <div className="grid grid-4">

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_77.webp" alt="Modern L-Shape Fabric Sofa" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Home Store</div>
                            <h3><a href="product.html">Modern L-Shape Fabric Sofa</a></h3>
                            <p className="desc">High-quality premium product with guaranteed authenticity.</p>
                            <div className="price"><span className="current">৳25,000</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="far fa-star ex-style-148"></i>
                                <span>(43)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(4397)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(4397,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_151.webp" alt="Minimalist Wooden Coffee Table" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Home Store</div>
                            <h3><a href="product.html">Minimalist Wooden Coffee Table</a></h3>
                            <p className="desc">High-quality premium product with guaranteed authenticity.</p>
                            <div className="price"><span className="current">৳8,999</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="far fa-star ex-style-148"></i>
                                <span>(444)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(8635)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(8635,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/generated/ergonomic_office_chair.jpg" alt="Ergonomic Office Chair" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Home Store</div>
                            <h3><a href="product.html">Ergonomic Office Chair</a></h3>
                            <p className="desc">High-quality premium product with guaranteed authenticity.</p>
                            <div className="price"><span className="current">৳15,000</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i>
                                <span>(108)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(3254)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(3254,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/generated/smart_rice_cooker_1_8l.jpg" alt="Smart Rice Cooker 1.8L" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Kitchen Store</div>
                            <h3><a href="product.html">Smart Rice Cooker 1.8L</a></h3>
                            <p className="desc">High-quality premium product with guaranteed authenticity.</p>
                            <div className="price"><span className="current">৳3,399</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i>
                                <span>(363)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(1470)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(1470,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
            </div>
        </section>

        <section className="pdp-section">
            <div className="section-title"><h2>Products Related To This Item</h2></div>
            <div className="topic-pills">
                <span className="topic-pill is-active">All</span>
                <span className="topic-pill">Samsung</span>
                <span className="topic-pill">Electronics</span>
            </div>
            <div className="grid grid-4">

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_179.webp" alt="Luxury Eau de Parfum 100ml" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Beauty Store</div>
                            <h3><a href="product.html">Luxury Eau de Parfum 100ml</a></h3>
                            <p className="desc">High-quality premium product with guaranteed authenticity.</p>
                            <div className="price"><span className="current">৳3,500</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i>
                                <span>(118)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(5701)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(5701,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_86.webp" alt="Organic Skincare Value Set" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Beauty Store</div>
                            <h3><a href="product.html">Organic Skincare Value Set</a></h3>
                            <p className="desc">High-quality premium product with guaranteed authenticity.</p>
                            <div className="price"><span className="current">৳2,200</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i>
                                <span>(139)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(9884)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(9884,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_107.webp" alt="Aesthetic Ceramic Vase" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Home Store</div>
                            <h3><a href="product.html">Aesthetic Ceramic Vase</a></h3>
                            <p className="desc">High-quality premium product with guaranteed authenticity.</p>
                            <div className="price"><span className="current">৳1,200</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="far fa-star ex-style-148"></i>
                                <span>(123)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(5217)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(5217,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_60.webp" alt="LED Ring Light with Tripod" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Electronics Store</div>
                            <h3><a href="product.html">LED Ring Light with Tripod</a></h3>
                            <p className="desc">High-quality premium product with guaranteed authenticity.</p>
                            <div className="price"><span className="current">৳2,500</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="far fa-star ex-style-148"></i>
                                <span>(148)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(5048)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(5048,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
            </div>
        </section>

        <section className="pdp-section card compare-section">
            <div className="compare-head">
                <h2>Compare With Similar Products</h2>
                <a href="products.html" className="compare-cta">Compare similar products</a>
            </div>
            <div className="compare-table-wrap">
                <table className="compare-matrix">
                    <thead>
                        <tr>
                            <th className="compare-label-head">Products</th>
                            <th className="compare-product-head is-current">
                                <span className="compare-current-tag">CURRENTLY VIEWING</span>
                                <a href="product.html" className="compare-thumb"><img src="/frontend/images/products/dummy/dummy_61.webp" alt="Smartphone 1" /></a>
                                <p className="compare-name"><a href="product.html">Samsung Galaxy S24 Ultra</a></p>
                                <button type="button" className="compare-add-btn ex-style-180">Add to cart</button>
                                <span className="compare-seller-tag ex-style-181">Best Seller</span>
                            </th>
                            <th className="compare-product-head">
                                <a href="product.html" className="compare-thumb"><img src="/frontend/images/products/dummy/dummy_90.webp" alt="Smartphone 2" /></a>
                                <p className="compare-name"><a href="product.html">Xiaomi 14 Pro Max</a></p>
                                <button type="button" className="compare-add-btn ex-style-180">Add to cart</button>
                                <span className="compare-seller-tag ex-style-181">Best Seller</span>
                            </th>
                            <th className="compare-product-head">
                                <a href="product.html" className="compare-thumb"><img src="/frontend/images/products/dummy/dummy_10.webp" alt="Smartphone 3" /></a>
                                <p className="compare-name"><a href="product.html">Google Pixel 8 Pro</a></p>
                                <button type="button" className="compare-add-btn ex-style-180">Add to cart</button>
                                <span className="compare-seller-tag ex-style-181">Best Seller</span>
                            </th>
                            <th className="compare-product-head">
                                <a href="product.html" className="compare-thumb"><img src="/frontend/images/products/dummy/dummy_39.webp" alt="Smartphone 4" /></a>
                                <p className="compare-name"><a href="product.html">OnePlus 12 5G</a></p>
                                <button type="button" className="compare-add-btn ex-style-180">Add to cart</button>
                                <span className="compare-seller-tag ex-style-181">Best Seller</span>
                            </th>
                            <th className="compare-product-head">
                                <a href="product.html" className="compare-thumb"><img src="/frontend/images/products/dummy/dummy_70.webp" alt="Smartphone 5" /></a>
                                <p className="compare-name"><a href="product.html">Vivo X100 Pro</a></p>
                                <button type="button" className="compare-add-btn ex-style-180">Add to cart</button>
                                <span className="compare-seller-tag ex-style-181">Best Seller</span>
                            </th>
                            <th className="compare-product-head">
                                <a href="product.html" className="compare-thumb"><img src="/frontend/images/products/dummy/dummy_68.webp" alt="Smartphone 6" /></a>
                                <p className="compare-name"><a href="product.html">Oppo Find X7 Ultra</a></p>
                                <button type="button" className="compare-add-btn ex-style-180">Add to cart</button>
                                <span className="compare-seller-tag ex-style-181">Best Seller</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th className="compare-row-label">Price</th>
                            <td className="compare-cell">Tk 89,999</td>
                            <td className="compare-cell">Tk 75,499</td>
                            <td className="compare-cell">Tk 95,000</td>
                            <td className="compare-cell">Tk 68,900</td>
                            <td className="compare-cell">Tk 82,500</td>
                            <td className="compare-cell">Tk 79,999</td>
                        </tr>
                        <tr>
                            <th className="compare-row-label">Rating</th>
                            <td className="compare-cell"><i className="fas fa-star ex-style-155"></i><i className="fas fa-star ex-style-155"></i><i className="fas fa-star ex-style-155"></i><i className="fas fa-star ex-style-155"></i><i className="fas fa-star-half-alt ex-style-155"></i> (90)</td>
                            <td className="compare-cell"><i className="fas fa-star ex-style-155"></i><i className="fas fa-star ex-style-155"></i><i className="fas fa-star ex-style-155"></i><i className="fas fa-star ex-style-155"></i><i className="fas fa-star-half-alt ex-style-155"></i> (87)</td>
                            <td className="compare-cell"><i className="fas fa-star ex-style-155"></i><i className="fas fa-star ex-style-155"></i><i className="fas fa-star ex-style-155"></i><i className="fas fa-star ex-style-155"></i><i className="far fa-star ex-style-155"></i> (84)</td>
                            <td className="compare-cell"><i className="fas fa-star ex-style-155"></i><i className="fas fa-star ex-style-155"></i><i className="fas fa-star ex-style-155"></i><i className="fas fa-star ex-style-155"></i><i className="fas fa-star ex-style-155"></i> (81)</td>
                            <td className="compare-cell"><i className="fas fa-star ex-style-155"></i><i className="fas fa-star ex-style-155"></i><i className="fas fa-star ex-style-155"></i><i className="fas fa-star ex-style-155"></i><i className="fas fa-star-half-alt ex-style-155"></i> (78)</td>
                            <td className="compare-cell"><i className="fas fa-star ex-style-155"></i><i className="fas fa-star ex-style-155"></i><i className="fas fa-star ex-style-155"></i><i className="fas fa-star ex-style-155"></i><i className="far fa-star ex-style-155"></i> (75)</td>
                        </tr>
                        <tr>
                            <th className="compare-row-label">Sold By</th>
                            <td className="compare-cell"><a href="#" className="ex-style-182">Nova Electronics</a></td>
                            <td className="compare-cell"><a href="#" className="ex-style-182">Nova Electronics</a></td>
                            <td className="compare-cell"><a href="#" className="ex-style-182">Nova Electronics</a></td>
                            <td className="compare-cell"><a href="#" className="ex-style-182">Nova Electronics</a></td>
                            <td className="compare-cell"><a href="#" className="ex-style-182">Nova Electronics</a></td>
                            <td className="compare-cell"><a href="#" className="ex-style-182">Nova Electronics</a></td>
                        </tr>
                        <tr>
                            <th className="compare-row-label">Brand</th>
                            <td className="compare-cell">Samsung</td>
                            <td className="compare-cell">Xiaomi</td>
                            <td className="compare-cell">Google</td>
                            <td className="compare-cell">OnePlus</td>
                            <td className="compare-cell">Vivo</td>
                            <td className="compare-cell">Oppo</td>
                        </tr>
                        <tr>
                            <th className="compare-row-label">Series</th>
                            <td className="compare-cell">Galaxy S</td>
                            <td className="compare-cell">Xiaomi 14</td>
                            <td className="compare-cell">Pixel</td>
                            <td className="compare-cell">Number Series</td>
                            <td className="compare-cell">X Series</td>
                            <td className="compare-cell">Find X</td>
                        </tr>
                        <tr>
                            <th className="compare-row-label">GPU</th>
                            <td className="compare-cell">Adreno 750</td>
                            <td className="compare-cell">Adreno 750</td>
                            <td className="compare-cell">Immortalis-G715</td>
                            <td className="compare-cell">Adreno 750</td>
                            <td className="compare-cell">Immortalis-G720</td>
                            <td className="compare-cell">Adreno 750</td>
                        </tr>
                        <tr>
                            <th className="compare-row-label">DirectX</th>
                            <td className="compare-cell">N/A</td>
                            <td className="compare-cell">N/A</td>
                            <td className="compare-cell">N/A</td>
                            <td className="compare-cell">N/A</td>
                            <td className="compare-cell">N/A</td>
                            <td className="compare-cell">N/A</td>
                        </tr>
                        <tr>
                            <th className="compare-row-label">Model</th>
                            <td className="compare-cell">SM-S928B</td>
                            <td className="compare-cell">23116PN5BC</td>
                            <td className="compare-cell">GC3VE</td>
                            <td className="compare-cell">CPH2581</td>
                            <td className="compare-cell">V2308A</td>
                            <td className="compare-cell">PHY110</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section className="pdp-section card seller-more-section">
            <div className="section-title"><h2>More From This Seller</h2></div>
            <div className="seller-more-layout">
                <aside className="seller-more-sidebar">
                    <div className="seller-card">
                        <img src="/frontend/images/products/dummy/dummy_124.webp" alt="TechStore BD" />
                        <strong>TechStore BD</strong>
                    </div>
                    <div className="seller-cat-list">
                        <a href="products.html">Electronics</a>
                        <a href="products.html">Accessories</a>
                    </div>
                    <a href="products.html" className="seller-more-link">Click for More Products</a>
                </aside>
                <div className="seller-more-products grid grid-3">

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_88.webp" alt="LED Ring Light with Tripod" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Electronics Store</div>
                            <h3><a href="product.html">LED Ring Light with Tripod</a></h3>
                            <p className="desc">High-quality premium product with guaranteed authenticity.</p>
                            <div className="price"><span className="current">৳2,500</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="far fa-star ex-style-148"></i>
                                <span>(148)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(5048)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(5048,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_100.webp" alt="Philips Air Fryer XL" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Kitchen Store</div>
                            <h3><a href="product.html">Philips Air Fryer XL</a></h3>
                            <p className="desc">High-quality premium product with guaranteed authenticity.</p>
                            <div className="price"><span className="current">৳12,000</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i>
                                <span>(302)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(2819)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(2819,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_185.webp" alt="Atomic Habits Book Hardcover" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Books Store</div>
                            <h3><a href="product.html">Atomic Habits Book Hardcover</a></h3>
                            <p className="desc">High-quality premium product with guaranteed authenticity.</p>
                            <div className="price"><span className="current">৳450</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="far fa-star ex-style-148"></i>
                                <span>(216)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(8993)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(8993,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section className="pdp-section card warranty-section">
            <div className="section-title"><h2>Warranty & Returns</h2></div>
            <div className="warranty-grid">
                <article className="warranty-card">
                    <h3>Warranty</h3><p>Please contact the seller directly for warranty information.</p><a href="contact.html">Contact seller support</a>
                </article>
                <article className="warranty-card">
                    <h3>Return Policies</h3><p>Eligible products can be returned as per store policy.</p><a href="#">Read return policy</a>
                </article>
                <article className="warranty-card">
                    <h3>Manufacturer Contact</h3><p>Need brand documentation or support center details.</p><a href="products.html">View manufacturer products</a>
                </article>
            </div>
        </section>

        <section className="pdp-section card buying-options-section">
            <div className="section-title"><h2>More Buying Options</h2><p>2 options from ৳89,999</p></div>
            <div className="buying-options-wrap">
                <table className="buying-options-table">
                    <thead><tr><th>Condition</th><th>Delivery</th><th>Seller</th><th>Price + Shipping</th><th>Action</th></tr></thead>
                    <tbody>
                        <tr><td>New</td><td><p>Free shipping</p><small>Arrives in 3-7 days</small></td><td>TechStore BD</td><td>৳89,999</td><td><button type="button" className="buying-btn">Add to cart</button></td></tr>
                        <tr><td>New</td><td><p>Shipping may vary</p><small>Arrives in 4-9 days</small></td><td>GadgetWorld</td><td>৳92,000</td><td><a href="product.html" className="buying-btn">View item</a></td></tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section className="deals-signup">
            <div className="deals-copy">
                <p className="deals-kicker">Stay Updated</p>
                <h3>Get Price Drop Alerts</h3>
                <p>Never miss a deal. Get notified when the price drops.</p>
                <form className="deals-form"><input type="email" placeholder="Enter your email" /><button type="submit">Subscribe</button></form>
            </div>
            <div className="ex-style-183"><i className="fas fa-bell"></i></div>
        </section>
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
