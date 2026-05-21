<?php

class TourController extends Controller
{
    public function index()
    {
        $this->listing(null, 'Tour đang mở bán', 'Chọn một hành trình đúng gu, đặt nhanh và theo dõi ngay trong tài khoản.');
    }

    public function domestic()
    {
        $this->listing('domestic', 'Tour trong nước', 'Việt Nam như một thước phim rộng mở: biển đảo, núi rừng, di sản và phố cổ.');
    }

    public function foreign()
    {
        $this->listing('foreign', 'Tour nước ngoài', 'Những thành phố sáng đèn, bờ biển điện ảnh và hành trình được thiết kế trọn gói.');
    }

    public function deals()
    {
        $this->view('tours/deals', [
            'title' => 'Ưu đãi du lịch',
            'dealTours' => Tour::deals(8),
            'foreignTours' => Tour::all(['type' => 'foreign', 'limit' => 5]),
        ]);
    }

    public function show($slug)
    {
        $tour = Tour::findBySlug($slug);
        if (!$tour) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Không tìm thấy tour']);
            return;
        }

        $this->view('tours/show', [
            'title' => $tour['title'],
            'tour' => $tour,
            'relatedTours' => Tour::all(['type' => $tour['type'], 'limit' => 4]),
            'isFavorite' => Auth::check() ? Favorite::exists(Auth::user()['id'], $tour['id']) : false,
        ]);
    }

    private function listing($type, $heading, $subtitle)
    {
        $filters = [
            'type' => $type,
            'keyword' => trim($_GET['keyword'] ?? ''),
            'region' => trim($_GET['region'] ?? ''),
            'category' => trim($_GET['category'] ?? ''),
            'start_date' => trim($_GET['start_date'] ?? ''),
            'sort' => trim($_GET['sort'] ?? ''),
        ];

        $priceRange = $_GET['price_range'] ?? '';
        if ($priceRange === 'under_3') {
            $filters['max_price'] = 3000000;
        } elseif ($priceRange === '3_8') {
            $filters['min_price'] = 3000000;
            $filters['max_price'] = 8000000;
        } elseif ($priceRange === 'over_8') {
            $filters['min_price'] = 8000000;
        }

        $duration = $_GET['duration'] ?? '';
        if ($duration === 'short') {
            $filters['duration'] = 3;
        } elseif ($duration === 'medium') {
            $filters['duration'] = 5;
        }

        $this->view('tours/index', [
            'title' => $heading,
            'heading' => $heading,
            'subtitle' => $subtitle,
            'tours' => Tour::all($filters),
            'filters' => $_GET,
            'type' => $type,
            'isDeals' => false,
            'regions' => Tour::regions($type),
            'categories' => Tour::categories($type),
        ]);
    }
}
