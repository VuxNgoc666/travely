<?php

class AiController extends Controller
{
    public function ask()
    {
        $this->verifyCsrf();

        $query = trim((string) ($_POST['query'] ?? ''));
        $mode = trim((string) ($_POST['mode'] ?? 'site'));
        $pageTitle = trim((string) ($_POST['page_title'] ?? ''));
        $currentPath = trim((string) ($_POST['current_path'] ?? ''));
        $bookingContext = [
            'total' => $this->toNumber($_POST['booking_total'] ?? null),
            'guests' => $this->toNumber($_POST['booking_guests'] ?? null),
            'tourPrice' => $this->toNumber($_POST['tour_price'] ?? null),
            'reference' => trim((string) ($_POST['booking_reference'] ?? '')),
            'tourTitle' => trim((string) ($_POST['tour_title'] ?? '')),
        ];

        if ($query === '') {
            $this->json([
                'replyHtml' => 'Mình cần một câu hỏi ngắn, ví dụ: <strong>gợi ý tour biển 3 ngày dưới 5 triệu</strong>.',
                'suggestions' => $this->quickSuggestions($mode),
            ]);
        }

        $payload = $this->buildResponse($query, $mode, $pageTitle, $currentPath, $bookingContext);
        $this->json($payload);
    }

    private function buildResponse($query, $mode, $pageTitle, $currentPath, array $bookingContext)
    {
        $normalized = $this->normalize($query);
        $contextual = $this->buildContextualResponse($normalized, $mode, $pageTitle, $currentPath, $bookingContext);
        if ($contextual !== null) {
            return $contextual;
        }

        $filters = $this->buildTourFilters($normalized);
        $tours = Tour::all($filters);
        $suggestions = array_slice($this->mapTours($tours), 0, 3);

        if ($mode === 'admin') {
            return $this->buildAdminResponse($normalized, $pageTitle, $currentPath);
        }

        if ($this->matches($normalized, ['đặt tour', 'dat tour', 'booking', 'thanh toán', 'thanh toan', 'pay', 'chuyển khoản', 'chuyen khoan'])) {
            return [
                'replyHtml' => 'Mình có thể dẫn bạn qua 3 bước: chọn tour phù hợp, mở trang thanh toán, và lưu mã giao dịch vào booking.',
                'suggestions' => $this->mergeSuggestions([
                    ['title' => 'Xem booking của tôi', 'url' => url('account'), 'meta' => 'Theo dõi trạng thái đơn'],
                    ['title' => 'Xem ưu đãi', 'url' => url('deals'), 'meta' => 'Chọn tour có giảm giá'],
                ], $suggestions),
            ];
        }

        if ($this->matches($normalized, ['giá', 'gia', 'ngân sách', 'ngan sach', 'rẻ', 're', 'triệu', 'tr', 'budget'])) {
            $budgetText = $this->describeBudget($normalized);
            return [
                'replyHtml' => $budgetText ? "Mình đã lọc theo ngân sách <strong>{$budgetText}</strong>." : 'Mình đã lọc theo ngân sách bạn vừa nói.',
                'suggestions' => $suggestions ?: $this->quickSuggestions($mode),
            ];
        }

        if ($this->matches($normalized, ['biển', 'bien', 'đảo', 'dao', 'nha trang', 'phú quốc', 'phu quoc', 'hạ long', 'ha long'])) {
            return [
                'replyHtml' => 'Mình đã ưu tiên các tour biển, đảo và nghỉ dưỡng gần nước.',
                'suggestions' => $suggestions ?: $this->quickSuggestions($mode),
            ];
        }

        if ($this->matches($normalized, ['nước ngoài', 'nuoc ngoai', 'foreign', 'singapore', 'hàn quốc', 'han quoc', 'nhật', 'nhat', 'thái', 'thai'])) {
            return [
                'replyHtml' => 'Mình đã ưu tiên các tour nước ngoài phù hợp với nhu cầu của bạn.',
                'suggestions' => $suggestions ?: $this->quickSuggestions($mode),
            ];
        }

        if ($this->matches($normalized, ['trong nước', 'trong nuoc', 'domestic', 'việt nam', 'viet nam', 'sapa', 'đà nẵng', 'da nang'])) {
            return [
                'replyHtml' => 'Mình đã ưu tiên các tour trong nước theo nhu cầu của bạn.',
                'suggestions' => $suggestions ?: $this->quickSuggestions($mode),
            ];
        }

        if ($suggestions) {
            return [
                'replyHtml' => 'Mình tìm được vài tour có vẻ hợp với bạn. Nếu muốn, mình có thể lọc thêm theo ngân sách hoặc số ngày.',
                'suggestions' => $suggestions,
            ];
        }

        return [
            'replyHtml' => 'Mình chưa chắc ý bạn đang nghiêng về tour nào. Hãy thử hỏi: <strong>tour biển 3 ngày dưới 5 triệu</strong> hoặc <strong>tour nước ngoài cho gia đình</strong>.',
            'suggestions' => $this->quickSuggestions($mode),
        ];
    }

