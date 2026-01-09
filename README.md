# Ahrefs Web Analytics — PrestaShop module

A tiny, **theme-update-safe** PrestaShop module that injects **Ahrefs Web Analytics** tracking script into the **`<head>`** of your store using the `displayHeader` hook.

✅ No theme edits (safe with theme updates)  
✅ Simple Back Office configuration  
✅ Works great with Ahrefs free **Webmaster Tools**  
✅ PrestaShop 1.7 / 8.x compatible (should also work on 9 if `displayHeader` is available)
✅ Back Office UI in **English & French**

---

## Download / Install

➡️ Download the **latest release** here:  
`https://github.com/iamadlx/prestashop-ahrefs-analytics/releases`

Then in PrestaShop Back Office:

1. **Modules → Module Manager**
2. **Upload a module**
3. Upload the `.zip` and install

---

## Configure

Back Office → **Modules → Ahrefs Web Analytics → Configure**

1. Create a free account: `https://ahrefs.com/webmaster-tools`
2. In Ahrefs: **Web Analytics → Installation guide**
3. Copy the value from `data-key="..."`
4. Paste it into the module settings, enable tracking, and save
5. In Ahrefs, click **Verify installation**

Injected snippet:

```html
<script src="https://analytics.ahrefs.com/analytics.js" data-key="YOUR_KEY" async></script>
```

---

## Privacy / GDPR note

Ahrefs states their Web Analytics is privacy-friendly by design.  
You are still responsible for complying with GDPR/ePrivacy regulations that apply to your business.

---

## License

MIT
