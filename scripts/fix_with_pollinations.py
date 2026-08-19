import re
import os
import urllib.parse

base_dir = 'G:/laragon/www/laravel/E-Commerce/html-templates'
html_files = [f for f in os.listdir(base_dir) if f.endswith('.html')]

# We will find every product card and replace the image src inside it with a targeted AI prompt.
# A product card looks like:
# <div class="product-card"...>
# ... <img src="..." alt="PRODUCT TITLE" ...>
# or <h3><a href="...">PRODUCT TITLE</a></h3>

for html_file in html_files:
    path = os.path.join(base_dir, html_file)
    with open(path, 'r', encoding='utf-8') as f:
        html = f.read()
    
    # We will use regex to find <img ... alt="Title" ...> inside product cards.
    # To be safe, we will just replace the src of any img that has 'curated', 'loremflickr', 'placeholder'
    # with a pollinations URL based on its alt text!
    
    def replace_img(match):
        full_tag = match.group(0)
        
        # Try to find alt text
        alt_match = re.search(r'alt=[\'"]([^\'"]+)[\'"]', full_tag)
        if alt_match:
            title = alt_match.group(1).strip()
        else:
            title = "Premium_Product"
            
        # Clean up title for prompt
        clean_title = re.sub(r'[^a-zA-Z0-9\s]', '', title)
        prompt = f"premium_ecommerce_product_photography_of_{clean_title.replace(' ', '_')}_isolated_on_clean_white_background_high_quality_studio_lighting"
        
        # Extract src
        src_match = re.search(r'src=[\'"]([^\'"]+)[\'"]', full_tag)
        if not src_match:
            return full_tag
            
        src = src_match.group(1)
        
        # If it's one of our problematic images (curated, loremflickr, dummyimage, placeholders)
        # Actually, let's just replace curated and placeholders, because we want to fix the current broken ones.
        if 'curated' in src or 'loremflickr' in src or 'placeholder' in src or 'no-product-image' in src:
            new_src = f"https://image.pollinations.ai/prompt/{urllib.parse.quote(prompt)}?width=400&height=400&nologo=true"
            new_tag = full_tag.replace(f'"{src}"', f'"{new_src}"').replace(f"'{src}'", f"'{new_src}'")
            return new_tag
            
        return full_tag

    new_html = re.sub(r'<img[^>]+>', replace_img, html)
    
    # Also remove any onerror attributes that might be messing things up
    new_html = re.sub(r'onerror=[\'"][^\'"]*[\'"]', '', new_html)

    with open(path, 'w', encoding='utf-8') as f:
        f.write(new_html)

print("Successfully applied precise AI image generation for all products!")
