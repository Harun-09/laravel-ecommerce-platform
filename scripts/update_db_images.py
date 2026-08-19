import os
import re
import urllib.parse
from sqlalchemy import create_engine, text
import pymysql

# Laravel database connection
engine = create_engine('mysql+pymysql://root:@localhost/ecommerce_db')

with engine.connect() as conn:
    # Get all products and their primary images
    query = text("""
        SELECT p.id, p.name, pi.id as image_id, pi.image
        FROM products p
        LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
    """)
    result = conn.execute(query)
    
    updated_count = 0
    
    for row in result:
        product_id = row[0]
        name = row[1]
        image_id = row[2]
        
        # Generate pollinations AI prompt based on name
        clean_name = re.sub(r'[^a-zA-Z0-9\s]', '', name)
        prompt = f"premium_ecommerce_product_photography_of_{clean_name.replace(' ', '_')}_isolated_on_clean_white_background_high_quality_studio_lighting"
        new_url = f"https://image.pollinations.ai/prompt/{urllib.parse.quote(prompt)}?width=400&height=400&nologo=true"
        
        if image_id:
            # Update existing primary image
            update_q = text("UPDATE product_images SET image = :img WHERE id = :id")
            conn.execute(update_q, {"img": new_url, "id": image_id})
        else:
            # Insert new primary image
            insert_q = text("INSERT INTO product_images (product_id, image, is_primary, `order`, created_at, updated_at) VALUES (:pid, :img, 1, 0, NOW(), NOW())")
            conn.execute(insert_q, {"pid": product_id, "img": new_url})
            
        updated_count += 1
        
    conn.commit()
    print(f"Updated {updated_count} product images with Pollinations AI!")
