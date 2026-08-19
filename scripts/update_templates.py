import os
import re

html_dir = r'G:\laragon\www\laravel\E-Commerce\html-templates'

with open(os.path.join(html_dir, 'index.html'), 'r', encoding='utf-8') as f:
    index_content = f.read()

# Extract header (everything from <!DOCTYPE html> to </header>)
header_match = re.search(r'<!DOCTYPE html>.*?</header>', index_content, re.DOTALL)
header = header_match.group(0)

nav = """
    <!-- Navigation -->
    <nav class="nav">
        <div class="container">
            <ul class="nav-menu">
                <li><a href="products.html"><i class="fas fa-th-large"></i> Shop All Departments</a></li>
                <li><a href="products.html">Electronics</a></li>
                <li><a href="products.html">Fashion</a></li>
                <li><a href="products.html">Home & Living</a></li>
                <li><a href="products.html">Beauty</a></li>
                <li><a href="products.html">Sports</a></li>
                <li><a href="products.html">Groceries</a></li>
            </ul>
        </div>
    </nav>
"""

# Extract footer (everything from <footer class="footer"> to the end)
footer_match = re.search(r'<footer class="footer">.*</html>', index_content, re.DOTALL)
footer = footer_match.group(0)

# We want to replace the shell of all files except index.html, admin-dashboard.html
files = [f for f in os.listdir(html_dir) if f.endswith('.html') and f not in ('index.html', 'admin-dashboard.html')]

for file in files:
    file_path = os.path.join(html_dir, file)
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Extract the main content of the file
    # Usually it's after </header> and before <footer class="footer">
    # Some files use <main...>, some use <div class="container section pdp">
    
    main_match = re.search(r'</header>(.*?)<footer class="footer">', content, re.DOTALL)
    if not main_match:
        # Maybe no footer?
        main_match = re.search(r'</header>(.*?)(<script>|</body>)', content, re.DOTALL)
    
    if main_match:
        main_content = main_match.group(1).strip()
        new_content = header + '\n' + nav + '\n    ' + main_content + '\n\n    ' + footer
        
        # In product.html, the title should be Product title, but let's just leave the one from index.html and maybe replace the title tag
        # Let's extract the title from the original file
        title_match = re.search(r'<title>(.*?)</title>', content)
        if title_match:
            original_title = title_match.group(1)
            new_content = re.sub(r'<title>.*?</title>', f'<title>{original_title}</title>', new_content)
        
        # Also extract any custom styles from the original <head>
        style_match = re.search(r'<style>(.*?)</style>', content, re.DOTALL)
        if style_match:
            original_style = style_match.group(0)
            new_content = new_content.replace('</head>', f'{original_style}\n</head>')
            
        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f'Updated {file}')
    else:
        print(f'Could not find main content bounds in {file}')
