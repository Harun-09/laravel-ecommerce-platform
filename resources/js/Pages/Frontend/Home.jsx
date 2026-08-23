import React, { useState, useEffect } from 'react';
import { Head, Link } from '@inertiajs/react';

export default function Home({ banners = [] }) {
    const [activeBanner, setActiveBanner] = useState(0);

    useEffect(() => {
        if (banners.length <= 1) return;
        
        const timer = setInterval(() => {
            setActiveBanner((prev) => (prev + 1) % banners.length);
        }, 6000);
        
        return () => clearInterval(timer);
    }, [banners]);

    const handlePrev = () => setActiveBanner((prev) => (prev - 1 + banners.length) % banners.length);
    const handleNext = () => setActiveBanner((prev) => (prev + 1) % banners.length);


    return (
        

        


<>
    <header className="header">
        <div className="header-top"><div className="container header-top-row"><div className="header-top-links"><a href="outlet.html">Best Buy Outlet</a><a href="business.html">Best Buy Business</a></div><div className="header-top-right"><details className="pref-switcher"><summary className="pref-trigger"><span className="pref-current">EN</span><span className="pref-current">BDT</span><i className="fas fa-angle-down"></i></summary><div className="pref-dropdown-menu"><p className="pref-section-title">Change Language</p><button className="pref-option active"><span className="pref-radio"><i className="fas fa-dot-circle"></i></span><span>English<small>- EN</small></span></button><button className="pref-option"><span className="pref-radio"><i className="far fa-circle"></i></span><span>Bangla<small>- BN</small></span></button><div className="pref-divider"></div><p className="pref-section-title">Change Currency</p><button className="pref-option active"><span className="pref-radio"><i className="fas fa-dot-circle"></i></span><span>৳ - BDT<small>- Bangladeshi Taka</small></span></button><button className="pref-option"><span className="pref-radio"><i className="far fa-circle"></i></span><span>$ - USD<small>- US Dollar</small></span></button></div></details><a href="tel:+8801701885707"><i className="fas fa-phone"></i> +8801701885707</a><a href="order-status.html">Order Status</a><a href="help.html">Help</a></div></div></div>
        <div className="header-main"><div className="container header-main-row"><div className="header-main-left"><a href="index.html" className="logo">Nova<span>Mart</span></a><a href="gift-ideas.html" className="menu-launcher"><i className="fas fa-bars"></i> Menu</a></div><form className="search-box"><input type="text" name="q" placeholder="Search NovaMart" autocomplete="off" /><button type="submit"><i className="fas fa-search"></i></button><div className="search-suggestions" hidden></div></form><div className="header-actions"><a href="#"><i className="fas fa-gift"></i><span>Gift Ideas</span></a><a href="login.html"><i className="fas fa-user"></i><span>Sign in</span></a><a href="wishlist.html"><i className="fas fa-heart"></i><span>Saved Items</span></a><a href="cart.html" className="cart-badge"><i className="fas fa-shopping-cart"></i><span id="cart-count">0</span><small>Cart</small></a></div></div></div>
        <div className="header-utility"><div className="container header-utility-row"><div className="header-utility-links"><a href="top-deals.html">Top Deals</a><a href="deal-of-the-day.html">Deal of the Day</a><a href="discover.html">Discover</a><a href="memberships.html">My NovaMart Memberships</a><a href="credit-cards.html">Credit Cards</a><a href="gift-cards.html">Gift Cards</a></div><div className="header-utility-links"><a href="recently-viewed.html">Recently Viewed</a></div></div></div>
    </header>

    <main className="main-content">
        {/*  Hero Banner  */}
        <section className="hero-banner-pick" id="hero-banner-pick" 
            onMouseEnter={() => {}} 
            onMouseLeave={() => {}}>
            <div className="hero-banner-pick__slider" data-hero-slider>
                {banners.map((banner, index) => (
                    <article key={banner.id} className={`hero-banner-pick__slide ${index === activeBanner ? 'is-active' : ''}`} style={{"--hero-slide-bg": banner.id % 2 === 0 ? "linear-gradient(120deg, #5f82e8 0%, #7054ce 100%)" : "linear-gradient(120deg, #77dede 0%, #7ccff8 100%)"}} data-hero-slide>
                        <a className="hero-banner-pick__link" href={banner.link || '#'}>
                            <div className="container hero-banner-pick__content">
                                <picture className="hero-banner-pick__media">
                                    <img src={banner.image_url} alt={banner.title || 'Promotional Banner'} draggable="false" loading={index === 0 ? "eager" : "lazy"} />
                                </picture>
                            </div>
                        </a>
                    </article>
                ))}
            </div>
            <button type="button" className="hero-banner-pick__nav hero-banner-pick__nav--prev" onClick={handlePrev} data-hero-prev><i className="fas fa-chevron-left ex-style-148"></i></button>
            <button type="button" className="hero-banner-pick__nav hero-banner-pick__nav--next" onClick={handleNext} data-hero-next><i className="fas fa-chevron-right ex-style-148"></i></button>
            <div className="hero-banner-pick__dots" data-hero-dots>
                {banners.map((banner, index) => (
                    <button key={index} type="button" className={`hero-banner-pick__dot ${index === activeBanner ? 'is-active' : ''}`} onClick={() => setActiveBanner(index)} data-hero-dot={index}></button>
                ))}
            </div>
        </section>

        {/*  Flash Sale  */}
        <section className="section ex-style-144">
            <div className="container">
                <div className="section-title ex-style-145"><div className="ex-style-43"><h2 className="ex-style-146"><i className="fas fa-bolt"></i> Flash Sale</h2><div className="ex-style-147">Ends in: <span id="flash-timer">05:32:18</span></div></div><a href="#" className="ex-style-146">View All</a></div>
                <div className="grid grid-5">

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
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0"><div className="badge">-25%</div><a href="product.html"><img src="/frontend/images/products/dummy/dummy_32.webp" alt="Samsung Galaxy S24 Ultra" loading="lazy" /></a><div className="content"><div className="vendor">TechStore BD</div><h3><a href="product.html">Samsung Galaxy S24 Ultra 5G 256GB</a></h3><p className="desc">Experience the future of mobile technology with AI-powered camera system and S Pen integration.</p><div className="price"><span className="current">৳89,999</span><span className="old">৳1,19,999</span></div><div className="rating"><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star-half-alt ex-style-81"></i><span>(42)</span></div><div className="actions"><button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(1)"><i className="fas fa-shopping-cart"></i> Add to Cart</button><button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(1,this)"><i className="fas fa-heart"></i></button></div></div></div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0"><div className="badge">-30%</div><a href="product.html"><img src="/frontend/images/products/generated/premium_denim_jeans.jpg" alt="Premium Denim Jeans" loading="lazy" /></a><div className="content"><div className="vendor">Fashion Hub</div><h3><a href="product.html">Premium Denim Jeans Collection</a></h3><p className="desc">Classic fit denim jeans crafted from premium cotton for all-day comfort and style.</p><div className="price"><span className="current">৳1,399</span><span className="old">৳1,999</span></div><div className="rating"><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="far fa-star ex-style-148"></i><span>(28)</span></div><div className="actions"><button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(2)"><i className="fas fa-shopping-cart"></i> Add to Cart</button><button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(2,this)"><i className="fas fa-heart"></i></button></div></div></div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0"><div className="badge">-15%</div><a href="product.html"><img src="/frontend/images/products/dummy/dummy_135.webp" alt="Smart Rice Cooker" loading="lazy" /></a><div className="content"><div className="vendor">Home Appliances</div><h3><a href="product.html">Smart Rice Cooker 1.8L Multi-Function</a></h3><p className="desc">Multi-functional rice cooker with fuzzy logic technology for perfect rice every time.</p><div className="price"><span className="current">৳3,399</span><span className="old">৳3,999</span></div><div className="rating"><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><span>(67)</span></div><div className="actions"><button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(3)"><i className="fas fa-shopping-cart"></i> Add to Cart</button><button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(3,this)"><i className="fas fa-heart"></i></button></div></div></div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0"><a href="product.html"><img src="/frontend/images/products/dummy/dummy_113.webp" alt="Atomic Habits" loading="lazy" /></a><div className="content"><div className="vendor">Book Corner</div><h3><a href="product.html">Atomic Habits by James Clear</a></h3><p className="desc">An easy and proven way to build good habits and break bad ones.</p><div className="price"><span className="current">৳450</span></div><div className="rating"><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star-half-alt ex-style-81"></i><span>(15)</span></div><div className="actions"><button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(4)"><i className="fas fa-shopping-cart"></i> Add to Cart</button><button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(4,this)"><i className="fas fa-heart"></i></button></div></div></div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0"><div className="badge">-40%</div><a href="product.html"><img src="/frontend/images/products/dummy/dummy_157.webp" alt="Xiaomi 14" loading="lazy" /></a><div className="content"><div className="vendor">Gadget World</div><h3><a href="product.html">Xiaomi 14 Flagship Smartphone</a></h3><p className="desc">Leica optics, Snapdragon 8 Gen 3, and premium titanium design.</p><div className="price"><span className="current">৳49,999</span><span className="old">৳84,999</span></div><div className="rating"><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="far fa-star ex-style-148"></i><span>(89)</span></div><div className="actions"><button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(5)"><i className="fas fa-shopping-cart"></i> Add to Cart</button><button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(5,this)"><i className="fas fa-heart"></i></button></div></div></div>
                </div>
            </div>
        </section>

        {/*  Categories  */}
        <section className="section ex-style-149">
            <div className="container">
                <div className="section-title"><h2>Shop By Category</h2><a href="products.html">View All <i className="fas fa-arrow-right"></i></a></div>
                <div className="ex-style-150">
                    <a href="#" className="ex-style-151"><div className="ex-style-152"><img src="/frontend/images/categories/electronics.png" alt="Electronics" className="ex-style-153" /></div><span className="ex-style-154">Electronics</span></a>
                    <a href="#" className="ex-style-151"><div className="ex-style-152"><img src="/frontend/images/categories/fashion.png" alt="Fashion" className="ex-style-153" /></div><span className="ex-style-154">Fashion</span></a>
                    <a href="#" className="ex-style-151"><div className="ex-style-152"><img src="/frontend/images/categories/home-living.png" alt="Home & Living" className="ex-style-153" /></div><span className="ex-style-154">Home & Living</span></a>
                    <a href="#" className="ex-style-151"><div className="ex-style-152"><img src="/frontend/images/categories/beauty-health.png" alt="Beauty & Health" className="ex-style-153" /></div><span className="ex-style-154">Beauty</span></a>
                    <a href="#" className="ex-style-151"><div className="ex-style-152"><img src="/frontend/images/categories/sports-outdoors.png" alt="Sports & Outdoors" className="ex-style-153" /></div><span className="ex-style-154">Sports</span></a>
                    <a href="#" className="ex-style-151"><div className="ex-style-152"><img src="/frontend/images/categories/groceries.png" alt="Groceries" className="ex-style-153" /></div><span className="ex-style-154">Groceries</span></a>
                    <a href="#" className="ex-style-151"><div className="ex-style-152"><img src="/frontend/images/categories/books-stationery.png" alt="Books & Stationery" className="ex-style-153" /></div><span className="ex-style-154">Books</span></a>
                </div>
            </div>
        </section>

        {/*  Showcase: Electronics  */}
        <section className="section home-showcase-group">
            <div className="container">
                <div className="section-title"><h2>Your Go-to Destination for Electronics!</h2><a href="#">View All <i className="fas fa-arrow-right"></i></a></div>
                <div className="home-showcase-grid">
                    <a href="#" className="home-showcase-card"><img src="/frontend/images/products/dummy/dummy_92.webp" alt="Electronics & Appliances" loading="lazy" /><div className="home-showcase-card__body"><span className="home-showcase-card__subtitle">Official Warranty | EMI with 33 Banks</span><span className="home-showcase-card__title">Electronics & Appliances</span></div></a>
                    <a href="#" className="home-showcase-card"><img src="/frontend/images/products/dummy/dummy_34.webp" alt="Official Smartphones" loading="lazy" /><div className="home-showcase-card__body"><span className="home-showcase-card__subtitle">Display Insurance | Fast Delivery</span><span className="home-showcase-card__title">Official Smartphones</span></div></a>
                    <a href="#" className="home-showcase-card"><img src="/frontend/images/products/dummy/dummy_159.webp" alt="Gadgets & Accessories" loading="lazy" /><div className="home-showcase-card__body"><span className="home-showcase-card__subtitle">Brand Warranty | Same-day Delivery</span><span className="home-showcase-card__title">Gadgets & Accessories</span></div></a>
                    <a href="#" className="home-showcase-card"><img src="/frontend/images/products/dummy/dummy_189.webp" alt="Kitchen Appliances" loading="lazy" /><div className="home-showcase-card__body"><span className="home-showcase-card__subtitle">Top Brands | Best Prices</span><span className="home-showcase-card__title">Kitchen Appliances</span></div></a>
                    <a href="#" className="home-showcase-card"><img src="/frontend/images/products/dummy/dummy_145.webp" alt="Lifestyle Essentials" loading="lazy" /><div className="home-showcase-card__body"><span className="home-showcase-card__subtitle">Free Delivery | Same-day Delivery</span><span className="home-showcase-card__title">Lifestyle Essentials</span></div></a>
                </div>
            </div>
        </section>

        {/*  Featured Products  */}
        <section className="section">
            <div className="container">
                <div className="section-title"><h2><i className="fas fa-star ex-style-155"></i> Featured Products</h2><a href="#">View All <i className="fas fa-arrow-right"></i></a></div>
                <div className="grid grid-5">

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
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0"><a href="product.html"><img src="/frontend/images/products/generated/apple_macbook_pro_m3.jpg" alt="Apple MacBook Pro M3" loading="lazy" /></a><div className="content"><div className="vendor">TechStore BD</div><h3><a href="product.html">Apple MacBook Pro M3 Chip</a></h3><p className="desc">Powerful M3 chip, stunning Liquid Retina XDR display, up to 22 hours battery life.</p><div className="price"><span className="current">৳1,89,999</span></div><div className="rating"><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><span>(120)</span></div><div className="actions"><button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(6)"><i className="fas fa-shopping-cart"></i> Add to Cart</button><button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(6,this)"><i className="fas fa-heart"></i></button></div></div></div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0"><div className="badge">-20%</div><a href="product.html"><img src="/frontend/images/products/dummy/dummy_23.webp" alt="iPhone 15 Pro Max" loading="lazy" /></a><div className="content"><div className="vendor">TechZone</div><h3><a href="product.html">Apple iPhone 15 Pro Max</a></h3><p className="desc">Titanium design, A17 Pro chip, and the most powerful iPhone camera system ever.</p><div className="price"><span className="current">৳1,34,999</span><span className="old">৳1,69,999</span></div><div className="rating"><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star-half-alt ex-style-81"></i><span>(56)</span></div><div className="actions"><button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(7)"><i className="fas fa-shopping-cart"></i> Add to Cart</button><button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(7,this)"><i className="fas fa-heart"></i></button></div></div></div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0"><a href="product.html"><img src="/frontend/images/products/dummy/dummy_53.webp" alt="Denim Jacket" loading="lazy" /></a><div className="content"><div className="vendor">StyleShop</div><h3><a href="product.html">Everyday Denim Jacket</a></h3><p className="desc">Classic denim jacket with modern fit, perfect for layering in any season.</p><div className="price"><span className="current">৳2,499</span></div><div className="rating"><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="far fa-star ex-style-148"></i><span>(34)</span></div><div className="actions"><button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(8)"><i className="fas fa-shopping-cart"></i> Add to Cart</button><button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(8,this)"><i className="fas fa-heart"></i></button></div></div></div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0"><div className="badge">-35%</div><a href="product.html"><img src="/frontend/images/products/dummy/dummy_181.webp" alt="Dumbbell Set" loading="lazy" /></a><div className="content"><div className="vendor">FitnessPro</div><h3><a href="product.html">Adjustable Dumbbell Set 20kg</a></h3><p className="desc">Space-saving adjustable dumbbells with quick-lock mechanism for home gym.</p><div className="price"><span className="current">৳4,549</span><span className="old">৳6,999</span></div><div className="rating"><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><span>(78)</span></div><div className="actions"><button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(9)"><i className="fas fa-shopping-cart"></i> Add to Cart</button><button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(9,this)"><i className="fas fa-heart"></i></button></div></div></div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0"><a href="product.html"><img src="/frontend/images/products/dummy/dummy_164.webp" alt="Pure Honey" loading="lazy" /></a><div className="content"><div className="vendor">EcoLife</div><h3><a href="product.html">Pure Honey Jar 500g Organic</a></h3><p className="desc">100% pure organic honey sourced directly from local beekeepers in Bangladesh.</p><div className="price"><span className="current">৳599</span></div><div className="rating"><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star-half-alt ex-style-81"></i><span>(45)</span></div><div className="actions"><button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(10)"><i className="fas fa-shopping-cart"></i> Add to Cart</button><button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(10,this)"><i className="fas fa-heart"></i></button></div></div></div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0"><a href="product.html"><img src="/frontend/images/products/dummy/dummy_153.webp" alt="Dell XPS 15" loading="lazy" /></a><div className="content"><div className="vendor">TechStore BD</div><h3><a href="product.html">Dell XPS 15 Laptop 2024</a></h3><p className="desc">InfinityEdge display, 13th Gen Intel Core, and premium build quality.</p><div className="price"><span className="current">৳1,29,999</span></div><div className="rating"><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><span>(95)</span></div><div className="actions"><button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(11)"><i className="fas fa-shopping-cart"></i> Add to Cart</button><button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(11,this)"><i className="fas fa-heart"></i></button></div></div></div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0"><a href="product.html"><img src="/frontend/images/products/dummy/dummy_144.webp" alt="L-Shape Sofa" loading="lazy" /></a><div className="content"><div className="vendor">Home Decor</div><h3><a href="product.html">Modern L-Shape Sofa Set</a></h3><p className="desc">Spacious L-shaped sofa with premium fabric upholstery and solid frame.</p><div className="price"><span className="current">৳24,999</span></div><div className="rating"><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="far fa-star ex-style-148"></i><span>(23)</span></div><div className="actions"><button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(12)"><i className="fas fa-shopping-cart"></i> Add to Cart</button><button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(12,this)"><i className="fas fa-heart"></i></button></div></div></div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0"><div className="badge">-20%</div><a href="product.html"><img src="/frontend/images/products/dummy/dummy_18.webp" alt="Vitamin C Serum" loading="lazy" /></a><div className="content"><div className="vendor">Beauty Plus</div><h3><a href="product.html">Vitamin C Brightening Serum</a></h3><p className="desc">Advanced formula with 20% Vitamin C for radiant, youthful skin.</p><div className="price"><span className="current">৳1,299</span><span className="old">৳1,599</span></div><div className="rating"><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star-half-alt ex-style-81"></i><span>(56)</span></div><div className="actions"><button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(13)"><i className="fas fa-shopping-cart"></i> Add to Cart</button><button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(13,this)"><i className="fas fa-heart"></i></button></div></div></div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0"><a href="product.html"><img src="/frontend/images/products/dummy/dummy_78.webp" alt="Yoga Mat" loading="lazy" /></a><div className="content"><div className="vendor">FitnessPro</div><h3><a href="product.html">Premium Yoga Mat Non-Slip</a></h3><p className="desc">Extra thick, eco-friendly yoga mat with non-slip surface for safe practice.</p><div className="price"><span className="current">৳1,599</span></div><div className="rating"><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><span>(245)</span></div><div className="actions"><button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(14)"><i className="fas fa-shopping-cart"></i> Add to Cart</button><button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(14,this)"><i className="fas fa-heart"></i></button></div></div></div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0"><div className="badge">-10%</div><a href="product.html"><img src="/frontend/images/products/dummy/dummy_81.webp" alt="Water Flask" loading="lazy" /></a><div className="content"><div className="vendor">EcoLife</div><h3><a href="product.html">Thermal Water Flask 1L</a></h3><p className="desc">Double-wall vacuum insulated flask keeps drinks hot/cold for 24 hours.</p><div className="price"><span className="current">৳899</span><span className="old">৳999</span></div><div className="rating"><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star-half-alt ex-style-81"></i><span>(156)</span></div><div className="actions"><button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(15)"><i className="fas fa-shopping-cart"></i> Add to Cart</button><button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(15,this)"><i className="fas fa-heart"></i></button></div></div></div>
                </div>
            </div>
        </section>

        {/*  Services  */}
        <section className="ex-style-156">
            <div className="container">
                <div className="ex-style-157">
                    <div className="ex-style-158"><div className="ex-style-159"><i className="fas fa-truck"></i></div><div><h4 className="ex-style-45">Fast Delivery</h4><p className="ex-style-46">Delivery within 2-3 days</p></div></div>
                    <div className="ex-style-158"><div className="ex-style-160"><i className="fas fa-shield-alt"></i></div><div><h4 className="ex-style-45">Secure Payment</h4><p className="ex-style-46">100% secure payment</p></div></div>
                    <div className="ex-style-158"><div className="ex-style-161"><i className="fas fa-undo"></i></div><div><h4 className="ex-style-45">Easy Returns</h4><p className="ex-style-46">7 days return policy</p></div></div>
                    <div className="ex-style-158"><div className="ex-style-162"><i className="fas fa-headset"></i></div><div><h4 className="ex-style-45">24/7 Support</h4><p className="ex-style-46">Dedicated customer support</p></div></div>
                </div>
            </div>
        </section>

        {/*  Showcase: Home & Lifestyle  */}
        <section className="section home-showcase-group">
            <div className="container">
                <div className="section-title"><h2>Upgrade Your Home & Lifestyle Today!</h2><a href="#">View All <i className="fas fa-arrow-right"></i></a></div>
                <div className="home-showcase-grid">
                    <a href="#" className="home-showcase-card"><img src="/frontend/images/products/dummy/dummy_44.webp" alt="Laptops & Computers" loading="lazy" /><div className="home-showcase-card__body"><span className="home-showcase-card__subtitle">Official Warranty | Fast Delivery</span><span className="home-showcase-card__title">Laptops & Computers</span></div></a>
                    <a href="#" className="home-showcase-card"><img src="/frontend/images/products/dummy/dummy_0.webp" alt="Furniture & Living" loading="lazy" /><div className="home-showcase-card__body"><span className="home-showcase-card__subtitle">Durable Build | Smart Design</span><span className="home-showcase-card__title">Furniture & Living</span></div></a>
                    <a href="#" className="home-showcase-card"><img src="/frontend/images/products/dummy/dummy_149.webp" alt="Kitchen Essentials" loading="lazy" /><div className="home-showcase-card__body"><span className="home-showcase-card__subtitle">Free Delivery | Easy EMI</span><span className="home-showcase-card__title">Kitchen Essentials</span></div></a>
                    <a href="#" className="home-showcase-card"><img src="/frontend/images/products/dummy/dummy_94.webp" alt="Beauty & Skincare" loading="lazy" /><div className="home-showcase-card__body"><span className="home-showcase-card__subtitle">Authentic Products | Fast Delivery</span><span className="home-showcase-card__title">Beauty & Skincare</span></div></a>
                    <a href="#" className="home-showcase-card"><img src="/frontend/images/products/dummy/dummy_95.webp" alt="Fitness Essentials" loading="lazy" /><div className="home-showcase-card__body"><span className="home-showcase-card__subtitle">Top Picks | Best Performance</span><span className="home-showcase-card__title">Fitness Essentials</span></div></a>
                </div>
            </div>
        </section>

        {/*  New Arrivals  */}
        <section className="section">
            <div className="container">
                <div className="section-title"><h2><i className="fas fa-sparkles ex-style-117"></i> New Arrivals</h2><a href="#">View All <i className="fas fa-arrow-right"></i></a></div>
                <div className="grid grid-5">
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0"><a href="product.html"><img src="/frontend/images/products/dummy/dummy_19.webp" alt="Redmi Note 13 Pro" loading="lazy" /></a><div className="content"><div className="vendor">TechStore BD</div><h3><a href="product.html">Xiaomi Redmi Note 13 Pro</a></h3><p className="desc">200MP camera, AMOLED display, and 5100mAh battery for all-day use.</p><div className="price"><span className="current">৳28,999</span></div><div className="actions"><button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(16)"><i className="fas fa-shopping-cart"></i> Add to Cart</button><button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(16,this)"><i className="fas fa-heart"></i></button></div></div></div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0"><a href="product.html"><img src="/frontend/images/products/dummy/dummy_108.webp" alt="Cotton T-Shirt" loading="lazy" /></a><div className="content"><div className="vendor">Fashion Hub</div><h3><a href="product.html">Premium Cotton T-Shirt</a></h3><p className="desc">Soft, breathable cotton t-shirt with a relaxed fit for everyday comfort.</p><div className="price"><span className="current">৳799</span></div><div className="actions"><button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(17)"><i className="fas fa-shopping-cart"></i> Add to Cart</button><button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(17,this)"><i className="fas fa-heart"></i></button></div></div></div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0"><a href="product.html"><img src="/frontend/images/products/dummy/dummy_28.webp" alt="Coffee Table" loading="lazy" /></a><div className="content"><div className="vendor">Home Decor</div><h3><a href="product.html">Modern Coffee Table</a></h3><p className="desc">Sleek minimalist coffee table with solid wood top and metal legs.</p><div className="price"><span className="current">৳8,999</span></div><div className="actions"><button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(18)"><i className="fas fa-shopping-cart"></i> Add to Cart</button><button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(18,this)"><i className="fas fa-heart"></i></button></div></div></div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0"><a href="product.html"><img src="/frontend/images/products/dummy/dummy_4.webp" alt="Face Wash" loading="lazy" /></a><div className="content"><div className="vendor">Beauty Plus</div><h3><a href="product.html">Hydrating Aloe Vera Face Wash</a></h3><p className="desc">Gentle daily cleanser with aloe vera and niacinamide for fresh skin.</p><div className="price"><span className="current">৳699</span></div><div className="actions"><button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(19)"><i className="fas fa-shopping-cart"></i> Add to Cart</button><button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(19,this)"><i className="fas fa-heart"></i></button></div></div></div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0"><a href="product.html"><img src="/frontend/images/products/dummy/dummy_128.webp" alt="Resistance Bands" loading="lazy" /></a><div className="content"><div className="vendor">FitnessPro</div><h3><a href="product.html">Resistance Band Kit Set</a></h3><p className="desc">5-level resistance bands for strength training and physical therapy.</p><div className="price"><span className="current">৳899</span></div><div className="actions"><button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(20)"><i className="fas fa-shopping-cart"></i> Add to Cart</button><button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(20,this)"><i className="fas fa-heart"></i></button></div></div></div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0"><a href="product.html"><img src="/frontend/images/products/dummy/dummy_116.webp" alt="Galaxy A55" loading="lazy" /></a><div className="content"><div className="vendor">TechZone</div><h3><a href="product.html">Samsung Galaxy A55 5G</a></h3><p className="desc">Premium design with AMOLED display and 50MP OIS camera.</p><div className="price"><span className="current">৳35,999</span></div><div className="actions"><button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(21)"><i className="fas fa-shopping-cart"></i> Add to Cart</button><button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(21,this)"><i className="fas fa-heart"></i></button></div></div></div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0"><a href="product.html"><img src="/frontend/images/products/dummy/dummy_37.webp" alt="Oats" loading="lazy" /></a><div className="content"><div className="vendor">EcoLife</div><h3><a href="product.html">Breakfast Oats 1kg Premium</a></h3><p className="desc">Whole grain oats for a healthy and nutritious start to your day.</p><div className="price"><span className="current">৳350</span></div><div className="actions"><button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(22)"><i className="fas fa-shopping-cart"></i> Add to Cart</button><button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(22,this)"><i className="fas fa-heart"></i></button></div></div></div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0"><a href="product.html"><img src="/frontend/images/products/dummy/dummy_45.webp" alt="Electric Kettle" loading="lazy" /></a><div className="content"><div className="vendor">Home Appliances</div><h3><a href="product.html">Electric Kettle 1.8L Premium</a></h3><p className="desc">Fast boiling with auto shut-off and boil-dry protection safety.</p><div className="price"><span className="current">৳1,299</span></div><div className="actions"><button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(23)"><i className="fas fa-shopping-cart"></i> Add to Cart</button><button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(23,this)"><i className="fas fa-heart"></i></button></div></div></div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0"><a href="product.html"><img src="/frontend/images/products/dummy/dummy_48.webp" alt="Notebook" loading="lazy" /></a><div className="content"><div className="vendor">Stationery Plus</div><h3><a href="product.html">Premium A4 Notebook Set</a></h3><p className="desc">Durable notebook set with premium quality paper for writing.</p><div className="price"><span className="current">৳799</span></div><div className="actions"><button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(24)"><i className="fas fa-shopping-cart"></i> Add to Cart</button><button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(24,this)"><i className="fas fa-heart"></i></button></div></div></div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0"><a href="product.html"><img src="/frontend/images/products/dummy/dummy_114.webp" alt="Winter Hoodie" loading="lazy" /></a><div className="content"><div className="vendor">Fashion Hub</div><h3><a href="product.html">Winter Hoodie Premium</a></h3><p className="desc">Warm fleece-lined hoodie with kangaroo pocket and adjustable hood.</p><div className="price"><span className="current">৳1,899</span></div><div className="actions"><button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(25)"><i className="fas fa-shopping-cart"></i> Add to Cart</button><button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(25,this)"><i className="fas fa-heart"></i></button></div></div></div>
                </div>
            </div>
        </section>

        {/*  Showcase: Smartphones  */}
        <section className="section home-showcase-group">
            <div className="container">
                <div className="section-title"><h2>Buy Official Smartphones with Brand Warranty!</h2><a href="#">View All <i className="fas fa-arrow-right"></i></a></div>
                <div className="home-showcase-grid">
                    <a href="#" className="home-showcase-card"><img src="/frontend/images/products/dummy/dummy_183.webp" alt="OnePlus" loading="lazy" /><div className="home-showcase-card__body"><span className="home-showcase-card__subtitle">Lifetime Display Warranty (Green Line)</span><span className="home-showcase-card__title">OnePlus Smartphones</span></div></a>
                    <a href="#" className="home-showcase-card"><img src="/frontend/images/products/dummy/dummy_115.webp" alt="Samsung" loading="lazy" /><div className="home-showcase-card__body"><span className="home-showcase-card__subtitle">Easy EMI | Display Insurance</span><span className="home-showcase-card__title">Samsung Smartphones</span></div></a>
                    <a href="#" className="home-showcase-card"><img src="/frontend/images/products/dummy/dummy_127.webp" alt="Xiaomi" loading="lazy" /><div className="home-showcase-card__body"><span className="home-showcase-card__subtitle">Cash/Card on Delivery</span><span className="home-showcase-card__title">Xiaomi Smartphones</span></div></a>
                    <a href="#" className="home-showcase-card"><img src="/frontend/images/products/dummy/dummy_80.webp" alt="Apple" loading="lazy" /><div className="home-showcase-card__body"><span className="home-showcase-card__subtitle">Best Prices | Fast Delivery</span><span className="home-showcase-card__title">Apple & Premium Phones</span></div></a>
                    <a href="#" className="home-showcase-card"><img src="/frontend/images/products/dummy/dummy_171.webp" alt="Android" loading="lazy" /><div className="home-showcase-card__body"><span className="home-showcase-card__subtitle">Official Warranty | Fast Delivery</span><span className="home-showcase-card__title">Android Flagship Picks</span></div></a>
                </div>
            </div>
        </section>

        {/*  Best Sellers  */}
        <section className="section ex-style-163">
            <div className="container">
                <div className="section-title"><h2><i className="fas fa-fire ex-style-164"></i> Best Sellers</h2><a href="#">View All <i className="fas fa-arrow-right"></i></a></div>
                <div className="grid grid-5">

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_55.webp" alt="Samsung Galaxy Watch 6 Classic" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Electronics Store</div>
                            <h3><a href="product.html">Samsung Galaxy Watch 6 Classic</a></h3>
                            <p className="desc">High-quality premium product with guaranteed authenticity.</p>
                            <div className="price"><span className="current">৳35,000</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="far fa-star ex-style-148"></i>
                                <span>(77)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(1220)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(1220,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_170.webp" alt="DJI Mini 4 Pro Drone" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Electronics Store</div>
                            <h3><a href="product.html">DJI Mini 4 Pro Drone</a></h3>
                            <p className="desc">High-quality premium product with guaranteed authenticity.</p>
                            <div className="price"><span className="current">৳95,000</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i>
                                <span>(352)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(8890)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(8890,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_147.webp" alt="Razer DeathAdder V3 Pro Mouse" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Electronics Store</div>
                            <h3><a href="product.html">Razer DeathAdder V3 Pro Mouse</a></h3>
                            <p className="desc">High-quality premium product with guaranteed authenticity.</p>
                            <div className="price"><span className="current">৳12,000</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="far fa-star ex-style-148"></i>
                                <span>(161)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(1591)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(1591,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_125.webp" alt="Keychron Q1 Pro Mechanical Keyboard" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Electronics Store</div>
                            <h3><a href="product.html">Keychron Q1 Pro Mechanical Keyboard</a></h3>
                            <p className="desc">High-quality premium product with guaranteed authenticity.</p>
                            <div className="price"><span className="current">৳18,000</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="far fa-star ex-style-148"></i>
                                <span>(456)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(5834)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(5834,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_176.webp" alt="Nintendo Switch OLED" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Electronics Store</div>
                            <h3><a href="product.html">Nintendo Switch OLED</a></h3>
                            <p className="desc">High-quality premium product with guaranteed authenticity.</p>
                            <div className="price"><span className="current">৳38,000</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="far fa-star ex-style-148"></i>
                                <span>(112)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(9292)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(9292,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_33.webp" alt="Premium Cotton T-Shirt White" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Store</div>
                            <h3><a href="product.html">Premium Cotton T-Shirt White</a></h3>
                            <p className="desc">High-quality premium product with guaranteed authenticity.</p>
                            <div className="price"><span className="current">৳799</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i>
                                <span>(168)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(4627)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(4627,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_43.webp" alt="Classic Blue Denim Jacket" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Store</div>
                            <h3><a href="product.html">Classic Blue Denim Jacket</a></h3>
                            <p className="desc">High-quality premium product with guaranteed authenticity.</p>
                            <div className="price"><span className="current">৳2,499</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i>
                                <span>(462)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(6748)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(6748,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_193.webp" alt="Nike Air Force 1 Sneakers" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Store</div>
                            <h3><a href="product.html">Nike Air Force 1 Sneakers</a></h3>
                            <p className="desc">High-quality premium product with guaranteed authenticity.</p>
                            <div className="price"><span className="current">৳9,500</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="far fa-star ex-style-148"></i>
                                <span>(387)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(3823)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(3823,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_175.webp" alt="Adidas Ultraboost Running Shoes" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Store</div>
                            <h3><a href="product.html">Adidas Ultraboost Running Shoes</a></h3>
                            <p className="desc">High-quality premium product with guaranteed authenticity.</p>
                            <div className="price"><span className="current">৳12,000</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="far fa-star ex-style-148"></i>
                                <span>(270)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(7018)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(7018,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_69.webp" alt="Men's Leather Biker Jacket" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Store</div>
                            <h3><a href="product.html">Men's Leather Biker Jacket</a></h3>
                            <p className="desc">High-quality premium product with guaranteed authenticity.</p>
                            <div className="price"><span className="current">৳4,500</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="far fa-star ex-style-148"></i>
                                <span>(435)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(3326)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(3326,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/generated/polarized_aviator_sunglasses.jpg" alt="Polarized Aviator Sunglasses" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Store</div>
                            <h3><a href="product.html">Polarized Aviator Sunglasses</a></h3>
                            <p className="desc">High-quality premium product with guaranteed authenticity.</p>
                            <div className="price"><span className="current">৳1,500</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="far fa-star ex-style-148"></i>
                                <span>(331)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(1053)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(1053,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_169.webp" alt="Minimalist Leather Handbag" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Store</div>
                            <h3><a href="product.html">Minimalist Leather Handbag</a></h3>
                            <p className="desc">High-quality premium product with guaranteed authenticity.</p>
                            <div className="price"><span className="current">৳8,000</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i>
                                <span>(11)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(8746)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(8746,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_104.webp" alt="Luxury Analog Wrist Watch" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Store</div>
                            <h3><a href="product.html">Luxury Analog Wrist Watch</a></h3>
                            <p className="desc">High-quality premium product with guaranteed authenticity.</p>
                            <div className="price"><span className="current">৳3,000</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i>
                                <span>(288)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(8449)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(8449,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/generated/casual_chino_pants.jpg" alt="Casual Chino Pants" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Store</div>
                            <h3><a href="product.html">Casual Chino Pants</a></h3>
                            <p className="desc">High-quality premium product with guaranteed authenticity.</p>
                            <div className="price"><span className="current">৳1,200</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i>
                                <span>(74)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(7532)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(7532,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <a href="product.html"><img src="/frontend/images/products/generated/winter_fleece_pullover_hoodie.jpg" alt="Winter Fleece Pullover Hoodie" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Store</div>
                            <h3><a href="product.html">Winter Fleece Pullover Hoodie</a></h3>
                            <p className="desc">High-quality premium product with guaranteed authenticity.</p>
                            <div className="price"><span className="current">৳1,899</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i><i className="fas fa-star ex-style-81"></i>
                                <span>(440)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(7700)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(7700,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        


        {/*  Category: Top Electronics & Gadgets!  */}
        <section className="section" id="cat-electronics">
            <div className="container">
                <div className="section-title">
                    <h2>Top Electronics & Gadgets!</h2>
                    <a href="javascript:void(0)" data-expanded="false" onClick="toggleViewMore('cat-electronics', this)">View All <i className="fas fa-arrow-right"></i></a>
                </div>
                <div className="grid grid-5">

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-31%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_129.webp" alt="Exclusive Headphones 1X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Tech Galaxy</div>
                            <h3><a href="product.html">Exclusive Headphones 1X</a></h3>
                            <p className="desc">Must-have laptop item.</p>
                            <div className="price"><span className="current">Tk 6,928</span> <span className="old ex-style-166">Tk 10,139</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(49)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(2)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(2,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-21%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_35.webp" alt="Elite Headphones 2X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Tech Galaxy</div>
                            <h3><a href="product.html">Elite Headphones 2X</a></h3>
                            <p className="desc">Bestselling laptop item.</p>
                            <div className="price"><span className="current">Tk 4,234</span> <span className="old ex-style-166">Tk 5,391</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(158)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(3)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(3,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-14%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_59.webp" alt="Ultra Mouse 3X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Tech Galaxy</div>
                            <h3><a href="product.html">Ultra Mouse 3X</a></h3>
                            <p className="desc">Bestselling laptop item.</p>
                            <div className="price"><span className="current">Tk 12,488</span> <span className="old ex-style-166">Tk 14,562</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(165)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(4)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(4,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-9%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_192.webp" alt="Classic Workstation 4X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Tech Galaxy</div>
                            <h3><a href="product.html">Classic Workstation 4X</a></h3>
                            <p className="desc">Customer favorite laptop item.</p>
                            <div className="price"><span className="current">Tk 8,517</span> <span className="old ex-style-166">Tk 9,417</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(183)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(5)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(5,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-16%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_1.webp" alt="Everyday Speaker 5X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Tech Galaxy</div>
                            <h3><a href="product.html">Everyday Speaker 5X</a></h3>
                            <p className="desc">Perfect for daily use laptop item.</p>
                            <div className="price"><span className="current">Tk 2,129</span> <span className="old ex-style-166">Tk 2,554</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(89)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(6)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(6,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-32%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_186.webp" alt="Pro Keyboard 6X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Tech Galaxy</div>
                            <h3><a href="product.html">Pro Keyboard 6X</a></h3>
                            <p className="desc">Durable laptop item.</p>
                            <div className="price"><span className="current">Tk 9,474</span> <span className="old ex-style-166">Tk 13,937</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(110)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(7)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(7,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-31%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_3.webp" alt="Elite Speaker 7X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Tech Galaxy</div>
                            <h3><a href="product.html">Elite Speaker 7X</a></h3>
                            <p className="desc">Must-have laptop item.</p>
                            <div className="price"><span className="current">Tk 8,175</span> <span className="old ex-style-166">Tk 11,985</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(137)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(8)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(8,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-32%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_22.webp" alt="Essential Webcam 8X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Tech Galaxy</div>
                            <h3><a href="product.html">Essential Webcam 8X</a></h3>
                            <p className="desc">High-quality laptop item.</p>
                            <div className="price"><span className="current">Tk 14,132</span> <span className="old ex-style-166">Tk 21,087</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(189)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(9)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(9,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-26%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_65.webp" alt="Premium Webcam 9X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Tech Galaxy</div>
                            <h3><a href="product.html">Premium Webcam 9X</a></h3>
                            <p className="desc">Bestselling laptop item.</p>
                            <div className="price"><span className="current">Tk 14,766</span> <span className="old ex-style-166">Tk 20,031</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(96)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(10)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(10,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-12%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_172.webp" alt="Classic Webcam 10X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Tech Galaxy</div>
                            <h3><a href="product.html">Classic Webcam 10X</a></h3>
                            <p className="desc">Designed for excellence laptop item.</p>
                            <div className="price"><span className="current">Tk 7,297</span> <span className="old ex-style-166">Tk 8,319</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(135)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(11)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(11,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-32%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_36.webp" alt="Elite Webcam 11X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Tech Galaxy</div>
                            <h3><a href="product.html">Elite Webcam 11X</a></h3>
                            <p className="desc">High-quality laptop item.</p>
                            <div className="price"><span className="current">Tk 833</span> <span className="old ex-style-166">Tk 1,234</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(111)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(12)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(12,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-31%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_27.webp" alt="Exclusive Headphones 12X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Tech Galaxy</div>
                            <h3><a href="product.html">Exclusive Headphones 12X</a></h3>
                            <p className="desc">Customer favorite laptop item.</p>
                            <div className="price"><span className="current">Tk 8,900</span> <span className="old ex-style-166">Tk 12,994</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(129)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(13)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(13,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-27%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_6.webp" alt="Lux Ultrabook 13X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Tech Galaxy</div>
                            <h3><a href="product.html">Lux Ultrabook 13X</a></h3>
                            <p className="desc">New arrival laptop item.</p>
                            <div className="price"><span className="current">Tk 6,721</span> <span className="old ex-style-166">Tk 9,239</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(106)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(14)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(14,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-32%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_146.webp" alt="Elite Tablet 14X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Tech Galaxy</div>
                            <h3><a href="product.html">Elite Tablet 14X</a></h3>
                            <p className="desc">Durable laptop item.</p>
                            <div className="price"><span className="current">Tk 5,674</span> <span className="old ex-style-166">Tk 8,405</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(193)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(15)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(15,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-15%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_2.webp" alt="Essential Webcam 15X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Tech Galaxy</div>
                            <h3><a href="product.html">Essential Webcam 15X</a></h3>
                            <p className="desc">Durable laptop item.</p>
                            <div className="price"><span className="current">Tk 10,355</span> <span className="old ex-style-166">Tk 12,281</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(52)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(16)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(16,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-17%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_156.webp" alt="Elite Monitor 16X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Tech Galaxy</div>
                            <h3><a href="product.html">Elite Monitor 16X</a></h3>
                            <p className="desc">New arrival laptop item.</p>
                            <div className="price"><span className="current">Tk 4,748</span> <span className="old ex-style-166">Tk 5,782</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(27)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(17)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(17,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-29%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_158.webp" alt="Classic Monitor 17X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Tech Galaxy</div>
                            <h3><a href="product.html">Classic Monitor 17X</a></h3>
                            <p className="desc">Durable laptop item.</p>
                            <div className="price"><span className="current">Tk 10,839</span> <span className="old ex-style-166">Tk 15,293</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(92)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(18)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(18,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-31%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_122.webp" alt="Everyday Headphones 18X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Tech Galaxy</div>
                            <h3><a href="product.html">Everyday Headphones 18X</a></h3>
                            <p className="desc">Perfect for daily use laptop item.</p>
                            <div className="price"><span className="current">Tk 7,161</span> <span className="old ex-style-166">Tk 10,387</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(86)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(19)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(19,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-23%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_54.webp" alt="Classic Ultrabook 19X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Tech Galaxy</div>
                            <h3><a href="product.html">Classic Ultrabook 19X</a></h3>
                            <p className="desc">Perfect for daily use laptop item.</p>
                            <div className="price"><span className="current">Tk 5,605</span> <span className="old ex-style-166">Tk 7,370</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(33)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(20)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(20,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-32%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_62.webp" alt="Pro Ultrabook 20X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Tech Galaxy</div>
                            <h3><a href="product.html">Pro Ultrabook 20X</a></h3>
                            <p className="desc">New arrival laptop item.</p>
                            <div className="price"><span className="current">Tk 13,718</span> <span className="old ex-style-166">Tk 20,317</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(108)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(21)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(21,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-12%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_42.webp" alt="Exclusive Speaker 21X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Tech Galaxy</div>
                            <h3><a href="product.html">Exclusive Speaker 21X</a></h3>
                            <p className="desc">Must-have laptop item.</p>
                            <div className="price"><span className="current">Tk 1,892</span> <span className="old ex-style-166">Tk 2,165</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(74)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(22)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(22,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-27%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_118.webp" alt="Pro Speaker 22X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Tech Galaxy</div>
                            <h3><a href="product.html">Pro Speaker 22X</a></h3>
                            <p className="desc">Must-have laptop item.</p>
                            <div className="price"><span className="current">Tk 10,242</span> <span className="old ex-style-166">Tk 14,224</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(51)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(23)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(23,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-26%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_63.webp" alt="Heavy-Duty Headphones 23X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Tech Galaxy</div>
                            <h3><a href="product.html">Heavy-Duty Headphones 23X</a></h3>
                            <p className="desc">New arrival laptop item.</p>
                            <div className="price"><span className="current">Tk 11,413</span> <span className="old ex-style-166">Tk 15,550</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(78)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(24)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(24,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-29%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_87.webp" alt="Classic Tablet 24X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Tech Galaxy</div>
                            <h3><a href="product.html">Classic Tablet 24X</a></h3>
                            <p className="desc">Top-rated laptop item.</p>
                            <div className="price"><span className="current">Tk 11,096</span> <span className="old ex-style-166">Tk 15,712</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(183)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(25)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(25,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-32%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_117.webp" alt="Premium Workstation 25X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Tech Galaxy</div>
                            <h3><a href="product.html">Premium Workstation 25X</a></h3>
                            <p className="desc">Durable laptop item.</p>
                            <div className="price"><span className="current">Tk 6,595</span> <span className="old ex-style-166">Tk 9,815</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(164)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(26)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(26,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-12%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_82.webp" alt="Modern Tablet 26X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Tech Galaxy</div>
                            <h3><a href="product.html">Modern Tablet 26X</a></h3>
                            <p className="desc">Top-rated laptop item.</p>
                            <div className="price"><span className="current">Tk 1,560</span> <span className="old ex-style-166">Tk 1,773</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(94)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(27)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(27,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-15%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_110.webp" alt="Smart Mouse 27X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Tech Galaxy</div>
                            <h3><a href="product.html">Smart Mouse 27X</a></h3>
                            <p className="desc">Top-rated laptop item.</p>
                            <div className="price"><span className="current">Tk 9,202</span> <span className="old ex-style-166">Tk 10,901</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(127)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(28)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(28,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-25%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_52.webp" alt="Premium Ultrabook 28X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Tech Galaxy</div>
                            <h3><a href="product.html">Premium Ultrabook 28X</a></h3>
                            <p className="desc">Top-rated laptop item.</p>
                            <div className="price"><span className="current">Tk 4,430</span> <span className="old ex-style-166">Tk 5,985</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(15)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(29)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(29,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-30%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_140.webp" alt="Essential Mouse 29X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Tech Galaxy</div>
                            <h3><a href="product.html">Essential Mouse 29X</a></h3>
                            <p className="desc">Reliable laptop item.</p>
                            <div className="price"><span className="current">Tk 3,645</span> <span className="old ex-style-166">Tk 5,282</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(25)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(30)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(30,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-20%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_12.webp" alt="Essential Mouse 30X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Tech Galaxy</div>
                            <h3><a href="product.html">Essential Mouse 30X</a></h3>
                            <p className="desc">Must-have laptop item.</p>
                            <div className="price"><span className="current">Tk 5,001</span> <span className="old ex-style-166">Tk 6,260</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(142)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(31)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(31,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        {/*  Category: Top Selling Home Appliances!  */}
        <section className="section" id="cat-appliances">
            <div className="container">
                <div className="section-title">
                    <h2>Top Selling Home Appliances!</h2>
                    <a href="javascript:void(0)" data-expanded="false" onClick="toggleViewMore('cat-appliances', this)">View All <i className="fas fa-arrow-right"></i></a>
                </div>
                <div className="grid grid-5">

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-28%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_13.webp" alt="Essential Kettle 1X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Home Essentials</div>
                            <h3><a href="product.html">Essential Kettle 1X</a></h3>
                            <p className="desc">Customer favorite appliance item.</p>
                            <div className="price"><span className="current">Tk 2,607</span> <span className="old ex-style-166">Tk 3,654</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(163)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(32)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(32,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-15%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_24.webp" alt="Modern Iron 2X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Home Essentials</div>
                            <h3><a href="product.html">Modern Iron 2X</a></h3>
                            <p className="desc">Durable appliance item.</p>
                            <div className="price"><span className="current">Tk 11,355</span> <span className="old ex-style-166">Tk 13,407</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(140)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(33)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(33,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-19%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_49.webp" alt="Ultra Purifier 3X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Home Essentials</div>
                            <h3><a href="product.html">Ultra Purifier 3X</a></h3>
                            <p className="desc">Top-rated appliance item.</p>
                            <div className="price"><span className="current">Tk 11,043</span> <span className="old ex-style-166">Tk 13,747</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(41)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(34)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(34,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-21%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_142.webp" alt="Elite Purifier 4X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Home Essentials</div>
                            <h3><a href="product.html">Elite Purifier 4X</a></h3>
                            <p className="desc">Perfect for daily use appliance item.</p>
                            <div className="price"><span className="current">Tk 2,681</span> <span className="old ex-style-166">Tk 3,436</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(130)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(35)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(35,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-28%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_79.webp" alt="Lux Blender 5X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Home Essentials</div>
                            <h3><a href="product.html">Lux Blender 5X</a></h3>
                            <p className="desc">Perfect for daily use appliance item.</p>
                            <div className="price"><span className="current">Tk 6,260</span> <span className="old ex-style-166">Tk 8,731</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(149)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(36)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(36,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-31%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_177.webp" alt="Compact Purifier 6X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Home Essentials</div>
                            <h3><a href="product.html">Compact Purifier 6X</a></h3>
                            <p className="desc">Reliable appliance item.</p>
                            <div className="price"><span className="current">Tk 9,165</span> <span className="old ex-style-166">Tk 13,403</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(137)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(37)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(37,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-18%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_162.webp" alt="Classic Heater 7X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Home Essentials</div>
                            <h3><a href="product.html">Classic Heater 7X</a></h3>
                            <p className="desc">Durable appliance item.</p>
                            <div className="price"><span className="current">Tk 5,689</span> <span className="old ex-style-166">Tk 6,989</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(48)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(38)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(38,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-27%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_5.webp" alt="Lux Cooler 8X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Home Essentials</div>
                            <h3><a href="product.html">Lux Cooler 8X</a></h3>
                            <p className="desc">Perfect for daily use appliance item.</p>
                            <div className="price"><span className="current">Tk 3,406</span> <span className="old ex-style-166">Tk 4,706</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(143)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(39)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(39,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-16%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_105.webp" alt="Classic Toaster 9X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Home Essentials</div>
                            <h3><a href="product.html">Classic Toaster 9X</a></h3>
                            <p className="desc">Perfect for daily use appliance item.</p>
                            <div className="price"><span className="current">Tk 10,159</span> <span className="old ex-style-166">Tk 12,158</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(15)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(40)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(40,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-12%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_137.webp" alt="Compact Kettle 10X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Home Essentials</div>
                            <h3><a href="product.html">Compact Kettle 10X</a></h3>
                            <p className="desc">Must-have appliance item.</p>
                            <div className="price"><span className="current">Tk 11,410</span> <span className="old ex-style-166">Tk 13,033</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(92)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(41)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(41,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-21%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_57.webp" alt="Exclusive Heater 11X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Home Essentials</div>
                            <h3><a href="product.html">Exclusive Heater 11X</a></h3>
                            <p className="desc">Customer favorite appliance item.</p>
                            <div className="price"><span className="current">Tk 7,712</span> <span className="old ex-style-166">Tk 9,768</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(85)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(42)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(42,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-24%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_141.webp" alt="Modern Blender 12X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Home Essentials</div>
                            <h3><a href="product.html">Modern Blender 12X</a></h3>
                            <p className="desc">Durable appliance item.</p>
                            <div className="price"><span className="current">Tk 14,312</span> <span className="old ex-style-166">Tk 18,921</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(159)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(43)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(43,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-13%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_165.webp" alt="Sleek Oven 13X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Home Essentials</div>
                            <h3><a href="product.html">Sleek Oven 13X</a></h3>
                            <p className="desc">Bestselling appliance item.</p>
                            <div className="price"><span className="current">Tk 8,788</span> <span className="old ex-style-166">Tk 10,191</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(172)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(44)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(44,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-31%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_139.webp" alt="Sleek Purifier 14X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Home Essentials</div>
                            <h3><a href="product.html">Sleek Purifier 14X</a></h3>
                            <p className="desc">New arrival appliance item.</p>
                            <div className="price"><span className="current">Tk 10,436</span> <span className="old ex-style-166">Tk 15,236</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(34)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(45)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(45,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-30%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_25.webp" alt="Everyday Oven 15X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Home Essentials</div>
                            <h3><a href="product.html">Everyday Oven 15X</a></h3>
                            <p className="desc">Reliable appliance item.</p>
                            <div className="price"><span className="current">Tk 11,509</span> <span className="old ex-style-166">Tk 16,525</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(84)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(46)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(46,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-16%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_97.webp" alt="Everyday Blender 16X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Home Essentials</div>
                            <h3><a href="product.html">Everyday Blender 16X</a></h3>
                            <p className="desc">High-quality appliance item.</p>
                            <div className="price"><span className="current">Tk 8,657</span> <span className="old ex-style-166">Tk 10,306</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(182)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(47)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(47,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-17%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_143.webp" alt="Lux Heater 17X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Home Essentials</div>
                            <h3><a href="product.html">Lux Heater 17X</a></h3>
                            <p className="desc">Reliable appliance item.</p>
                            <div className="price"><span className="current">Tk 6,228</span> <span className="old ex-style-166">Tk 7,541</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(67)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(48)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(48,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-25%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_166.webp" alt="Compact Heater 18X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Home Essentials</div>
                            <h3><a href="product.html">Compact Heater 18X</a></h3>
                            <p className="desc">Perfect for daily use appliance item.</p>
                            <div className="price"><span className="current">Tk 844</span> <span className="old ex-style-166">Tk 1,129</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(14)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(49)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(49,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-25%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_72.webp" alt="Ultra Purifier 19X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Home Essentials</div>
                            <h3><a href="product.html">Ultra Purifier 19X</a></h3>
                            <p className="desc">Perfect for daily use appliance item.</p>
                            <div className="price"><span className="current">Tk 1,362</span> <span className="old ex-style-166">Tk 1,826</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(15)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(50)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(50,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-12%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_7.webp" alt="Advanced Cooler 20X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Home Essentials</div>
                            <h3><a href="product.html">Advanced Cooler 20X</a></h3>
                            <p className="desc">Must-have appliance item.</p>
                            <div className="price"><span className="current">Tk 1,073</span> <span className="old ex-style-166">Tk 1,226</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(158)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(51)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(51,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-20%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_14.webp" alt="Modern Purifier 21X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Home Essentials</div>
                            <h3><a href="product.html">Modern Purifier 21X</a></h3>
                            <p className="desc">Top-rated appliance item.</p>
                            <div className="price"><span className="current">Tk 1,752</span> <span className="old ex-style-166">Tk 2,195</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(115)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(52)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(52,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-9%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_123.webp" alt="Elite Toaster 22X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Home Essentials</div>
                            <h3><a href="product.html">Elite Toaster 22X</a></h3>
                            <p className="desc">Reliable appliance item.</p>
                            <div className="price"><span className="current">Tk 3,186</span> <span className="old ex-style-166">Tk 3,521</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(41)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(53)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(53,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-11%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_184.webp" alt="Elite Purifier 23X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Home Essentials</div>
                            <h3><a href="product.html">Elite Purifier 23X</a></h3>
                            <p className="desc">New arrival appliance item.</p>
                            <div className="price"><span className="current">Tk 14,790</span> <span className="old ex-style-166">Tk 16,645</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(98)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(54)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(54,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-14%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_91.webp" alt="Ultra Purifier 24X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Home Essentials</div>
                            <h3><a href="product.html">Ultra Purifier 24X</a></h3>
                            <p className="desc">High-quality appliance item.</p>
                            <div className="price"><span className="current">Tk 6,197</span> <span className="old ex-style-166">Tk 7,209</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(49)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(55)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(55,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-19%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_101.webp" alt="Sleek Purifier 25X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Home Essentials</div>
                            <h3><a href="product.html">Sleek Purifier 25X</a></h3>
                            <p className="desc">High-quality appliance item.</p>
                            <div className="price"><span className="current">Tk 14,618</span> <span className="old ex-style-166">Tk 18,244</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(97)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(56)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(56,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-30%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_74.webp" alt="Pro Vacuum 26X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Home Essentials</div>
                            <h3><a href="product.html">Pro Vacuum 26X</a></h3>
                            <p className="desc">Must-have appliance item.</p>
                            <div className="price"><span className="current">Tk 2,322</span> <span className="old ex-style-166">Tk 3,334</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(190)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(57)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(57,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-20%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_31.webp" alt="Modern Iron 27X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Home Essentials</div>
                            <h3><a href="product.html">Modern Iron 27X</a></h3>
                            <p className="desc">Designed for excellence appliance item.</p>
                            <div className="price"><span className="current">Tk 7,582</span> <span className="old ex-style-166">Tk 9,482</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(104)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(58)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(58,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-24%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_130.webp" alt="Ultra Blender 28X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Home Essentials</div>
                            <h3><a href="product.html">Ultra Blender 28X</a></h3>
                            <p className="desc">Reliable appliance item.</p>
                            <div className="price"><span className="current">Tk 10,332</span> <span className="old ex-style-166">Tk 13,598</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(52)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(59)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(59,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-21%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_106.webp" alt="Essential Vacuum 29X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Home Essentials</div>
                            <h3><a href="product.html">Essential Vacuum 29X</a></h3>
                            <p className="desc">Durable appliance item.</p>
                            <div className="price"><span className="current">Tk 14,511</span> <span className="old ex-style-166">Tk 18,369</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(100)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(60)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(60,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-31%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_41.webp" alt="Sleek Iron 30X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Home Essentials</div>
                            <h3><a href="product.html">Sleek Iron 30X</a></h3>
                            <p className="desc">Bestselling appliance item.</p>
                            <div className="price"><span className="current">Tk 8,680</span> <span className="old ex-style-166">Tk 12,589</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(75)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(61)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(61,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        {/*  Category: Fashion Essentials on Sale!  */}
        <section className="section" id="cat-fashion">
            <div className="container">
                <div className="section-title">
                    <h2>Fashion Essentials on Sale!</h2>
                    <a href="javascript:void(0)" data-expanded="false" onClick="toggleViewMore('cat-fashion', this)">View All <i className="fas fa-arrow-right"></i></a>
                </div>
                <div className="grid grid-5">

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-16%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_67.webp" alt="Essential Watch 1X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Hub BD</div>
                            <h3><a href="product.html">Essential Watch 1X</a></h3>
                            <p className="desc">Perfect for daily use fashion item.</p>
                            <div className="price"><span className="current">Tk 714</span> <span className="old ex-style-166">Tk 856</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(92)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(62)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(62,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-19%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_93.webp" alt="Ultra Wallet 2X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Hub BD</div>
                            <h3><a href="product.html">Ultra Wallet 2X</a></h3>
                            <p className="desc">High-quality fashion item.</p>
                            <div className="price"><span className="current">Tk 11,843</span> <span className="old ex-style-166">Tk 14,704</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(27)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(63)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(63,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-9%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_17.webp" alt="Pro Jeans 3X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Hub BD</div>
                            <h3><a href="product.html">Pro Jeans 3X</a></h3>
                            <p className="desc">Perfect for daily use fashion item.</p>
                            <div className="price"><span className="current">Tk 10,075</span> <span className="old ex-style-166">Tk 11,146</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(77)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(64)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(64,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-28%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_173.webp" alt="Elite Sneakers 4X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Hub BD</div>
                            <h3><a href="product.html">Elite Sneakers 4X</a></h3>
                            <p className="desc">Designed for excellence fashion item.</p>
                            <div className="price"><span className="current">Tk 6,073</span> <span className="old ex-style-166">Tk 8,543</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(133)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(65)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(65,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-24%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_190.webp" alt="Exclusive Jacket 5X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Hub BD</div>
                            <h3><a href="product.html">Exclusive Jacket 5X</a></h3>
                            <p className="desc">New arrival fashion item.</p>
                            <div className="price"><span className="current">Tk 7,826</span> <span className="old ex-style-166">Tk 10,346</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(131)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(66)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(66,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-28%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_187.webp" alt="Sleek Backpack 6X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Hub BD</div>
                            <h3><a href="product.html">Sleek Backpack 6X</a></h3>
                            <p className="desc">Top-rated fashion item.</p>
                            <div className="price"><span className="current">Tk 2,717</span> <span className="old ex-style-166">Tk 3,793</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(192)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(67)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(67,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-22%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_16.webp" alt="Advanced Backpack 7X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Hub BD</div>
                            <h3><a href="product.html">Advanced Backpack 7X</a></h3>
                            <p className="desc">Must-have fashion item.</p>
                            <div className="price"><span className="current">Tk 11,817</span> <span className="old ex-style-166">Tk 15,160</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(66)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(68)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(68,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-27%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_71.webp" alt="Essential Jeans 8X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Hub BD</div>
                            <h3><a href="product.html">Essential Jeans 8X</a></h3>
                            <p className="desc">Must-have fashion item.</p>
                            <div className="price"><span className="current">Tk 2,874</span> <span className="old ex-style-166">Tk 3,937</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(144)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(69)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(69,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-29%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_51.webp" alt="Ultra Sunglasses 9X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Hub BD</div>
                            <h3><a href="product.html">Ultra Sunglasses 9X</a></h3>
                            <p className="desc">Perfect for daily use fashion item.</p>
                            <div className="price"><span className="current">Tk 500</span> <span className="old ex-style-166">Tk 711</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(66)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(70)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(70,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-32%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_150.webp" alt="Compact Jeans 10X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Hub BD</div>
                            <h3><a href="product.html">Compact Jeans 10X</a></h3>
                            <p className="desc">Reliable fashion item.</p>
                            <div className="price"><span className="current">Tk 6,923</span> <span className="old ex-style-166">Tk 10,317</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(39)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(71)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(71,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-23%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_85.webp" alt="Pro Wallet 11X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Hub BD</div>
                            <h3><a href="product.html">Pro Wallet 11X</a></h3>
                            <p className="desc">Perfect for daily use fashion item.</p>
                            <div className="price"><span className="current">Tk 8,881</span> <span className="old ex-style-166">Tk 11,544</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(39)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(72)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(72,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-20%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_75.webp" alt="Lux Belt 12X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Hub BD</div>
                            <h3><a href="product.html">Lux Belt 12X</a></h3>
                            <p className="desc">Designed for excellence fashion item.</p>
                            <div className="price"><span className="current">Tk 9,545</span> <span className="old ex-style-166">Tk 11,991</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(184)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(73)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(73,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-17%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_131.webp" alt="Elite Backpack 13X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Hub BD</div>
                            <h3><a href="product.html">Elite Backpack 13X</a></h3>
                            <p className="desc">High-quality fashion item.</p>
                            <div className="price"><span className="current">Tk 5,538</span> <span className="old ex-style-166">Tk 6,741</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(40)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(74)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(74,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-30%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_132.webp" alt="Everyday Watch 14X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Hub BD</div>
                            <h3><a href="product.html">Everyday Watch 14X</a></h3>
                            <p className="desc">Customer favorite fashion item.</p>
                            <div className="price"><span className="current">Tk 7,663</span> <span className="old ex-style-166">Tk 11,049</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(116)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(75)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(75,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-16%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_96.webp" alt="Ultra Sneakers 15X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Hub BD</div>
                            <h3><a href="product.html">Ultra Sneakers 15X</a></h3>
                            <p className="desc">Durable fashion item.</p>
                            <div className="price"><span className="current">Tk 8,786</span> <span className="old ex-style-166">Tk 10,465</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(173)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(76)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(76,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-15%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_8.webp" alt="Compact Sunglasses 16X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Hub BD</div>
                            <h3><a href="product.html">Compact Sunglasses 16X</a></h3>
                            <p className="desc">Bestselling fashion item.</p>
                            <div className="price"><span className="current">Tk 2,071</span> <span className="old ex-style-166">Tk 2,437</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(38)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(77)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(77,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-14%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_76.webp" alt="Pro T-Shirt 17X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Hub BD</div>
                            <h3><a href="product.html">Pro T-Shirt 17X</a></h3>
                            <p className="desc">Bestselling fashion item.</p>
                            <div className="price"><span className="current">Tk 9,946</span> <span className="old ex-style-166">Tk 11,612</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(64)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(78)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(78,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-24%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_154.webp" alt="Compact Wallet 18X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Hub BD</div>
                            <h3><a href="product.html">Compact Wallet 18X</a></h3>
                            <p className="desc">Must-have fashion item.</p>
                            <div className="price"><span className="current">Tk 4,861</span> <span className="old ex-style-166">Tk 6,412</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(20)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(79)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(79,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-9%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_98.webp" alt="Modern Sneakers 19X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Hub BD</div>
                            <h3><a href="product.html">Modern Sneakers 19X</a></h3>
                            <p className="desc">Customer favorite fashion item.</p>
                            <div className="price"><span className="current">Tk 4,879</span> <span className="old ex-style-166">Tk 5,371</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(143)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(80)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(80,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-12%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_73.webp" alt="Pro T-Shirt 20X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Hub BD</div>
                            <h3><a href="product.html">Pro T-Shirt 20X</a></h3>
                            <p className="desc">New arrival fashion item.</p>
                            <div className="price"><span className="current">Tk 4,313</span> <span className="old ex-style-166">Tk 4,917</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(135)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(81)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(81,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-31%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_15.webp" alt="Exclusive Wallet 21X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Hub BD</div>
                            <h3><a href="product.html">Exclusive Wallet 21X</a></h3>
                            <p className="desc">Top-rated fashion item.</p>
                            <div className="price"><span className="current">Tk 2,148</span> <span className="old ex-style-166">Tk 3,129</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(166)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(82)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(82,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-31%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_11.webp" alt="Compact Backpack 22X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Hub BD</div>
                            <h3><a href="product.html">Compact Backpack 22X</a></h3>
                            <p className="desc">High-quality fashion item.</p>
                            <div className="price"><span className="current">Tk 13,831</span> <span className="old ex-style-166">Tk 20,249</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(189)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(83)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(83,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-29%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_174.webp" alt="Advanced Jeans 23X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Hub BD</div>
                            <h3><a href="product.html">Advanced Jeans 23X</a></h3>
                            <p className="desc">Customer favorite fashion item.</p>
                            <div className="price"><span className="current">Tk 12,405</span> <span className="old ex-style-166">Tk 17,517</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(167)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(84)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(84,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-29%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_133.webp" alt="Modern Belt 24X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Hub BD</div>
                            <h3><a href="product.html">Modern Belt 24X</a></h3>
                            <p className="desc">Bestselling fashion item.</p>
                            <div className="price"><span className="current">Tk 3,181</span> <span className="old ex-style-166">Tk 4,495</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(76)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(85)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(85,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-20%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_29.webp" alt="Modern Backpack 25X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Hub BD</div>
                            <h3><a href="product.html">Modern Backpack 25X</a></h3>
                            <p className="desc">Bestselling fashion item.</p>
                            <div className="price"><span className="current">Tk 1,944</span> <span className="old ex-style-166">Tk 2,456</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(40)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(86)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(86,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-31%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_120.webp" alt="Sleek Jacket 26X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Hub BD</div>
                            <h3><a href="product.html">Sleek Jacket 26X</a></h3>
                            <p className="desc">Designed for excellence fashion item.</p>
                            <div className="price"><span className="current">Tk 12,963</span> <span className="old ex-style-166">Tk 18,827</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(61)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(87)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(87,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-27%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_126.webp" alt="Modern Sneakers 27X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Hub BD</div>
                            <h3><a href="product.html">Modern Sneakers 27X</a></h3>
                            <p className="desc">High-quality fashion item.</p>
                            <div className="price"><span className="current">Tk 3,461</span> <span className="old ex-style-166">Tk 4,787</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(169)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(88)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(88,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-12%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_40.webp" alt="Premium Scarf 28X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Hub BD</div>
                            <h3><a href="product.html">Premium Scarf 28X</a></h3>
                            <p className="desc">Perfect for daily use fashion item.</p>
                            <div className="price"><span className="current">Tk 10,825</span> <span className="old ex-style-166">Tk 12,347</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(171)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(89)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(89,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-22%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_121.webp" alt="Advanced T-Shirt 29X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Hub BD</div>
                            <h3><a href="product.html">Advanced T-Shirt 29X</a></h3>
                            <p className="desc">Must-have fashion item.</p>
                            <div className="price"><span className="current">Tk 1,850</span> <span className="old ex-style-166">Tk 2,375</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(65)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(90)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(90,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-9%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_188.webp" alt="Heavy-Duty T-Shirt 30X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Fashion Hub BD</div>
                            <h3><a href="product.html">Heavy-Duty T-Shirt 30X</a></h3>
                            <p className="desc">High-quality fashion item.</p>
                            <div className="price"><span className="current">Tk 9,315</span> <span className="old ex-style-166">Tk 10,256</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(100)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(91)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(91,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        {/*  Category: Beauty & Grooming Essentials!  */}
        <section className="section" id="cat-beauty">
            <div className="container">
                <div className="section-title">
                    <h2>Beauty & Grooming Essentials!</h2>
                    <a href="javascript:void(0)" data-expanded="false" onClick="toggleViewMore('cat-beauty', this)">View All <i className="fas fa-arrow-right"></i></a>
                </div>
                <div className="grid grid-5">

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-23%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_111.webp" alt="Advanced Perfume 1X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Glow Up BD</div>
                            <h3><a href="product.html">Advanced Perfume 1X</a></h3>
                            <p className="desc">Customer favorite beauty item.</p>
                            <div className="price"><span className="current">Tk 4,136</span> <span className="old ex-style-166">Tk 5,431</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(133)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(92)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(92,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-32%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_9.webp" alt="Smart Conditioner 2X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Glow Up BD</div>
                            <h3><a href="product.html">Smart Conditioner 2X</a></h3>
                            <p className="desc">Bestselling beauty item.</p>
                            <div className="price"><span className="current">Tk 9,056</span> <span className="old ex-style-166">Tk 13,495</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(30)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(93)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(93,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-26%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_102.webp" alt="Classic Conditioner 3X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Glow Up BD</div>
                            <h3><a href="product.html">Classic Conditioner 3X</a></h3>
                            <p className="desc">Durable beauty item.</p>
                            <div className="price"><span className="current">Tk 7,203</span> <span className="old ex-style-166">Tk 9,798</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(114)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(94)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(94,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-29%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_21.webp" alt="Modern Cleanser 4X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Glow Up BD</div>
                            <h3><a href="product.html">Modern Cleanser 4X</a></h3>
                            <p className="desc">Must-have beauty item.</p>
                            <div className="price"><span className="current">Tk 7,231</span> <span className="old ex-style-166">Tk 10,268</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(137)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(95)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(95,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-20%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_66.webp" alt="Elite Moisturizer 5X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Glow Up BD</div>
                            <h3><a href="product.html">Elite Moisturizer 5X</a></h3>
                            <p className="desc">New arrival beauty item.</p>
                            <div className="price"><span className="current">Tk 6,785</span> <span className="old ex-style-166">Tk 8,566</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(141)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(96)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(96,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-24%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_191.webp" alt="Smart Perfume 6X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Glow Up BD</div>
                            <h3><a href="product.html">Smart Perfume 6X</a></h3>
                            <p className="desc">Reliable beauty item.</p>
                            <div className="price"><span className="current">Tk 7,795</span> <span className="old ex-style-166">Tk 10,313</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(100)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(97)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(97,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-12%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_77.webp" alt="Everyday Cleanser 7X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Glow Up BD</div>
                            <h3><a href="product.html">Everyday Cleanser 7X</a></h3>
                            <p className="desc">Customer favorite beauty item.</p>
                            <div className="price"><span className="current">Tk 11,126</span> <span className="old ex-style-166">Tk 12,765</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(23)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(98)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(98,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-15%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_151.webp" alt="Everyday Scrub 8X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Glow Up BD</div>
                            <h3><a href="product.html">Everyday Scrub 8X</a></h3>
                            <p className="desc">Customer favorite beauty item.</p>
                            <div className="price"><span className="current">Tk 3,824</span> <span className="old ex-style-166">Tk 4,501</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(106)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(99)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(99,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-24%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_84.webp" alt="Essential Lotion 9X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Glow Up BD</div>
                            <h3><a href="product.html">Essential Lotion 9X</a></h3>
                            <p className="desc">Customer favorite beauty item.</p>
                            <div className="price"><span className="current">Tk 13,203</span> <span className="old ex-style-166">Tk 17,512</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(182)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(100)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(100,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-31%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_20.webp" alt="Lux Mask 10X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Glow Up BD</div>
                            <h3><a href="product.html">Lux Mask 10X</a></h3>
                            <p className="desc">Designed for excellence beauty item.</p>
                            <div className="price"><span className="current">Tk 990</span> <span className="old ex-style-166">Tk 1,440</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(88)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(101)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(101,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-33%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_179.webp" alt="Classic Moisturizer 11X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Glow Up BD</div>
                            <h3><a href="product.html">Classic Moisturizer 11X</a></h3>
                            <p className="desc">New arrival beauty item.</p>
                            <div className="price"><span className="current">Tk 13,568</span> <span className="old ex-style-166">Tk 20,269</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(168)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(102)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(102,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-29%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_86.webp" alt="Essential Moisturizer 12X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Glow Up BD</div>
                            <h3><a href="product.html">Essential Moisturizer 12X</a></h3>
                            <p className="desc">Customer favorite beauty item.</p>
                            <div className="price"><span className="current">Tk 7,939</span> <span className="old ex-style-166">Tk 11,255</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(133)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(103)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(103,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-20%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_107.webp" alt="Essential Conditioner 13X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Glow Up BD</div>
                            <h3><a href="product.html">Essential Conditioner 13X</a></h3>
                            <p className="desc">New arrival beauty item.</p>
                            <div className="price"><span className="current">Tk 13,404</span> <span className="old ex-style-166">Tk 16,940</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(31)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(104)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(104,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-28%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_60.webp" alt="Elite Moisturizer 14X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Glow Up BD</div>
                            <h3><a href="product.html">Elite Moisturizer 14X</a></h3>
                            <p className="desc">Must-have beauty item.</p>
                            <div className="price"><span className="current">Tk 6,116</span> <span className="old ex-style-166">Tk 8,581</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(33)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(105)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(105,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-23%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_61.webp" alt="Everyday Conditioner 15X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Glow Up BD</div>
                            <h3><a href="product.html">Everyday Conditioner 15X</a></h3>
                            <p className="desc">Bestselling beauty item.</p>
                            <div className="price"><span className="current">Tk 4,385</span> <span className="old ex-style-166">Tk 5,722</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(169)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(106)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(106,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-26%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_90.webp" alt="Advanced Toner 16X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Glow Up BD</div>
                            <h3><a href="product.html">Advanced Toner 16X</a></h3>
                            <p className="desc">Bestselling beauty item.</p>
                            <div className="price"><span className="current">Tk 8,722</span> <span className="old ex-style-166">Tk 11,874</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(30)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(107)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(107,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-32%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_10.webp" alt="Elite Conditioner 17X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Glow Up BD</div>
                            <h3><a href="product.html">Elite Conditioner 17X</a></h3>
                            <p className="desc">Durable beauty item.</p>
                            <div className="price"><span className="current">Tk 9,716</span> <span className="old ex-style-166">Tk 14,414</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(21)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(108)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(108,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-10%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_39.webp" alt="Everyday Toner 18X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Glow Up BD</div>
                            <h3><a href="product.html">Everyday Toner 18X</a></h3>
                            <p className="desc">Customer favorite beauty item.</p>
                            <div className="price"><span className="current">Tk 11,825</span> <span className="old ex-style-166">Tk 13,249</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(34)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(109)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(109,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-27%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_70.webp" alt="Smart Serum 19X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Glow Up BD</div>
                            <h3><a href="product.html">Smart Serum 19X</a></h3>
                            <p className="desc">Reliable beauty item.</p>
                            <div className="price"><span className="current">Tk 7,416</span> <span className="old ex-style-166">Tk 10,222</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(132)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(110)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(110,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-23%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_68.webp" alt="Sleek Moisturizer 20X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Glow Up BD</div>
                            <h3><a href="product.html">Sleek Moisturizer 20X</a></h3>
                            <p className="desc">Perfect for daily use beauty item.</p>
                            <div className="price"><span className="current">Tk 5,536</span> <span className="old ex-style-166">Tk 7,240</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(108)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(111)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(111,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-29%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_124.webp" alt="Lux Lotion 21X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Glow Up BD</div>
                            <h3><a href="product.html">Lux Lotion 21X</a></h3>
                            <p className="desc">Designed for excellence beauty item.</p>
                            <div className="price"><span className="current">Tk 8,422</span> <span className="old ex-style-166">Tk 11,869</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(22)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(112)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(112,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-21%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_88.webp" alt="Sleek Moisturizer 22X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Glow Up BD</div>
                            <h3><a href="product.html">Sleek Moisturizer 22X</a></h3>
                            <p className="desc">Must-have beauty item.</p>
                            <div className="price"><span className="current">Tk 5,685</span> <span className="old ex-style-166">Tk 7,270</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(28)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(113)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(113,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-24%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_100.webp" alt="Compact Cleanser 23X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Glow Up BD</div>
                            <h3><a href="product.html">Compact Cleanser 23X</a></h3>
                            <p className="desc">Bestselling beauty item.</p>
                            <div className="price"><span className="current">Tk 4,354</span> <span className="old ex-style-166">Tk 5,768</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(130)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(114)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(114,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-32%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_185.webp" alt="Smart Moisturizer 24X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Glow Up BD</div>
                            <h3><a href="product.html">Smart Moisturizer 24X</a></h3>
                            <p className="desc">Reliable beauty item.</p>
                            <div className="price"><span className="current">Tk 4,707</span> <span className="old ex-style-166">Tk 6,985</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(200)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(115)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(115,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-27%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_148.webp" alt="Modern Scrub 25X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Glow Up BD</div>
                            <h3><a href="product.html">Modern Scrub 25X</a></h3>
                            <p className="desc">Reliable beauty item.</p>
                            <div className="price"><span className="current">Tk 7,205</span> <span className="old ex-style-166">Tk 9,957</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(124)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(116)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(116,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-29%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_47.webp" alt="Essential Conditioner 26X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Glow Up BD</div>
                            <h3><a href="product.html">Essential Conditioner 26X</a></h3>
                            <p className="desc">Customer favorite beauty item.</p>
                            <div className="price"><span className="current">Tk 13,748</span> <span className="old ex-style-166">Tk 19,538</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(114)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(117)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(117,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-32%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_58.webp" alt="Exclusive Mask 27X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Glow Up BD</div>
                            <h3><a href="product.html">Exclusive Mask 27X</a></h3>
                            <p className="desc">Durable beauty item.</p>
                            <div className="price"><span className="current">Tk 3,515</span> <span className="old ex-style-166">Tk 5,222</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(124)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(118)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(118,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-30%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_99.webp" alt="Advanced Cleanser 28X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Glow Up BD</div>
                            <h3><a href="product.html">Advanced Cleanser 28X</a></h3>
                            <p className="desc">Designed for excellence beauty item.</p>
                            <div className="price"><span className="current">Tk 14,942</span> <span className="old ex-style-166">Tk 21,418</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(152)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(119)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(119,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-16%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_112.webp" alt="Essential Perfume 29X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Glow Up BD</div>
                            <h3><a href="product.html">Essential Perfume 29X</a></h3>
                            <p className="desc">Bestselling beauty item.</p>
                            <div className="price"><span className="current">Tk 9,884</span> <span className="old ex-style-166">Tk 11,888</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(50)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(120)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(120,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-28%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_168.webp" alt="Lux Conditioner 30X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Glow Up BD</div>
                            <h3><a href="product.html">Lux Conditioner 30X</a></h3>
                            <p className="desc">Durable beauty item.</p>
                            <div className="price"><span className="current">Tk 10,263</span> <span className="old ex-style-166">Tk 14,442</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(98)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(121)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(121,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        {/*  Category: Fitness & Sports Picks!  */}
        <section className="section" id="cat-sports">
            <div className="container">
                <div className="section-title">
                    <h2>Fitness & Sports Picks!</h2>
                    <a href="javascript:void(0)" data-expanded="false" onClick="toggleViewMore('cat-sports', this)">View All <i className="fas fa-arrow-right"></i></a>
                </div>
                <div className="grid grid-5">

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-25%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_50.webp" alt="Ultra Bottle 1X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FitGear</div>
                            <h3><a href="product.html">Ultra Bottle 1X</a></h3>
                            <p className="desc">High-quality fitness item.</p>
                            <div className="price"><span className="current">Tk 8,189</span> <span className="old ex-style-166">Tk 10,930</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(32)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(122)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(122,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-30%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_119.webp" alt="Exclusive Jump Rope 2X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FitGear</div>
                            <h3><a href="product.html">Exclusive Jump Rope 2X</a></h3>
                            <p className="desc">Perfect for daily use fitness item.</p>
                            <div className="price"><span className="current">Tk 8,986</span> <span className="old ex-style-166">Tk 12,935</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(191)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(123)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(123,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-30%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_152.webp" alt="Premium Bottle 3X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FitGear</div>
                            <h3><a href="product.html">Premium Bottle 3X</a></h3>
                            <p className="desc">Perfect for daily use fitness item.</p>
                            <div className="price"><span className="current">Tk 5,876</span> <span className="old ex-style-166">Tk 8,513</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(53)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(124)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(124,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-11%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_26.webp" alt="Sleek Yoga Mat 4X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FitGear</div>
                            <h3><a href="product.html">Sleek Yoga Mat 4X</a></h3>
                            <p className="desc">Designed for excellence fitness item.</p>
                            <div className="price"><span className="current">Tk 2,368</span> <span className="old ex-style-166">Tk 2,664</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(120)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(125)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(125,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-9%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_182.webp" alt="Modern Gloves 5X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FitGear</div>
                            <h3><a href="product.html">Modern Gloves 5X</a></h3>
                            <p className="desc">Perfect for daily use fitness item.</p>
                            <div className="price"><span className="current">Tk 1,532</span> <span className="old ex-style-166">Tk 1,687</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(186)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(126)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(126,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-18%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_167.webp" alt="Essential Gloves 6X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FitGear</div>
                            <h3><a href="product.html">Essential Gloves 6X</a></h3>
                            <p className="desc">Top-rated fitness item.</p>
                            <div className="price"><span className="current">Tk 11,979</span> <span className="old ex-style-166">Tk 14,707</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(143)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(127)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(127,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-31%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_38.webp" alt="Premium Gloves 7X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FitGear</div>
                            <h3><a href="product.html">Premium Gloves 7X</a></h3>
                            <p className="desc">Customer favorite fitness item.</p>
                            <div className="price"><span className="current">Tk 4,877</span> <span className="old ex-style-166">Tk 7,071</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(164)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(128)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(128,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-20%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_155.webp" alt="Smart Bench 8X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FitGear</div>
                            <h3><a href="product.html">Smart Bench 8X</a></h3>
                            <p className="desc">Reliable fitness item.</p>
                            <div className="price"><span className="current">Tk 2,113</span> <span className="old ex-style-166">Tk 2,661</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(150)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(129)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(129,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-22%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_178.webp" alt="Premium Resistance Band 9X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FitGear</div>
                            <h3><a href="product.html">Premium Resistance Band 9X</a></h3>
                            <p className="desc">Bestselling fitness item.</p>
                            <div className="price"><span className="current">Tk 12,543</span> <span className="old ex-style-166">Tk 16,092</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(161)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(130)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(130,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-26%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_180.webp" alt="Compact Treadmill 10X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FitGear</div>
                            <h3><a href="product.html">Compact Treadmill 10X</a></h3>
                            <p className="desc">New arrival fitness item.</p>
                            <div className="price"><span className="current">Tk 14,437</span> <span className="old ex-style-166">Tk 19,766</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(110)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(131)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(131,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-14%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_134.webp" alt="Exclusive Gloves 11X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FitGear</div>
                            <h3><a href="product.html">Exclusive Gloves 11X</a></h3>
                            <p className="desc">Designed for excellence fitness item.</p>
                            <div className="price"><span className="current">Tk 13,933</span> <span className="old ex-style-166">Tk 16,285</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(81)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(132)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(132,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-28%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_32.webp" alt="Sleek Resistance Band 12X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FitGear</div>
                            <h3><a href="product.html">Sleek Resistance Band 12X</a></h3>
                            <p className="desc">Durable fitness item.</p>
                            <div className="price"><span className="current">Tk 5,203</span> <span className="old ex-style-166">Tk 7,308</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(182)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(133)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(133,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-16%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_56.webp" alt="Smart Resistance Band 13X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FitGear</div>
                            <h3><a href="product.html">Smart Resistance Band 13X</a></h3>
                            <p className="desc">Perfect for daily use fitness item.</p>
                            <div className="price"><span className="current">Tk 3,173</span> <span className="old ex-style-166">Tk 3,821</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(156)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(134)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(134,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-22%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_135.webp" alt="Exclusive Dumbbell 14X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FitGear</div>
                            <h3><a href="product.html">Exclusive Dumbbell 14X</a></h3>
                            <p className="desc">Customer favorite fitness item.</p>
                            <div className="price"><span className="current">Tk 13,808</span> <span className="old ex-style-166">Tk 17,921</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(101)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(135)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(135,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-24%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_113.webp" alt="Premium Foam Roller 15X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FitGear</div>
                            <h3><a href="product.html">Premium Foam Roller 15X</a></h3>
                            <p className="desc">New arrival fitness item.</p>
                            <div className="price"><span className="current">Tk 2,561</span> <span className="old ex-style-166">Tk 3,403</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(20)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(136)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(136,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-22%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_157.webp" alt="Lux Gloves 16X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FitGear</div>
                            <h3><a href="product.html">Lux Gloves 16X</a></h3>
                            <p className="desc">Top-rated fitness item.</p>
                            <div className="price"><span className="current">Tk 9,123</span> <span className="old ex-style-166">Tk 11,816</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(36)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(137)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(137,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-31%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_92.webp" alt="Everyday Foam Roller 17X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FitGear</div>
                            <h3><a href="product.html">Everyday Foam Roller 17X</a></h3>
                            <p className="desc">Designed for excellence fitness item.</p>
                            <div className="price"><span className="current">Tk 3,284</span> <span className="old ex-style-166">Tk 4,791</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(146)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(138)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(138,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-29%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_34.webp" alt="Classic Gloves 18X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FitGear</div>
                            <h3><a href="product.html">Classic Gloves 18X</a></h3>
                            <p className="desc">Reliable fitness item.</p>
                            <div className="price"><span className="current">Tk 6,455</span> <span className="old ex-style-166">Tk 9,107</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(44)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(139)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(139,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-9%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_159.webp" alt="Sleek Gloves 19X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FitGear</div>
                            <h3><a href="product.html">Sleek Gloves 19X</a></h3>
                            <p className="desc">Reliable fitness item.</p>
                            <div className="price"><span className="current">Tk 7,112</span> <span className="old ex-style-166">Tk 7,835</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(60)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(140)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(140,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-11%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_189.webp" alt="Heavy-Duty Gloves 20X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FitGear</div>
                            <h3><a href="product.html">Heavy-Duty Gloves 20X</a></h3>
                            <p className="desc">Customer favorite fitness item.</p>
                            <div className="price"><span className="current">Tk 2,877</span> <span className="old ex-style-166">Tk 3,250</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(72)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(141)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(141,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-20%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_145.webp" alt="Sleek Dumbbell 21X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FitGear</div>
                            <h3><a href="product.html">Sleek Dumbbell 21X</a></h3>
                            <p className="desc">Durable fitness item.</p>
                            <div className="price"><span className="current">Tk 9,291</span> <span className="old ex-style-166">Tk 11,616</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(187)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(142)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(142,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-11%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_136.webp" alt="Classic Bottle 22X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FitGear</div>
                            <h3><a href="product.html">Classic Bottle 22X</a></h3>
                            <p className="desc">Durable fitness item.</p>
                            <div className="price"><span className="current">Tk 4,610</span> <span className="old ex-style-166">Tk 5,228</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(131)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(143)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(143,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-10%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_89.webp" alt="Advanced Bottle 23X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FitGear</div>
                            <h3><a href="product.html">Advanced Bottle 23X</a></h3>
                            <p className="desc">Reliable fitness item.</p>
                            <div className="price"><span className="current">Tk 4,197</span> <span className="old ex-style-166">Tk 4,668</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(165)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(144)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(144,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-25%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_103.webp" alt="Smart Foam Roller 24X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FitGear</div>
                            <h3><a href="product.html">Smart Foam Roller 24X</a></h3>
                            <p className="desc">Designed for excellence fitness item.</p>
                            <div className="price"><span className="current">Tk 10,151</span> <span className="old ex-style-166">Tk 13,546</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(116)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(145)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(145,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-11%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_161.webp" alt="Modern Foam Roller 25X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FitGear</div>
                            <h3><a href="product.html">Modern Foam Roller 25X</a></h3>
                            <p className="desc">Durable fitness item.</p>
                            <div className="price"><span className="current">Tk 8,846</span> <span className="old ex-style-166">Tk 9,958</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(72)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(146)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(146,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-25%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_83.webp" alt="Exclusive Bench 26X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FitGear</div>
                            <h3><a href="product.html">Exclusive Bench 26X</a></h3>
                            <p className="desc">Bestselling fitness item.</p>
                            <div className="price"><span className="current">Tk 2,061</span> <span className="old ex-style-166">Tk 2,751</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(183)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(147)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(147,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-20%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_46.webp" alt="Elite Bottle 27X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FitGear</div>
                            <h3><a href="product.html">Elite Bottle 27X</a></h3>
                            <p className="desc">Customer favorite fitness item.</p>
                            <div className="price"><span className="current">Tk 14,237</span> <span className="old ex-style-166">Tk 17,860</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(140)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(148)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(148,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-23%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_163.webp" alt="Smart Bench 28X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FitGear</div>
                            <h3><a href="product.html">Smart Bench 28X</a></h3>
                            <p className="desc">High-quality fitness item.</p>
                            <div className="price"><span className="current">Tk 7,346</span> <span className="old ex-style-166">Tk 9,663</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(22)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(149)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(149,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-21%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_138.webp" alt="Compact Foam Roller 29X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FitGear</div>
                            <h3><a href="product.html">Compact Foam Roller 29X</a></h3>
                            <p className="desc">Designed for excellence fitness item.</p>
                            <div className="price"><span className="current">Tk 1,656</span> <span className="old ex-style-166">Tk 2,110</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(24)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(150)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(150,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-29%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_30.webp" alt="Compact Bottle 30X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FitGear</div>
                            <h3><a href="product.html">Compact Bottle 30X</a></h3>
                            <p className="desc">Durable fitness item.</p>
                            <div className="price"><span className="current">Tk 9,083</span> <span className="old ex-style-166">Tk 12,949</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(190)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(151)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(151,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        {/*  Category: Daily Groceries & Essentials!  */}
        <section className="section" id="cat-groceries">
            <div className="container">
                <div className="section-title">
                    <h2>Daily Groceries & Essentials!</h2>
                    <a href="javascript:void(0)" data-expanded="false" onClick="toggleViewMore('cat-groceries', this)">View All <i className="fas fa-arrow-right"></i></a>
                </div>
                <div className="grid grid-5">

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-11%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_23.webp" alt="Sleek Coffee Beans 1X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FreshMart</div>
                            <h3><a href="product.html">Sleek Coffee Beans 1X</a></h3>
                            <p className="desc">New arrival grocery item.</p>
                            <div className="price"><span className="current">Tk 7,051</span> <span className="old ex-style-166">Tk 7,924</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(57)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(152)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(152,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-25%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_53.webp" alt="Elite Coffee Beans 2X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FreshMart</div>
                            <h3><a href="product.html">Elite Coffee Beans 2X</a></h3>
                            <p className="desc">Top-rated grocery item.</p>
                            <div className="price"><span className="current">Tk 10,744</span> <span className="old ex-style-166">Tk 14,483</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(56)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(153)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(153,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-16%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_181.webp" alt="Pro Pasta 3X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FreshMart</div>
                            <h3><a href="product.html">Pro Pasta 3X</a></h3>
                            <p className="desc">Customer favorite grocery item.</p>
                            <div className="price"><span className="current">Tk 1,904</span> <span className="old ex-style-166">Tk 2,270</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(108)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(154)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(154,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-22%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_164.webp" alt="Sleek Pasta 4X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FreshMart</div>
                            <h3><a href="product.html">Sleek Pasta 4X</a></h3>
                            <p className="desc">Reliable grocery item.</p>
                            <div className="price"><span className="current">Tk 10,955</span> <span className="old ex-style-166">Tk 14,080</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(169)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(155)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(155,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-9%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_153.webp" alt="Essential Green Tea 5X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FreshMart</div>
                            <h3><a href="product.html">Essential Green Tea 5X</a></h3>
                            <p className="desc">High-quality grocery item.</p>
                            <div className="price"><span className="current">Tk 3,621</span> <span className="old ex-style-166">Tk 3,998</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(126)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(156)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(156,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-14%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_144.webp" alt="Premium Pasta 6X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FreshMart</div>
                            <h3><a href="product.html">Premium Pasta 6X</a></h3>
                            <p className="desc">High-quality grocery item.</p>
                            <div className="price"><span className="current">Tk 4,041</span> <span className="old ex-style-166">Tk 4,737</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(17)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(157)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(157,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-13%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_18.webp" alt="Exclusive Olive Oil 7X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FreshMart</div>
                            <h3><a href="product.html">Exclusive Olive Oil 7X</a></h3>
                            <p className="desc">Must-have grocery item.</p>
                            <div className="price"><span className="current">Tk 1,953</span> <span className="old ex-style-166">Tk 2,266</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(200)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(158)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(158,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-21%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_78.webp" alt="Everyday Honey 8X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FreshMart</div>
                            <h3><a href="product.html">Everyday Honey 8X</a></h3>
                            <p className="desc">High-quality grocery item.</p>
                            <div className="price"><span className="current">Tk 9,168</span> <span className="old ex-style-166">Tk 11,738</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(165)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(159)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(159,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-30%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_81.webp" alt="Exclusive Cereal 9X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FreshMart</div>
                            <h3><a href="product.html">Exclusive Cereal 9X</a></h3>
                            <p className="desc">Top-rated grocery item.</p>
                            <div className="price"><span className="current">Tk 11,215</span> <span className="old ex-style-166">Tk 16,125</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(198)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(160)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(160,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-26%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_44.webp" alt="Lux Cereal 10X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FreshMart</div>
                            <h3><a href="product.html">Lux Cereal 10X</a></h3>
                            <p className="desc">Durable grocery item.</p>
                            <div className="price"><span className="current">Tk 6,987</span> <span className="old ex-style-166">Tk 9,564</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(160)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(161)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(161,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-16%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_0.webp" alt="Pro Organic Rice 11X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FreshMart</div>
                            <h3><a href="product.html">Pro Organic Rice 11X</a></h3>
                            <p className="desc">New arrival grocery item.</p>
                            <div className="price"><span className="current">Tk 12,377</span> <span className="old ex-style-166">Tk 14,777</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(38)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(162)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(162,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-27%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_149.webp" alt="Premium Honey 12X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FreshMart</div>
                            <h3><a href="product.html">Premium Honey 12X</a></h3>
                            <p className="desc">Customer favorite grocery item.</p>
                            <div className="price"><span className="current">Tk 965</span> <span className="old ex-style-166">Tk 1,337</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(149)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(163)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(163,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-21%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_94.webp" alt="Classic Pasta 13X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FreshMart</div>
                            <h3><a href="product.html">Classic Pasta 13X</a></h3>
                            <p className="desc">Designed for excellence grocery item.</p>
                            <div className="price"><span className="current">Tk 7,478</span> <span className="old ex-style-166">Tk 9,532</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(11)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(164)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(164,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-27%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_95.webp" alt="Premium Cereal 14X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FreshMart</div>
                            <h3><a href="product.html">Premium Cereal 14X</a></h3>
                            <p className="desc">Bestselling grocery item.</p>
                            <div className="price"><span className="current">Tk 4,823</span> <span className="old ex-style-166">Tk 6,667</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(17)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(165)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(165,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-20%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_19.webp" alt="Exclusive Spices 15X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FreshMart</div>
                            <h3><a href="product.html">Exclusive Spices 15X</a></h3>
                            <p className="desc">New arrival grocery item.</p>
                            <div className="price"><span className="current">Tk 9,294</span> <span className="old ex-style-166">Tk 11,756</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(10)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(166)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(166,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-9%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_108.webp" alt="Classic Almonds 16X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FreshMart</div>
                            <h3><a href="product.html">Classic Almonds 16X</a></h3>
                            <p className="desc">Reliable grocery item.</p>
                            <div className="price"><span className="current">Tk 10,948</span> <span className="old ex-style-166">Tk 12,095</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(148)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(167)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(167,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-19%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_28.webp" alt="Sleek Honey 17X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FreshMart</div>
                            <h3><a href="product.html">Sleek Honey 17X</a></h3>
                            <p className="desc">Designed for excellence grocery item.</p>
                            <div className="price"><span className="current">Tk 12,598</span> <span className="old ex-style-166">Tk 15,629</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(143)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(168)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(168,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-20%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_4.webp" alt="Modern Coffee Beans 18X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FreshMart</div>
                            <h3><a href="product.html">Modern Coffee Beans 18X</a></h3>
                            <p className="desc">Must-have grocery item.</p>
                            <div className="price"><span className="current">Tk 7,808</span> <span className="old ex-style-166">Tk 9,812</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(85)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(169)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(169,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-15%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_128.webp" alt="Modern Spices 19X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FreshMart</div>
                            <h3><a href="product.html">Modern Spices 19X</a></h3>
                            <p className="desc">Customer favorite grocery item.</p>
                            <div className="price"><span className="current">Tk 11,182</span> <span className="old ex-style-166">Tk 13,242</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(92)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(170)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(170,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-11%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_116.webp" alt="Essential Oats 20X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FreshMart</div>
                            <h3><a href="product.html">Essential Oats 20X</a></h3>
                            <p className="desc">Durable grocery item.</p>
                            <div className="price"><span className="current">Tk 578</span> <span className="old ex-style-166">Tk 654</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(126)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(171)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(171,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-26%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_37.webp" alt="Elite Green Tea 21X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FreshMart</div>
                            <h3><a href="product.html">Elite Green Tea 21X</a></h3>
                            <p className="desc">Durable grocery item.</p>
                            <div className="price"><span className="current">Tk 11,454</span> <span className="old ex-style-166">Tk 15,485</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(15)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(172)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(172,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-32%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_45.webp" alt="Smart Honey 22X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FreshMart</div>
                            <h3><a href="product.html">Smart Honey 22X</a></h3>
                            <p className="desc">High-quality grocery item.</p>
                            <div className="price"><span className="current">Tk 9,717</span> <span className="old ex-style-166">Tk 14,344</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(89)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(173)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(173,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-11%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_48.webp" alt="Compact Cereal 23X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FreshMart</div>
                            <h3><a href="product.html">Compact Cereal 23X</a></h3>
                            <p className="desc">Customer favorite grocery item.</p>
                            <div className="price"><span className="current">Tk 14,756</span> <span className="old ex-style-166">Tk 16,598</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(124)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(174)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(174,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-22%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_114.webp" alt="Elite Honey 24X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FreshMart</div>
                            <h3><a href="product.html">Elite Honey 24X</a></h3>
                            <p className="desc">Customer favorite grocery item.</p>
                            <div className="price"><span className="current">Tk 12,854</span> <span className="old ex-style-166">Tk 16,482</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(24)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(175)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(175,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-25%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_183.webp" alt="Heavy-Duty Almonds 25X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FreshMart</div>
                            <h3><a href="product.html">Heavy-Duty Almonds 25X</a></h3>
                            <p className="desc">Bestselling grocery item.</p>
                            <div className="price"><span className="current">Tk 1,333</span> <span className="old ex-style-166">Tk 1,799</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(114)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(176)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(176,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-30%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_115.webp" alt="Ultra Organic Rice 26X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FreshMart</div>
                            <h3><a href="product.html">Ultra Organic Rice 26X</a></h3>
                            <p className="desc">Top-rated grocery item.</p>
                            <div className="price"><span className="current">Tk 9,204</span> <span className="old ex-style-166">Tk 13,219</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(59)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(177)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(177,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-11%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_127.webp" alt="Compact Oats 27X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FreshMart</div>
                            <h3><a href="product.html">Compact Oats 27X</a></h3>
                            <p className="desc">Top-rated grocery item.</p>
                            <div className="price"><span className="current">Tk 5,944</span> <span className="old ex-style-166">Tk 6,717</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(59)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(178)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(178,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-9%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_80.webp" alt="Everyday Honey 28X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FreshMart</div>
                            <h3><a href="product.html">Everyday Honey 28X</a></h3>
                            <p className="desc">Designed for excellence grocery item.</p>
                            <div className="price"><span className="current">Tk 3,499</span> <span className="old ex-style-166">Tk 3,849</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(21)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(179)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(179,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-31%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_171.webp" alt="Smart Organic Rice 29X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FreshMart</div>
                            <h3><a href="product.html">Smart Organic Rice 29X</a></h3>
                            <p className="desc">Durable grocery item.</p>
                            <div className="price"><span className="current">Tk 3,738</span> <span className="old ex-style-166">Tk 5,475</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(57)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(180)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(180,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-32%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_55.webp" alt="Modern Organic Rice 30X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">FreshMart</div>
                            <h3><a href="product.html">Modern Organic Rice 30X</a></h3>
                            <p className="desc">New arrival grocery item.</p>
                            <div className="price"><span className="current">Tk 6,914</span> <span className="old ex-style-166">Tk 10,238</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(185)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(181)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(181,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        {/*  Category: Bestselling Books & Stationery!  */}
        <section className="section" id="cat-books">
            <div className="container">
                <div className="section-title">
                    <h2>Bestselling Books & Stationery!</h2>
                    <a href="javascript:void(0)" data-expanded="false" onClick="toggleViewMore('cat-books', this)">View All <i className="fas fa-arrow-right"></i></a>
                </div>
                <div className="grid grid-5">

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-25%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_170.webp" alt="Essential Journal 1X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Read & Write</div>
                            <h3><a href="product.html">Essential Journal 1X</a></h3>
                            <p className="desc">Reliable books item.</p>
                            <div className="price"><span className="current">Tk 9,889</span> <span className="old ex-style-166">Tk 13,199</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(184)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(182)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(182,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-15%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_147.webp" alt="Everyday Sketchbook 2X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Read & Write</div>
                            <h3><a href="product.html">Everyday Sketchbook 2X</a></h3>
                            <p className="desc">Customer favorite books item.</p>
                            <div className="price"><span className="current">Tk 10,973</span> <span className="old ex-style-166">Tk 13,010</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(102)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(183)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(183,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-29%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_125.webp" alt="Premium Journal 3X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Read & Write</div>
                            <h3><a href="product.html">Premium Journal 3X</a></h3>
                            <p className="desc">Customer favorite books item.</p>
                            <div className="price"><span className="current">Tk 2,479</span> <span className="old ex-style-166">Tk 3,534</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(154)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(184)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(184,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-29%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_176.webp" alt="Advanced Planner 4X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Read & Write</div>
                            <h3><a href="product.html">Advanced Planner 4X</a></h3>
                            <p className="desc">New arrival books item.</p>
                            <div className="price"><span className="current">Tk 5,618</span> <span className="old ex-style-166">Tk 7,995</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(134)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(185)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(185,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-29%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_33.webp" alt="Heavy-Duty Folder 5X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Read & Write</div>
                            <h3><a href="product.html">Heavy-Duty Folder 5X</a></h3>
                            <p className="desc">Must-have books item.</p>
                            <div className="price"><span className="current">Tk 3,903</span> <span className="old ex-style-166">Tk 5,530</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(191)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(186)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(186,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-32%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_43.webp" alt="Everyday Sketchbook 6X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Read & Write</div>
                            <h3><a href="product.html">Everyday Sketchbook 6X</a></h3>
                            <p className="desc">New arrival books item.</p>
                            <div className="price"><span className="current">Tk 12,167</span> <span className="old ex-style-166">Tk 17,926</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(143)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(187)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(187,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-9%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_193.webp" alt="Smart Notebook 7X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Read & Write</div>
                            <h3><a href="product.html">Smart Notebook 7X</a></h3>
                            <p className="desc">High-quality books item.</p>
                            <div className="price"><span className="current">Tk 1,293</span> <span className="old ex-style-166">Tk 1,435</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(149)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(188)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(188,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-14%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_175.webp" alt="Sleek Calculator 8X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Read & Write</div>
                            <h3><a href="product.html">Sleek Calculator 8X</a></h3>
                            <p className="desc">Reliable books item.</p>
                            <div className="price"><span className="current">Tk 11,132</span> <span className="old ex-style-166">Tk 13,078</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(10)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(189)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(189,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-11%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_69.webp" alt="Essential Pen Set 9X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Read & Write</div>
                            <h3><a href="product.html">Essential Pen Set 9X</a></h3>
                            <p className="desc">Reliable books item.</p>
                            <div className="price"><span className="current">Tk 9,439</span> <span className="old ex-style-166">Tk 10,682</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(188)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(190)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(190,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="product-card" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-17%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_160.webp" alt="Modern Journal 10X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Read & Write</div>
                            <h3><a href="product.html">Modern Journal 10X</a></h3>
                            <p className="desc">Bestselling books item.</p>
                            <div className="price"><span className="current">Tk 11,302</span> <span className="old ex-style-166">Tk 13,667</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(70)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(191)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(191,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-22%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_169.webp" alt="Exclusive Desk Organizer 11X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Read & Write</div>
                            <h3><a href="product.html">Exclusive Desk Organizer 11X</a></h3>
                            <p className="desc">Designed for excellence books item.</p>
                            <div className="price"><span className="current">Tk 10,349</span> <span className="old ex-style-166">Tk 13,404</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(83)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(192)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(192,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-10%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_104.webp" alt="Heavy-Duty Marker 12X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Read & Write</div>
                            <h3><a href="product.html">Heavy-Duty Marker 12X</a></h3>
                            <p className="desc">Durable books item.</p>
                            <div className="price"><span className="current">Tk 7,302</span> <span className="old ex-style-166">Tk 8,122</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(75)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(193)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(193,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-27%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_64.webp" alt="Ultra Journal 13X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Read & Write</div>
                            <h3><a href="product.html">Ultra Journal 13X</a></h3>
                            <p className="desc">Perfect for daily use books item.</p>
                            <div className="price"><span className="current">Tk 11,685</span> <span className="old ex-style-166">Tk 16,081</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(81)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(194)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(194,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-31%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_109.webp" alt="Ultra Folder 14X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Read & Write</div>
                            <h3><a href="product.html">Ultra Folder 14X</a></h3>
                            <p className="desc">Reliable books item.</p>
                            <div className="price"><span className="current">Tk 9,896</span> <span className="old ex-style-166">Tk 14,545</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(116)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(195)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(195,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-25%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_129.webp" alt="Smart Planner 15X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Read & Write</div>
                            <h3><a href="product.html">Smart Planner 15X</a></h3>
                            <p className="desc">Must-have books item.</p>
                            <div className="price"><span className="current">Tk 13,558</span> <span className="old ex-style-166">Tk 18,165</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(84)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(196)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(196,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-20%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_35.webp" alt="Essential Journal 16X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Read & Write</div>
                            <h3><a href="product.html">Essential Journal 16X</a></h3>
                            <p className="desc">Bestselling books item.</p>
                            <div className="price"><span className="current">Tk 4,699</span> <span className="old ex-style-166">Tk 5,908</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(51)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(197)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(197,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-20%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_59.webp" alt="Pro Pen Set 17X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Read & Write</div>
                            <h3><a href="product.html">Pro Pen Set 17X</a></h3>
                            <p className="desc">High-quality books item.</p>
                            <div className="price"><span className="current">Tk 7,238</span> <span className="old ex-style-166">Tk 9,059</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(176)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(198)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(198,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-25%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_192.webp" alt="Exclusive Desk Organizer 18X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Read & Write</div>
                            <h3><a href="product.html">Exclusive Desk Organizer 18X</a></h3>
                            <p className="desc">Perfect for daily use books item.</p>
                            <div className="price"><span className="current">Tk 1,605</span> <span className="old ex-style-166">Tk 2,165</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(138)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(199)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(199,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-9%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_1.webp" alt="Heavy-Duty Desk Organizer 19X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Read & Write</div>
                            <h3><a href="product.html">Heavy-Duty Desk Organizer 19X</a></h3>
                            <p className="desc">Designed for excellence books item.</p>
                            <div className="price"><span className="current">Tk 3,404</span> <span className="old ex-style-166">Tk 3,770</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(125)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(200)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(200,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-10%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_186.webp" alt="Smart Folder 20X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Read & Write</div>
                            <h3><a href="product.html">Smart Folder 20X</a></h3>
                            <p className="desc">Reliable books item.</p>
                            <div className="price"><span className="current">Tk 6,898</span> <span className="old ex-style-166">Tk 7,729</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(115)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(201)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(201,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-19%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_3.webp" alt="Everyday Marker 21X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Read & Write</div>
                            <h3><a href="product.html">Everyday Marker 21X</a></h3>
                            <p className="desc">Designed for excellence books item.</p>
                            <div className="price"><span className="current">Tk 6,286</span> <span className="old ex-style-166">Tk 7,761</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(76)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(202)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(202,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-23%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_22.webp" alt="Elite Folder 22X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Read & Write</div>
                            <h3><a href="product.html">Elite Folder 22X</a></h3>
                            <p className="desc">Perfect for daily use books item.</p>
                            <div className="price"><span className="current">Tk 4,395</span> <span className="old ex-style-166">Tk 5,734</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(84)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(203)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(203,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-15%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_65.webp" alt="Classic Marker 23X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Read & Write</div>
                            <h3><a href="product.html">Classic Marker 23X</a></h3>
                            <p className="desc">Perfect for daily use books item.</p>
                            <div className="price"><span className="current">Tk 3,617</span> <span className="old ex-style-166">Tk 4,296</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(25)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(204)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(204,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-33%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_172.webp" alt="Premium Sketchbook 24X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Read & Write</div>
                            <h3><a href="product.html">Premium Sketchbook 24X</a></h3>
                            <p className="desc">Must-have books item.</p>
                            <div className="price"><span className="current">Tk 6,915</span> <span className="old ex-style-166">Tk 10,350</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(71)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(205)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(205,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-24%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_36.webp" alt="Sleek Calculator 25X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Read & Write</div>
                            <h3><a href="product.html">Sleek Calculator 25X</a></h3>
                            <p className="desc">Perfect for daily use books item.</p>
                            <div className="price"><span className="current">Tk 6,129</span> <span className="old ex-style-166">Tk 8,146</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(124)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(206)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(206,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-16%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_27.webp" alt="Smart Folder 26X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Read & Write</div>
                            <h3><a href="product.html">Smart Folder 26X</a></h3>
                            <p className="desc">Durable books item.</p>
                            <div className="price"><span className="current">Tk 7,422</span> <span className="old ex-style-166">Tk 8,917</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(130)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(207)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(207,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-29%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_6.webp" alt="Smart Calculator 27X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Read & Write</div>
                            <h3><a href="product.html">Smart Calculator 27X</a></h3>
                            <p className="desc">High-quality books item.</p>
                            <div className="price"><span className="current">Tk 3,004</span> <span className="old ex-style-166">Tk 4,269</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(114)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(208)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(208,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-20%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_146.webp" alt="Pro Desk Organizer 28X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Read & Write</div>
                            <h3><a href="product.html">Pro Desk Organizer 28X</a></h3>
                            <p className="desc">Durable books item.</p>
                            <div className="price"><span className="current">Tk 10,502</span> <span className="old ex-style-166">Tk 13,271</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(69)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(209)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(209,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-10%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_2.webp" alt="Premium Notebook 29X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Read & Write</div>
                            <h3><a href="product.html">Premium Notebook 29X</a></h3>
                            <p className="desc">Customer favorite books item.</p>
                            <div className="price"><span className="current">Tk 10,303</span> <span className="old ex-style-166">Tk 11,568</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(49)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(210)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(210,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                    <div className="ex-style-167 product-card hidden-item" data-product-url="product.html" role="link" tabIndex="0">
                        <div className="ex-style-165">-10%</div>
                        <a href="product.html"><img src="/frontend/images/products/dummy/dummy_156.webp" alt="Essential Sketchbook 30X" loading="lazy" /></a>
                        <div className="content">
                            <div className="vendor">Read & Write</div>
                            <h3><a href="product.html">Essential Sketchbook 30X</a></h3>
                            <p className="desc">Bestselling books item.</p>
                            <div className="price"><span className="current">Tk 2,864</span> <span className="old ex-style-166">Tk 3,189</span></div>
                            <div className="rating">
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star ex-style-81"></i>
                                <i className="fas fa-star-half-alt ex-style-81"></i>
                                <span>(45)</span>
                            </div>
                            <div className="actions">
                                <button type="button" className="add-cart" onClick="event.preventDefault();event.stopPropagation();addToCart(211)"><i className="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" className="wishlist" onClick="event.preventDefault();event.stopPropagation();toggleWishlist(211,this)"><i className="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>


        {/*  Newsletter  */}
        <section className="ex-style-168">
            <div className="container ex-style-169">
                <h2 className="ex-style-170">Subscribe to Our Newsletter</h2>
                <p className="ex-style-171">Get exclusive offers, new arrivals, and more delivered to your inbox!</p>
                <form id="home-newsletter-form" action="#" method="POST" className="ex-style-172">
                    <input type="hidden" name="source" value="home_newsletter" />
                    <div id="home-newsletter-email-wrap" className="ex-style-84"><input type="email" id="home-newsletter-email" name="email" required autocomplete="email" placeholder="Enter your email address" className="ex-style-173" /></div>
                    <button id="home-newsletter-submit" type="submit" data-subscribed="0" className="btn btn-secondary ex-style-174">Subscribe</button>
                </form>
            </div>
        </section>
    </main>

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
