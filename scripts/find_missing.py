import os
import re

paths = [
    r'G:\laragon\www\laravel\E-Commerce\html-templates\index.html',
    r'G:\laragon\www\laravel\E-Commerce\html-templates\products.html',
    r'G:\laragon\www\laravel\E-Commerce\html-templates\product.html'
]

base_dir = r'G:\laragon\www\laravel\E-Commerce\html-templates'
missing = set()

for p in paths:
    if not os.path.exists(p): continue
    with open(p, 'r', encoding='utf-8') as f:
        html = f.read()
        for match in re.findall(r'src=["\'](images/[^"\']+)["\']', html):
            full_path = os.path.join(base_dir, match)
            if not os.path.exists(full_path):
                missing.add(match)

print("Missing images:")
for m in sorted(missing):
    print(m)
