import re
with open('G:/laragon/www/laravel/E-Commerce/html-templates/products.html', 'r', encoding='utf-8') as f:
    html = f.read()
for m in re.finditer(r'<img.*?src=["\']([^"\']+)["\'].*?alt=["\']([^"\']+)["\']', html):
    print(m.group(2)[:20], ':', m.group(1)[:50])
