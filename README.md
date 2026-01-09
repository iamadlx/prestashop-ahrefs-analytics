# Ahrefs Web Analytics for PrestaShop

A minimal, update-proof Ahrefs Web Analytics integration for PrestaShop.
This module injects the official Ahrefs tracking script inside the `<head>` via `displayHeader`.

✅ No theme edits (safe for theme updates)  
✅ Back-office configuration (enable/disable + data-key)  
✅ Works with PrestaShop 1.7 / 8.x (and should be compatible with newer versions that keep `displayHeader`)  

## Download

Get the latest version from the **Releases** page:  
https://github.com/iamadlx/prestashop-ahrefs-analytics/releases

## Installation (30 seconds)

1. Download the latest release zip.
2. In PrestaShop Back Office: **Modules → Module Manager → Upload a module**
3. Upload the zip and install it.

## Setup

1. Create a free Ahrefs Webmaster Tools account: https://ahrefs.com/webmaster-tools
2. In Ahrefs, open **Web Analytics** and copy your `data-key`.
3. In PrestaShop: **Modules → Ahrefs Web Analytics → Configure**
4. Paste the key, enable tracking, and save.
5. Back in Ahrefs, click **Verify installation**.

## Privacy / GDPR

Ahrefs states that Web Analytics is privacy-friendly and doesn’t require cookie banners by default.
You are still responsible for complying with GDPR and local privacy regulations.

## License

MIT
