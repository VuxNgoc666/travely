const header = document.querySelector('[data-header]');
const nav = document.querySelector('[data-nav]');
const navToggle = document.querySelector('[data-nav-toggle]');

if (header) {
    const setHeader = () => header.classList.toggle('is-scrolled', window.scrollY > 8);
    setHeader();
    window.addEventListener('scroll', setHeader, { passive: true });
}

if (navToggle && nav) {
    navToggle.addEventListener('click', () => nav.classList.toggle('is-open'));
}

document.querySelectorAll('[data-min-today]').forEach((field) => {
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    const minDate = `${yyyy}-${mm}-${dd}`;

    field.min = minDate;
    if (field.value && field.value < minDate) {
        field.value = minDate;
    }

    field.addEventListener('change', () => {
        if (field.value && field.value < minDate) {
            field.value = minDate;
        }
    });
});

const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
        }
    });
}, { threshold: 0.12 });

document.querySelectorAll('.reveal').forEach((item) => observer.observe(item));

document.querySelectorAll('.gallery-thumbs img').forEach((thumb) => {
    thumb.addEventListener('click', () => {
        const main = thumb.closest('.detail-gallery')?.querySelector('.gallery-main');
        if (main) {
            main.src = thumb.src;
        }
    });
});

document.querySelectorAll('[data-image-preview-input]').forEach((input) => {
    const preview = document.querySelector(`[data-image-preview="${input.dataset.imagePreviewInput}"]`);
    const image = preview?.querySelector('img');
    if (!preview || !image) {
        return;
    }

    const updatePreview = () => {
        const url = input.value.trim();
        preview.classList.toggle('image-url-preview-empty', !url);
        preview.classList.remove('image-url-preview-broken');

        if (url) {
            image.src = url;
        } else {
            image.removeAttribute('src');
        }
    };

    image.addEventListener('error', () => {
        if (input.value.trim()) {
            preview.classList.add('image-url-preview-broken');
        }
    });
    image.addEventListener('load', () => preview.classList.remove('image-url-preview-broken'));
    input.addEventListener('input', updatePreview);
    updatePreview();
});

document.querySelectorAll('[data-tabs]').forEach((tabs) => {
    const buttons = tabs.querySelectorAll('[data-tab]');
    const panels = tabs.parentElement.querySelectorAll('[data-panel]');

    buttons.forEach((button) => {
        button.addEventListener('click', () => {
            buttons.forEach((item) => item.classList.remove('active'));
            panels.forEach((panel) => panel.classList.remove('active'));
            button.classList.add('active');
            tabs.parentElement.querySelector(`[data-panel="${button.dataset.tab}"]`)?.classList.add('active');
        });
    });
});

setTimeout(() => {
    document.querySelectorAll('.toast').forEach((toast) => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-12px)';
    });
}, 4200);

