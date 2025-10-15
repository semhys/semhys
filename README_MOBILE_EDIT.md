Guía rápida para editar el proyecto desde tablet o móvil

Opciones principales (elige una):

1) GitHub + editor web (recomendado)
  - Crea un repositorio en GitHub y sube el contenido de `public_html`.
  - Desde la tablet o móvil abre el sitio del repo en el navegador y presiona `.` (punto) o cambia la URL a `github.dev` para usar el editor web; funciona en navegadores modernos.
  - También puedes usar la app oficial de GitHub para revisar cambios, issues y pull requests.

2) Git + Working Copy (iOS) / MGit (Android)
  - iOS: Working Copy permite clonar repos, editar y hacer push. Úsalo para editar archivos directamente y hacer commits.
  - Android: MGit o Termux + git permiten clonar y editar con un editor como JuiceSSH o editores con soporte SFTP.

3) Edición por SFTP directo
  - Apps como "Solid Explorer" o "FileBrowser" en Android/iOS permiten conectarse por SFTP y editar archivos en remoto.
  - Recomendado sólo para cambios pequeños; cuidado con permisos y backups.

4) IDE remoto (opcional, avanzado)
  - Ejecuta un servidor `code-server` en una VPS o en tu PC y accede desde el navegador del móvil.
  - Proporciona experiencia VS Code completa desde tablet o móvil.

Despliegue automatizado (GitHub Actions)
  - Si subes el repo a GitHub puedes usar el workflow `.github/workflows/deploy.yml` incluido para desplegar por SFTP a Hostinger.
  - Configura los secrets del repo: `HOSTINGER_HOST`, `HOSTINGER_PORT`, `HOSTINGER_USER`, `HOSTINGER_PASSWORD`, `REMOTE_PATH`.

Recomendaciones de flujo
- Haz commits pequeños y frecuentes.
- No pongas `includes/config.php` con credenciales en el repo público. Usa variables de entorno en el servidor o mueve el archivo fuera del webroot.
- Mantén una copia de seguridad antes de sobrescribir archivos en el hosting.

Soporte
- Si quieres, puedo crear el repo en GitHub desde los archivos actuales y configurar el workflow. Dime si quieres que lo haga y qué método de despliegue prefieres (SFTP o FTP).
