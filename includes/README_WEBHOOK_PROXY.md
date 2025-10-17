Webhook proxy README

Purpose
-------
This small proxy accepts POSTs from your frontend and forwards them to the configured n8n webhook
while injecting a server-side token in the header (`X-Webhook-Token`). That keeps the secret out of
client-side code.

Installation
------------
1. Place `webhook_proxy.php` under `includes/` (already present).
2. Set the environment variable `WEBHOOK_SECRET` on your server. Example (Linux):
   export WEBHOOK_SECRET="mi_token_seguro"
   On shared hosting you may need to set this in the control panel or in an .env loader.
3. Ensure your front-end form posts to `/includes/webhook_proxy.php`.
4. In n8n, in your Webhook node add a `Function` node immediately after it with validation that
   checks `headers['x-webhook-token']` equals your secret.

Testing
-------
From PowerShell (client -> proxy):
```powershell
$payload = @{ nombre='Proxy test'; correo='proxy@ejemplo.com'; ciudad='X'; mensaje='Hola desde proxy' }
Invoke-RestMethod -Uri 'https://tu-dominio.tld/includes/webhook_proxy.php' -Method Post -ContentType 'application/json' -Body ($payload | ConvertTo-Json)
```

From server (bypass proxy, direct to n8n) if you want to validate the proxy header is being added:
```bash
curl.exe -i -X POST 'https://semhys.app.n8n.cloud/webhook/semhys-contact' \
  -H 'Content-Type: application/json' \
  -H 'X-Webhook-Token: MI_TOKEN_LOCAL_DE_PRUEBA' \
  -d '{"nombre":"Direct test","correo":"d@e.com","ciudad":"X","mensaje":"ok"}'
```

Notes
-----
- This proxy is intentionally simple. For production, add input validation, logging, rate-limiting and
  error handling tuned to your environment.
 - A simple audit log is written to `includes/webhook_proxy.log` (one JSON line per request). Ensure
   log permissions are restricted and rotate/archive the file regularly to avoid unbounded growth.
  Example (Linux):
  - chown www-data:www-data includes/webhook_proxy.log
  - chmod 640 includes/webhook_proxy.log
  - Use logrotate or a cron job to rotate the file daily/weekly.

Rotation examples
-----------------
Two example rotation helpers are provided in this repository under `includes/`:

- `logrotate_webhook_proxy.conf` — sample `logrotate` config for Linux servers. Replace the path
  placeholder in the file with the absolute path to `includes/webhook_proxy.log` on your server and
  drop it into `/etc/logrotate.d/` or include it from your logrotate configuration.

- `rotate_webhook_proxy.ps1` — a simple PowerShell rotation script for Windows/IIS hosts. It moves
  the current `webhook_proxy.log` into `includes/log_archive/` with a timestamp and recreates an empty
  `webhook_proxy.log`. Schedule this script with Task Scheduler to run daily/weekly as needed.

How to use the Linux `logrotate` example
----------------------------------------
1. Edit `includes/logrotate_webhook_proxy.conf` and replace `/path/to/public_html` with your site's
  absolute path (for example `/var/www/semhys/public_html`).
2. Copy the file to `/etc/logrotate.d/webhook_proxy` (requires root):

  sudo cp includes/logrotate_webhook_proxy.conf /etc/logrotate.d/webhook_proxy

3. Test the configuration:

  sudo logrotate -d /etc/logrotate.d/webhook_proxy

4. If the dry-run looks good, run it once to rotate now:

  sudo logrotate -f /etc/logrotate.d/webhook_proxy

How to use the Windows PowerShell example
----------------------------------------
1. Place `rotate_webhook_proxy.ps1` in the same folder as `webhook_proxy.log` (already under `includes/`).
2. Create a scheduled task in Task Scheduler that runs the script daily at the time you prefer.
3. Ensure the scheduled task runs under an account that can move and create files in the `includes/` folder
  (for example the IIS AppPool identity or a dedicated service account).

Security note
-------------
Keep the log files private: restrict permissions to the web server account and rotate/compress them
regularly. Avoid logging secrets. If you need richer logging, consider shipping logs to a central
logging system (ELK/CloudWatch/Stackdriver) with secure transport.
- Rotate the secret regularly and store it in a secure place.
