-- Fresh Produce (Category 1)
INSERT INTO products (category_id, name, description, price, stock_quantity, image_url) VALUES
(1, 'Sayur Kangkung', 'Fresh water spinach, perfect for stir-fry', 2.50, 100, 'kangkung.jpg'),
(1, 'Cili Padi', 'Fresh bird''s eye chili (Extra hot)', 1.50, 150, 'cili_padi.jpg'),
(1, 'Rambutan', 'Sweet and fresh rambutan from Pahang', 12.90, 50, 'rambutan.jpg'),
(1, 'Durian Musang King', 'Premium grade Musang King durian', 99.90, 20, 'musang_king.jpg'),
(1, 'Ulam Raja', 'Fresh cosmos leaves for traditional salad', 3.50, 80, 'ulam_raja.jpg');

-- Groceries (Category 2)
INSERT INTO products (category_id, name, description, price, stock_quantity, image_url) VALUES
(2, 'Beras Siam Super', 'Premium Thai white rice (5kg)', 28.90, 200, 'beras_siam.jpg'),
(2, 'Mee Kuning Cap Kilang', 'Yellow noodles (400g)', 3.20, 150, 'mee_kuning.jpg'),
(2, 'Santan Kara', 'Coconut milk UHT (200ml)', 2.50, 300, 'santan_kara.jpg'),
(2, 'Kicap Manis Adabi', 'Sweet soy sauce (345ml)', 4.90, 200, 'kicap_manis.jpg'),
(2, 'Gula Melaka', 'Premium palm sugar (500g)', 8.90, 100, 'gula_melaka.jpg');

-- Beverages (Category 3)
INSERT INTO products (category_id, name, description, price, stock_quantity, image_url) VALUES
(3, 'Teh Boh', 'Malaysian tea bags (100 pcs)', 12.90, 150, 'teh_boh.jpg'),
(3, 'Milo Tin', 'Chocolate malt drink (1.5kg)', 29.90, 100, 'milo.jpg'),
(3, 'F&N Rose Syrup', 'Rose flavored syrup (2L)', 12.50, 80, 'rose_syrup.jpg'),
(3, 'Kopi O Cap Televisyen', 'Traditional coffee powder (500g)', 8.90, 120, 'kopi_o.jpg'),
(3, '100 Plus', 'Isotonic drink (1.5L)', 3.90, 200, '100plus.jpg');

-- Snacks (Category 4)
INSERT INTO products (category_id, name, description, price, stock_quantity, image_url) VALUES
(4, 'Muruku', 'Traditional Indian crunchy snack (200g)', 5.90, 100, 'muruku.jpg'),
(4, 'Kuih Bahulu', 'Traditional Malay cake (10 pcs)', 8.90, 50, 'bahulu.jpg'),
(4, 'Keropok Lekor', 'Terengganu fish crackers (500g)', 15.90, 80, 'keropok_lekor.jpg'),
(4, 'Dodol', 'Traditional sweet toffee-like candy (500g)', 12.90, 60, 'dodol.jpg'),
(4, 'Kuaci', 'Roasted sunflower seeds (200g)', 4.90, 150, 'kuaci.jpg');

-- Household (Category 5)
INSERT INTO products (category_id, name, description, price, stock_quantity, image_url) VALUES
(5, 'Sabun Serai', 'Lemongrass dishwashing liquid (1L)', 8.90, 100, 'sabun_serai.jpg'),
(5, 'Pewangi Sekaki', 'Floor cleaner with floral scent (2L)', 12.90, 80, 'pewangi.jpg'),
(5, 'Racun Serangga Bio', 'Natural insect repellent (500ml)', 15.90, 60, 'racun_serangga.jpg'),
(5, 'Penyapu Lidi', 'Traditional bamboo broom', 19.90, 40, 'penyapu.jpg'),
(5, 'Bakul Rotan', 'Handwoven rattan basket', 25.90, 30, 'bakul_rotan.jpg');

-- Add some inventory logs for initial stock
INSERT INTO inventory_logs (product_id, action, quantity, notes) 
SELECT 
    id, 
    'STOCK_IN', 
    stock_quantity, 
    'Initial stock'
FROM products; 