import re
from collections import Counter

with open('G:/laragon/www/laravel/E-Commerce/html-templates/index.html', 'r', encoding='utf-8') as f:
    html = f.read()

imgs = re.findall(r'<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>', html)
counts = Counter(imgs)
for src, count in counts.most_common(20):
    print(f'{count}x {src}')
