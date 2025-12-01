# Bend Cut Send – Static Test Site for GitHub Pages

This repository contains a static demonstration version of the Bend Cut Send website. It is intended for previewing design, navigation, and content on GitHub Pages. Dynamic backend functionality from the production PHP version (database storage, email sending, admin dashboard, PayFast integration, secure file upload handling) is not available in GitHub Pages because GitHub Pages serves only static content.

## Live Preview
Once deployed, the site will be available at:
```
https://<owner>.github.io/<repo>/
```

## Contents
- `index.html`: Home page with hero section and steps.
- `how-it-works.html`: Process explanation.
- `metals.html`: Metals & thicknesses table.
- `qa.html`: Q&A content in table form.
- `request-quote.html`: Static demo of quote form (no processing).
- `ask-question.html`: Static demo of question form.
- `payment.html`: Static payment placeholder page.
- `css/styles.css`: Global styling.
- `images/hero_bend_cut_send.jpg`: Provide your hero image.
- (Optional) `images/favicon.png`: Provide a favicon.

## Forms (Static Mode)
The forms currently prevent submission and display an alert. To make them send somewhere for testing:
1. Create a Formspree form: https://formspree.io/
2. Replace the `<form>` tag:
   ```html
   <form action="https://formspree.io/f/yourid" method="POST" class="form-card">
   ```
3. Add `name` attributes to each input (e.g., `name="first_name"`).
4. Remove the `onsubmit="alert(...); return false;"`.

## Payment Page
`payment.html` is a placeholder. Real payment processing:
- Requires server-side code (e.g., PayFast signature generation, ITN validation).
- Should not be done purely client-side for security reasons.

## Differences from Production PHP Version
| Feature | Production (PHP) | GitHub Pages Static |
|---------|------------------|---------------------|
| File Upload Security | Server-side validation & storage | Browser only (no processing) |
| Email Notifications | PHP `mail()` / SMTP | Not possible directly |
| Admin Dashboard | PHP + DB | Not included |
| Payment Integration | Secure form + ITN handler | Placeholder button |
| CSRF/Session | Yes | Not applicable |

## Deploy Instructions
1. Create a new repository (e.g., `BendCutSend-static`).
2. Add all files to the root (including `css/` and `images/` folders).
3. Commit and push.
4. In GitHub: Settings → Pages → Select branch `main` and root.
5. Wait for build → Access published URL.

## Customization
- Replace site name everywhere ("Bend Cut Send").
- Provide hero image in `images/hero_bend_cut_send.jpg`.
- Add a favicon at `images/favicon.png`.
- Adjust color palette in `css/styles.css`.

## Next Steps Toward Production
- Migrate forms to backend (PHP on Plesk).
- Implement database storage and admin tooling.
- Add PayFast or preferred SA payment gateway (server-side).
- Harden security (validation, scanning, HTTPS, auth).

## License
Internal use / proprietary unless you specify otherwise.