    private function buildContextualResponse($normalized, $mode, $pageTitle, $currentPath, array $bookingContext)
    {
        if ($mode === 'admin') {
            return null;
        }

        $faq = $this->faqEntries();
        foreach ($faq as $entry) {
            if ($this->matches($normalized, $entry['keywords'])) {
                return [
                    'replyHtml' => $this->renderFaqAnswer($entry['answer'], $bookingContext, $pageTitle, $currentPath),
                    'suggestions' => $entry['suggestions'] ?? $this->quickSuggestions($mode),
                ];
            }
        }

        if ($this->matches($normalized, ['đơn giá', 'don gia', 'tổng tiền', 'tong tien', 'sao', 'vậy', 'tai sao', 'sao lai', 'tại sao', '2tr6', '5tr3'])) {
            $priceExplanation = $this->explainBookingPrice($normalized, $bookingContext);
            if ($priceExplanation !== null) {
                return [
                    'replyHtml' => $priceExplanation,
                    'suggestions' => $this->mergeSuggestions([
                        ['title' => 'Xem lại booking', 'url' => url('account'), 'meta' => 'Kiểm tra số khách và trạng thái đơn'],
                        ['title' => 'Mở trang thanh toán', 'url' => url('account'), 'meta' => 'Xem lại QR và tổng tiền'],
                    ], []),
                ];
            }
        }

        if ($this->matches($normalized, ['quét qr', 'quet qr', 'mã giao dịch', 'ma giao dich', 'chuyển khoản', 'chuyen khoan']) || str_contains($currentPath, 'payment/')) {
            return [
                'replyHtml' => $this->renderPaymentHelp($bookingContext, $pageTitle),
                'suggestions' => [
                    ['title' => 'Mở tài khoản', 'url' => url('account'), 'meta' => 'Xem booking của bạn'],
                    ['title' => 'Xem tour', 'url' => url('tours'), 'meta' => 'Chọn tour khác'],
                ],
            ];
        }

        if ($this->matches($normalized, ['booking của tôi', 'booking cua toi', 'đơn của tôi', 'don cua toi', 'trạng thái booking', 'trang thai booking']) || str_contains($currentPath, 'account')) {
            return [
                'replyHtml' => 'Trang tài khoản sẽ cho bạn thấy booking, trạng thái thanh toán và tour yêu thích. Nếu đơn chưa thanh toán, bạn có thể mở trang thanh toán từ đây.',
                'suggestions' => [
                    ['title' => 'Tài khoản', 'url' => url('account'), 'meta' => 'Xem booking của bạn'],
                    ['title' => 'Thanh toán', 'url' => url('account'), 'meta' => 'Mở lại booking để thanh toán'],
                ],
            ];
        }

        if ($this->matches($normalized, ['ngày khởi hành', 'ngay khoi hanh', 'start date', 'lịch khởi hành', 'lich khoi hanh'])) {
            return [
                'replyHtml' => 'Ngày khởi hành là ngày tour bắt đầu. Travely chỉ cho chọn các ngày nằm trong lịch mở bán của tour và từ hôm nay trở đi.',
                'suggestions' => $this->quickSuggestions($mode),
            ];
        }

        return null;
    }

