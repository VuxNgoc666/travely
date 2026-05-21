<?php

class Tour
{
    public static function all(array $filters = [])
    {
        $where = [];
        $params = [];

        if (($filters['admin'] ?? false) !== true) {
            $where[] = 'status = :status';
            $params[':status'] = 'active';
        }

        if (!empty($filters['type'])) {
            $where[] = 'type = :type';
            $params[':type'] = $filters['type'];
        }

        if (!empty($filters['region'])) {
            $where[] = 'region = :region';
            $params[':region'] = $filters['region'];
        }

        if (!empty($filters['category'])) {
            $where[] = 'category = :category';
            $params[':category'] = $filters['category'];
        }

        if (!empty($filters['keyword'])) {
            $where[] = '(title LIKE :keyword_title OR destination LIKE :keyword_destination OR country LIKE :keyword_country)';
            $keyword = '%' . $filters['keyword'] . '%';
            $params[':keyword_title'] = $keyword;
            $params[':keyword_destination'] = $keyword;
            $params[':keyword_country'] = $keyword;
        }

        if (!empty($filters['start_date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $filters['start_date'])) {
            $where[] = 'start_dates LIKE :start_date';
            $params[':start_date'] = '%"' . $filters['start_date'] . '"%';
        }

        if (!empty($filters['max_price'])) {
            $where[] = 'price <= :max_price';
            $params[':max_price'] = (float) $filters['max_price'];
        }

        if (!empty($filters['min_price'])) {
            $where[] = 'price >= :min_price';
            $params[':min_price'] = (float) $filters['min_price'];
        }

        if (!empty($filters['duration'])) {
            $where[] = 'duration_days <= :duration';
            $params[':duration'] = (int) $filters['duration'];
        }

        $order = 'featured DESC, rating DESC, created_at DESC';
        $sort = $filters['sort'] ?? '';
        if ($sort === 'price_asc') {
            $order = 'price ASC';
        } elseif ($sort === 'price_desc') {
            $order = 'price DESC';
        } elseif ($sort === 'newest') {
            $order = 'created_at DESC';
        }

        $sql = 'SELECT * FROM tours';
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY ' . $order;

        if (!empty($filters['limit'])) {
            $sql .= ' LIMIT ' . (int) $filters['limit'];
        }

        return Database::query($sql, $params)->fetchAll();
    }

    public static function featured($limit = 6)
    {
        return Database::query(
            'SELECT * FROM tours WHERE status = "active" ORDER BY featured DESC, id ASC LIMIT ' . (int) $limit
        )->fetchAll();
    }

    public static function deals($limit = 8)
    {
        return Database::query(
            'SELECT * FROM tours WHERE status = "active" AND old_price > price ORDER BY (old_price - price) DESC LIMIT ' . (int) $limit
        )->fetchAll();
    }

    public static function find($id)
    {
        return Database::query('SELECT * FROM tours WHERE id = :id', [':id' => $id])->fetch();
    }

    public static function findBySlug($slug)
    {
        return Database::query('SELECT * FROM tours WHERE slug = :slug AND status = "active"', [':slug' => $slug])->fetch();
    }

    public static function slugExists($slug, $ignoreId = null)
    {
        $sql = 'SELECT COUNT(*) AS total FROM tours WHERE slug = :slug';
        $params = [':slug' => $slug];

        if ($ignoreId) {
            $sql .= ' AND id <> :id';
            $params[':id'] = (int) $ignoreId;
        }

        return (int) Database::query($sql, $params)->fetch()['total'] > 0;
    }

    public static function create(array $data)
    {
        $data = self::sanitize($data);
        Database::query(
            'INSERT INTO tours (title, slug, type, region, destination, country, category, price, old_price, discount_label, duration_days, duration_nights, transport, hotel, rating, review_count, thumbnail, hero_image, gallery, description, highlights, itinerary, included, start_dates, status, featured)
             VALUES (:title, :slug, :type, :region, :destination, :country, :category, :price, :old_price, :discount_label, :duration_days, :duration_nights, :transport, :hotel, :rating, :review_count, :thumbnail, :hero_image, :gallery, :description, :highlights, :itinerary, :included, :start_dates, :status, :featured)',
            $data
        );

        return Database::lastInsertId();
    }

    public static function update($id, array $data)
    {
        $data = self::sanitize($data);
        $data[':id'] = $id;

        Database::query(
            'UPDATE tours SET title = :title, slug = :slug, type = :type, region = :region, destination = :destination, country = :country, category = :category, price = :price, old_price = :old_price, discount_label = :discount_label, duration_days = :duration_days, duration_nights = :duration_nights, transport = :transport, hotel = :hotel, rating = :rating, review_count = :review_count, thumbnail = :thumbnail, hero_image = :hero_image, gallery = :gallery, description = :description, highlights = :highlights, itinerary = :itinerary, included = :included, start_dates = :start_dates, status = :status, featured = :featured, updated_at = CURRENT_TIMESTAMP WHERE id = :id',
            $data
        );
    }

    public static function delete($id)
    {
        Database::query('DELETE FROM tours WHERE id = :id', [':id' => $id]);
    }

    public static function count()
    {
        return (int) Database::query('SELECT COUNT(*) AS total FROM tours')->fetch()['total'];
    }

    public static function regions($type = null)
    {
        if ($type) {
            return Database::query(
                'SELECT DISTINCT region FROM tours WHERE region <> "" AND type = :type ORDER BY region',
                [':type' => $type]
            )->fetchAll();
        }

        return Database::query('SELECT DISTINCT region FROM tours WHERE region <> "" ORDER BY region')->fetchAll();
    }

    public static function categories($type = null)
    {
        if ($type) {
            return Database::query(
                'SELECT DISTINCT category FROM tours WHERE category <> "" AND type = :type ORDER BY category',
                [':type' => $type]
            )->fetchAll();
        }

        return Database::query('SELECT DISTINCT category FROM tours WHERE category <> "" ORDER BY category')->fetchAll();
    }

    public static function blank()
    {
        return [
            'id' => '',
            'title' => '',
            'slug' => '',
            'type' => 'domestic',
            'region' => 'Miền Bắc',
            'destination' => '',
            'country' => 'Việt Nam',
            'category' => 'Biển đảo',
            'price' => 0,
            'old_price' => 0,
            'discount_label' => '',
            'duration_days' => 3,
            'duration_nights' => 2,
            'transport' => 'Máy bay',
            'hotel' => 'Khách sạn 4 sao',
            'rating' => 4.8,
            'review_count' => 120,
            'thumbnail' => '',
            'hero_image' => '',
            'gallery' => '[]',
            'description' => '',
            'highlights' => '[]',
            'itinerary' => '[]',
            'included' => '[]',
            'start_dates' => '[]',
            'status' => 'active',
            'featured' => 1,
        ];
    }

    private static function sanitize(array $data)
    {
        $title = trim($data['title'] ?? '');
        $slug = trim($data['slug'] ?? '') ?: slugify($title);

        return [
            ':title' => $title,
            ':slug' => slugify($slug),
            ':type' => $data['type'] ?? 'domestic',
            ':region' => trim($data['region'] ?? ''),
            ':destination' => trim($data['destination'] ?? ''),
            ':country' => trim($data['country'] ?? ''),
            ':category' => trim($data['category'] ?? ''),
            ':price' => (float) ($data['price'] ?? 0),
            ':old_price' => (float) ($data['old_price'] ?? 0),
            ':discount_label' => trim($data['discount_label'] ?? ''),
            ':duration_days' => (int) ($data['duration_days'] ?? 1),
            ':duration_nights' => (int) ($data['duration_nights'] ?? 0),
            ':transport' => trim($data['transport'] ?? ''),
            ':hotel' => trim($data['hotel'] ?? ''),
            ':rating' => (float) ($data['rating'] ?? 4.8),
            ':review_count' => (int) ($data['review_count'] ?? 0),
            ':thumbnail' => trim($data['thumbnail'] ?? ''),
            ':hero_image' => trim($data['hero_image'] ?? ''),
            ':gallery' => self::linesToJson($data['gallery'] ?? ''),
            ':description' => trim($data['description'] ?? ''),
            ':highlights' => self::linesToJson($data['highlights'] ?? ''),
            ':itinerary' => self::linesToJson($data['itinerary'] ?? ''),
            ':included' => self::linesToJson($data['included'] ?? ''),
            ':start_dates' => self::linesToJson($data['start_dates'] ?? ''),
            ':status' => $data['status'] ?? 'active',
            ':featured' => !empty($data['featured']) ? 1 : 0,
        ];
    }

    private static function linesToJson($value)
    {
        if (is_array($value)) {
            return json_encode(array_values(array_filter($value)), JSON_UNESCAPED_UNICODE);
        }

        $value = trim((string) $value);
        if ($value === '') {
            return '[]';
        }

        $decoded = json_decode($value, true);
        if (is_array($decoded)) {
            return json_encode($decoded, JSON_UNESCAPED_UNICODE);
        }

        $lines = preg_split('/\r\n|\r|\n/', $value);
        $lines = array_values(array_filter(array_map('trim', $lines)));

        return json_encode($lines, JSON_UNESCAPED_UNICODE);
    }
}
