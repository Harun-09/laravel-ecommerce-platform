import os, shutil, random

base = 'G:/laragon/www/laravel/E-Commerce/html-templates/images/products'
premium_dir = os.path.join(base, 'premium')

# Get all valid premium images
premium_imgs = [os.path.join(premium_dir, f) for f in os.listdir(premium_dir) if f.endswith('.jpg') and os.path.getsize(os.path.join(premium_dir, f)) > 1000]

keywords = {
    'laptop': ['macbook', 'dell', 'laptop', 'hp', 'lenovo'],
    'phone': ['iphone', 'samsung', 'xiaomi', 'oneplus', 'smartphone'],
    'watch': ['watch', 'smartwatch'],
    'tv': ['tv', 'oled', 'qled'],
    'camera': ['camera', 'canon', 'gopro'],
    'shoe': ['shoe', 'sneaker', 'nike', 'adidas'],
    'shirt': ['tshirt', 'shirt', 'cotton', 'tee', 'polo', 'hoodie', 'jacket'],
    'pant': ['pant', 'trouser', 'jeans', 'jogger', 'chino'],
    'bag': ['bag', 'handbag'],
    'furniture': ['sofa', 'table', 'chair', 'furniture', 'home', 'bed', 'cabinet', 'shelf', 'desk', 'rack'],
    'skincare': ['skincare', 'serum', 'wash', 'moisturizer', 'cream', 'gel'],
    'kitchen': ['cookware', 'kettle', 'flask', 'cooker', 'rice', 'kitchen', 'plate', 'pan', 'box', 'board', 'knife'],
    'fitness': ['dumbbell', 'yoga', 'gym', 'fitness', 'band'],
    'stationery': ['notebook', 'calculator', 'stapler', 'pen'],
    'grocery': ['honey', 'oats', 'oil', 'flour', 'semolina', 'habit']
}

count = 0
for root, dirs, files in os.walk(base):
    if 'premium' in root:
        continue
    for f in files:
        p = os.path.join(root, f)
        if (f.endswith('.jpg') or f.endswith('.png')) and os.path.getsize(p) < 1000:
            filename = f.lower()
            
            selected_source = random.choice(premium_imgs)
            for category, terms in keywords.items():
                if any(term in filename for term in terms):
                    matching = [pi for pi in premium_imgs if any(term in os.path.basename(pi).lower() for term in terms)]
                    if matching:
                        selected_source = random.choice(matching)
                    break
            
            print(f"Replacing broken {f} with {os.path.basename(selected_source)}")
            shutil.copy(selected_source, p)
            count += 1

print(f"Successfully replaced {count} broken images with premium ones!")
