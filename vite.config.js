import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';
import { VitePWA } from 'vite-plugin-pwa';

function normalizeBuildBase(baseValue) {
    const trimmed = (baseValue ?? '').trim();

    if (trimmed === '') {
        return '/build/';
    }

    const normalized = `/${trimmed.replace(/^\/+|\/+$/g, '')}/`;

    return normalized === '//' ? '/build/' : normalized;
}

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    const buildBase = normalizeBuildBase(env.VITE_BUILD_BASE);

    return {
        base: buildBase,
        cacheDir: 'storage/framework/vite',
        build: {
            manifest: 'manifest.json',
            emptyOutDir: false,
        },
        plugins: [
            laravel({
                input: [
                    'resources/css/app.css',
                    'resources/js/app.js',
                    'resources/js/app.jsx',
                    'resources/css/frontend.css',
                    'resources/js/frontend.js',
                    'resources/css/admin.css',
                    'resources/js/admin.js',
                    'resources/css/auth.css',
                    'resources/js/auth.js',
                ],
                refresh: true,
            }),
            react(),
            tailwindcss(),
            VitePWA({
                registerType: 'autoUpdate',
                workbox: {
                    skipWaiting: true,
                    clientsClaim: true,
                    cleanupOutdatedCaches: true,
                    runtimeCaching: [
                        {
                            urlPattern: /^https:\/\/fonts\.(?:googleapis|gstatic)\.com\/.*/i,
                            handler: 'CacheFirst',
                            options: {
                                cacheName: 'google-fonts',
                                expiration: {
                                    maxEntries: 10,
                                    maxAgeSeconds: 60 * 60 * 24 * 365
                                },
                                cacheableResponse: {
                                    statuses: [0, 200]
                                }
                            }
                        },
                        {
                            urlPattern: /\.(?:png|gif|jpg|jpeg|svg)$/,
                            handler: 'StaleWhileRevalidate',
                            options: {
                                cacheName: 'images',
                                expiration: {
                                    maxEntries: 50,
                                    maxAgeSeconds: 30 * 24 * 60 * 60
                                }
                            }
                        },
                        {
                            urlPattern: /\/api\/cart\/.*$/i,
                            handler: 'NetworkOnly',
                            options: {
                                backgroundSync: {
                                    name: 'cart-queue',
                                    options: {
                                        maxRetentionTime: 24 * 60
                                    }
                                }
                            }
                        }
                    ]
                }
            })
        ],
        server: {
            watch: {
                ignored: ['**/storage/framework/views/**'],
            },
        },
    };
});
