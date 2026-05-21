<section class="contact-hero">
    <div>
        <p class="eyebrow">Trang chủ · Liên hệ</p>
        <h1>Liên hệ với Travely</h1>
        <p>Chúng tôi luôn sẵn sàng hỗ trợ và giải đáp mọi thắc mắc để bạn có trải nghiệm du lịch tuyệt vời nhất.</p>
    </div>
</section>

<section class="contact-layout">
    <aside class="contact-info">
        <h2>Thông tin liên hệ</h2>
        <div class="contact-list">
            <article>
                <span>☎</span>
                <div><small>Hotline miễn phí</small><strong>1900 1234</strong><p>Hỗ trợ 24/7 tất cả các ngày trong tuần</p></div>
            </article>
            <article>
                <span>✉</span>
                <div><small>Email</small><strong>support@travely.vn</strong><p>Phản hồi trong vòng 30 phút</p></div>
            </article>
            <article>
                <span>⌖</span>
                <div><small>Địa chỉ trụ sở</small><strong>Tầng 8, 123 Nguyễn Huệ</strong><p>Quận 1, TP. Hồ Chí Minh</p></div>
            </article>
            <article>
                <span>◷</span>
                <div><small>Giờ làm việc</small><strong>Thứ 2 - Chủ nhật: 8:00 - 21:00</strong><p>Kể cả ngày lễ và Tết</p></div>
            </article>
        </div>
    </aside>

    <div class="contact-main">
        <form class="contact-form" method="post" action="<?= url('contact') ?>">
            <?= csrf_field() ?>
            <h2>Gửi yêu cầu cho chúng tôi</h2>
            <div class="contact-form-grid">
                <label>Họ và tên *
                    <input name="name" required>
                </label>
                <label>Số điện thoại *
                    <input name="phone" required>
                </label>
                <label>Email *
                    <input type="email" name="email" required>
                </label>
                <label>Chủ đề *
                    <select name="subject" required>
                        <option value="">Chọn chủ đề</option>
                        <option>Tư vấn đặt tour</option>
                        <option>Hỗ trợ thanh toán</option>
                        <option>Thay đổi booking</option>
                        <option>Hợp tác đối tác</option>
                    </select>
                </label>
                <label class="wide">Nội dung tin nhắn *
                    <textarea name="message" rows="7" placeholder="Nhập nội dung bạn cần hỗ trợ..." required></textarea>
                </label>
            </div>
            <div class="contact-form-bottom">
                <p>Thông tin của bạn được bảo mật và chỉ sử dụng để hỗ trợ bạn tốt nhất.</p>
                <button class="btn primary" type="submit">Gửi tin nhắn</button>
            </div>
        </form>

    </div>
</section>

<section class="faq-section">
    <h2>Câu hỏi thường gặp</h2>
    <div class="faq-list">
        <details><summary>Làm thế nào để đặt tour tại Travely?</summary><p>Chọn tour, đăng nhập, nhập thông tin khách và gửi yêu cầu đặt tour. Admin sẽ xác nhận trạng thái booking.</p></details>
        <details><summary>Phương thức thanh toán nào được chấp nhận?</summary><p>Travely hỗ trợ chuyển khoản, thẻ ngân hàng và ví điện tử trong bản demo giao diện.</p></details>
        <details><summary>Chính sách hủy tour như thế nào?</summary><p>Bạn có thể liên hệ tư vấn viên để được hỗ trợ theo từng lịch trình cụ thể.</p></details>
        <details><summary>Tôi có thể thay đổi thông tin đặt tour không?</summary><p>Có. Gửi yêu cầu qua form liên hệ hoặc hotline để được cập nhật booking.</p></details>
    </div>
</section>
