<#
create_remote_diag_token.ps1
Helper script to upload the local .diag_token file to the remote server's parent folder using scp.
It will prompt for host, user, port and remote path, then invoke scp (OpenSSH must be available on your Windows).

Usage: Open PowerShell, cd to this repo folder and run:
  .\create_remote_diag_token.ps1

This script does NOT accept passwords inline; scp will prompt for the password securely.
#>

Write-Host "Upload .diag_token to remote server (uses scp)." -ForegroundColor Cyan

$local = Join-Path -Path (Get-Location) -ChildPath '.diag_token'
if (-not (Test-Path $local)) {
    Write-Host "Local .diag_token not found at $local" -ForegroundColor Red
    Write-Host "Create the file locally first with your token (no trailing spaces)." -ForegroundColor Yellow
    exit 1
}

$host = Read-Host 'Host (e.g. sftp.example.com)'
$user = Read-Host 'User'
$port = Read-Host 'Port (press Enter for default 22)'
if (-not $port) { $port = 22 }
$remotePath = Read-Host 'Remote target path (full path e.g. /home/usuario/.diag_token)'

Write-Host "Uploading $local -> $user@$host:$remotePath (port $port)" -ForegroundColor Green

$scpCmd = "scp -P $port `"$local`" $user@$host:`"$remotePath`""
Write-Host "Executing scp (you will be asked for the password)" -ForegroundColor Cyan
Write-Host $scpCmd

# Run scp and return exit code
$proc = Start-Process -FilePath scp -ArgumentList "-P", $port, $local, "$user@$host:$remotePath" -NoNewWindow -Wait -PassThru
if ($proc.ExitCode -eq 0) {
    Write-Host "Upload successful." -ForegroundColor Green
} else {
    Write-Host "scp exited with code $($proc.ExitCode). If scp isn't available, try WinSCP or PSCP." -ForegroundColor Red
}
