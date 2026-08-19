import os
import re
import random

base_dir = 'G:/laragon/www/laravel/E-Commerce/html-templates'
html_files = [f for f in os.listdir(base_dir) if f.endswith('.html')]

# List of all missing local images to replace
lock_counter = 500

for html_file in html_files:
    path = os.path.join(base_dir, html_file)
    with open(path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # We will find all <img src="..."> tags
    # and check if the src exists locally. If not, or if it is a placeholder, we replace it.
    
    def replace_src(match):
        global lock_counter
        full_tag = match.group(0)
        src = match.group(1)
        
        # If it's an external url, leave it alone
        if src.startswith('http'):
            return full_tag
            
        # Check if local file exists
        local_path = os.path.join(base_dir, src)
        
        is_missing = not os.path.exists(local_path)
        is_placeholder = 'no-product-image' in src
        
        if is_missing or is_placeholder:
            # Generate a loremflickr URL
            keyword = 'product'
            if 'electronics' in src or 'laptop' in src or 'phone' in src or 'watch' in src:
                keyword = 'electronics'
            elif 'fashion' in src or 'shirt' in src or 'shoes' in src or 'jacket' in src:
                keyword = 'fashion'
            elif 'kitchen' in src or 'home' in src or 'sofa' in src:
                keyword = 'furniture'
            
            new_src = f'https://loremflickr.com/400/400/{keyword}?lock={lock_counter}'
            lock_counter += 1
            
            # Replace the src in the tag
            new_tag = full_tag.replace(f'"{src}"', f'"{new_src}"').replace(f"'{src}'", f"'{new_src}'")
            return new_tag
            
        return full_tag

    # Use regex to find and replace <img ... src="X">
    new_content = re.sub(r'<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>', replace_src, content)
    
    # Also replace any onerror fallback that sets to placeholder
    new_content = re.sub(r'this\.src=[\'"]images/placeholders/no-product-image\.svg[\'"]', f"this.src='https://loremflickr.com/400/400/product?lock=999'", new_content)

    with open(path, 'w', encoding='utf-8') as f:
        f.write(new_content)

print(f"Replaced all missing images and placeholders globally! Next lock: {lock_counter}")
