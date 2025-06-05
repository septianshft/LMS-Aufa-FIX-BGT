#!/bin/bash
# Academy LMS Complete Setup Script
# This script will completely set up the Academy LMS application

echo "🚀 Academy LMS Complete Setup Script"
echo "===================================="
echo ""

# Check if we're in the correct directory
if [ ! -f "composer.json" ]; then
    echo "❌ Error: Please run this script from the Academy LMS root directory"
    exit 1
fi

echo "📋 Step 1: Environment Setup"
echo "----------------------------"

# Copy .env.example to .env if it doesn't exist
if [ ! -f ".env" ]; then
    if [ -f ".env.example" ]; then
        cp .env.example .env
        echo "✅ .env file created from .env.example"
    else
        echo "⚠️  .env.example not found, you may need to create .env manually"
    fi
else
    echo "✅ .env file already exists"
fi

echo ""
echo "📦 Step 2: Installing Dependencies"
echo "--------------------------------"

# Install Composer dependencies
echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader
if [ $? -eq 0 ]; then
    echo "✅ Composer dependencies installed successfully"
else
    echo "❌ Failed to install Composer dependencies"
    exit 1
fi

# Install Node.js dependencies
echo "Installing Node.js dependencies..."
npm install --production
if [ $? -eq 0 ]; then
    echo "✅ Node.js dependencies installed successfully"
else
    echo "❌ Failed to install Node.js dependencies"
    exit 1
fi

echo ""
echo "🔐 Step 3: Application Key Generation"
echo "-----------------------------------"

# Generate application key
php artisan key:generate
if [ $? -eq 0 ]; then
    echo "✅ Application key generated successfully"
else
    echo "❌ Failed to generate application key"
    exit 1
fi

echo ""
echo "🗄️  Step 4: Database Setup"
echo "-------------------------"

# Run migrations
echo "Running database migrations..."
php artisan migrate --force
if [ $? -eq 0 ]; then
    echo "✅ Database migrations completed successfully"
else
    echo "❌ Failed to run database migrations"
    exit 1
fi

# Seed the database
echo "Seeding database with initial data..."
php artisan db:seed
if [ $? -eq 0 ]; then
    echo "✅ Database seeded successfully"
else
    echo "❌ Failed to seed database"
    exit 1
fi

echo ""
echo "🗂️  Step 5: Storage Setup"
echo "------------------------"

# Create storage link
php artisan storage:link
if [ $? -eq 0 ]; then
    echo "✅ Storage link created successfully"
else
    echo "⚠️  Storage link may already exist or failed to create"
fi

echo ""
echo "🧹 Step 6: Cache Optimization"
echo "----------------------------"

# Clear and optimize caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Cache optimization completed"

echo ""
echo "🎯 Step 7: File Permissions (Linux/Mac)"
echo "--------------------------------------"

# Set proper file permissions (Linux/Mac only)
if [[ "$OSTYPE" == "linux-gnu"* ]] || [[ "$OSTYPE" == "darwin"* ]]; then
    chmod -R 755 storage/
    chmod -R 755 bootstrap/cache/
    echo "✅ File permissions set"
else
    echo "⚠️  Skipping file permissions (Windows detected)"
fi

echo ""
echo "🔍 Step 8: Final Verification"
echo "----------------------------"

# Test if the application is working
echo "Testing application..."
php artisan --version
if [ $? -eq 0 ]; then
    echo "✅ Laravel application is working"
else
    echo "❌ Laravel application test failed"
    exit 1
fi

echo ""
echo "🎉 Setup Complete!"
echo "=================="
echo ""
echo "Your Academy LMS application is now ready!"
echo ""
echo "📝 Default Login Credentials:"
echo "   Admin: admin@academylms.com (password: password123)"
echo "   Instructor: instructor@academylms.com (password: password123)"
echo "   Student: student@academylms.com (password: password123)"
echo ""
echo "🚀 To start the development server:"
echo "   php artisan serve"
echo ""
echo "🌐 Then visit: http://127.0.0.1:8000"
echo ""
echo "⚠️  IMPORTANT: Change default passwords in production!"
echo "📋 Next Steps:"
echo "   1. Configure your .env file with proper database credentials"
echo "   2. Set up your mail configuration for notifications"
echo "   3. Configure payment gateways if needed"
echo "   4. Customize the theme and branding"
echo "   5. Add SSL certificate for production"
echo ""
