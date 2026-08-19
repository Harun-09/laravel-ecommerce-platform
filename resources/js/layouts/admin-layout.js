(function () {
    const MOBILE_BREAKPOINT = 1024;
    let closeNotificationMenu = () => {};
    let closeMessageMenu = () => {};

    function isMobileViewport() {
        return window.matchMedia(`(max-width: ${MOBILE_BREAKPOINT}px)`).matches;
    }

    function closeMobileSidebar(body) {
        body.classList.remove('sidebar-open');
    }

    function syncSidebarStateForViewport(body) {
        if (isMobileViewport()) {
            body.classList.remove('sidebar-collapsed');
            return;
        }

        closeMobileSidebar(body);
    }

    function initSidebarToggle() {
        const body = document.body;
        const toggleButtons = document.querySelectorAll('.button-show-hide');
        const sidebar = document.querySelector('.sidebar');
        if (!toggleButtons.length || !sidebar) return;

        const toggleSidebar = () => {
            if (isMobileViewport()) {
                body.classList.toggle('sidebar-open');
                return;
            }

            body.classList.toggle('sidebar-collapsed');
        };

        toggleButtons.forEach((button) => {
            button.addEventListener('click', (event) => {
                event.stopPropagation();
                toggleSidebar();
            });
        });

        document.addEventListener('click', (event) => {
            if (!isMobileViewport() || !body.classList.contains('sidebar-open')) return;
            if (!(event.target instanceof Node)) return;

            const clickedToggle = Array.from(toggleButtons).some((button) => button.contains(event.target));
            if (clickedToggle || sidebar.contains(event.target)) return;

            closeMobileSidebar(body);
        });

        window.addEventListener('resize', () => {
            syncSidebarStateForViewport(body);
        });

        syncSidebarStateForViewport(body);
    }

    function isBlockingOverlay(element) {
        if (!(element instanceof HTMLElement)) return false;
        if (element.classList.contains('sidebar')) return false;
        if (element.closest('.sidebar') || element.closest('.main-wrapper')) return false;

        const style = window.getComputedStyle(element);
        const isFixedLayer = style.position === 'fixed' || style.position === 'absolute';
        const hasBackdropLikeClass =
            /overlay|backdrop|modal/i.test(element.className || '') ||
            element.id === 'overlay' ||
            element.id === 'backdrop';

        const rect = element.getBoundingClientRect();
        const coversViewport =
            rect.width >= window.innerWidth * 0.95 &&
            rect.height >= window.innerHeight * 0.95 &&
            rect.top <= 2 &&
            rect.left <= 2;

        const zIndex = Number.parseInt(style.zIndex || '0', 10) || 0;
        const blocksPointer = style.pointerEvents !== 'none';

        return isFixedLayer && hasBackdropLikeClass && coversViewport && blocksPointer && zIndex >= 90;
    }

    function clearBlockingOverlays() {
        const candidates = document.querySelectorAll('body *');
        candidates.forEach((element) => {
            if (isBlockingOverlay(element)) {
                element.remove();
            }
        });
    }

    function getCsrfToken() {
        const tokenNode = document.querySelector('meta[name="csrf-token"]');
        return tokenNode ? tokenNode.getAttribute('content') || '' : '';
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function initMessageDropdown() {
        const root = document.querySelector('[data-message-root]');
        if (!root) return;

        const trigger = root.querySelector('[data-message-trigger]');
        const menu = root.querySelector('[data-message-menu]');
        const badge = root.querySelector('[data-message-badge]');
        const list = root.querySelector('[data-message-list]');
        const markAllBtn = root.querySelector('[data-message-mark-all]');
        const feedUrl = root.dataset.feedUrl || '';
        const markAllUrl = root.dataset.markAllUrl || '';
        const readUrlTemplate = root.dataset.readUrlTemplate || '';

        if (!trigger || !menu || !list || !markAllBtn || !feedUrl || !markAllUrl || !readUrlTemplate) {
            return;
        }

        const csrfToken = getCsrfToken();
        let unreadCount = Number.parseInt(badge?.textContent || '0', 10) || 0;
        let isLoadingFeed = false;

        const requestHeaders = (includeCsrf = false) => {
            const headers = {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            };

            if (includeCsrf && csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken;
            }

            return headers;
        };

        const setBadgeCount = (count) => {
            unreadCount = Number.isFinite(count) ? count : 0;
            if (!badge) return;

            if (unreadCount <= 0) {
                badge.textContent = '0';
                badge.classList.add('is-hidden');
            } else {
                badge.textContent = unreadCount > 99 ? '99+' : String(unreadCount);
                badge.classList.remove('is-hidden');
            }

            markAllBtn.disabled = unreadCount <= 0;
        };

        const renderEmpty = (message) => {
            list.innerHTML = `<div class="message-empty">${escapeHtml(message)}</div>`;
        };

        const renderMessages = (messages) => {
            if (!Array.isArray(messages) || messages.length === 0) {
                renderEmpty('No messages yet.');
                return;
            }

            list.innerHTML = messages
                .map((message) => {
                    const id = escapeHtml(message.id);
                    const name = escapeHtml(message.name || 'Customer');
                    const subject = escapeHtml(message.subject || 'No subject');
                    const body = escapeHtml(message.message || '');
                    const time = escapeHtml(message.created_at_human || 'Just now');
                    const url = escapeHtml(message.url || '#');
                    const isUnread = Boolean(message.is_unread);

                    return `
                        <a href="${url}" class="message-item ${isUnread ? 'unread' : ''}"
                           data-message-item
                           data-message-id="${id}"
                           data-message-unread="${isUnread ? '1' : '0'}">
                            <div class="message-item-head">
                                <span class="message-item-name">${name}</span>
                                <span class="message-item-time">${time}</span>
                            </div>
                            <div class="message-item-subject">${subject}</div>
                            <div class="message-item-body">${body}</div>
                        </a>
                    `;
                })
                .join('');
        };

        const markAsRead = async (messageId) => {
            const requestUrl = readUrlTemplate.replace('__MESSAGE_ID__', encodeURIComponent(messageId));
            const response = await fetch(requestUrl, {
                method: 'POST',
                headers: requestHeaders(true),
            });

            if (!response.ok) {
                throw new Error('Failed to mark message as read.');
            }

            const payload = await response.json();
            setBadgeCount(Number(payload?.unread_count ?? 0));
            return payload;
        };

        const loadFeed = async (silent = false) => {
            if (isLoadingFeed) return;
            isLoadingFeed = true;

            if (!silent) {
                renderEmpty('Loading messages...');
            }

            try {
                const response = await fetch(feedUrl, {
                    method: 'GET',
                    headers: requestHeaders(false),
                });

                if (!response.ok) {
                    throw new Error('Unable to load messages.');
                }

                const payload = await response.json();
                setBadgeCount(Number(payload?.unread_count ?? 0));
                renderMessages(payload?.messages ?? []);
            } catch (error) {
                if (!silent) {
                    renderEmpty('Failed to load messages. Please retry.');
                }
            } finally {
                isLoadingFeed = false;
            }
        };

        const openMenu = () => {
            closeNotificationMenu();
            menu.hidden = false;
            trigger.setAttribute('aria-expanded', 'true');
            loadFeed(false);
        };

        const closeMenu = () => {
            menu.hidden = true;
            trigger.setAttribute('aria-expanded', 'false');
        };

        trigger.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();

            if (menu.hidden) {
                openMenu();
            } else {
                closeMenu();
            }
        });

        markAllBtn.addEventListener('click', async (event) => {
            event.preventDefault();
            if (unreadCount <= 0) return;

            markAllBtn.disabled = true;
            try {
                const response = await fetch(markAllUrl, {
                    method: 'POST',
                    headers: requestHeaders(true),
                });

                if (!response.ok) {
                    throw new Error('Failed to mark all messages as read.');
                }

                const payload = await response.json();
                setBadgeCount(Number(payload?.unread_count ?? 0));
                await loadFeed(true);
            } catch (error) {
                markAllBtn.disabled = false;
            }
        });

        list.addEventListener('click', (event) => {
            const item = event.target.closest('[data-message-item]');
            if (!item) return;

            const messageId = item.getAttribute('data-message-id') || '';
            const isUnread = item.getAttribute('data-message-unread') === '1';
            const targetUrl = item.getAttribute('href') || '#';

            if (targetUrl === '#') {
                event.preventDefault();
            }

            if (!messageId || !isUnread) return;

            if (targetUrl !== '#') {
                event.preventDefault();
            }

            markAsRead(messageId)
                .then(() => {
                    item.classList.remove('unread');
                    item.setAttribute('data-message-unread', '0');
                })
                .finally(() => {
                    if (targetUrl !== '#') {
                        window.location.href = targetUrl;
                    }
                });
        });

        document.addEventListener('click', (event) => {
            if (!root.contains(event.target)) {
                closeMenu();
            }
        });

        setInterval(() => {
            loadFeed(true);
        }, 45000);

        setBadgeCount(unreadCount);
        loadFeed(true);
        closeMessageMenu = closeMenu;
    }

    function initNotificationDropdown() {
        const root = document.querySelector('[data-notification-root]');
        if (!root) return;

        const trigger = root.querySelector('[data-notification-trigger]');
        const menu = root.querySelector('[data-notification-menu]');
        const badge = root.querySelector('[data-notification-badge]');
        const list = root.querySelector('[data-notification-list]');
        const markAllBtn = root.querySelector('[data-notification-mark-all]');
        const feedUrl = root.dataset.feedUrl || '';
        const markAllUrl = root.dataset.markAllUrl || '';
        const readUrlTemplate = root.dataset.readUrlTemplate || '';

        if (!trigger || !menu || !list || !markAllBtn || !feedUrl || !markAllUrl || !readUrlTemplate) {
            return;
        }

        const csrfToken = getCsrfToken();
        let unreadCount = Number.parseInt(badge?.textContent || '0', 10) || 0;
        let isLoadingFeed = false;

        const requestHeaders = (includeCsrf = false) => {
            const headers = {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            };

            if (includeCsrf && csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken;
            }

            return headers;
        };

        const setBadgeCount = (count) => {
            unreadCount = Number.isFinite(count) ? count : 0;
            if (!badge) return;

            if (unreadCount <= 0) {
                badge.textContent = '0';
                badge.classList.add('is-hidden');
            } else {
                badge.textContent = unreadCount > 99 ? '99+' : String(unreadCount);
                badge.classList.remove('is-hidden');
            }

            markAllBtn.disabled = unreadCount <= 0;
        };

        const renderEmpty = (message) => {
            list.innerHTML = `<div class="notification-empty">${escapeHtml(message)}</div>`;
        };

        const renderNotifications = (notifications) => {
            if (!Array.isArray(notifications) || notifications.length === 0) {
                renderEmpty('No notifications yet.');
                return;
            }

            list.innerHTML = notifications
                .map((notification) => {
                    const id = escapeHtml(notification.id);
                    const title = escapeHtml(notification.title || 'System Notification');
                    const message = escapeHtml(notification.message || 'You have a new notification.');
                    const time = escapeHtml(notification.created_at_human || 'Just now');
                    const url = escapeHtml(notification.url || '#');
                    const isRead = Boolean(notification.is_read);

                    return `
                        <a href="${url}" class="notification-item ${isRead ? '' : 'unread'}"
                           data-notification-item
                           data-notification-id="${id}"
                           data-notification-read="${isRead ? '1' : '0'}">
                            <div class="notification-item-title">${title}</div>
                            <div class="notification-item-message">${message}</div>
                            <div class="notification-item-time">${time}</div>
                        </a>
                    `;
                })
                .join('');
        };

        const markAsRead = async (notificationId) => {
            const requestUrl = readUrlTemplate.replace('__NOTIFICATION_ID__', encodeURIComponent(notificationId));
            const response = await fetch(requestUrl, {
                method: 'POST',
                headers: requestHeaders(true),
            });

            if (!response.ok) {
                throw new Error('Failed to mark notification as read.');
            }

            const payload = await response.json();
            setBadgeCount(Number(payload?.unread_count ?? 0));
            return payload;
        };

        const loadFeed = async (silent = false) => {
            if (isLoadingFeed) return;
            isLoadingFeed = true;

            if (!silent) {
                renderEmpty('Loading notifications...');
            }

            try {
                const response = await fetch(feedUrl, {
                    method: 'GET',
                    headers: requestHeaders(false),
                });

                if (!response.ok) {
                    throw new Error('Unable to load notifications.');
                }

                const payload = await response.json();
                setBadgeCount(Number(payload?.unread_count ?? 0));
                renderNotifications(payload?.notifications ?? []);
            } catch (error) {
                if (!silent) {
                    renderEmpty('Failed to load notifications. Please retry.');
                }
            } finally {
                isLoadingFeed = false;
            }
        };

        const openMenu = () => {
            closeMessageMenu();
            menu.hidden = false;
            trigger.setAttribute('aria-expanded', 'true');
            loadFeed(false);
        };

        const closeMenu = () => {
            menu.hidden = true;
            trigger.setAttribute('aria-expanded', 'false');
        };

        trigger.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();

            if (menu.hidden) {
                openMenu();
            } else {
                closeMenu();
            }
        });

        markAllBtn.addEventListener('click', async (event) => {
            event.preventDefault();
            if (unreadCount <= 0) return;

            markAllBtn.disabled = true;
            try {
                const response = await fetch(markAllUrl, {
                    method: 'POST',
                    headers: requestHeaders(true),
                });

                if (!response.ok) {
                    throw new Error('Failed to mark all as read.');
                }

                const payload = await response.json();
                setBadgeCount(Number(payload?.unread_count ?? 0));
                await loadFeed(true);
            } catch (error) {
                markAllBtn.disabled = false;
            }
        });

        list.addEventListener('click', (event) => {
            const item = event.target.closest('[data-notification-item]');
            if (!item) return;

            const notificationId = item.getAttribute('data-notification-id') || '';
            const isRead = item.getAttribute('data-notification-read') === '1';
            const targetUrl = item.getAttribute('href') || '#';

            if (targetUrl === '#') {
                event.preventDefault();
            }

            if (!notificationId || isRead) return;

            if (targetUrl !== '#') {
                event.preventDefault();
            }

            markAsRead(notificationId)
                .then(() => {
                    item.classList.remove('unread');
                    item.setAttribute('data-notification-read', '1');
                })
                .finally(() => {
                    if (targetUrl !== '#') {
                        window.location.href = targetUrl;
                    }
                });
        });

        document.addEventListener('click', (event) => {
            if (!root.contains(event.target)) {
                closeMenu();
            }
        });

        setInterval(() => {
            loadFeed(true);
        }, 45000);

        setBadgeCount(unreadCount);
        loadFeed(true);
        closeNotificationMenu = closeMenu;
    }

    document.addEventListener('DOMContentLoaded', () => {
        clearBlockingOverlays();
        initSidebarToggle();
        initMessageDropdown();
        initNotificationDropdown();
    });
    window.addEventListener('load', clearBlockingOverlays);
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            clearBlockingOverlays();
            closeMobileSidebar(document.body);
            closeMessageMenu();
            closeNotificationMenu();
        }
    });
})();
