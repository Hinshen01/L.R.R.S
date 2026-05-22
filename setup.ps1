# Setup script for L.R.R.S project
# This script installs all dependencies for both backend and frontend

Write-Host "Setting up L.R.R.S project..." -ForegroundColor Cyan

# Install backend dependencies
Write-Host "`nInstalling backend dependencies..." -ForegroundColor Yellow
$backendPath = Join-Path $PSScriptRoot "backend"
Set-Location $backendPath

if (!(Test-Path "vendor")) {
    composer install
    if ($LASTEXITCODE -ne 0) {
        Write-Host "Error: Failed to install backend dependencies" -ForegroundColor Red
        exit 1
    }
}
else {
    Write-Host "Backend dependencies already installed" -ForegroundColor Green
}

# Install frontend dependencies
Write-Host "`nInstalling frontend dependencies..." -ForegroundColor Yellow
$frontendPath = Join-Path $PSScriptRoot "frontend"
Set-Location $frontendPath

if (!(Test-Path "node_modules")) {
    npm install
    if ($LASTEXITCODE -ne 0) {
        Write-Host "Error: Failed to install frontend dependencies" -ForegroundColor Red
        exit 1
    }
}
else {
    Write-Host "Frontend dependencies already installed" -ForegroundColor Green
}

Write-Host "`nSetup completed successfully!" -ForegroundColor Green
Set-Location $PSScriptRoot
