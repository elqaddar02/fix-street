# prepare_deploy.ps1
$deployPath = "$HOME\Desktop\InfinityFree_Deploy"
$projectPath = Get-Location

# 1. Clean up old folder
if (Test-Path $deployPath) { Remove-Item -Recurse -Force $deployPath }
New-Item -ItemType Directory -Path "$deployPath\laravel"
New-Item -ItemType Directory -Path "$deployPath\htdocs"

Write-Host "🚀 Preparing deployment files in $deployPath..." -ForegroundColor Cyan

# 2. Copy Core Files (Everything except public, node_modules, .git)
Get-ChildItem -Path $projectPath -Exclude "public", "node_modules", ".git", "deploy_me", "InfinityFree_Deploy" | ForEach-Object {
    Copy-Item -Path $_.FullName -Destination "$deployPath\laravel" -Recurse -Container
}

# 3. Copy Public Files to htdocs
Copy-Item -Path "$projectPath\public\*" -Destination "$deployPath\htdocs" -Recurse

# 4. Swap index.php with the pre-configured deployment version
if (Test-Path "$projectPath\public\index.php.deploy") {
    Copy-Item -Path "$projectPath\public\index.php.deploy" -Destination "$deployPath\htdocs\index.php" -Force
    Write-Host "✅ Deployment index.php applied." -ForegroundColor Green
}

# 5. Clean up htdocs
Remove-Item -Path "$deployPath\htdocs\index.php.deploy" -ErrorAction SilentlyContinue

Write-Host "✨ Done! Your files are ready on your Desktop in 'InfinityFree_Deploy'." -ForegroundColor Green
Write-Host "Next: Upload the CONTENTS of this folder to your InfinityFree FTP root." -ForegroundColor Yellow
