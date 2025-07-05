

// AJAX navigation tối ưu: fade mượt, không bị trắng, giữ khung khi loading

// Bộ nhớ cache cho preload
const ajaxPageCache = {};
// Trạng thái đang loading trang mới
let isPageLoading = false;

document.addEventListener('DOMContentLoaded', function () {
    // Spinner nhỏ gọn
    const spinner = document.createElement('div');
    spinner.id = 'ajax-spinner';
    spinner.style = 'display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:9999;background:rgba(0,0,0,0.08);justify-content:center;align-items:center;pointer-events:none;';
    spinner.innerHTML = '<div style="border:6px solid #f3f3f3;border-top:6px solid #fd7e14;border-radius:50%;width:44px;height:44px;animation:spin 1s linear infinite;"></div>';
    document.body.appendChild(spinner);
    const style = document.createElement('style');
    style.innerHTML = '@keyframes spin{0%{transform:rotate(0deg);}100%{transform:rotate(360deg);}}';
    document.head.appendChild(style);

    // Bắt sự kiện click trên tất cả thẻ a nội bộ
    // Preload khi hover vào link nội bộ (chặn khi đang loading trang)
    document.body.addEventListener('mouseover', function (e) {
        if (isPageLoading) return;
        const a = e.target.closest('a');
        if (!a) return;
        const href = a.getAttribute('href');
        if (!href || href.startsWith('http') || href.startsWith('javascript') || href === '#' || a.target === '_blank') return;
        if (href.includes('logout') || href.includes('ajax.php') || href.match(/\.(pdf|jpg|png|mp3|zip|rar|docx?)$/i)) return;
        if (ajaxPageCache[href]) return; // Đã preload
        // Preload bằng AJAX
        fetch(href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                ajaxPageCache[href] = html;
            });
    });

    // Click link nội bộ (chặn khi đang loading trang)
    document.body.addEventListener('click', function (e) {
        if (isPageLoading) { e.preventDefault(); return; }
        const a = e.target.closest('a');
        if (!a) return;
        const href = a.getAttribute('href');
        if (!href || href.startsWith('http') || href.startsWith('javascript') || href === '#' || a.target === '_blank') return;
        if (href.includes('logout') || href.includes('ajax.php') || href.match(/\.(pdf|jpg|png|mp3|zip|rar|docx?)$/i)) return;
        e.preventDefault();
        ajaxNavigate(href);
    });

    // Biến lưu request hiện tại
    let currentAjaxController = null;
    let currentAjaxId = 0;
    function ajaxNavigate(url, push = true) {
        if (isPageLoading) return;
        isPageLoading = true;
        const content = document.querySelector('.content-wrapper');
        if (!content) { window.location.href = url; return; }
        // Luôn giữ min-height tối thiểu để tránh layout nhảy
        const MIN_HEIGHT = 600;
        const oldMinHeight = content.style.minHeight;
        content.style.minHeight = Math.max(content.offsetHeight, MIN_HEIGHT) + 'px';
        spinner.style.display = 'flex';
        // Hủy request cũ nếu có
        if (currentAjaxController) currentAjaxController.abort();
        currentAjaxController = new AbortController();
        currentAjaxId++;
        const thisAjaxId = currentAjaxId;
        // Hàm bỏ min-height khi DOM ổn định
        function removeMinHeightWhenStable() {
            // Đợi 2 frame để browser render xong
            setTimeout(() => {
                content.style.minHeight = oldMinHeight || '';
                isPageLoading = false;
            }, 120);
        }
        // Nếu đã preload thì dùng luôn
        if (ajaxPageCache[url]) {
            setTimeout(() => {
                if (thisAjaxId !== currentAjaxId) return; // Đã có request mới hơn
                const temp = document.createElement('div');
                temp.innerHTML = ajaxPageCache[url];
                const newContent = temp.querySelector('.content-wrapper');
                if (newContent) {
                    content.innerHTML = newContent.innerHTML;
                    removeMinHeightWhenStable();
                    spinner.style.display = 'none';
                    if (push) window.history.pushState({}, '', url);
                    if (window.initPageScripts) window.initPageScripts();
                } else {
                    isPageLoading = false;
                    window.location.href = url;
                }
            }, 0);
            return;
        }
        // Nếu chưa preload thì fetch như cũ, có thể bị abort nếu request mới
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, signal: currentAjaxController.signal })
            .then(res => res.text())
            .then(html => {
                if (thisAjaxId !== currentAjaxId) return; // Đã có request mới hơn
                ajaxPageCache[url] = html;
                const temp = document.createElement('div');
                temp.innerHTML = html;
                const newContent = temp.querySelector('.content-wrapper');
                if (newContent) {
                    content.innerHTML = newContent.innerHTML;
                    removeMinHeightWhenStable();
                    spinner.style.display = 'none';
                    if (push) window.history.pushState({}, '', url);
                    if (window.initPageScripts) window.initPageScripts();
                } else {
                    isPageLoading = false;
                    window.location.href = url;
                }
            })
            .catch((err) => {
                isPageLoading = false;
                if (err.name === 'AbortError') return; // Bị hủy do request mới
                window.location.href = url;
            });
    }

    // Hỗ trợ back/forward
    window.addEventListener('popstate', function () {
        ajaxNavigate(location.pathname + location.search, false);
    });
});
