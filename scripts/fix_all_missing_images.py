import re, os, shutil, random

paths = ['G:/laragon/www/laravel/E-Commerce/html-templates/index.html', 'G:/laragon/www/laravel/E-Commerce/html-templates/products.html', 'G:/laragon/www/laravel/E-Commerce/html-templates/product.html']
base = 'G:/laragon/www/laravel/E-Commerce/html-templates/'
premium_dir = os.path.join(base, 'images', 'products', 'premium')

# Get list of premium images to use as sources
premium_imgs = [os.path.join(premium_dir, f) for f in os.listdir(premium_dir) if f.endswith('.jpg')]

missing_imgs = set()

for p in paths:
    with open(p, 'r', encoding='utf-8') as f:
        html = f.read()
    
    # Find all image src attributes that start with images/products
    imgs = set(re.findall(r'src=[\"\'](images/products/[^\"\']+)[\"\']', html))
    for img in imgs:
        full_path = os.path.join(base, img)
        if not os.path.exists(full_path):
            missing_imgs.add(full_path)

print(f"Found {len(missing_imgs)} missing images to fix.")

for missing_path in missing_imgs:
    # Ensure directory exists
    os.makedirs(os.path.dirname(missing_path), exist_ok=True)
    
    # Try to find a somewhat related premium image based on the filename
    filename = os.path.basename(missing_path).lower()
    
    # Keyword matching for better assignments
    assigned = None
    keywords = {
        'laptop': ['macbook', 'dell', 'laptop', 'hp', 'lenovo'],
        'phone': ['iphone', 'samsung', 'xiaomi', 'oneplus', 'smartphone'],
        'watch': ['watch', 'smartwatch'],
        'tv': ['tv', 'oled', 'qled'],
        'camera': ['camera', 'canon', 'gopro'],
        'shoe': ['shoe', 'sneaker', 'nike', 'adidas'],
        'shirt': ['tshirt', 'shirt', 'cotton'],
        'jacket': ['jacket', 'hoodie', 'denim'],
        'bag': ['bag', 'handbag'],
        'sofa': ['sofa', 'table', 'chair', 'furniture', 'home'],
        'skincare': ['skincare', 'serum', 'wash', 'moisturizer'],
        'kitchen': ['cookware', 'kettle', 'flask', 'cooker', 'rice', 'kitchen']
    }
    
    selected_source = random.choice(premium_imgs) # default random
    
    for category, terms in keywords.items():
        if any(term in filename for term in terms):
            # Find a premium image matching this category
            matching_premiums = [p for p in premium_imgs if any(term in os.path.basename(p).lower() for term in terms)]
            if matching_premiums:
                selected_source = random.choice(matching_premiums)
            break
            
    print(f"Fixing {missing_path} with {os.path.basename(selected_source)}")
    shutil.copy(selected_source, missing_path)

print("All missing images have been filled with premium open-source images!")