    private function buildAdminResponse($normalized, $pageTitle, $currentPath)
    {
        if ($this->matches($normalized, ['booking', 'xác nhận', 'xac nhan', 'pending', 'chờ', 'cho'])) {
            return [
                'replyHtml' => 'Bạn đang ở luồng xử lý booking. Hãy mở danh sách booking để đổi trạng thái sang <strong>Đã xác nhận</strong> hoặc <strong>Đã hủy</strong>.',
                'suggestions' => [
                    ['title' => 'Mở booking', 'url' => url('admin/bookings'), 'meta' => 'Duyệt đơn mới'],
                    ['title' => 'Xem dashboard', 'url' => url('admin'), 'meta' => 'Tổng quan nhanh'],
                ],
            ];
        }

        if ($this->matches($normalized, ['tour', 'thêm', 'them', 'sửa', 'sua'])) {
            return [
                'replyHtml' => 'Bạn có thể thêm hoặc sửa tour trong khu vực quản trị, rồi cập nhật ảnh, giá và ngày khởi hành.',
                'suggestions' => [
                    ['title' => 'Quản lý tour', 'url' => url('admin/tours'), 'meta' => 'Danh sách tour'],
                    ['title' => 'Thêm tour mới', 'url' => url('admin/tours/create'), 'meta' => 'Tạo tour mới'],
                ],
            ];
        }

        return [
            'replyHtml' => 'Mình có thể hỗ trợ admin xử lý booking, quản lý tour và kiểm tra dashboard.',
            'suggestions' => $this->quickSuggestions($mode),
        ];
    }

    private function faqEntries()
    {
        return [
            [
                'keywords' => ['cách đặt tour', 'dat tour', 'đặt tour', 'booking', 'đặt như nào', 'dat nhu nao'],
                'answer' => 'Bạn chọn tour, đăng nhập, chọn ngày khởi hành và số khách, sau đó bấm <strong>Đặt tour ngay</strong>. Nếu cần, mình có thể dẫn bạn đến trang thanh toán.',
                'suggestions' => [
                    ['title' => 'Tour trong nước', 'url' => url('tours/domestic'), 'meta' => 'Xem ngay'],
                    ['title' => 'Tour nước ngoài', 'url' => url('tours/foreign'), 'meta' => 'Xem ngay'],
                ],
            ],
            [
                'keywords' => ['thanh toán', 'thanh toan', 'chuyển khoản', 'chuyen khoan', 'qr', 'mã giao dịch', 'ma giao dich'],
                'answer' => 'Ở trang thanh toán, bạn quét QR VietQR, chuyển đúng số tiền hiển thị, rồi nhập mã giao dịch để Travely ghi nhận đơn.',
                'suggestions' => [
                    ['title' => 'Xem booking', 'url' => url('account'), 'meta' => 'Theo dõi đơn'],
                    ['title' => 'Xem ưu đãi', 'url' => url('deals'), 'meta' => 'Deal tốt'],
                ],
            ],
            [
                'keywords' => ['ngày khởi hành', 'ngay khoi hanh', 'start date', 'lịch khởi hành', 'lich khoi hanh'],
                'answer' => 'Ngày khởi hành là ngày tour bắt đầu. Travely chỉ cho chọn các ngày hợp lệ trong lịch mở bán và không cho chọn ngày đã qua.',
            ],
            [
                'keywords' => ['tour yêu thích', 'tour yeu thich', 'favorite', 'yêu thích', 'yeu thich'],
                'answer' => 'Bạn có thể bấm tim để lưu tour yêu thích. Danh sách này nằm trong trang tài khoản.',
                'suggestions' => [
                    ['title' => 'Tài khoản', 'url' => url('account'), 'meta' => 'Mở danh sách yêu thích'],
                ],
            ],
            [
                'keywords' => ['hủy tour', 'huy tour', 'đổi ngày', 'doi ngay', 'cancel', 'thay đổi', 'thay doi'],
                'answer' => 'Các thao tác đổi ngày hay hủy tour hiện đang cần admin xử lý. Bạn có thể vào tài khoản hoặc nhắn hỗ trợ để được đổi trạng thái đơn.',
                'suggestions' => [
                    ['title' => 'Liên hệ', 'url' => url('contact'), 'meta' => 'Gửi yêu cầu hỗ trợ'],
                    ['title' => 'Tài khoản', 'url' => url('account'), 'meta' => 'Xem booking'],
                ],
            ],
        ];
    }

