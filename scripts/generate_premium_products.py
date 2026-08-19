import os
import urllib.request
import random

ids = [
    ('headphones', '1505740420928-5e560c06d30e', 'Premium Noise-Cancelling Headphones', '8,999', 'Electronics'),
    ('watch', '1523275335684-37898b6baf30', 'Classic Analog Watch', '4,500', 'Fashion'),
    ('laptop', '1507764923504-cd90bf7da772', 'UltraSlim Business Laptop', '75,000', 'Electronics'),
    ('apple_watch', '1546868871-7041f2a55e12', 'Smart Watch Series 8', '35,000', 'Electronics'),
    ('iphone', '1585060544812-6b45742d762f', 'Smartphone 13 Pro Max', '95,000', 'Electronics'),
    ('camera', '1526170375885-4d8ecf77b99f', 'DSLR Camera 4K', '45,000', 'Electronics'),
    ('red_shoes', '1542291026-7eec264c27ff', 'Red Running Sneakers', '2,500', 'Fashion'),
    ('white_sneakers', '1595950653106-6c9ebd614d3a', 'Classic White Sneakers', '1,800', 'Fashion'),
    ('sofa', '1505691938895-1758d7feb511', 'Modern Living Room Sofa', '25,000', 'Home & Living'),
    ('perfume', '1559551409-dadc959f76b8', 'Luxury Fragrance 100ml', '3,500', 'Beauty & Health'),
    ('tshirt', '1521572163474-6864f9cf17ab', 'Essential White T-Shirt', '600', 'Fashion'),
    ('earbuds', '1583394838336-acd977736f90', 'Wireless Earbuds Pro', '5,500', 'Electronics'),
    ('bag', '1591369822096-ffd140ec948f', 'Designer Leather Handbag', '8,000', 'Fashion'),
    ('leather_jacket', '1551028719-00167b16eac5', 'Men\'s Leather Jacket', '4,500', 'Fashion'),
    ('kitchen', '1513694203232-719a280e022f', 'Minimalist Kitchen Set', '15,000', 'Home & Living'),
    ('decor', '1584622650111-993a426fbf0a', 'Aesthetic Room Decor', '1,200', 'Home & Living'),
    ('drone', '1611186871340-1e5f884a441e', '4K Aerial Drone', '42,000', 'Electronics'),
    ('sunglasses', '1572569696892-2e55716ce54b', 'Polarized Sunglasses', '1,500', 'Fashion'),
    ('skincare', '1611930022073-b7a4ba5fcccd', 'Organic Skincare Routine', '2,200', 'Beauty & Health'),
    ('watch2', '1523170335258-f5ed11844a49', 'Minimalist Wrist Watch', '3,000', 'Fashion'),
]

base_dir = r'G:\laragon\www\laravel\E-Commerce\html-templates'
img_dir = os.path.join(base_dir, 'images', 'products', 'premium')
os.makedirs(img_dir, exist_ok=True)

html_cards = []

for i, (name, uid, title, price, cat) in enumerate(ids):
    img_path = os.path.join(img_dir, f'{name}.jpg')
    img_url = f'https://images.unsplash.com/photo-{uid}?w=400&h=400&fit=crop&q=80'
    
    if not os.path.exists(img_path):
        print(f"Downloading {title}...")
        try:
            req = urllib.request.Request(img_url, headers={'User-Agent': 'Mozilla/5.0'})
            with urllib.request.urlopen(req) as response, open(img_path, 'wb') as out_file:
                out_file.write(response.read())
        except Exception as e:
            print(f"Error downloading {img_url}: {e}")
            continue

    rel_path = f'images/products/premium/{name}.jpg'
    
    card_html = f'''
                    <div class="product-card" data-product-url="product.html" role="link" tabindex="0">
                        <a href="product.html"><img src="{rel_path}" alt="{title}" loading="lazy"></a>
                        <div class="content">
                            <div class="vendor">{cat} Store</div>
                            <h3><a href="product.html">{title}</a></h3>
                            <p class="desc">Premium quality product curated for you.</p>
                            <div class="price"><span class="current">৳{price}</span></div>
                            <div class="rating">
                                <i class="fas fa-star" style="color:#facc15;"></i>
                                <i class="fas fa-star" style="color:#facc15;"></i>
                                <i class="fas fa-star" style="color:#facc15;"></i>
                                <i class="fas fa-star" style="color:#facc15;"></i>
                                <i class="fas fa-star" style="color:#facc15;"></i>
                                <span>({random.randint(10, 500)})</span>
                            </div>
                            <div class="actions">
                                <button type="button" class="add-cart" onclick="event.preventDefault();event.stopPropagation();addToCart({100+i})"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" class="wishlist" onclick="event.preventDefault();event.stopPropagation();toggleWishlist({100+i},this)"><i class="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>'''
    html_cards.append(card_html)

products_html_insert = ''.join(html_cards)

path_products = os.path.join(base_dir, 'products.html')
with open(path_products, 'r', encoding='utf-8') as f:
    ph = f.read()
if 'images/products/premium' not in ph:
    ph = ph.replace('<div class="grid grid-4">', '<div class="grid grid-4">\n' + products_html_insert)
    with open(path_products, 'w', encoding='utf-8') as f:
        f.write(ph)

path_index = os.path.join(base_dir, 'index.html')
with open(path_index, 'r', encoding='utf-8') as f:
    ih = f.read()

if 'images/products/premium' not in ih:
    parts = ih.split('<div class="grid grid-5">')
    if len(parts) >= 3:
        ih = parts[0] + '<div class="grid grid-5">\n' + ''.join(html_cards[:10]) + parts[1] + '<div class="grid grid-5">\n' + ''.join(html_cards[10:]) + parts[2]
        if len(parts) > 3:
            for extra in parts[3:]:
                ih += '<div class="grid grid-5">' + extra
        with open(path_index, 'w', encoding='utf-8') as f:
            f.write(ih)

print("Done generating premium products!")
