import os

file_path = r'G:\laragon\www\laravel\E-Commerce\html-templates\product.html'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. AI Assistant
target_1 = '''<div class="quick-spec-item"><span>Sold</span><strong>312</strong></div>
                    </div>
                </section>'''

ai_assistant = '''
                <section class="card ai-assistant">
                    <p class="ai-kicker"><i class="fas fa-robot" aria-hidden="true"></i> Ask AI Assistant</p>
                    <div class="ai-suggestions">
                        <button type="button" class="ai-chip">What are the key features of this product?</button>
                        <button type="button" class="ai-chip">Is this product in stock and how fast is delivery?</button>
                        <button type="button" class="ai-chip">What should I check before buying this product?</button>
                    </div>
                    <div class="ai-ask-row">
                        <input type="text" id="ai-question-input" placeholder="Ask something else about this product">
                        <button type="button" id="ai-ask-btn">Ask</button>
                    </div>
                    <div class="ai-answer hidden" id="ai-answer-box">
                        <p class="ai-answer-title">AI Answer</p>
                        <p id="ai-answer-text"></p>
                    </div>
                </section>'''

if target_1 in content and 'class="card ai-assistant"' not in content:
    content = content.replace(target_1, target_1 + ai_assistant)

# 2. Tabs
target_2 = '''<p>Water Resistance</p><strong>IP68</strong></div></div>
                <div class="tab-pane">
                    <div class="review">'''

qa_and_price = '''
                <div class="tab-pane" data-pane="qa">
                    <div class="qa-list">
                        <article class="qa-item">
                            <p class="qa-question"><strong>Q:</strong> Does it come with a charger?</p>
                            <p class="qa-meta">Asked by John D. on 10 Jul 2025</p>
                            <p class="qa-answer"><strong>A:</strong> No, Samsung no longer includes chargers in the box.</p>
                            <p class="qa-meta">Answered 11 Jul 2025</p>
                        </article>
                    </div>
                    <div class="qa-form-wrap">
                        <h3>Ask a Question</h3>
                        <form class="qa-form">
                            <div class="form-group">
                                <label for="question-input">Your Question</label>
                                <textarea id="question-input" name="question" class="form-control" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Question</button>
                        </form>
                    </div>
                </div>
                <div class="tab-pane" data-pane="price-history">
                    <div class="price-history-wrap">
                        <table class="price-history-table">
                            <thead>
                                <tr><th>Date</th><th>Price</th><th>Compare Price</th><th>Changed By</th></tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>15 Jul 2025, 10:00 AM</td>
                                    <td>৳1,19,999 <span class="arrow">&rarr;</span> ৳89,999</td>
                                    <td>N/A <span class="arrow">&rarr;</span> ৳1,19,999</td>
                                    <td>Vendor</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>'''

if target_2 in content and 'data-pane="qa"' not in content:
    content = content.replace(target_2, target_2.replace('<div class="tab-pane">\n                    <div class="review">', qa_and_price + '\n                <div class="tab-pane">\n                    <div class="review">'))


# 3. Related and Compare Sections
target_3 = '''</section>

        <section class="deals-signup">'''

