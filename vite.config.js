import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/global/theme.css',
                'resources/css/components/dashboard-shared.css',
                'resources/css/components/forms-extended.css',
                'resources/css/components/public-shared.css',
                'resources/css/components/navbar.css',
                'resources/css/components/footer.css',
                'resources/css/components/pitch-buttons.css',
                'resources/css/dashboard/dashboard-partials.css',

                'resources/css/auth/login.css',
                'resources/css/admin/admin-dashboard.css',
                'resources/css/admin/activity-logs.css',
                'resources/css/admin/verification.css',
                'resources/css/users/users-list.css',
                'resources/css/conversations/conversations.css',
                'resources/css/conversations/chat.css',
                'resources/css/investments/investments.css',
                'resources/css/documents/documents.css',
                'resources/css/pitch_decks/pitch-decks.css',
                'resources/css/meetings/meetings.css',
                'resources/css/startups/discovery.css',
                'resources/css/startups/startup-cards.css',
                'resources/css/startups/show.css',
                'resources/css/public/landing.css',

                'resources/css/app.css',
                'resources/css/public.css',

                'resources/js/dashboard.js',
                'resources/js/invesmal-ui.js',
            ],
            refresh: true,
        }),
    ],

    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['alpinejs'],
                },
            },
        },
        chunkSizeWarningLimit: 1000,
    },

    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
        hmr: {
            overlay: false,
        },
    },
});