# Academy LMS - GitHub Ready Setup Script (PowerShell)
# Run this script to set up the project for development and collaboration

Write-Host "🚀 Academy LMS - GitHub Ready Setup Script" -ForegroundColor Green
Write-Host "==========================================" -ForegroundColor Green
Write-Host "This script will help you set up the project for collaborative development" -ForegroundColor White
Write-Host ""

# Step 1: Create proper .env.example
Write-Host "📝 Creating .env.example..." -ForegroundColor Yellow

$envExample = @"
APP_NAME="Academy LMS"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=academy_lms
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME=`${APP_NAME}

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY=`${PUSHER_APP_KEY}
VITE_PUSHER_HOST=`${PUSHER_HOST}
VITE_PUSHER_PORT=`${PUSHER_PORT}
VITE_PUSHER_SCHEME=`${PUSHER_SCHEME}
VITE_PUSHER_APP_CLUSTER=`${PUSHER_APP_CLUSTER}

# Payment Gateway Keys (Use test keys)
RAZORPAY_KEY=your_test_key_here
RAZORPAY_SECRET=your_test_secret_here
"@

Set-Content -Path ".env.example" -Value $envExample

# Step 2: Update .gitignore
Write-Host "📝 Updating .gitignore..." -ForegroundColor Yellow

$gitignoreAdditions = @"

# Academy LMS Specific
/public/assets/install.sql
/upload/
.env.backup
.env.production
/storage/app/public/uploads
/public/uploads
fix_migrations.php
setup_project.ps1
setup_project.sh

# IDE
.vscode/
.idea/
*.swp
*.swo

# OS
.DS_Store
Thumbs.db
"@

Add-Content -Path ".gitignore" -Value $gitignoreAdditions

Write-Host "✅ .env.example created" -ForegroundColor Green
Write-Host "✅ .gitignore updated" -ForegroundColor Green

# Step 3: Setup environment
Write-Host ""
Write-Host "🔧 Setting up development environment..." -ForegroundColor Yellow

if (-Not (Test-Path ".env")) {
    Copy-Item ".env.example" ".env"
    Write-Host "✅ .env file created from example" -ForegroundColor Green
} else {
    Write-Host "⚠️  .env already exists, skipping..." -ForegroundColor Yellow
}

# Step 4: Install dependencies
Write-Host ""
Write-Host "📦 Installing dependencies..." -ForegroundColor Yellow
Write-Host "Running: composer install"
& composer install --no-interaction

Write-Host "Running: npm install"
& npm install

# Step 5: Generate key
Write-Host ""
Write-Host "🔑 Generating application key..." -ForegroundColor Yellow
& php artisan key:generate

# Step 6: Database setup
Write-Host ""
Write-Host "🗄️  Database Setup" -ForegroundColor Cyan
Write-Host "==================" -ForegroundColor Cyan
Write-Host "Please ensure you have created a MySQL database named 'academy_lms'"
Write-Host "Update your .env file with the correct database credentials"
Write-Host ""
Read-Host "Press Enter after you've set up the database and updated .env"

# Step 7: Run migrations
Write-Host ""
Write-Host "🏃 Running migrations..." -ForegroundColor Yellow
& php artisan migrate

# Step 8: Seed database
Write-Host ""
Write-Host "🌱 Seeding database..." -ForegroundColor Yellow
if (Test-Path "database/seeders/DatabaseSeeder.php") {
    & php artisan db:seed
    Write-Host "✅ Database seeded" -ForegroundColor Green
} else {
    Write-Host "ℹ️  No seeders found, skipping..." -ForegroundColor Blue
}

# Step 9: Create storage link
Write-Host ""
Write-Host "🔗 Creating storage link..." -ForegroundColor Yellow
& php artisan storage:link

# Step 10: Clear caches
Write-Host ""
Write-Host "🧹 Clearing caches..." -ForegroundColor Yellow
& php artisan config:clear
& php artisan cache:clear
& php artisan route:clear
& php artisan view:clear

Write-Host ""
Write-Host "🎉 Setup Complete!" -ForegroundColor Green
Write-Host "==================" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:"
Write-Host "1. Update your .env file with your database credentials"
Write-Host "2. Run: php artisan serve"
Write-Host "3. Visit: http://localhost:8000"
Write-Host ""
Write-Host "For your friend to set up:"
Write-Host "1. Clone the repository"
Write-Host "2. Run: .\setup_project.ps1"
Write-Host "3. Follow the prompts"
Write-Host ""
Write-Host "Happy coding! 🚀" -ForegroundColor Green
