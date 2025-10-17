# Despliegue automático del frontend

Este repositorio incluye un workflow de GitHub Actions (`.github/workflows/deploy_frontend.yml`) que construye la aplicación React en `frontend/` y despliega el contenido de `frontend/dist` al servidor.

Opciones de despliegue:

- SFTP (recomendado): usa una clave privada SSH para copiar los archivos al servidor.
- FTP (fallback): este método usa usuario/contraseña FTP.

Configurar secretos en GitHub (Settings → Secrets → Actions):

- Para SFTP (recomendado):
  - `SFTP_HOST` — host o IP del servidor SFTP
  - `SFTP_USER` — usuario SSH
  - `SFTP_PRIVATE_KEY` — clave privada (PEM) sin passphrase o con passphrase si usas `SFTP_PASSPHRASE`
  - `SFTP_PORT` — puerto (opcional, default 22)
  - `SFTP_TARGET_DIR` — directorio remoto donde copiar (ej. `/public_html`)

- Para FTP (si no hay SFTP):
  - `FTP_HOST` — host FTP
  - `FTP_USER` — usuario FTP
  - `FTP_PASSWORD` — contraseña FTP
  - `FTP_TARGET_DIR` — carpeta remota objetivo (ej. `/public_html`)

Recomendaciones:
- Prueba el workflow en una rama separada primero.
- Asegúrate de tener permisos en el servidor para sobrescribir archivos en la carpeta objetivo.
- Mantén backups periódicos (el workflow puede ampliarse para crear backups remotos).

Alternativas:
- Usar rsync sobre SSH para despliegues incrementales (más rápido para grandes sitios).
- Subir los assets a un bucket estático y servirlos desde ahí (Cloudflare Pages, Netlify, S3 + CloudFront).

Si quieres, genero una versión que use `rsync` o `lftp` en lugar de las acciones usadas actualmente.
