import re
import os

base = 'G:/laragon/www/laravel/E-Commerce/html-templates'
with open(os.path.join(base, 'index.html'), 'r', encoding='utf-8') as f:
    html = f.read()
    
missing_count = 0
for match in re.finditer(r'<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>', html):
    src = match.group(1)
    if 'curated' in src:
        path = os.path.join(base, src)
        if not os.path.exists(path):
            print('MISSING:', src)
            missing_count += 1
print('Total missing:', missing_count)