    private function renderFaqAnswer($answer, array $bookingContext, $pageTitle, $currentPath)
    {
        $answer = $answer;
        if (str_contains($currentPath, 'payment/') && $bookingContext['tourPrice'] !== null && $bookingContext['guests'] !== null && $bookingContext['total'] !== null) {
            $answer .= '<br><br><strong>Mẹo nhanh:</strong> ' . money($bookingContext['tourPrice']) . ' x ' . (int) $bookingContext['guests'] . ' = ' . money($bookingContext['total']) . '.';
        }

        if ($pageTitle !== '') {
            $answer .= '<br><small>Ngữ cảnh đang xem: ' . e($pageTitle) . '</small>';
        }

        return $answer;
    }

    private function renderPaymentHelp(array $bookingContext, $pageTitle)
    {
        if ($bookingContext['tourPrice'] !== null && $bookingContext['guests'] !== null && $bookingContext['total'] !== null) {
            return 'Bạn đang ở trang thanh toán cho <strong>' . e($bookingContext['tourTitle'] ?: 'booking hiện tại') . '</strong>. <br>Đơn giá: <strong>' . money($bookingContext['tourPrice']) . '</strong> x ' . (int) $bookingContext['guests'] . ' khách = <strong>' . money($bookingContext['total']) . '</strong>. <br>Quét QR, chuyển đúng số tiền, rồi nhập mã giao dịch.';
        }

        return 'Bạn đang ở trang thanh toán. Hãy quét QR, chuyển đúng số tiền hiển thị và nhập mã giao dịch sau khi chuyển xong.';
    }

    private function buildTourFilters($normalized)
    {
        $filters = ['limit' => 12];

        if ($this->matches($normalized, ['nước ngoài', 'nuoc ngoai', 'foreign', 'singapore', 'hàn quốc', 'han quoc', 'nhật', 'nhat', 'thái', 'thai'])) {
            $filters['type'] = 'foreign';
        } elseif ($this->matches($normalized, ['trong nước', 'trong nuoc', 'domestic', 'việt nam', 'viet nam', 'sapa', 'đà nẵng', 'da nang', 'hạ long', 'ha long'])) {
            $filters['type'] = 'domestic';
        }

        $budget = $this->extractBudget($normalized);
        if ($budget['max'] !== null) {
            $filters['max_price'] = $budget['max'];
        }
        if ($budget['min'] !== null) {
            $filters['min_price'] = $budget['min'];
        }

        $duration = $this->extractDuration($normalized);
        if ($duration !== null) {
            $filters['duration'] = $duration;
        }

        foreach (['hạ long', 'ha long', 'sapa', 'đà nẵng', 'da nang', 'nha trang', 'phú quốc', 'phu quoc'] as $regionKey) {
            if (str_contains($normalized, $regionKey)) {
                $filters['keyword'] = $regionKey;
                break;
            }
        }

        return $filters;
    }

    private function mapTours(array $tours)
    {
        return array_map(function ($tour) {
            return [
                'title' => $tour['title'],
                'url' => url('tour/' . $tour['slug']),
                'meta' => trim(($tour['destination'] ?: $tour['country']) . ' · ' . (int) $tour['duration_days'] . ' ngày · ' . money($tour['price'])),
            ];
        }, $tours);
    }

    private function mergeSuggestions(array $base, array $tours)
    {
        return array_slice(array_merge($base, $tours), 0, 4);
    }

    private function quickSuggestions($mode)
    {
        if ($mode === 'admin') {
            return [
                ['title' => 'Mở booking', 'url' => url('admin/bookings'), 'meta' => 'Duyệt đơn'],
                ['title' => 'Quản lý tour', 'url' => url('admin/tours'), 'meta' => 'Sửa tour'],
                ['title' => 'Quản lý user', 'url' => url('admin/users'), 'meta' => 'Phân quyền'],
            ];
        }

        return [
            ['title' => 'Tour biển', 'url' => url('tours/domestic'), 'meta' => 'Gợi ý nghỉ dưỡng'],
            ['title' => 'Tour nước ngoài', 'url' => url('tours/foreign'), 'meta' => 'Khám phá mới'],
            ['title' => 'Xem ưu đãi', 'url' => url('deals'), 'meta' => 'Deal tốt'],
        ];
    }

