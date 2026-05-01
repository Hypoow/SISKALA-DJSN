import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;
window.Pusher = Pusher;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const csrfToken = document.head.querySelector('meta[name="csrf-token"]')?.content;

if (csrfToken) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
}

const fallbackAppRoutes = {
    dashboard: '/dashboard',
    login: '/login',
};

const appRoutes = {
    ...fallbackAppRoutes,
    ...(window.Schedulo?.routes || {}),
};

const refreshTimers = new Map();
const pendingRefreshes = new Map();
let dayBoundaryTimerId = null;
let hiddenAt = null;
let requestErrorAlertOpen = false;

const hasTopic = (detail, topic) => Array.isArray(detail?.topics) && detail.topics.includes(topic);
const matchesAnyTopic = (detail, topics) => topics.some((topic) => hasTopic(detail, topic));

const isBlockingInteraction = () => {
    const activeElement = document.activeElement;
    const hasBlockingOverlay = Boolean(document.querySelector('.modal.show, .swal2-container.swal2-shown'));
    const isEditingField = activeElement instanceof HTMLElement
        && !activeElement.hasAttribute('readonly')
        && !activeElement.hasAttribute('disabled')
        && (
            ['INPUT', 'TEXTAREA', 'SELECT'].includes(activeElement.tagName)
            || activeElement.isContentEditable
        );

    return hasBlockingOverlay || isEditingField;
};

const dispatchRealtimeEvent = (detail) => {
    window.dispatchEvent(new CustomEvent('schedulo:realtime', {
        detail: {
            topics: [],
            receivedAt: new Date().toISOString(),
            ...detail,
        },
    }));
};

const livewireFailureCopy = (status, content = '') => {
    const lowerContent = String(content || '').toLowerCase();

    if (status === 419) {
        return {
            icon: 'warning',
            title: 'Sesi halaman kedaluwarsa',
            message: 'Halaman ini sudah terlalu lama terbuka sehingga token keamanan tidak lagi cocok. Muat ulang halaman untuk menyambungkan ulang data.',
            note: 'Ini biasa terjadi setelah tab dibiarkan lama atau server aplikasi dimuat ulang.',
            confirmText: 'Muat ulang halaman',
            action: 'reload',
        };
    }

    if ([401, 440].includes(status)) {
        return {
            icon: 'warning',
            title: 'Sesi login berakhir',
            message: 'Akses Anda perlu diverifikasi ulang sebelum data dapat diproses.',
            note: 'Silakan masuk kembali agar perubahan berikutnya tersimpan dengan aman.',
            confirmText: 'Masuk kembali',
            action: 'login',
        };
    }

    if (status === 403) {
        return {
            icon: 'error',
            title: 'Akses tidak tersedia',
            message: 'Akun Anda tidak memiliki izin untuk menjalankan aksi ini.',
            note: 'Jika akses seharusnya tersedia, hubungi admin sistem untuk penyesuaian role.',
            confirmText: 'Mengerti',
            action: 'dismiss',
        };
    }

    if (status === 404 || lowerContent.includes('endpoint tidak ditemukan')) {
        return {
            icon: 'info',
            title: 'Koneksi halaman tidak sinkron',
            message: 'Endpoint pembaruan data tidak ditemukan oleh server. Biasanya ini terjadi setelah halaman dibiarkan lama, route berubah, atau server lokal sempat restart.',
            note: 'Muat ulang halaman agar aplikasi mengambil alamat endpoint terbaru.',
            confirmText: 'Muat ulang halaman',
            action: 'reload',
        };
    }

    if (status === 422) {
        return {
            icon: 'warning',
            title: 'Data belum lengkap',
            message: 'Ada isian yang belum valid sehingga permintaan tidak dapat diproses.',
            note: 'Periksa kembali form yang sedang dibuka lalu coba simpan lagi.',
            confirmText: 'Periksa form',
            action: 'dismiss',
        };
    }

    if ([500, 502, 503, 504].includes(status)) {
        return {
            icon: 'error',
            title: 'Server belum siap merespons',
            message: 'Permintaan ke server gagal diproses. Halaman tetap aman, tetapi data perlu disinkronkan ulang.',
            note: 'Jika Anda baru menjalankan server lokal, pastikan service Laravel dan Vite masih aktif.',
            confirmText: 'Muat ulang halaman',
            action: 'reload',
        };
    }

    return {
        icon: 'error',
        title: 'Permintaan gagal',
        message: 'Aplikasi tidak dapat menyelesaikan permintaan terakhir.',
        note: 'Coba muat ulang halaman jika pesan ini muncul lagi.',
        confirmText: 'Muat ulang halaman',
        action: 'reload',
    };
};

const removeDefaultLivewireErrorDialog = () => {
    const dialog = document.getElementById('livewire-error');

    if (!dialog) {
        return;
    }

    try {
        if (typeof dialog.close === 'function' && dialog.open) {
            dialog.close();
        }
    } catch (error) {
        // Ignore close errors; the node is removed below.
    }

    dialog.remove();
    document.body.style.overflow = '';
};

const performRequestErrorAction = (action) => {
    if (action === 'login') {
        window.location.href = appRoutes.login || fallbackAppRoutes.login;
        return;
    }

    if (action === 'reload') {
        window.location.reload();
    }
};

