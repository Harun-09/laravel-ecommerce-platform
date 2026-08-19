import os
import random

categories = [
    {
        "id": "cat-electronics",
        "title": "Top Electronics & Gadgets!",
        "keyword": "laptop",
        "vendor": "Tech Galaxy"
    },
    {
        "id": "cat-appliances",
        "title": "Top Selling Home Appliances!",
        "keyword": "appliance",
        "vendor": "Home Essentials"
    },
    {
        "id": "cat-fashion",
        "title": "Fashion Essentials on Sale!",
        "keyword": "fashion",
        "vendor": "Fashion Hub BD"
    },
    {
        "id": "cat-beauty",
        "title": "Beauty & Grooming Essentials!",
        "keyword": "beauty",
        "vendor": "Glow Up BD"
    },
    {
        "id": "cat-sports",
        "title": "Fitness & Sports Picks!",
        "keyword": "fitness",
        "vendor": "FitGear"
    },
    {
        "id": "cat-groceries",
        "title": "Daily Groceries & Essentials!",
        "keyword": "grocery",
        "vendor": "FreshMart"
    },
    {
        "id": "cat-books",
        "title": "Bestselling Books & Stationery!",
        "keyword": "books",
        "vendor": "Read & Write"
    }
]

adjectives = ["Premium", "Essential", "Modern", "Classic", "Advanced", "Ultra", "Smart", "Pro", "Elite", "Lux", "Sleek", "Compact", "Heavy-Duty", "Everyday", "Exclusive"]
nouns = {
    "laptop": ["Notebook", "Ultrabook", "Workstation", "Monitor", "Keyboard", "Mouse", "Headphones", "Speaker", "Webcam", "Tablet"],
    "appliance": ["Blender", "Microwave", "Oven", "Kettle", "Toaster", "Vacuum", "Purifier", "Heater", "Cooler", "Iron"],
    "fashion": ["T-Shirt", "Jeans", "Jacket", "Sneakers", "Watch", "Sunglasses", "Wallet", "Belt", "Scarf", "Backpack"],
    "beauty": ["Serum", "Moisturizer", "Cleanser", "Toner", "Mask", "Scrub", "Lotion", "Perfume", "Shampoo", "Conditioner"],
    "fitness": ["Dumbbell", "Yoga Mat", "Resistance Band", "Treadmill", "Kettlebell", "Foam Roller", "Jump Rope", "Bench", "Gloves", "Bottle"],
    "grocery": ["Organic Rice", "Olive Oil", "Coffee Beans", "Green Tea", "Honey", "Almonds", "Oats", "Pasta", "Cereal", "Spices"],
    "books": ["Notebook", "Planner", "Pen Set", "Marker", "Desk Organizer", "Journal", "Sketchbook", "Calculator", "Sticky Notes", "Folder"]
}
desc_prefixes = ["High-quality", "Durable", "Reliable", "Bestselling", "Top-rated", "Must-have", "Perfect for daily use", "Designed for excellence", "Customer favorite", "New arrival"]

html_output = ""
html_output += """
<script>
function toggleViewMore(sectionId, btn) {
    var section = document.getElementById(sectionId);
    var hiddenItems = section.querySelectorAll('.hidden-item');
    var isExpanded = btn.getAttribute('data-expanded') === 'true';
    
    hiddenItems.forEach(function(item) {
        if (isExpanded) {
            item.style.display = 'none';
        } else {
            item.style.display = '';
        }
    });
    
    if (isExpanded) {
        btn.innerHTML = 'View All <i class="fas fa-arrow-right"></i>';
        btn.setAttribute('data-expanded', 'false');
    } else {
        btn.innerHTML = 'View Less <i class="fas fa-arrow-up"></i>';
        btn.setAttribute('data-expanded', 'true');
    }
}
</script>
"""

global_image_id = 1

for cat in categories:
    html_output += f"""
        <!-- Category: {cat['title']} -->
        <section class="section" id="{cat['id']}">
            <div class="container">
                <div class="section-title">
                    <h2>{cat['title']}</h2>
                    <a href="javascript:void(0)" data-expanded="false" onclick="toggleViewMore('{cat['id']}', this)">View All <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="grid grid-5">
"""
    
    for i in range(30):
        # Generate unique product data
        name = f"{random.choice(adjectives)} {random.choice(nouns[cat['keyword']])} {i+1}X"
        desc = f"{random.choice(desc_prefixes)} {cat['keyword'].lower()} item."
        price = random.randint(500, 15000)
        old_price = int(price * random.uniform(1.1, 1.5))
        discount = int((1 - (price / old_price)) * 100)
        
        # Unique loremflickr image
        img_url = f"https://loremflickr.com/400/400/{cat['keyword']}?lock={global_image_id}"
        global_image_id += 1
        
        hidden_style = ' style="display: none;" class="product-card hidden-item"' if i >= 10 else ' class="product-card"'
        
        html_output += f"""
                    <div{hidden_style} data-product-url="product.html" role="link" tabindex="0">
                        <div style="position: absolute; top: 10px; left: 10px; background: #ef4444; color: white; font-size: 11px; font-weight: 700; padding: 4px 8px; border-radius: 4px; z-index: 2;">-{discount}%</div>
                        <a href="product.html"><img src="{img_url}" alt="{name}" loading="lazy"></a>
                        <div class="content">
                            <div class="vendor">{cat['vendor']}</div>
                            <h3><a href="product.html">{name}</a></h3>
                            <p class="desc">{desc}</p>
                            <div class="price"><span class="current">Tk {price:,}</span> <span class="old" style="text-decoration: line-through; color: #9ca3af; font-size: 13px; margin-left: 8px;">Tk {old_price:,}</span></div>
                            <div class="rating">
                                <i class="fas fa-star" style="color:#facc15;"></i>
                                <i class="fas fa-star" style="color:#facc15;"></i>
                                <i class="fas fa-star" style="color:#facc15;"></i>
                                <i class="fas fa-star" style="color:#facc15;"></i>
                                <i class="fas fa-star-half-alt" style="color:#facc15;"></i>
                                <span>({random.randint(10, 200)})</span>
                            </div>
                            <div class="actions">
                                <button type="button" class="add-cart" onclick="event.preventDefault();event.stopPropagation();addToCart({global_image_id})"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
                                <button type="button" class="wishlist" onclick="event.preventDefault();event.stopPropagation();toggleWishlist({global_image_id},this)"><i class="fas fa-heart"></i></button>
                            </div>
                        </div>
                    </div>
"""
    
    html_output += """
                </div>
            </div>
        </section>
"""

# Insert into index.html
html_file = 'G:/laragon/www/laravel/E-Commerce/html-templates/index.html'
with open(html_file, 'r', encoding='utf-8') as f:
    content = f.read()

newsletter_marker = "<!-- Newsletter -->"

if newsletter_marker in content:
    # Insert right before Newsletter
    new_content = content.replace(newsletter_marker, html_output + "\n\n        " + newsletter_marker)
    with open(html_file, 'w', encoding='utf-8') as f:
        f.write(new_content)
    print("Successfully injected 210 unique products across 7 categories into index.html!")
else:
    print("Error: Could not find Newsletter section to insert before.")
