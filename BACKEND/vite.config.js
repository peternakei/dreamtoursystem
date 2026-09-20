import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // "resources/sass/app.scss",
                "resources/css/login.css",
                "public/assets/css/app-saas.min.css",
                "public/assets/vendor/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.css",
            ],
            refresh: true,
        }),
    ],
});