const showRequestErrorAlert = (status, content = '') => {
    removeDefaultLivewireErrorDialog();

    if (requestErrorAlertOpen) {
        return;
    }

    const copy = livewireFailureCopy(status, content);
    const statusText = status ? `Kode ${status}` : 'Koneksi';
    const html = `
        <div class="siskala-request-error">
            <div class="siskala-request-error__code">${statusText}</div>
            <p class="siskala-request-error__message">${copy.message}</p>
            <div class="siskala-request-error__note">
                <i class="fe fe-info" aria-hidden="true"></i>
                <span>${copy.note}</span>
            </div>
        </div>
    `;

    if (!window.Swal) {
        const shouldReload = window.confirm(`${copy.title}\n\n${copy.message}`);
        if (shouldReload) {
            performRequestErrorAction(copy.action);
        }
        return;
    }

    requestErrorAlertOpen = true;

    window.Swal.fire({
        icon: copy.icon,
        title: copy.title,
        html,
        showCancelButton: copy.action !== 'dismiss',
        confirmButtonText: copy.confirmText,
        cancelButtonText: 'Tutup',
        reverseButtons: true,
        buttonsStyling: false,
        customClass: {
            popup: 'siskala-request-error-popup',
            title: 'siskala-request-error-title',
            htmlContainer: 'siskala-request-error-container',
            confirmButton: 'btn btn-primary rounded-pill px-4 shadow-sm font-weight-bold',
            cancelButton: 'btn btn-light border rounded-pill px-4 mr-2',
        },
    }).then((result) => {
        if (result.isConfirmed) {
            performRequestErrorAction(copy.action);
        }
    }).finally(() => {
        requestErrorAlertOpen = false;
    });
};

const registerLivewireRequestGuard = () => {
    if (!window.Livewire?.hook || window.Schedulo?.livewireRequestGuardRegistered) {
        return;
    }

    window.Schedulo = {
        ...(window.Schedulo || {}),
        livewireRequestGuardRegistered: true,
    };

    window.Livewire.hook('request', ({ fail }) => {
        fail(({ status, content, preventDefault }) => {
            preventDefault();
            showRequestErrorAlert(status, content);
        });
    });
};

const queueRefresh = (key, callback, options = {}) => {
    const {
        delay = 250,
        deferWhileBusy = true,
    } = options;

    if (deferWhileBusy && isBlockingInteraction()) {
        pendingRefreshes.set(key, { callback, options });
        return false;
    }

    const activeTimer = refreshTimers.get(key);
    if (activeTimer) {
        window.clearTimeout(activeTimer);
    }

    const timerId = window.setTimeout(() => {
        refreshTimers.delete(key);
        callback();
    }, delay);

    refreshTimers.set(key, timerId);

    return true;
};

const flushPendingRefreshes = () => {
    if (isBlockingInteraction()) {
        return;
    }

    const entries = Array.from(pendingRefreshes.entries());
    pendingRefreshes.clear();

    for (const [key, entry] of entries) {
        queueRefresh(key, entry.callback, {
            ...entry.options,
            deferWhileBusy: false,
        });
    }
};

const millisecondsUntilNextDay = () => {
    const now = new Date();
    const next = new Date(now);

    next.setHours(24, 0, 5, 0);

    return Math.max(next.getTime() - now.getTime(), 1000);
};

const scheduleDayBoundarySync = () => {
    if (dayBoundaryTimerId) {
        window.clearTimeout(dayBoundaryTimerId);
    }

    dayBoundaryTimerId = window.setTimeout(() => {
        dispatchRealtimeEvent({
            source: 'timer',
            reason: 'day-boundary',
            action: 'time-boundary',
            topics: ['activities', 'followups', 'notifications', 'dashboard'],
        });

        scheduleDayBoundarySync();
    }, millisecondsUntilNextDay());
};

window.scheduloRealtime = {
    dispatch: dispatchRealtimeEvent,
    queueRefresh,
    flushPendingRefreshes,
    hasTopic,
    matchesAnyTopic,
    isBlockingInteraction,
};

document.addEventListener('DOMContentLoaded', () => {
    registerLivewireRequestGuard();
    scheduleDayBoundarySync();

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            hiddenAt = Date.now();
            return;
        }

        flushPendingRefreshes();

        if (hiddenAt && Date.now() - hiddenAt > 60000) {
            dispatchRealtimeEvent({
                source: 'browser',
                reason: 'tab-visible',
                action: 'visibility-sync',
                topics: ['activities', 'followups', 'notifications', 'dashboard'],
            });
        }

        hiddenAt = null;
    });

    document.addEventListener('focusin', flushPendingRefreshes);
    document.addEventListener('hidden.bs.modal', flushPendingRefreshes);
    window.addEventListener('focus', flushPendingRefreshes);
});

document.addEventListener('livewire:init', registerLivewireRequestGuard);
document.addEventListener('livewire:initialized', registerLivewireRequestGuard);

const reverbKey = import.meta.env.VITE_REVERB_APP_KEY;
const currentUserId = window.Schedulo?.user?.id;

if (reverbKey) {
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: reverbKey,
        wsHost: import.meta.env.VITE_REVERB_HOST || window.location.hostname,
        wsPort: Number(import.meta.env.VITE_REVERB_PORT || 8080),
        wssPort: Number(import.meta.env.VITE_REVERB_PORT || 8080),
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME || window.location.protocol.replace(':', '')) === 'https',
        enabledTransports: ['ws', 'wss'],
        disableStats: true,
    });

    if (currentUserId) {
        window.Echo
            .private(`App.Models.User.${currentUserId}`)
            .listen('.schedulo.realtime.synced', (payload) => {
                dispatchRealtimeEvent({
                    source: 'reverb',
                    ...payload,
                });
            });
    }

    const connection = window.Echo.connector?.pusher?.connection;
    let hasConnectedOnce = false;

    if (connection) {
        connection.bind('connected', () => {
            if (hasConnectedOnce) {
                dispatchRealtimeEvent({
                    source: 'reverb',
                    reason: 'socket-reconnected',
                    action: 'reconnected',
                    topics: ['activities', 'followups', 'notifications', 'dashboard'],
                });
            }

            hasConnectedOnce = true;
        });
    }
}