document.querySelectorAll('[data-ai-assistant]').forEach((assistant) => {
    const toggle = assistant.querySelector('[data-ai-toggle]');
    const panel = assistant.querySelector('[data-ai-panel]');
    const close = assistant.querySelector('[data-ai-close]');
    const minimize = assistant.querySelector('[data-ai-minimize]');
    const restore = assistant.querySelector('[data-ai-restore]');
    const messages = assistant.querySelector('[data-ai-messages]');
    const form = assistant.querySelector('[data-ai-form]');
    const input = assistant.querySelector('[data-ai-input]');
    const walkToggle = assistant.querySelector('[data-ai-walk-toggle]');
    const toursSource = assistant.querySelector('[data-ai-tours]');
    const endpoint = assistant.dataset.aiEndpoint || '';
    const csrfToken = assistant.dataset.aiCsrf || '';
    const pageTitle = assistant.dataset.aiPageTitle || '';
    const currentPath = assistant.dataset.aiCurrentPath || '';
    const bookingTotal = assistant.dataset.aiBookingTotal || '';
    const bookingGuests = assistant.dataset.aiBookingGuests || '';
    const tourPrice = assistant.dataset.aiTourPrice || '';
    const bookingReference = assistant.dataset.aiBookingReference || '';
    const tourTitle = assistant.dataset.aiTourTitle || '';
    const chatOnly = assistant.classList.contains('ai-chat-only');
    const inlineAssistant = assistant.classList.contains('ai-inline-assistant');
    const assistantMode = assistant.dataset.aiMode || 'site';
    let tours = [];
    let isDragging = false;
    let moved = false;
    let autoWalk = true;
    let paused = false;
    let velocityX = -0.75;
    let velocityY = 0.45;
    let lastStep = 0;
    let directionTimer = 0;
    let startX = 0;
    let startY = 0;
    let startRight = 24;
    let startBottom = 24;

    try {
        tours = JSON.parse(toursSource?.textContent || '[]');
    } catch (error) {
        tours = [];
    }

    const getPosition = () => ({
        right: parseFloat(getComputedStyle(assistant).getPropertyValue('--assistant-x')) || 24,
        bottom: parseFloat(getComputedStyle(assistant).getPropertyValue('--assistant-y')) || 24,
    });

    const setPosition = (right, bottom) => {
        const maxRight = Math.max(14, window.innerWidth - assistant.offsetWidth - 14);
        const maxBottom = Math.max(14, window.innerHeight - assistant.offsetHeight - 14);
        assistant.style.setProperty('--assistant-x', `${Math.min(Math.max(14, right), maxRight)}px`);
        assistant.style.setProperty('--assistant-y', `${Math.min(Math.max(14, bottom), maxBottom)}px`);
    };

    const syncWalkingVisual = () => {
        assistant.classList.toggle('is-walking', autoWalk && !paused && !isDragging && panel.hidden);
    };

    const setPaused = (value) => {
        paused = value;
        assistant.classList.toggle('is-paused', paused);
        syncWalkingVisual();
    };

    const setAutoWalk = (value) => {
        autoWalk = value;
        syncWalkingVisual();
        if (walkToggle) {
            walkToggle.textContent = `Cho bé tự đi: ${autoWalk ? 'Bật' : 'Tắt'}`;
        }
    };

    const randomizeDirection = () => {
        const speed = 0.45 + Math.random() * 0.55;
        const angle = Math.random() * Math.PI * 2;
        velocityX = Math.cos(angle) * speed;
        velocityY = Math.sin(angle) * speed;
        if (Math.abs(velocityX) < 0.25) {
            velocityX += velocityX < 0 ? -0.35 : 0.35;
        }
    };

    const keepAwayFromSearch = (right, bottom) => {
        const search = document.querySelector('.home-search');
        if (!search) {
            return { right, bottom };
        }

        const mascotRect = {
            left: window.innerWidth - right - assistant.offsetWidth,
            right: window.innerWidth - right,
            top: window.innerHeight - bottom - assistant.offsetHeight,
            bottom: window.innerHeight - bottom,
        };
        const searchRect = search.getBoundingClientRect();
        const overlap = mascotRect.left < searchRect.right + 18
            && mascotRect.right > searchRect.left - 18
            && mascotRect.top < searchRect.bottom + 18
            && mascotRect.bottom > searchRect.top - 18;

        if (overlap) {
            bottom = Math.max(bottom, window.innerHeight - searchRect.top + 18);
            velocityY = Math.abs(velocityY);
        }

        return { right, bottom };
    };

    const walkStep = (time) => {
        if (!lastStep) {
            lastStep = time;
        }
        const delta = Math.min(32, time - lastStep);
        lastStep = time;

        if (autoWalk && !paused && !isDragging && panel.hidden) {
            let { right, bottom } = getPosition();
            right += velocityX * delta * 0.06;
            bottom += velocityY * delta * 0.06;

            const maxRight = Math.max(14, window.innerWidth - assistant.offsetWidth - 14);
            const maxBottom = Math.max(14, window.innerHeight - assistant.offsetHeight - 14);
            if (right <= 14 || right >= maxRight) {
                velocityX *= -1;
            }
            if (bottom <= 14 || bottom >= maxBottom) {
                velocityY *= -1;
            }

            const adjusted = keepAwayFromSearch(right, bottom);
            setPosition(adjusted.right, adjusted.bottom);
            directionTimer += delta;
            if (directionTimer > 4800) {
                directionTimer = 0;
                randomizeDirection();
            }
        }

        requestAnimationFrame(walkStep);
    };

    const addMessage = (text, type = 'bot') => {
        const item = document.createElement('article');
        item.className = `ai-message ${type}`;
        item.innerHTML = text;
        messages.appendChild(item);
        messages.scrollTop = messages.scrollHeight;
        return item;
    };

    const tourLink = (tour) => `<a href="${tour.url}">${tour.title}</a> · ${tour.duration} · ${tour.price}`;

    const adminLink = (path = '') => {
        const adminHome = document.querySelector('.admin-sidebar .brand')?.getAttribute('href') || '/admin';
        return `${adminHome.replace(/\/$/, '')}${path ? `/${path}` : ''}`;
    };

    const pickTours = (query) => {
        const q = query.toLowerCase();
        let filtered = tours;

        if (q.includes('biển') || q.includes('dao') || q.includes('đảo') || q.includes('phú quốc') || q.includes('nha trang')) {
            filtered = tours.filter((tour) => `${tour.title} ${tour.category} ${tour.destination}`.toLowerCase().includes('biển') || `${tour.title} ${tour.destination}`.toLowerCase().includes('phú quốc') || `${tour.title} ${tour.destination}`.toLowerCase().includes('nha trang'));
        } else if (q.includes('nước ngoài') || q.includes('han') || q.includes('hàn') || q.includes('nhật') || q.includes('âu') || q.includes('singapore')) {
            filtered = tours.filter((tour) => tour.type === 'foreign');
        } else if (q.includes('trong nước') || q.includes('việt') || q.includes('hạ long') || q.includes('đà nẵng') || q.includes('sapa')) {
            filtered = tours.filter((tour) => tour.type === 'domestic');
        }

        return (filtered.length ? filtered : tours).slice(0, 3);
    };

    const answer = (query) => {
        const q = query.toLowerCase();

        if (assistantMode === 'admin') {
            if (q.includes('booking') || q.includes('xác nhận') || q.includes('xac nhan') || q.includes('chờ') || q.includes('cho')) {
                return `Vào <a href="${adminLink('bookings')}">Booking</a>, kiểm tra các dòng <strong>Chờ xác nhận</strong>, đổi trạng thái sang <strong>Đã xác nhận</strong> hoặc <strong>Đã hủy</strong> rồi bấm <strong>Lưu</strong>.`;
            }

            if (q.includes('tour') || q.includes('thêm') || q.includes('them') || q.includes('sửa') || q.includes('sua')) {
                return `Vào <a href="${adminLink('tours')}">Quản lý tour</a> để sửa danh sách, hoặc mở nhanh <a href="${adminLink('tours/create')}">Thêm tour mới</a>. Nên chuẩn bị thumbnail, hero image, giá, lịch trình và ngày khởi hành trước khi lưu.`;
            }

            if (q.includes('người dùng') || q.includes('nguoi dung') || q.includes('user') || q.includes('quyền') || q.includes('quyen')) {
                return `Vào <a href="${adminLink('users')}">Người dùng</a> để đổi quyền <strong>user/admin</strong>. Tài khoản admin sẽ không hiện nút xóa để tránh mất quyền quản trị.`;
            }

            if (q.includes('doanh thu') || q.includes('revenue') || q.includes('tiền') || q.includes('tien')) {
                return `Doanh thu trên dashboard là tổng tiền của các booking đã được xác nhận. Muốn tăng số này, hãy xác nhận booking hợp lệ trong <a href="${adminLink('bookings')}">Booking</a>.`;
            }

            if (q.includes('website') || q.includes('trang chủ') || q.includes('trang chu')) {
                const websiteUrl = document.querySelector('.admin-sidebar nav a:last-of-type')?.getAttribute('href') || '/';
                return `Bấm <a href="${websiteUrl}">Xem website</a> trong sidebar để quay lại giao diện người dùng.`;
            }

            return `Mình có thể hỗ trợ nhanh các việc admin: <a href="${adminLink('bookings')}">xử lý booking</a>, <a href="${adminLink('tours')}">quản lý tour</a>, <a href="${adminLink('users')}">phân quyền người dùng</a> hoặc giải thích doanh thu trên dashboard.`;
        }

        if (q.includes('đặt') || q.includes('booking') || q.includes('cách')) {
            return 'Bạn chọn tour → đăng nhập → chọn ngày khởi hành/số khách → bấm <strong>Đặt tour ngay</strong>. Booking sẽ nằm trong trang tài khoản để admin xác nhận.';
        }

        if (q.includes('đăng nhập') || q.includes('tài khoản')) {
            return 'Bạn có thể đăng nhập bằng tài khoản demo: <strong>user@travely.local / 123456</strong>. Admin: <strong>admin@travely.local / 123456</strong>.';
        }

        if (q.includes('ưu đãi') || q.includes('sale') || q.includes('giảm')) {
            return `Có nhé. Bạn xem trang <a href="${window.location.origin}${document.querySelector('.brand')?.getAttribute('href') || '/'}deals">Ưu đãi</a>, hoặc thử các tour này:<br>${pickTours('ưu đãi').map(tourLink).join('<br>')}`;
        }

        if (q.includes('giá') || q.includes('rẻ') || q.includes('ngân sách')) {
            return `Nếu muốn tối ưu chi phí, mình gợi ý:<br>${pickTours(query).map(tourLink).join('<br>')}`;
        }

        if (q.includes('tour') || q.includes('đi') || q.includes('biển') || q.includes('nước ngoài') || q.includes('trong nước')) {
            return `Mình gợi ý 3 tour hợp với nhu cầu của bạn:<br>${pickTours(query).map(tourLink).join('<br>')}`;
        }

        return 'Mình có thể gợi ý tour biển, tour trong nước, tour nước ngoài, ưu đãi, hoặc hướng dẫn cách đặt tour. Bạn thử nhập: “gợi ý tour biển 3 ngày” nhé.';
    };

    const renderSuggestions = (suggestions = []) => {
        if (!suggestions.length) {
            return '';
        }

        const cards = suggestions.map((item) => `
            <a class="ai-suggestion-card" href="${item.url}">
                <strong>${item.title}</strong>
                <span>${item.meta || ''}</span>
            </a>
        `).join('');

        return `<div class="ai-suggestions">${cards}</div>`;
    };

    const submitQuery = async (query) => {
        const clean = query.trim();
        if (!clean) {
            return;
        }
        addMessage(clean, 'user');

        const loadingMessage = addMessage('Mình đang xử lý yêu cầu...', 'bot');

        if (!endpoint) {
            loadingMessage.innerHTML = answer(clean);
            return;
        }

        try {
            const body = new URLSearchParams({
                _csrf: csrfToken,
                query: clean,
                mode: assistantMode,
                page_title: pageTitle,
                current_path: currentPath,
                booking_total: bookingTotal,
                booking_guests: bookingGuests,
                tour_price: tourPrice,
                booking_reference: bookingReference,
                tour_title: tourTitle,
            });

            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: body.toString(),
            });

            if (!response.ok) {
                throw new Error(`AI request failed with ${response.status}`);
            }

            const payload = await response.json();
            loadingMessage.innerHTML = `${payload.replyHtml || 'Mình chưa tìm thấy kết quả phù hợp.'}${renderSuggestions(payload.suggestions || [])}`;
        } catch (error) {
            loadingMessage.innerHTML = answer(clean);
        }
    };

    const setMinimized = (value) => {
        if (!chatOnly || inlineAssistant) {
            return;
        }
        panel.hidden = value;
        if (restore) {
            restore.hidden = !value;
        }
        assistant.classList.toggle('is-minimized', value);
        if (!value) {
            input?.focus();
        }
        if (assistantMode === 'admin') {
            document.querySelectorAll('[data-admin-ai-open]').forEach((button) => {
                button.classList.toggle('active', !value);
            });
        }
    };

    if (chatOnly && !inlineAssistant && (document.querySelector('.auth-login') || assistantMode === 'admin' || assistantMode === 'site')) {
        setMinimized(true);
    }

    if (assistantMode === 'admin') {
        document.querySelectorAll('[data-admin-ai-open]').forEach((button) => {
            button.addEventListener('click', () => setMinimized(false));
        });
    }

    toggle?.addEventListener('pointerdown', (event) => {
        isDragging = true;
        setPaused(true);
        moved = false;
        startX = event.clientX;
        startY = event.clientY;
        const position = getPosition();
        startRight = position.right;
        startBottom = position.bottom;
        toggle.setPointerCapture(event.pointerId);
    });

    toggle?.addEventListener('pointermove', (event) => {
        if (!isDragging) {
            return;
        }
        const dx = event.clientX - startX;
        const dy = event.clientY - startY;
        if (Math.abs(dx) + Math.abs(dy) > 6) {
            moved = true;
        }
        setPosition(startRight - dx, startBottom - dy);
    });

    toggle?.addEventListener('pointerup', () => {
        isDragging = false;
        if (!moved) {
            panel.hidden = !panel.hidden;
            setPaused(!panel.hidden);
            if (!panel.hidden) {
                input?.focus();
            }
        } else {
            setPaused(false);
        }
    });

    minimize?.addEventListener('click', () => {
        setMinimized(true);
    });

    restore?.addEventListener('click', () => {
        setMinimized(false);
    });

    close?.addEventListener('click', () => {
        if (chatOnly) {
            setMinimized(true);
            return;
        }
        panel.hidden = true;
        setPaused(false);
    });

    assistant.addEventListener('mouseenter', () => setPaused(true));
    assistant.addEventListener('mouseleave', () => {
        if (panel.hidden && !isDragging) {
            setPaused(false);
        }
    });

    walkToggle?.addEventListener('click', () => {
        setAutoWalk(!autoWalk);
        panel.hidden = false;
        setPaused(!panel.hidden);
    });

    assistant.querySelectorAll('[data-ai-quick]').forEach((button) => {
        button.addEventListener('click', () => {
            submitQuery(button.dataset.aiQuick || button.textContent);
        });
    });

    form?.addEventListener('submit', (event) => {
        event.preventDefault();
        submitQuery(input.value);
        input.value = '';
    });

    if (!chatOnly) {
        setAutoWalk(true);
        randomizeDirection();
        requestAnimationFrame(walkStep);
    }
});
