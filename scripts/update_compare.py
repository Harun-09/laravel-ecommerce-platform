import re

html_file = 'G:/laragon/www/laravel/E-Commerce/html-templates/product.html'

with open(html_file, 'r', encoding='utf-8') as f:
    content = f.read()

new_table_html = """<table class="compare-matrix">
                    <thead>
                        <tr>
                            <th class="compare-label-head">Products</th>
                            <th class="compare-product-head is-current">
                                <span class="compare-current-tag">CURRENTLY VIEWING</span>
                                <a href="product.html" class="compare-thumb"><img src="https://loremflickr.com/400/400/smartphone?lock=1" alt="Smartphone 1"></a>
                                <p class="compare-name"><a href="product.html">Samsung Galaxy S24 Ultra</a></p>
                                <button type="button" class="compare-add-btn" style="background:#f59e0b; color:white; border:none; padding:4px 12px; border-radius:4px; font-weight:600; font-size:13px; margin-right:4px;">Add to cart</button>
                                <span class="compare-seller-tag" style="background:#fef08a; color:#a16207; font-size:11px; padding:2px 6px; border-radius:4px; font-weight:600;">Best Seller</span>
                            </th>
                            <th class="compare-product-head">
                                <a href="product.html" class="compare-thumb"><img src="https://loremflickr.com/400/400/smartphone?lock=2" alt="Smartphone 2"></a>
                                <p class="compare-name"><a href="product.html">Xiaomi 14 Pro Max</a></p>
                                <button type="button" class="compare-add-btn" style="background:#f59e0b; color:white; border:none; padding:4px 12px; border-radius:4px; font-weight:600; font-size:13px; margin-right:4px;">Add to cart</button>
                                <span class="compare-seller-tag" style="background:#fef08a; color:#a16207; font-size:11px; padding:2px 6px; border-radius:4px; font-weight:600;">Best Seller</span>
                            </th>
                            <th class="compare-product-head">
                                <a href="product.html" class="compare-thumb"><img src="https://loremflickr.com/400/400/smartphone?lock=3" alt="Smartphone 3"></a>
                                <p class="compare-name"><a href="product.html">Google Pixel 8 Pro</a></p>
                                <button type="button" class="compare-add-btn" style="background:#f59e0b; color:white; border:none; padding:4px 12px; border-radius:4px; font-weight:600; font-size:13px; margin-right:4px;">Add to cart</button>
                                <span class="compare-seller-tag" style="background:#fef08a; color:#a16207; font-size:11px; padding:2px 6px; border-radius:4px; font-weight:600;">Best Seller</span>
                            </th>
                            <th class="compare-product-head">
                                <a href="product.html" class="compare-thumb"><img src="https://loremflickr.com/400/400/smartphone?lock=4" alt="Smartphone 4"></a>
                                <p class="compare-name"><a href="product.html">OnePlus 12 5G</a></p>
                                <button type="button" class="compare-add-btn" style="background:#f59e0b; color:white; border:none; padding:4px 12px; border-radius:4px; font-weight:600; font-size:13px; margin-right:4px;">Add to cart</button>
                                <span class="compare-seller-tag" style="background:#fef08a; color:#a16207; font-size:11px; padding:2px 6px; border-radius:4px; font-weight:600;">Best Seller</span>
                            </th>
                            <th class="compare-product-head">
                                <a href="product.html" class="compare-thumb"><img src="https://loremflickr.com/400/400/smartphone?lock=5" alt="Smartphone 5"></a>
                                <p class="compare-name"><a href="product.html">Vivo X100 Pro</a></p>
                                <button type="button" class="compare-add-btn" style="background:#f59e0b; color:white; border:none; padding:4px 12px; border-radius:4px; font-weight:600; font-size:13px; margin-right:4px;">Add to cart</button>
                                <span class="compare-seller-tag" style="background:#fef08a; color:#a16207; font-size:11px; padding:2px 6px; border-radius:4px; font-weight:600;">Best Seller</span>
                            </th>
                            <th class="compare-product-head">
                                <a href="product.html" class="compare-thumb"><img src="https://loremflickr.com/400/400/smartphone?lock=6" alt="Smartphone 6"></a>
                                <p class="compare-name"><a href="product.html">Oppo Find X7 Ultra</a></p>
                                <button type="button" class="compare-add-btn" style="background:#f59e0b; color:white; border:none; padding:4px 12px; border-radius:4px; font-weight:600; font-size:13px; margin-right:4px;">Add to cart</button>
                                <span class="compare-seller-tag" style="background:#fef08a; color:#a16207; font-size:11px; padding:2px 6px; border-radius:4px; font-weight:600;">Best Seller</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th class="compare-row-label">Price</th>
                            <td class="compare-cell">Tk 89,999</td>
                            <td class="compare-cell">Tk 75,499</td>
                            <td class="compare-cell">Tk 95,000</td>
                            <td class="compare-cell">Tk 68,900</td>
                            <td class="compare-cell">Tk 82,500</td>
                            <td class="compare-cell">Tk 79,999</td>
                        </tr>
                        <tr>
                            <th class="compare-row-label">Rating</th>
                            <td class="compare-cell"><i class="fas fa-star" style="color:#f59e0b;"></i><i class="fas fa-star" style="color:#f59e0b;"></i><i class="fas fa-star" style="color:#f59e0b;"></i><i class="fas fa-star" style="color:#f59e0b;"></i><i class="fas fa-star-half-alt" style="color:#f59e0b;"></i> (90)</td>
                            <td class="compare-cell"><i class="fas fa-star" style="color:#f59e0b;"></i><i class="fas fa-star" style="color:#f59e0b;"></i><i class="fas fa-star" style="color:#f59e0b;"></i><i class="fas fa-star" style="color:#f59e0b;"></i><i class="fas fa-star-half-alt" style="color:#f59e0b;"></i> (87)</td>
                            <td class="compare-cell"><i class="fas fa-star" style="color:#f59e0b;"></i><i class="fas fa-star" style="color:#f59e0b;"></i><i class="fas fa-star" style="color:#f59e0b;"></i><i class="fas fa-star" style="color:#f59e0b;"></i><i class="far fa-star" style="color:#f59e0b;"></i> (84)</td>
                            <td class="compare-cell"><i class="fas fa-star" style="color:#f59e0b;"></i><i class="fas fa-star" style="color:#f59e0b;"></i><i class="fas fa-star" style="color:#f59e0b;"></i><i class="fas fa-star" style="color:#f59e0b;"></i><i class="fas fa-star" style="color:#f59e0b;"></i> (81)</td>
                            <td class="compare-cell"><i class="fas fa-star" style="color:#f59e0b;"></i><i class="fas fa-star" style="color:#f59e0b;"></i><i class="fas fa-star" style="color:#f59e0b;"></i><i class="fas fa-star" style="color:#f59e0b;"></i><i class="fas fa-star-half-alt" style="color:#f59e0b;"></i> (78)</td>
                            <td class="compare-cell"><i class="fas fa-star" style="color:#f59e0b;"></i><i class="fas fa-star" style="color:#f59e0b;"></i><i class="fas fa-star" style="color:#f59e0b;"></i><i class="fas fa-star" style="color:#f59e0b;"></i><i class="far fa-star" style="color:#f59e0b;"></i> (75)</td>
                        </tr>
                        <tr>
                            <th class="compare-row-label">Sold By</th>
                            <td class="compare-cell"><a href="#" style="color:#2563eb; font-weight:600;">Nova Electronics</a></td>
                            <td class="compare-cell"><a href="#" style="color:#2563eb; font-weight:600;">Nova Electronics</a></td>
                            <td class="compare-cell"><a href="#" style="color:#2563eb; font-weight:600;">Nova Electronics</a></td>
                            <td class="compare-cell"><a href="#" style="color:#2563eb; font-weight:600;">Nova Electronics</a></td>
                            <td class="compare-cell"><a href="#" style="color:#2563eb; font-weight:600;">Nova Electronics</a></td>
                            <td class="compare-cell"><a href="#" style="color:#2563eb; font-weight:600;">Nova Electronics</a></td>
                        </tr>
                        <tr>
                            <th class="compare-row-label">Brand</th>
                            <td class="compare-cell">Samsung</td>
                            <td class="compare-cell">Xiaomi</td>
                            <td class="compare-cell">Google</td>
                            <td class="compare-cell">OnePlus</td>
                            <td class="compare-cell">Vivo</td>
                            <td class="compare-cell">Oppo</td>
                        </tr>
                        <tr>
                            <th class="compare-row-label">Series</th>
                            <td class="compare-cell">Galaxy S</td>
                            <td class="compare-cell">Xiaomi 14</td>
                            <td class="compare-cell">Pixel</td>
                            <td class="compare-cell">Number Series</td>
                            <td class="compare-cell">X Series</td>
                            <td class="compare-cell">Find X</td>
                        </tr>
                        <tr>
                            <th class="compare-row-label">GPU</th>
                            <td class="compare-cell">Adreno 750</td>
                            <td class="compare-cell">Adreno 750</td>
                            <td class="compare-cell">Immortalis-G715</td>
                            <td class="compare-cell">Adreno 750</td>
                            <td class="compare-cell">Immortalis-G720</td>
                            <td class="compare-cell">Adreno 750</td>
                        </tr>
                        <tr>
                            <th class="compare-row-label">DirectX</th>
                            <td class="compare-cell">N/A</td>
                            <td class="compare-cell">N/A</td>
                            <td class="compare-cell">N/A</td>
                            <td class="compare-cell">N/A</td>
                            <td class="compare-cell">N/A</td>
                            <td class="compare-cell">N/A</td>
                        </tr>
                        <tr>
                            <th class="compare-row-label">Model</th>
                            <td class="compare-cell">SM-S928B</td>
                            <td class="compare-cell">23116PN5BC</td>
                            <td class="compare-cell">GC3VE</td>
                            <td class="compare-cell">CPH2581</td>
                            <td class="compare-cell">V2308A</td>
                            <td class="compare-cell">PHY110</td>
                        </tr>
                    </tbody>
                </table>"""

pattern = r'<table class="compare-matrix">.*?</table>'
new_content = re.sub(pattern, new_table_html, content, flags=re.DOTALL)

with open(html_file, 'w', encoding='utf-8') as f:
    f.write(new_content)

print("Updated compare-matrix in product.html successfully.")