extra_sections = '''</section>

        <section class="pdp-section">
            <div class="section-title"><h2>Products Related To This Item</h2></div>
            <div class="topic-pills">
                <span class="topic-pill is-active">All</span>
                <span class="topic-pill">Samsung</span>
                <span class="topic-pill">Electronics</span>
            </div>
            <div class="grid grid-4">
                <div class="product-card"><a href="product.html"><img src="images/products/electronics/samsung-galaxy-a55-5g.jpg" alt="Galaxy A55" style="width:100%;height:200px;object-fit:cover;"></a><div class="content"><div class="vendor">TechZone</div><h3><a href="product.html">Samsung Galaxy A55 5G</a></h3><div class="price"><span class="current">৳35,999</span></div><div class="actions"><button type="button" class="add-cart"><i class="fas fa-shopping-cart"></i> Add to Cart</button><button type="button" class="wishlist"><i class="fas fa-heart"></i></button></div></div></div>
            </div>
        </section>

        <section class="pdp-section card compare-section">
            <div class="compare-head">
                <h2>Compare With Similar Products</h2>
                <a href="products.html" class="compare-cta">Compare similar products</a>
            </div>
            <div class="compare-table-wrap">
                <table class="compare-matrix">
                    <thead>
                        <tr>
                            <th class="compare-label-head">Products</th>
                            <th class="compare-product-head is-current">
                                <span class="compare-current-tag">Currently Viewing</span>
                                <a href="product.html" class="compare-thumb"><img src="images/products/electronics/samsung-s24.jpg" alt="Galaxy S24"></a>
                                <p class="compare-name"><a href="product.html">Samsung Galaxy S24 Ultra</a></p>
                                <button type="button" class="compare-add-btn">Add to cart</button>
                                <span class="compare-seller-tag">Best Seller</span>
                            </th>
                            <th class="compare-product-head">
                                <a href="product.html" class="compare-thumb"><img src="images/products/electronics/xiaomi-14.jpg" alt="Xiaomi 14"></a>
                                <p class="compare-name"><a href="product.html">Xiaomi 14</a></p>
                                <button type="button" class="compare-add-btn">Add to cart</button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><th class="compare-row-label">Price</th><td class="compare-cell">৳89,999</td><td class="compare-cell">৳49,999</td></tr>
                        <tr><th class="compare-row-label">Rating</th><td class="compare-cell">4.5 (42)</td><td class="compare-cell">4.8 (100)</td></tr>
                        <tr><th class="compare-row-label">Brand</th><td class="compare-cell">Samsung</td><td class="compare-cell">Xiaomi</td></tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="pdp-section card seller-more-section">
            <div class="section-title"><h2>More From This Seller</h2></div>
            <div class="seller-more-layout">
                <aside class="seller-more-sidebar">
                    <div class="seller-card">
                        <img src="images/placeholders/no-product-image.svg" alt="TechStore BD">
                        <strong>TechStore BD</strong>
                    </div>
                    <div class="seller-cat-list">
                        <a href="products.html">Electronics</a>
                        <a href="products.html">Accessories</a>
                    </div>
                    <a href="products.html" class="seller-more-link">Click for More Products</a>
                </aside>
                <div class="seller-more-products grid grid-3">
                    <div class="product-card"><a href="product.html"><img src="images/products/electronics/xiaomi-14.jpg" alt="Xiaomi 14" style="width:100%;height:200px;object-fit:cover;"></a><div class="content"><div class="vendor">TechStore BD</div><h3><a href="product.html">Xiaomi 14 Flagship</a></h3><div class="price"><span class="current">৳49,999</span></div><div class="actions"><button type="button" class="add-cart"><i class="fas fa-shopping-cart"></i> Add to Cart</button><button type="button" class="wishlist"><i class="fas fa-heart"></i></button></div></div></div>
                </div>
            </div>
        </section>

        <section class="pdp-section card warranty-section">
            <div class="section-title"><h2>Warranty & Returns</h2></div>
            <div class="warranty-grid">
                <article class="warranty-card">
                    <h3>Warranty</h3><p>Please contact the seller directly for warranty information.</p><a href="contact.html">Contact seller support</a>
                </article>
                <article class="warranty-card">
                    <h3>Return Policies</h3><p>Eligible products can be returned as per store policy.</p><a href="#">Read return policy</a>
                </article>
                <article class="warranty-card">
                    <h3>Manufacturer Contact</h3><p>Need brand documentation or support center details.</p><a href="products.html">View manufacturer products</a>
                </article>
            </div>
        </section>

        <section class="pdp-section card buying-options-section">
            <div class="section-title"><h2>More Buying Options</h2><p>2 options from ৳89,999</p></div>
            <div class="buying-options-wrap">
                <table class="buying-options-table">
                    <thead><tr><th>Condition</th><th>Delivery</th><th>Seller</th><th>Price + Shipping</th><th>Action</th></tr></thead>
                    <tbody>
                        <tr><td>New</td><td><p>Free shipping</p><small>Arrives in 3-7 days</small></td><td>TechStore BD</td><td>৳89,999</td><td><button type="button" class="buying-btn">Add to cart</button></td></tr>
                        <tr><td>New</td><td><p>Shipping may vary</p><small>Arrives in 4-9 days</small></td><td>GadgetWorld</td><td>৳92,000</td><td><a href="product.html" class="buying-btn">View item</a></td></tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="deals-signup">'''

if target_3 in content and 'class="pdp-section card compare-section"' not in content:
    content = content.replace(target_3, extra_sections)


with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Done")