    private function extractBudget($normalized)
    {
        $budget = ['min' => null, 'max' => null];
        if (preg_match('/(\d+(?:[.,]\d+)?)\s*(triệu|tr|m)/', $normalized, $matches)) {
            $amount = (float) str_replace(',', '.', $matches[1]) * 1000000;
            if (str_contains($normalized, 'dưới') || str_contains($normalized, 'under') || str_contains($normalized, 'tối đa') || str_contains($normalized, 'toi da')) {
                $budget['max'] = $amount;
            } elseif (str_contains($normalized, 'trên') || str_contains($normalized, 'over') || str_contains($normalized, 'từ')) {
                $budget['min'] = $amount;
            } else {
                $budget['max'] = $amount;
            }
            return $budget;
        }

        if (preg_match('/(\d{1,3}(?:[.,]\d{3})+|\d{6,})/', $normalized, $matches)) {
            $amount = (float) preg_replace('/[^\d]/', '', $matches[1]);
            if ($amount > 0) {
                $budget['max'] = $amount;
            }
        }

        return $budget;
    }

    private function extractDuration($normalized)
    {
        if (preg_match('/(\d+)\s*(ngày|ngay)/', $normalized, $matches)) {
            return (int) $matches[1];
        }

        if (preg_match('/(\d+)n(\d+)đ?/u', $normalized, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    private function explainBookingPrice($normalized, array $bookingContext)
    {
        $total = $bookingContext['total'] ?? null;
        $guests = $bookingContext['guests'] ?? null;
        $tourPrice = $bookingContext['tourPrice'] ?? null;

        if ($total === null || $guests === null || $tourPrice === null || $guests <= 0) {
            return null;
        }

        $expectedTotal = $tourPrice * $guests;
        $difference = abs($expectedTotal - $total);
        $tourTitle = $bookingContext['tourTitle'] !== '' ? $bookingContext['tourTitle'] : 'booking hiện tại';

        if ($difference < 1) {
            return 'Đúng rồi, đây là booking của <strong>' . e($tourTitle) . '</strong>. <br>Đơn giá là <strong>' . money($tourPrice) . '</strong> mỗi khách, booking có <strong>' . (int) $guests . '</strong> khách nên tổng là <strong>' . money($total) . '</strong>. <br>QR và thanh toán luôn lấy theo <strong>tổng tiền booking</strong>, không phải đơn giá.';
        }

        return 'Mình thấy booking của bạn đang có dấu hiệu lệch số: đơn giá <strong>' . money($tourPrice) . '</strong>, số khách <strong>' . (int) $guests . '</strong>, đáng ra tổng là <strong>' . money($expectedTotal) . '</strong> nhưng hệ thống đang ghi <strong>' . money($total) . '</strong>. Nếu bạn muốn, mình có thể giúp kiểm tra lại booking này.';
    }

    private function describeBudget($normalized)
    {
        $budget = $this->extractBudget($normalized);
        if ($budget['max'] !== null && $budget['min'] !== null) {
            return money($budget['min']) . ' - ' . money($budget['max']);
        }

        if ($budget['max'] !== null) {
            return 'dưới ' . money($budget['max']);
        }

        if ($budget['min'] !== null) {
            return 'từ ' . money($budget['min']) . ' trở lên';
        }

        return '';
    }

    private function toNumber($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $digits = preg_replace('/[^\d.]/', '', (string) $value);
        return $digits === '' ? null : (float) $digits;
    }

    private function matches($normalized, array $needles)
    {
        foreach ($needles as $needle) {
            if (str_contains($normalized, $this->normalize($needle))) {
                return true;
            }
        }

        return false;
    }

    private function normalize($value)
    {
        $value = mb_strtolower(trim((string) $value), 'UTF-8');
        $value = str_replace(['đ', 'Đ'], ['d', 'd'], $value);
        $ascii = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        $value = $ascii !== false ? $ascii : $value;
        $value = preg_replace('/[^a-z0-9]+/', ' ', $value);

        return trim((string) $value);
    }

    private function json(array $payload)
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}
