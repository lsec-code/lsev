
function Get-DirSize($path) {
    if (-not (Test-Path $path)) { return }
    Get-ChildItem -Path $path -Force | ForEach-Object {
        $item = $_
        $size = 0
        if ($item.PSIsContainer) {
            $size = (Get-ChildItem $item.FullName -Recurse -Force -ErrorAction SilentlyContinue | Measure-Object -Property Length -Sum).Sum
        }
        else {
            $size = $item.Length
        }
        $sizeMB = [math]::Round($size / 1MB, 2)
        Write-Host "$($item.FullName): $sizeMB MB"
    }
}

Write-Host "--- PUBLIC UPLOADS ---"
Get-DirSize "c:\laragon\www\v2_laravel\public\uploads"
