@echo off
REM Setup script for L.R.R.S project
REM This script installs all dependencies for both backend and frontend

echo Setting up L.R.R.S project...

REM Install backend dependencies
echo.
echo Installing backend dependencies...
cd backend

if not exist "vendor" (
    call composer install
    if errorlevel 1 (
        echo Error: Failed to install backend dependencies
        exit /b 1
    )
) else (
    echo Backend dependencies already installed
)

REM Install frontend dependencies
echo.
echo Installing frontend dependencies...
cd ..\frontend

if not exist "node_modules" (
    call npm install
    if errorlevel 1 (
        echo Error: Failed to install frontend dependencies
        exit /b 1
    )
) else (
    echo Frontend dependencies already installed
)

echo.
echo Setup completed successfully!
cd ..
