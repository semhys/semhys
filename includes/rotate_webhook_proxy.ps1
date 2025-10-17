# rotate_webhook_proxy.ps1
# Simple rotation for webhook_proxy.log on Windows. Moves the current log to an archive folder
# with a timestamp and creates an empty webhook_proxy.log so the proxy can continue writing.

$dir = Split-Path -Parent $MyInvocation.MyCommand.Definition
$log = Join-Path $dir 'webhook_proxy.log'
$archiveDir = Join-Path $dir 'log_archive'

# Ensure archive folder exists
if (-not (Test-Path $archiveDir)) { New-Item -ItemType Directory -Path $archiveDir | Out-Null }

$timestamp = Get-Date -Format 'yyyyMMddHHmmss'
$archived = Join-Path $archiveDir "webhook_proxy_$timestamp.log"

if (Test-Path $log) {
    # Move current log to archive
    Move-Item -Path $log -Destination $archived -Force
}

# Recreate empty log file with same name so proxy can continue to write
New-Item -Path $log -ItemType File -Force | Out-Null

# Optionally compress the archived file if you have gzip or 7zip available.
# Example using gzip from Git for Windows (if installed):
# & "C:\Program Files\Git\usr\bin\gzip.exe" -9 "$archived"

# (Optional) Set restrictive ACLs. Adjust 'IIS_IUSRS' or the account that runs PHP/IIS.
# icacls $log /inheritance:r /grant 'IIS_IUSRS:(R,W)'

Write-Output "Rotated log to: $archived"