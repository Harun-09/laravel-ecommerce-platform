import os
import re
import urllib.request
import json
import random

base_dir = 'G:/laragon/www/laravel/E-Commerce/html-templates'
curated_dir = os.path.join(base_dir, 'images', 'products', 'curated')
os.makedirs(curated_dir, exist_ok=True)

# 1. Fetch products from DummyJSON
print("Fetching products from DummyJSON...")
req = urllib.request.Request('https://dummyjson.com/products?limit=194', headers={'User-Agent': 'Mozilla/5.0'})
try:
    resp = urllib.request.urlopen(req)
    data = json.loads(resp.read().decode('utf-8'))
    products = data.get('products', [])
except Exception as e:
    print("Failed to fetch dummyjson:", e)
    products = []

downloaded_images = []

print(f"Found {len(products)} products. Downloading images...")
for p in products:
    img_url = p.get('thumbnail')
    if not img_url:
        continue
    
    ext = img_url.split('.')[-1]
    if len(ext) > 4:
        ext = 'jpg'
    filename = f"prod_{p['id']}_{p['title'].replace(' ', '_').replace('/', '')}.{ext}"
    filename = "".join([c for c in filename if c.isalpha() or c.isdigit() or c in '._-']).rstrip()
    
    save_path = os.path.join(curated_dir, filename)
    
    if not os.path.exists(save_path):
        try:
            img_req = urllib.request.Request(img_url, headers={'User-Agent': 'Mozilla/5.0'})
            img_data = urllib.request.urlopen(img_req).read()
            with open(save_path, 'wb') as f:
                f.write(img_data)
        except Exception as e:
            print(f"Failed to download {img_url}: {e}")
            continue
            
    downloaded_images.append(f"images/products/curated/{filename}")

print(f"Successfully secured {len(downloaded_images)} local images.")

if not downloaded_images:
    print("No images downloaded. Checking if there are existing ones...")
    if os.path.exists(curated_dir):
        downloaded_images = [f"images/products/curated/{f}" for f in os.listdir(curated_dir) if f.endswith(('.jpg','.png','.webp'))]
    if not downloaded_images:
        print("No images available. Exiting.")
        exit()

# 2. Update HTML files
html_files = [f for f in os.listdir(base_dir) if f.endswith('.html')]

# We shuffle so it feels random, but they are all safe
random.shuffle(downloaded_images)
img_index = 0

for html_file in html_files:
    path = os.path.join(base_dir, html_file)
    with open(path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    def replace_src(match):
        global img_index
        full_tag = match.group(0)
        src = match.group(1)
        
        # Replace anything that is loremflickr or a placeholder or unsplash
        if 'loremflickr.com' in src or 'no-product-image' in src or 'unsplash' in src or 'dummyimage' in src:
            new_src = downloaded_images[img_index % len(downloaded_images)]
            img_index += 1
            new_tag = full_tag.replace(f'"{src}"', f'"{new_src}"').replace(f"'{src}'", f"'{new_src}'")
            return new_tag
        return full_tag

    new_content = re.sub(r'<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>', replace_src, content)
    
    # Also replace any explicit JS fallbacks that might trigger
    new_content = re.sub(r'this\.src=[\'"]https://loremflickr\.com/[^\'"]+[\'"]', f"this.src='{downloaded_images[0]}'", new_content)
    
    with open(path, 'w', encoding='utf-8') as f:
        f.write(new_content)

print(f"All HTML files updated with {len(downloaded_images)} safe local images! Total replacements: {img_index}")
