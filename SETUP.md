# Setup & Installation

## Quick Setup

Run one of these commands in the project root to install all dependencies:

**Windows (PowerShell):**
```powershell
.\setup.ps1
```

**Windows (Command Prompt):**
```cmd
setup.bat
```

## VS Code Tasks

You can also use VS Code tasks to install dependencies:

1. Open the Command Palette (`Ctrl+Shift+P`)
2. Type "Run Task"
3. Select one of:
   - **Install All Dependencies** - Installs both backend and frontend (recommended)
   - **Install Backend Dependencies** - Composer install only
   - **Install Frontend Dependencies** - npm install only

## Manual Installation

If you prefer to install manually:

**Backend:**
```bash
cd backend
composer install
```

**Frontend:**
```bash
cd frontend
npm install
```

## First-Time Setup

1. Run the setup script: `.\setup.ps1` or `setup.bat`
2. Copy `.env.example` to `.env` in the backend folder (if it exists)
3. Generate app key: `cd backend && php artisan key:generate`
4. Start the backend: `php artisan serve`
5. Start the frontend: `cd frontend && npm run dev`

## Notes

- Lock files (`composer.lock` and `package-lock.json`) are committed to ensure consistent installations
- The `vendor/` and `node_modules/` directories are in `.gitignore` and won't be tracked by git
- Once dependencies are installed, you only need to re-run setup if you update `composer.json` or `package.json`
