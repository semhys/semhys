How to place `.diag_token` outside `public_html` on Hostinger

Option A — Using Hostinger File Manager (web):
1. Log into Hostinger hPanel.
2. Open File Manager.
3. Navigate to the parent folder of `public_html` (usually `/home/your_user/`).
4. Click "New File" and name it `.diag_token` (include the leading dot).
5. Edit the file and paste the token string (no trailing newline/spaces). Save.
6. Set file permissions to 600 (if the File Manager supports it).

Option B — Using WinSCP (GUI SFTP):
1. Open WinSCP and create a session to your Hostinger server (SFTP, port 22).
2. Navigate to the folder above `public_html` (one level up).
3. Right-click → New → File → name `.diag_token`.
4. Edit and paste the token, save and close. Right-click → Properties → set permission 600.

Option C — Using the PowerShell helper (create_remote_diag_token.ps1):
1. Ensure OpenSSH `scp` is available on your system (Windows 10/11 has it in recent builds) or install PuTTY/PSCP.
2. Run the script from `public_html`:
   `powershell -ExecutionPolicy Bypass -File .\create_remote_diag_token.ps1`
3. Provide host, user, port and remote target path when prompted (e.g. `/home/your_user/.diag_token`).

Security note: Keep `.diag_token` outside `public_html` and do not commit it to git. We added `.diag_token` to `.gitignore` earlier.
