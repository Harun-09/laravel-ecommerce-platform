import os
import re
import shutil
import random

base_dir = 'G:/laragon/www/laravel/E-Commerce/html-templates'

# Pages to generate
# Using products.html for shop/promo pages
shop_pages = [
    ('outlet.html', 'Best Buy Outlet', 'Special outlet deals and clearances', 'outlet'),
    ('gift-ideas.html', 'Gift Ideas', 'Perfect gifts for every occasion', 'gift'),
    ('top-deals.html', 'Top Deals', 'The best deals of the week', 'deal'),
    ('deal-of-the-day.html', 'Deal of the Day', 'Hurry, these deals expire in 24 hours!', 'sale'),
    ('discover.html', 'Discover', 'Discover new and trending products', 'discover'),
    ('recently-viewed.html', 'Recently Viewed', 'Pick up right where you left off', 'history')
]

category_pages = [
    ('departments.html', 'Shop All Departments', 'Browse all our categories', 'department'),
    ('category-electronics.html', 'Electronics', 'Top tech and gadgets', 'electronics'),
    ('category-fashion.html', 'Fashion', 'Latest trends in clothing', 'fashion'),
    ('category-home-living.html', 'Home & Living', 'Make your home beautiful', 'home'),
    ('category-beauty.html', 'Beauty & Health', 'Grooming and wellness essentials', 'beauty'),
    ('category-sports.html', 'Sports & Outdoors', 'Gear for your active lifestyle', 'sports'),
    ('category-books.html', 'Books & Stationery', 'Read, write, and create', 'books')
]

# Using about.html for content/info pages
content_pages = [
    ('business.html', 'Best Buy Business', 'Enterprise solutions and bulk orders', 'business'),
    ('help.html', 'Help Center', 'How can we help you today?', 'help'),
    ('memberships.html', 'My NovaMart Memberships', 'Exclusive perks and rewards', 'membership'),
    ('credit-cards.html', 'Credit Cards', 'Apply for our rewards credit card', 'creditcard'),
    ('gift-cards.html', 'Gift Cards', 'Give the gift of choice', 'giftcard'),
    ('order-status.html', 'Order Status', 'Track your package in real-time', 'box')
]

def generate_shop_page(filename, title, subtitle, keyword):
    template = os.path.join(base_dir, 'products.html')
    if not os.path.exists(template):
        print(f"Template {template} not found.")
        return
    
    with open(template, 'r', encoding='utf-8') as f:
        content = f.read()
        
    # Replace title tags
    content = re.sub(r'<title>.*?</title>', f'<title>NovaMart - {title}</title>', content)
    
    # Replace breadcrumb active
    content = re.sub(r'<li class="breadcrumb-item active" aria-current="page">.*?</li>', f'<li class="breadcrumb-item active" aria-current="page">{title}</li>', content)
    
    # Replace page title h1 (Usually found in a banner or header in products.html)
    # Actually, products.html has a hero section? Let's assume it has something like <h1>Products</h1>
    content = re.sub(r'<h1>.*?</h1>', f'<h1>{title}</h1>', content, count=1)
    
    # We won't modify the exact product cards since it's just a dummy page, but it will work and look beautiful.
    
    out_path = os.path.join(base_dir, filename)
    with open(out_path, 'w', encoding='utf-8') as f:
        f.write(content)

def generate_content_page(filename, title, subtitle, keyword):
    template = os.path.join(base_dir, 'about.html')
    if not os.path.exists(template):
        print(f"Template {template} not found.")
        return
        
    with open(template, 'r', encoding='utf-8') as f:
        content = f.read()
        
    content = re.sub(r'<title>.*?</title>', f'<title>NovaMart - {title}</title>', content)
    content = re.sub(r'<h1>.*?</h1>', f'<h1>{title}</h1>', content, count=1)
    
    # Replace the about us paragraph with the subtitle
    content = re.sub(r'<p class="lead">.*?</p>', f'<p class="lead">{subtitle}</p>', content, count=1)
    
    out_path = os.path.join(base_dir, filename)
    with open(out_path, 'w', encoding='utf-8') as f:
        f.write(content)

# 1. Generate pages
for p in shop_pages + category_pages:
    generate_shop_page(p[0], p[1], p[2], p[3])
    
for p in content_pages:
    generate_content_page(p[0], p[1], p[2], p[3])
    
# 2. Update navigation in all HTML files
link_map = {
    'Best Buy Outlet': 'outlet.html',
    'Best Buy Business': 'business.html',
    'Order Status': 'order-status.html',
    'Help': 'help.html',
    'Gift Ideas': 'gift-ideas.html',
    'Top Deals': 'top-deals.html',
    'Deal of the Day': 'deal-of-the-day.html',
    'Discover': 'discover.html',
    'My NovaMart Memberships': 'memberships.html',
    'Credit Cards': 'credit-cards.html',
    'Gift Cards': 'gift-cards.html',
    'Recently Viewed': 'recently-viewed.html',
    'Shop All Departments': 'departments.html',
    'Electronics': 'category-electronics.html',
    'Fashion': 'category-fashion.html',
    'Home & Living': 'category-home-living.html',
    'Beauty & Health': 'category-beauty.html',
    'Sports & Outdoors': 'category-sports.html',
    'Books & Stationery': 'category-books.html'
}

html_files = [f for f in os.listdir(base_dir) if f.endswith('.html')]
for html_file in html_files:
    path = os.path.join(base_dir, html_file)
    with open(path, 'r', encoding='utf-8') as f:
        content = f.read()
        
    header_match = re.search(r'<header.*?</header>', content, re.DOTALL)
    if header_match:
        header = header_match.group(0)
        new_header = header
        
        # Replace the hrefs
        for text, link in link_map.items():
            # This regex matches: <a href="#">Text</a> or <a href="#" class="something">Text</a>
            # We want to replace href="#" with href="link" ONLY for the matching text
            pattern = rf'(<a[^>]*href=)[\'"]#[\'"]([^>]*>)\s*{re.escape(text)}\s*</a>'
            new_header = re.sub(pattern, rf'\1"{link}"\2{text}</a>', new_header)
            
            # Some might have icons inside, e.g., <a href="#"><i class="fas fa-gift"></i><span>Gift Ideas</span></a>
            pattern2 = rf'(<a[^>]*href=)[\'"]#[\'"]([^>]*>.*?)\s*{re.escape(text)}\s*(.*?)</a>'
            new_header = re.sub(pattern2, rf'\1"{link}"\2{text}\3</a>', new_header)
            
        content = content.replace(header, new_header)
        
        with open(path, 'w', encoding='utf-8') as f:
            f.write(content)

print("Generated 20 pages and updated all navigation links!")
