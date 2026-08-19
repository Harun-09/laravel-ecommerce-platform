import os
import shutil

img_dir = r'G:\laragon\www\laravel\E-Commerce\html-templates\images\products\premium'

product_slugs = [
    'samsung-s24', 'xiaomi-14', 'oneplus-nord-4', 'macbook-pro', 'dell-xps', 
    'ipad-pro', 'sony-headphones', 'airpods-pro', 'samsung-tv', 'lg-oled', 
    'ps5', 'xbox', 'canon-r5', 'gopro', 'apple-watch-ultra', 'galaxy-watch', 
    'dji-mini', 'razer-mouse', 'keychron', 'switch', 'cotton-tshirt', 
    'denim-jacket', 'nike-af1', 'adidas-shoes', 'leather-jacket', 
    'sunglasses', 'handbag', 'analog-watch', 'chino-pants', 'winter-hoodie', 
    'l-shape-sofa', 'coffee-table', 'office-chair', 'rice-cooker', 
    'perfume', 'skincare', 'ceramic-vase', 'ring-light', 'air-fryer', 'atomic-habits'
]

fallback_map = {
    'xiaomi-14': 'iphone.jpg',
    'ipad-pro': 'laptop.jpg',
    'samsung-tv': 'macbook-pro.jpg',
    'lg-oled': 'macbook-pro.jpg',
    'ps5': 'canon-r5.jpg',
    'xbox': 'canon-r5.jpg',
    'gopro': 'camera.jpg',
    'galaxy-watch': 'apple-watch-ultra.jpg',
    'dji-mini': 'camera.jpg',
    'razer-mouse': 'laptop.jpg',
    'keychron': 'laptop.jpg',
    'switch': 'apple-watch-ultra.jpg',
    'chino-pants': 'denim-jacket.jpg',
    'coffee-table': 'l-shape-sofa.jpg',
    'ring-light': 'ceramic-vase.jpg',
    'air-fryer': 'rice-cooker.jpg'
}

for slug in product_slugs:
    p = os.path.join(img_dir, f'{slug}.jpg')
    if not os.path.exists(p):
        fallback = fallback_map.get(slug)
        if fallback:
            src = os.path.join(img_dir, fallback)
            if os.path.exists(src):
                print(f'Copying {src} to {p}')
                shutil.copy(src, p)
            else:
                # pick any
                print(f'Fallback {fallback} missing, picking random')
                files = os.listdir(img_dir)
                if files:
                    shutil.copy(os.path.join(img_dir, files[0]), p)
        else:
            # pick random
            files = os.listdir(img_dir)
            if files:
                print(f'Random fallback for {slug}')
                shutil.copy(os.path.join(img_dir, files[0]), p)

print("Done fixing missing images.")
