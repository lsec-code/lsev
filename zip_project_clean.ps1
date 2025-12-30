
$ProjectPath = "c:\laragon\www\v2_laravel"
$ZipFile = "c:\laragon\www\v2_laravel_clean_backup.zip"
$TempDir = Join-Path $env:TEMP "v2_backup_$(Get-Random)"

Write-Host "Starting robust clean backup..." -ForegroundColor Cyan

# Prepare temp directory
if (Test-Path $TempDir) { Remove-Item $TempDir -Recurse -Force }
New-Item -ItemType Directory -Path $TempDir | Out-Null

# List of directories to exclude (Robocopy uses relative names or full paths)
$ExcludeDirs = @(
    "vendor",
    "node_modules",
    "chunks",
    "backups",
    "videos",
    "avatars",
    ".git",
    ".gemini"
)

# List of files to exclude
$ExcludeFiles = @(
    ".env",
    "*.log",
    "v2_laravel_clean_backup.zip",
    "zip_project_clean.ps1",
    "check_sizes.ps1"
)

Write-Host "Copying project files (excluding large assets)..." -ForegroundColor Yellow

# Use Robocopy for high performance and reliable exclusions
# /E = copy subdirectories including empty ones
# /XD = exclude directories
# /XF = exclude files
# /R:0 /W:0 = don't retry on locked files
# /NFL /NDL = no file/dir logging (faster)
robocopy $ProjectPath $TempDir /E /XD $ExcludeDirs /XF $ExcludeFiles /R:0 /W:0 /NFL /NDL | Out-Null

Write-Host "Creating placeholder .gitignore files in emptied directories..." -ForegroundColor Yellow
$Placeholders = @(
    "storage\app\chunks",
    "storage\app\backups",
    "public\uploads\videos",
    "public\uploads\avatars"
)

foreach ($p in $Placeholders) {
    $target = Join-Path $TempDir $p
    if (-not (Test-Path $target)) { New-Item -ItemType Directory -Path $target | Out-Null }
    Set-Content -Path (Join-Path $target ".gitignore") -Value "*`n!.gitignore"
}

Write-Host "Zipping clean project..." -ForegroundColor Yellow
if (Test-Path $ZipFile) { Remove-Item $ZipFile }
Compress-Archive -Path "$TempDir\*" -DestinationPath $ZipFile -Force

# Cleanup
Remove-Item $TempDir -Recurse -Force

$FinalSize = [math]::Round((Get-Item $ZipFile).Length / 1MB, 2)
Write-Host "Done! Clean backup created at: $ZipFile ($FinalSize MB)" -ForegroundColor Green
