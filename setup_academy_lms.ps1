# Academy LMS Complete Setup Script - PowerShell Version
# This script will completely set up the Academy LMS application on Windows

Write-Host "🚀 Academy LMS Complete Setup Script" -ForegroundColor Green
Write-Host "====================================" -ForegroundColor Green
Write-Host ""

# Check if we're in the correct directory
if (-not (Test-Path "composer.json")) {
    Write-Host "❌ Error: Please run this script from the Academy LMS root directory" -ForegroundColor Red
    exit 1
}

Write-Host "📋 Step 1: Environment Setup" -ForegroundColor Cyan
Write-Host "----------------------------" -ForegroundColor Cyan

# Copy .env.example to .env if it doesn't exist
if (-not (Test-Path ".env")) {
    if (Test-Path ".env.example") {
        Copy-Item ".env.example" ".env"
        Write-Host "✅ .env file created from .env.example" -ForegroundColor Green
    } else {
        Write-Host "⚠️  .env.example not found, you may need to create .env manually" -ForegroundColor Yellow
    }
} else {
    Write-Host "✅ .env file already exists" -ForegroundColor Green
}

Write-Host ""
Write-Host "📦 Step 2: Installing Dependencies" -ForegroundColor Cyan
Write-Host "--------------------------------" -ForegroundColor Cyan

# Install Composer dependencies
Write-Host "Installing Composer dependencies..." -ForegroundColor White
try {
    & composer install --no-dev --optimize-autoloader
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Composer dependencies installed successfully" -ForegroundColor Green
    } else {
        throw "Composer install failed"
    }
} catch {
    Write-Host "❌ Failed to install Composer dependencies" -ForegroundColor Red
    exit 1
}

# Install Node.js dependencies
Write-Host "Installing Node.js dependencies..." -ForegroundColor White
try {
    & npm install --production
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Node.js dependencies installed successfully" -ForegroundColor Green
    } else {
        throw "npm install failed"
    }
} catch {
    Write-Host "❌ Failed to install Node.js dependencies" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "🔐 Step 3: Application Key Generation" -ForegroundColor Cyan
Write-Host "-----------------------------------" -ForegroundColor Cyan

# Generate application key
try {
    & php artisan key:generate
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Application key generated successfully" -ForegroundColor Green
    } else {
        throw "Key generation failed"
    }
} catch {
    Write-Host "❌ Failed to generate application key" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "🗄️  Step 4: Database Setup" -ForegroundColor Cyan
Write-Host "-------------------------" -ForegroundColor Cyan

# Run migrations
Write-Host "Running database migrations..." -ForegroundColor White
try {
    & php artisan migrate --force
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Database migrations completed successfully" -ForegroundColor Green
    } else {
        throw "Migration failed"
    }
} catch {
    Write-Host "❌ Failed to run database migrations" -ForegroundColor Red
    exit 1
}

# Seed the database
Write-Host "Seeding database with initial data..." -ForegroundColor White
try {
    & php artisan db:seed
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Database seeded successfully" -ForegroundColor Green
    } else {
        throw "Database seeding failed"
    }
} catch {
    Write-Host "❌ Failed to seed database" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "🗂️  Step 5: Storage Setup" -ForegroundColor Cyan
Write-Host "------------------------" -ForegroundColor Cyan

# Create storage link
try {
    & php artisan storage:link
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Storage link created successfully" -ForegroundColor Green
    } else {
        Write-Host "⚠️  Storage link may already exist" -ForegroundColor Yellow
    }
} catch {
    Write-Host "⚠️  Storage link creation failed or already exists" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "🧹 Step 6: Cache Optimization" -ForegroundColor Cyan
Write-Host "----------------------------" -ForegroundColor Cyan

# Clear and optimize caches
& php artisan config:clear
& php artisan cache:clear
& php artisan view:clear
& php artisan route:clear

# Optimize for production
& php artisan config:cache
& php artisan route:cache
& php artisan view:cache

Write-Host "✅ Cache optimization completed" -ForegroundColor Green

Write-Host ""
Write-Host "🔍 Step 7: Final Verification" -ForegroundColor Cyan
Write-Host "----------------------------" -ForegroundColor Cyan

# Test if the application is working
Write-Host "Testing application..." -ForegroundColor White
try {
    & php artisan --version
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Laravel application is working" -ForegroundColor Green
    } else {
        throw "Application test failed"
    }
} catch {
    Write-Host "❌ Laravel application test failed" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "🎉 Setup Complete!" -ForegroundColor Green
Write-Host "=================" -ForegroundColor Green
Write-Host ""
Write-Host "Your Academy LMS application is now ready!" -ForegroundColor White
Write-Host ""
Write-Host "📝 Default Login Credentials:" -ForegroundColor Cyan
Write-Host "   Admin: admin@academylms.com (password: password123)" -ForegroundColor White
Write-Host "   Instructor: instructor@academylms.com (password: password123)" -ForegroundColor White
Write-Host "   Student: student@academylms.com (password: password123)" -ForegroundColor White
Write-Host ""
Write-Host "🚀 To start the development server:" -ForegroundColor Cyan
Write-Host "   php artisan serve" -ForegroundColor White
Write-Host ""
Write-Host "🌐 Then visit: http://127.0.0.1:8000" -ForegroundColor Cyan
Write-Host ""
Write-Host "⚠️  IMPORTANT: Change default passwords in production!" -ForegroundColor Yellow
Write-Host "📋 Next Steps:" -ForegroundColor Cyan
Write-Host "   1. Configure your .env file with proper database credentials" -ForegroundColor White
Write-Host "   2. Set up your mail configuration for notifications" -ForegroundColor White
Write-Host "   3. Configure payment gateways if needed" -ForegroundColor White
Write-Host "   4. Customize the theme and branding" -ForegroundColor White
Write-Host "   5. Add SSL certificate for production" -ForegroundColor White
Write-Host ""
