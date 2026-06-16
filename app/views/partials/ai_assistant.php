<?php
$assistantMode = $assistantMode ?? 'site';
$assistantPlacement = $assistantPlacement ?? 'floating';
$isAdminAssistant = $assistantMode === 'admin';
$isInlineAssistant = $assistantPlacement === 'inline';
$assistantTours = [];

try {
    $assistantTours = array_map(function ($tour) {
        return [
            'title' => $tour['title'],
            'slug' => $tour['slug'],
            'type' => $tour['type'],
            'destination' => $tour['destination'],
            'country' => $tour['country'],
            'category' => $tour['category'],
            'price' => money($tour['price']),
            'duration' => (int) $tour['duration_days'] . 'N' . (int) $tour['duration_nights'] . 'Đ',
            'url' => url('tour/' . $tour['slug']),
        ];
    }, Tour::featured(8));
} catch (Throwable $exception) {
    $assistantTours = [];
}

$assistantTitle = $isAdminAssistant ? 'Travely Admin AI' : 'Travely AI';
$assistantSubtitle = $isAdminAssistant ? 'Hỗ trợ quản trị nhanh' : 'Gợi ý tour và giải đáp nhanh';
$assistantGreeting = $isAdminAssistant
    ? 'Xin chào admin. Mình có thể nhắc cách xử lý booking, thêm tour mới, quản lý người dùng hoặc kiểm tra doanh thu demo.'
    : 'Xin chào, mình là Travely AI. Bạn muốn đi biển, đi nước ngoài, săn ưu đãi hay cần tư vấn đặt tour?';
$assistantPlaceholder = $isAdminAssistant ? 'Hỏi về booking, tour, user...' : 'Nhập câu hỏi của bạn...';
$quickReplies = $isAdminAssistant ? [
    ['label' => 'Booking chờ', 'query' => 'Kiểm tra booking chờ xác nhận'],
    ['label' => 'Thêm tour', 'query' => 'Cách thêm tour mới'],
    ['label' => 'Người dùng', 'query' => 'Quản lý người dùng'],
    ['label' => 'Doanh thu', 'query' => 'Xem doanh thu hôm nay'],
] : [
    ['label' => 'Tour biển', 'query' => 'Gợi ý tour biển đẹp'],
    ['label' => 'Nước ngoài', 'query' => 'Tôi muốn đi nước ngoài'],
    ['label' => 'Ưu đãi', 'query' => 'Có ưu đãi nào không?'],
    ['label' => 'Cách đặt tour', 'query' => 'Cách đặt tour như thế nào?'],
];
$assistantBookingTotal = isset($booking['total_price']) ? (float) $booking['total_price'] : null;
$assistantBookingGuests = isset($booking['guests']) ? (int) $booking['guests'] : null;
$assistantTourPrice = isset($tour['price']) ? (float) $tour['price'] : null;
?>
<section class="ai-assistant ai-chat-only <?= $isAdminAssistant ? 'admin-ai-assistant' : '' ?> <?= $isInlineAssistant ? 'ai-inline-assistant' : '' ?>" data-ai-assistant data-ai-mode="<?= e($assistantMode) ?>" data-ai-endpoint="<?= e(url('ai/ask')) ?>" data-ai-csrf="<?= e(csrf_token()) ?>" data-ai-page-title="<?= e($title ?? APP_NAME) ?>" data-ai-current-path="<?= e(trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/')) ?>" data-ai-booking-total="<?= e($assistantBookingTotal ?? '') ?>" data-ai-booking-guests="<?= e($assistantBookingGuests ?? '') ?>" data-ai-tour-price="<?= e($assistantTourPrice ?? '') ?>" data-ai-booking-reference="<?= e($booking['payment_reference'] ?? '') ?>" data-ai-tour-title="<?= e($tour['title'] ?? '') ?>">
    <div class="ai-chat-panel" data-ai-panel>
        <header>
            <div>
                <strong><?= e($assistantTitle) ?></strong>
                <span><?= e($assistantSubtitle) ?></span>
            </div>
            <button class="ai-minimize" type="button" data-ai-minimize aria-label="Thu nhỏ">&minus;</button>
            <button type="button" data-ai-close aria-label="Đóng trợ lý">×</button>
        </header>
        <div class="ai-messages" data-ai-messages>
            <article class="ai-message bot"><?= e($assistantGreeting) ?></article>
        </div>
        <div class="ai-quick-replies">
            <?php foreach ($quickReplies as $reply): ?>
                <button type="button" data-ai-quick="<?= e($reply['query']) ?>"><?= e($reply['label']) ?></button>
            <?php endforeach; ?>
        </div>
        <form class="ai-input-row" data-ai-form>
            <input data-ai-input placeholder="<?= e($assistantPlaceholder) ?>" autocomplete="off">
            <button type="submit">Gửi</button>
        </form>
    </div>
    <button class="ai-chat-mini" type="button" data-ai-restore hidden>
        <span><?= e($assistantTitle) ?></span>
        <strong>Mở chat</strong>
    </button>
    <script type="application/json" data-ai-tours><?= json_encode($assistantTours, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>
</section>
