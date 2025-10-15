# Copilot Instructions for AI Agents

## Project Overview
This codebase is a PHP web application with static HTML pages, custom PHP API endpoints, and an integrated copy of PHPMailer for email functionality. The structure is flat, with most HTML and PHP files at the root, and assets in subfolders.

## Key Components
- **Static Pages:** `index.html`, `about.html`, `contact.html`, etc. are public-facing pages.
- **API Endpoints:** `api_get.php`, `api_insert.php` provide backend data access (likely for AJAX or client-side JS).
- **PHPMailer:** Located in `phpmailer/`, used for sending emails. Do not modify unless updating the library. See `phpmailer/README.md` for usage.
- **User Management:** `usuarios/` contains PHP scripts for user-related operations (e.g., `consultar_docs.php`).
- **Assets:** CSS and JS in `assets/`.

## Patterns & Conventions
- **No Framework:** This is a custom PHP project, not using Laravel, Symfony, etc.
- **Direct PHP Includes:** PHP files are loaded directly; no autoloading except within PHPMailer.
- **Separation:** Business logic is in PHP files, presentation in HTML/CSS/JS.
- **PHPMailer Usage:** When sending email, instantiate from `phpmailer/src/PHPMailer.php` and related classes. See `phpmailer/README.md` for correct usage.
- **No Build Step:** Static files are served as-is. No minification or transpilation.

## Developer Workflows
- **Testing:** No automated tests detected. Manual testing via browser and API calls is expected.
- **Debugging:** Use `diag.php` or add `var_dump`/`echo` in PHP for debugging.
- **Adding Pages:** Copy an existing HTML file and update content. For new PHP endpoints, follow the pattern in `api_get.php` or `api_insert.php`.
- **Email:** Use PHPMailer for all outgoing email. Do not use PHP's `mail()` directly.

## Integration Points
- **PHPMailer:** All email-related code should use the PHPMailer classes in `phpmailer/src/`.
- **APIs:** Frontend JS (in `assets/js/`) communicates with backend via the PHP API endpoints.

## Examples
- To add a new API endpoint, create a PHP file at the root, following the structure of `api_get.php`.
- To send email, require PHPMailer classes from `phpmailer/src/` and follow the example in `phpmailer/README.md`.

## Do Not
- Do not modify files in `phpmailer/` unless updating the library.
- Do not introduce frameworks or build tools unless discussed.

---

_If you are unsure about a workflow or pattern, check for similar files or ask for clarification._
