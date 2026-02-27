# project-bridge

## Bridge Bootstrap Minimal WordPress Theme

This repository includes a minimal WordPress theme at `bridge-bootstrap-theme/` optimized for Core Web Vitals.

### Performance-focused architecture

- **Critical CSS inline** in `wp_head` for above-the-fold layout and typography.
- **Non-critical CSS split + minified** in `assets/css/non-critical.min.css` and loaded async via `rel=preload` swap.
- **No heavy front-end framework dependency** (Bootstrap removed) to reduce transfer size and render-blocking.
- **Tiny deferred navigation script** only (`assets/js/navigation.min.js`) with no jQuery dependency.
- **Image performance defaults** (`loading=lazy`, `decoding=async`) plus first-post thumbnail preload for LCP on home/front.
- **WordPress head cleanup** removes emoji scripts/styles and generator output.
- **Centralized typography controls** still managed in `theme.json`.

### Install

1. Copy `bridge-bootstrap-theme` into your WordPress site's `wp-content/themes/` directory.
2. Activate **Bridge Bootstrap Minimal** from the WordPress admin theme screen.
3. (Optional) Assign a menu to the `Primary Menu` location.
4. Manage global typography in **Site Editor → Styles** (powered by `theme.json`).
