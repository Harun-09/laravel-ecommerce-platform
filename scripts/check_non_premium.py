import re
with open('G:/laragon/www/laravel/E-Commerce/html-templates/index.html', 'r', encoding='utf-8') as f:
    html = f.read()

imgs = re.findall(r'<img[^>]+src=[\"\']([^\"\']+)[\"\']', html)
for i, img in enumerate(imgs):
    if 'images/products/' in img and 'premium/' not in img:
        print(f'{i}: {img}')
