CREATE DATABASE IF NOT EXISTS travely_cinematic_mvc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE travely_cinematic_mvc;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS favorites;
DROP TABLE IF EXISTS contact_messages;
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS tours;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL UNIQUE,
    phone VARCHAR(30) DEFAULT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tours (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(220) NOT NULL,
    slug VARCHAR(240) NOT NULL UNIQUE,
    type ENUM('domestic', 'foreign') NOT NULL DEFAULT 'domestic',
    region VARCHAR(80) DEFAULT NULL,
    destination VARCHAR(140) DEFAULT NULL,
    country VARCHAR(120) DEFAULT NULL,
    category VARCHAR(120) DEFAULT NULL,
    price DECIMAL(12,0) NOT NULL DEFAULT 0,
    old_price DECIMAL(12,0) NOT NULL DEFAULT 0,
    discount_label VARCHAR(80) DEFAULT NULL,
    duration_days INT UNSIGNED NOT NULL DEFAULT 1,
    duration_nights INT UNSIGNED NOT NULL DEFAULT 0,
    transport VARCHAR(120) DEFAULT NULL,
    hotel VARCHAR(120) DEFAULT NULL,
    rating DECIMAL(2,1) NOT NULL DEFAULT 4.8,
    review_count INT UNSIGNED NOT NULL DEFAULT 0,
    thumbnail TEXT,
    hero_image TEXT,
    gallery LONGTEXT,
    description TEXT,
    highlights LONGTEXT,
    itinerary LONGTEXT,
    included LONGTEXT,
    start_dates LONGTEXT,
    status ENUM('active', 'draft') NOT NULL DEFAULT 'active',
    featured TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE bookings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tour_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    start_date DATE NOT NULL,
    guests INT UNSIGNED NOT NULL DEFAULT 1,
    full_name VARCHAR(140) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    email VARCHAR(160) NOT NULL,
    notes TEXT,
    total_price DECIMAL(12,0) NOT NULL DEFAULT 0,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_bookings_tour FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE,
    CONSTRAINT fk_bookings_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE favorites (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    tour_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_favorite (user_id, tour_id),
    CONSTRAINT fk_favorites_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_favorites_tour FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE contact_messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(140) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    email VARCHAR(160) NOT NULL,
    subject VARCHAR(160) DEFAULT NULL,
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'resolved') NOT NULL DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users (id, name, email, phone, password, role) VALUES
(1, 'Quản trị Travely', 'admin', '1900 1234', '$2y$10$vyB.e883aJUUPu5TpcHv/Oo4ikUSyRus94ldz/V4UlEKPPRoyQA1u', 'admin'),
(2, 'Nguyễn Văn A', 'user@travely.local', '0909 123 456', '$2y$10$vyB.e883aJUUPu5TpcHv/Oo4ikUSyRus94ldz/V4UlEKPPRoyQA1u', 'user');

INSERT INTO tours
(id, title, slug, type, region, destination, country, category, price, old_price, discount_label, duration_days, duration_nights, transport, hotel, rating, review_count, thumbnail, hero_image, gallery, description, highlights, itinerary, included, start_dates, status, featured)
VALUES
(1, 'Hạ Long du thuyền đêm giữa vịnh 2N1Đ', 'ha-long-du-thuyen-dem-giua-vinh-2n1d', 'domestic', 'Miền Bắc', 'Quảng Ninh', 'Việt Nam', 'Biển đảo', 2690000, 3290000, 'Bán chạy', 2, 1, 'Xe limousine', 'Du thuyền 5 sao', 4.8, 328,
'https://images.unsplash.com/photo-1528127269322-539801943592?auto=format&fit=crop&w=900&q=85',
'https://images.unsplash.com/photo-1528127269322-539801943592?auto=format&fit=crop&w=1800&q=85',
'["https://images.unsplash.com/photo-1528127269322-539801943592?auto=format&fit=crop&w=900&q=85","https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=900&q=85","https://images.unsplash.com/photo-1518684079-3c830dcef090?auto=format&fit=crop&w=900&q=85"]',
'Du ngoạn vịnh Hạ Long trên du thuyền boutique, ngắm bình minh trên boong và thưởng thức hải sản trong không gian điện ảnh.',
'["Du thuyền 5 sao qua vùng lõi di sản","Kayak hang nước và bãi tắm riêng","Tiệc tối trên boong với view hoàng hôn","Xe limousine đưa đón Hà Nội - Hạ Long"]',
'["Hà Nội - Hạ Long, nhận phòng du thuyền, chèo kayak và ngắm hoàng hôn","Tập thái cực quyền, brunch trên vịnh, về lại Hà Nội"]',
'["Xe limousine khứ hồi","Phòng du thuyền tiêu chuẩn","Bữa ăn theo chương trình","Vé tham quan vịnh"]',
'["2026-06-12","2026-06-26","2026-07-10"]', 'active', 1),

(2, 'Đà Nẵng - Hội An phố đèn lồng 3N2Đ', 'da-nang-hoi-an-pho-den-long-3n2d', 'domestic', 'Miền Trung', 'Đà Nẵng', 'Việt Nam', 'Văn hóa - Lịch sử', 3490000, 3990000, 'Ưu đãi -10%', 3, 2, 'Máy bay', 'Khách sạn 4 sao', 4.9, 516,
'https://images.unsplash.com/photo-1559592413-7cec4d0cae2b?auto=format&fit=crop&w=900&q=85',
'https://images.unsplash.com/photo-1559592413-7cec4d0cae2b?auto=format&fit=crop&w=1800&q=85',
'["https://images.unsplash.com/photo-1559592413-7cec4d0cae2b?auto=format&fit=crop&w=900&q=85","https://images.unsplash.com/photo-1545569341-9eb8b30979d9?auto=format&fit=crop&w=900&q=85","https://images.unsplash.com/photo-1528181304800-259b08848526?auto=format&fit=crop&w=900&q=85"]',
'Một hành trình miền Trung có nhịp vừa đủ: biển Mỹ Khê, Bà Nà, phố cổ Hội An và đêm đèn lồng.',
'["Dạo phố Hội An về đêm","Check-in Cầu Vàng Bà Nà","Tắm biển Mỹ Khê","Thưởng thức đặc sản miền Trung"]',
'["TP.HCM/Hà Nội - Đà Nẵng, Sơn Trà, Mỹ Khê","Bà Nà Hills - Cầu Vàng - Hội An","Mua sắm đặc sản và bay về"]',
'["Vé máy bay khứ hồi","Khách sạn 4 sao","Xe du lịch đời mới","Hướng dẫn viên"]',
'["2026-06-18","2026-07-03","2026-07-24"]', 'active', 1),

(3, 'Sapa săn mây và ruộng bậc thang 3N2Đ', 'sapa-san-may-va-ruong-bac-thang-3n2d', 'domestic', 'Miền Bắc', 'Lào Cai', 'Việt Nam', 'Phiêu lưu', 2790000, 3390000, 'Mới', 3, 2, 'Xe cabin', 'Khách sạn view núi', 4.7, 271,
'https://images.unsplash.com/photo-1501785888041-af3ef285b470?auto=format&fit=crop&w=900&q=85',
'https://images.unsplash.com/photo-1501785888041-af3ef285b470?auto=format&fit=crop&w=1800&q=85',
'["https://images.unsplash.com/photo-1501785888041-af3ef285b470?auto=format&fit=crop&w=900&q=85","https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=900&q=85","https://images.unsplash.com/photo-1519681393784-d120267933ba?auto=format&fit=crop&w=900&q=85"]',
'Sapa được dựng như một cảnh phim sương sớm với bản làng, ruộng bậc thang và cung trekking nhẹ.',
'["Săn mây tại điểm cao","Trekking bản Cát Cát - Lao Chải","Thưởng thức lẩu cá hồi","Ảnh núi rừng hoàng hôn"]',
'["Hà Nội - Sapa, tự do dạo phố núi","Bản Cát Cát, Fansipan tùy chọn","Lao Chải - Tả Van, về Hà Nội"]',
'["Xe cabin khứ hồi","Khách sạn trung tâm","Bữa ăn theo lịch trình","Vé tham quan bản"]',
'["2026-06-20","2026-07-11","2026-08-01"]', 'active', 1),

(4, 'Nha Trang island hopping 3N3Đ', 'nha-trang-island-hopping-3n3d', 'domestic', 'Miền Trung', 'Khánh Hòa', 'Việt Nam', 'Biển đảo', 3290000, 3890000, 'Sale biển', 3, 3, 'Tàu cao tốc', 'Resort 4 sao', 4.6, 402,
'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=900&q=85',
'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1800&q=85',
'["https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=900&q=85","https://images.unsplash.com/photo-1510414842594-a61c69b5ae57?auto=format&fit=crop&w=900&q=85","https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=900&q=85"]',
'Nha Trang dành cho người thích nắng, biển xanh, đảo nhỏ và lịch trình nghỉ dưỡng có nhịp.',
'["Tour đảo bằng tàu cao tốc","Lặn ngắm san hô","Ăn hải sản ven biển","Resort gần biển"]',
'["Đón khách, nhận phòng, tự do biển đêm","Tour đảo, lặn san hô, tiệc hải sản","Cafe biển, mua sắm, kết thúc"]',
'["Xe đưa đón sân bay","Khách sạn/resort 4 sao","Tàu tour đảo","Bữa ăn theo chương trình"]',
'["2026-06-28","2026-07-19","2026-08-09"]', 'active', 1),

(5, 'Phú Quốc hoàng hôn nam đảo 3N2Đ', 'phu-quoc-hoang-hon-nam-dao-3n2d', 'domestic', 'Miền Nam', 'Kiên Giang', 'Việt Nam', 'Nghỉ dưỡng', 3590000, 4390000, 'Hot beach', 3, 2, 'Máy bay', 'Resort biển', 4.8, 318,
'https://images.unsplash.com/photo-1540206395-68808572332f?auto=format&fit=crop&w=900&q=85',
'https://images.unsplash.com/photo-1540206395-68808572332f?auto=format&fit=crop&w=1800&q=85',
'["https://images.unsplash.com/photo-1540206395-68808572332f?auto=format&fit=crop&w=900&q=85","https://images.unsplash.com/photo-1500375592092-40eb2168fd21?auto=format&fit=crop&w=900&q=85","https://images.unsplash.com/photo-1537953773345-d172ccf13cf1?auto=format&fit=crop&w=900&q=85"]',
'Đảo ngọc với biển xanh, cáp treo Hòn Thơm, sunset town và bữa tối hải sản.',
'["Cáp treo Hòn Thơm","Sunset Town và cầu Hôn","Tắm biển Bãi Sao","Hải sản địa phương"]',
'["Bay đến Phú Quốc, check-in resort","Nam đảo, cáp treo, Sunset Town","Tự do mua sắm, bay về"]',
'["Vé máy bay khứ hồi","Resort tiêu chuẩn","Xe du lịch","Hướng dẫn viên"]',
'["2026-06-15","2026-07-05","2026-07-26"]', 'active', 1),

(6, 'Hà Nội - Ninh Bình di sản 2N1Đ', 'ha-noi-ninh-binh-di-san-2n1d', 'domestic', 'Miền Bắc', 'Ninh Bình', 'Việt Nam', 'Văn hóa - Lịch sử', 2290000, 2790000, '', 2, 1, 'Xe du lịch', 'Homestay cao cấp', 4.7, 194,
'https://images.unsplash.com/photo-1528181304800-259b08848526?auto=format&fit=crop&w=900&q=85',
'https://images.unsplash.com/photo-1528181304800-259b08848526?auto=format&fit=crop&w=1800&q=85',
'["https://images.unsplash.com/photo-1528181304800-259b08848526?auto=format&fit=crop&w=900&q=85","https://images.unsplash.com/photo-1559827291-72ee739d0d9a?auto=format&fit=crop&w=900&q=85","https://images.unsplash.com/photo-1530789253388-582c481c54b0?auto=format&fit=crop&w=900&q=85"]',
'Một chuyến đi ngắn gọn nhưng giàu hình ảnh: Tràng An, Hang Múa, Tam Cốc và phố cổ Hoa Lư.',
'["Thuyền Tràng An","Leo Hang Múa ngắm toàn cảnh","Ẩm thực dê núi","Lịch trình cuối tuần"]',
'["Hà Nội - Hoa Lư - Tràng An - Hang Múa","Tam Cốc, mua sắm đặc sản, về Hà Nội"]',
'["Xe du lịch","Lưu trú 1 đêm","Bữa ăn theo lịch","Vé thắng cảnh"]',
'["2026-06-13","2026-06-27","2026-07-18"]', 'active', 0),

(7, 'Seoul - Nami - Everland 5N4Đ', 'seoul-nami-everland-5n4d', 'foreign', 'Châu Á', 'Seoul', 'Hàn Quốc', 'Văn hóa - Lịch sử', 12990000, 15990000, 'Ưu đãi -19%', 5, 4, 'Vietjet Air', 'Khách sạn 3-4 sao', 4.8, 128,
'https://images.unsplash.com/photo-1538485399081-7c8f5f2d4e71?auto=format&fit=crop&w=900&q=85',
'https://images.unsplash.com/photo-1538485399081-7c8f5f2d4e71?auto=format&fit=crop&w=1800&q=85',
'["https://images.unsplash.com/photo-1538485399081-7c8f5f2d4e71?auto=format&fit=crop&w=900&q=85","https://images.unsplash.com/photo-1517154421773-0529f29ea451?auto=format&fit=crop&w=900&q=85","https://images.unsplash.com/photo-1545569341-9eb8b30979d9?auto=format&fit=crop&w=900&q=85"]',
'Khám phá xứ sở kim chi với Seoul hiện đại, đảo Nami thơ mộng và công viên Everland sôi động.',
'["Đảo Nami phim trường mùa lá","Everland nguyên ngày","Gyeongbokgung và Hanbok","Mua sắm Myeongdong"]',
'["TP.HCM - Seoul, nghỉ đêm trên máy bay","Seoul city tour, cung điện, tháp N Seoul","Nami - Everland","Mua sắm và trải nghiệm ẩm thực","Seoul - TP.HCM"]',
'["Vé máy bay khứ hồi","Khách sạn 3-4 sao","Visa theo đoàn","Bữa ăn, xe và hướng dẫn viên"]',
'["2026-06-22","2026-07-13","2026-08-17"]', 'active', 1),

(8, 'Tokyo - Núi Phú Sĩ - Kyoto - Osaka 5N4Đ', 'tokyo-nui-phu-si-kyoto-osaka-5n4d', 'foreign', 'Châu Á', 'Tokyo', 'Nhật Bản', 'Văn hóa - Lịch sử', 19990000, 21990000, 'Giảm sâu', 5, 4, 'Bay thẳng', 'Khách sạn 4 sao', 4.8, 264,
'https://images.unsplash.com/photo-1528164344705-47542687000d?auto=format&fit=crop&w=900&q=85',
'https://images.unsplash.com/photo-1528164344705-47542687000d?auto=format&fit=crop&w=1800&q=85',
'["https://images.unsplash.com/photo-1528164344705-47542687000d?auto=format&fit=crop&w=900&q=85","https://images.unsplash.com/photo-1545569341-9eb8b30979d9?auto=format&fit=crop&w=900&q=85","https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?auto=format&fit=crop&w=900&q=85"]',
'Tuyến vàng Nhật Bản với nhịp đô thị Tokyo, núi Phú Sĩ, phố cổ Kyoto và ẩm thực Osaka.',
'["Check-in núi Phú Sĩ","Đền chùa Kyoto","Shibuya - Akihabara","Ẩm thực Osaka"]',
'["Bay đến Tokyo","Tokyo city tour","Núi Phú Sĩ - làng cổ Oshino Hakkai","Kyoto - Osaka","Osaka - TP.HCM"]',
'["Vé máy bay","Khách sạn","Visa đoàn","Bữa ăn theo tour"]',
'["2026-07-01","2026-07-29","2026-08-26"]', 'active', 1),

(9, 'Zurich - Interlaken - Lucerne 7N6Đ', 'zurich-interlaken-lucerne-7n6d', 'foreign', 'Châu Âu', 'Zurich', 'Thụy Sĩ', 'Nghỉ dưỡng', 39990000, 45990000, 'Ưu đãi 50%', 7, 6, 'Emirates', 'Khách sạn 4 sao', 4.9, 96,
'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=900&q=85',
'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=1800&q=85',
'["https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=900&q=85","https://images.unsplash.com/photo-1501785888041-af3ef285b470?auto=format&fit=crop&w=900&q=85","https://images.unsplash.com/photo-1527004013197-933c4bb611b3?auto=format&fit=crop&w=900&q=85"]',
'Một hành trình Thụy Sĩ xanh trong với hồ, núi tuyết, tàu ngắm cảnh và các thị trấn yên bình.',
'["Tàu ngắm cảnh Interlaken","Hồ Lucerne","Phố cổ Zurich","Ảnh núi tuyết"]',
'["TP.HCM - Zurich","Zurich city tour","Lucerne - hồ và cầu gỗ","Interlaken tự do","Jungfrau tùy chọn","Mua sắm Zurich","Bay về"]',
'["Vé máy bay quốc tế","Khách sạn 4 sao","Xe và hướng dẫn viên","Bảo hiểm du lịch"]',
'["2026-07-08","2026-08-12","2026-09-09"]', 'active', 1),

(10, 'Bangkok - Pattaya biển và phố đêm 4N3Đ', 'bangkok-pattaya-bien-va-pho-dem-4n3d', 'foreign', 'Châu Á', 'Bangkok', 'Thái Lan', 'Giải trí', 7990000, 9490000, 'Siêu tiết kiệm', 4, 3, 'Bay thẳng', 'Khách sạn 4 sao', 4.5, 312,
'https://images.unsplash.com/photo-1528181304800-259b08848526?auto=format&fit=crop&w=900&q=85',
'https://images.unsplash.com/photo-1528181304800-259b08848526?auto=format&fit=crop&w=1800&q=85',
'["https://images.unsplash.com/photo-1528181304800-259b08848526?auto=format&fit=crop&w=900&q=85","https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=900&q=85","https://images.unsplash.com/photo-1523906834658-6e24ef2386f9?auto=format&fit=crop&w=900&q=85"]',
'Bangkok - Pattaya dành cho nhóm bạn: biển, chợ đêm, show diễn và ẩm thực đường phố.',
'["Pattaya Coral Island","Chợ nổi bốn miền","IconSiam và phố đêm","Ẩm thực Thái"]',
'["Bay Bangkok, di chuyển Pattaya","Đảo Coral - show Alcazar","Bangkok city tour","Mua sắm, bay về"]',
'["Vé máy bay","Khách sạn","Xe tour","Bữa ăn theo lịch trình"]',
'["2026-06-25","2026-07-16","2026-08-06"]', 'active', 1),

(11, 'Singapore - Sentosa - Garden by the Bay 4N3Đ', 'singapore-sentosa-garden-by-the-bay-4n3d', 'foreign', 'Châu Á', 'Singapore', 'Singapore', 'Gia đình', 8990000, 10990000, 'Nhóm đông giá tốt', 4, 3, 'Bay thẳng', 'Khách sạn 4 sao', 4.6, 212,
'https://images.unsplash.com/photo-1525625293386-3f8f99389edd?auto=format&fit=crop&w=900&q=85',
'https://images.unsplash.com/photo-1525625293386-3f8f99389edd?auto=format&fit=crop&w=1800&q=85',
'["https://images.unsplash.com/photo-1525625293386-3f8f99389edd?auto=format&fit=crop&w=900&q=85","https://images.unsplash.com/photo-1496939376851-89342e90adcd?auto=format&fit=crop&w=900&q=85","https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=900&q=85"]',
'Singapore gọn, sạch và nhiều điểm chơi cho gia đình: Sentosa, Gardens by the Bay và Marina Bay.',
'["Gardens by the Bay","Sentosa Island","Marina Bay Sands","Lịch trình hợp gia đình"]',
'["Bay đến Singapore","City tour - Marina Bay","Sentosa tự chọn","Mua sắm, bay về"]',
'["Vé máy bay","Khách sạn","Xe đưa đón","Hướng dẫn viên"]',
'["2026-06-30","2026-07-21","2026-08-18"]', 'active', 1),

(12, 'Bali retreat biển xanh và đền thiêng 5N4Đ', 'bali-retreat-bien-xanh-va-den-thieng-5n4d', 'foreign', 'Châu Á', 'Bali', 'Indonesia', 'Nghỉ dưỡng', 11990000, 13990000, 'Retreat', 5, 4, 'Bay nối chuyến', 'Resort 4 sao', 4.7, 187,
'https://images.unsplash.com/photo-1537953773345-d172ccf13cf1?auto=format&fit=crop&w=900&q=85',
'https://images.unsplash.com/photo-1537953773345-d172ccf13cf1?auto=format&fit=crop&w=1800&q=85',
'["https://images.unsplash.com/photo-1537953773345-d172ccf13cf1?auto=format&fit=crop&w=900&q=85","https://images.unsplash.com/photo-1548013146-72479768bada?auto=format&fit=crop&w=900&q=85","https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=900&q=85"]',
'Bali có biển, ruộng bậc thang, đền thiêng và resort phù hợp cho chuyến nghỉ dưỡng nhiều cảm xúc.',
'["Đền Tanah Lot","Ubud và ruộng bậc thang","Biển Nusa Dua","Resort nghỉ dưỡng"]',
'["Bay đến Bali","Ubud - Tegallalang","Tanah Lot - biển Nusa Dua","Tự do resort","Bay về"]',
'["Vé máy bay","Resort 4 sao","Xe và HDV địa phương","Bữa ăn theo tour"]',
'["2026-07-04","2026-08-01","2026-08-29"]', 'active', 0);

INSERT INTO bookings (tour_id, user_id, start_date, guests, full_name, phone, email, notes, total_price, status) VALUES
(7, 2, '2026-07-13', 2, 'Nguyễn Văn A', '0909 123 456', 'user@travely.local', 'Muốn phòng twin.', 25980000, 'confirmed'),
(2, 2, '2026-07-03', 3, 'Nguyễn Văn A', '0909 123 456', 'user@travely.local', 'Có một bé 8 tuổi đi cùng.', 10470000, 'pending');

INSERT INTO favorites (user_id, tour_id) VALUES
(2, 1),
(2, 7),
(2, 9);

-- Local destination images refreshed from real place photos.
UPDATE tours SET
    thumbnail = 'images/tours/ha-long-1.jpg',
    hero_image = 'images/tours/ha-long-1.jpg',
    gallery = '["images/tours/ha-long-1.jpg","images/tours/ha-long-2.jpg","images/tours/ha-long-3.jpg"]'
WHERE id = 1;

UPDATE tours SET
    thumbnail = 'images/tours/da-nang-hoi-an-1.jpg',
    hero_image = 'images/tours/da-nang-hoi-an-1.jpg',
    gallery = '["images/tours/da-nang-hoi-an-1.jpg","images/tours/da-nang-hoi-an-2.jpg","images/tours/da-nang-hoi-an-3.jpg"]'
WHERE id = 2;

UPDATE tours SET
    thumbnail = 'images/tours/sapa-1.jpg',
    hero_image = 'images/tours/sapa-1.jpg',
    gallery = '["images/tours/sapa-1.jpg","images/tours/sapa-2.jpg","images/tours/sapa-3.jpg"]'
WHERE id = 3;

UPDATE tours SET
    thumbnail = 'images/tours/nha-trang-1.jpg',
    hero_image = 'images/tours/nha-trang-1.jpg',
    gallery = '["images/tours/nha-trang-1.jpg","images/tours/nha-trang-2.jpg","images/tours/nha-trang-3.jpg"]'
WHERE id = 4;

UPDATE tours SET
    thumbnail = 'images/tours/phu-quoc-1.jpg',
    hero_image = 'images/tours/phu-quoc-1.jpg',
    gallery = '["images/tours/phu-quoc-1.jpg","images/tours/phu-quoc-2.jpg","images/tours/phu-quoc-3.jpg"]'
WHERE id = 5;

UPDATE tours SET
    thumbnail = 'images/tours/ninh-binh-1.jpg',
    hero_image = 'images/tours/ninh-binh-1.jpg',
    gallery = '["images/tours/ninh-binh-1.jpg","images/tours/ninh-binh-2.jpg","images/tours/ninh-binh-3.jpg"]'
WHERE id = 6;

UPDATE tours SET
    thumbnail = 'images/tours/seoul-1.jpg',
    hero_image = 'images/tours/seoul-1.jpg',
    gallery = '["images/tours/seoul-1.jpg","images/tours/seoul-2.jpg","images/tours/seoul-3.jpg"]'
WHERE id = 7;

UPDATE tours SET
    thumbnail = 'images/tours/japan-1.jpg',
    hero_image = 'images/tours/japan-1.jpg',
    gallery = '["images/tours/japan-1.jpg","images/tours/japan-2.jpg","images/tours/japan-3.jpg"]'
WHERE id = 8;

UPDATE tours SET
    thumbnail = 'images/tours/switzerland-1.jpg',
    hero_image = 'images/tours/switzerland-1.jpg',
    gallery = '["images/tours/switzerland-1.jpg","images/tours/switzerland-2.jpg","images/tours/switzerland-3.jpg"]'
WHERE id = 9;

UPDATE tours SET
    thumbnail = 'images/tours/bangkok-pattaya-1.jpg',
    hero_image = 'images/tours/bangkok-pattaya-1.jpg',
    gallery = '["images/tours/bangkok-pattaya-1.jpg","images/tours/bangkok-pattaya-2.jpg","images/tours/bangkok-pattaya-3.jpg"]'
WHERE id = 10;

UPDATE tours SET
    thumbnail = 'images/tours/singapore-1.jpg',
    hero_image = 'images/tours/singapore-1.jpg',
    gallery = '["images/tours/singapore-1.jpg","images/tours/singapore-2.jpg","images/tours/singapore-3.jpg"]'
WHERE id = 11;

UPDATE tours SET
    thumbnail = 'images/tours/bali-1.jpg',
    hero_image = 'images/tours/bali-1.jpg',
    gallery = '["images/tours/bali-1.jpg","images/tours/bali-2.jpg","images/tours/bali-3.jpg"]'
WHERE id = 12;
