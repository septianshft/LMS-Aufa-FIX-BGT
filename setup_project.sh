#!/bin/bash

echo "🚀 Academy LMS - GitHub Ready Setup Script"
echo "=========================================="
echo ""

# Step 1: Create proper .env.example
echo "📝 Creating .env.example..."
cat > .env.example << 'EOF'
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
MAIL_FROM_NAME="${APP_NAME}"

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

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

# Payment Gateway Keys (Use test keys)
RAZORPAY_KEY=your_test_key_here
RAZORPAY_SECRET=your_test_secret_here
EOF

# Step 2: Update .gitignore
echo "📝 Updating .gitignore..."
cat >> .gitignore << 'EOF'

# Academy LMS Specific
/public/assets/install.sql
/upload/
.env.backup
.env.production
/storage/app/public/uploads
/public/uploads
fix_migrations.php
setup_project.sh

# IDE
.vscode/
.idea/
*.swp
*.swo

# OS
.DS_Store
Thumbs.db
EOF

echo "✅ .env.example created"
echo "✅ .gitignore updated"

# Step 3: Setup environment
echo ""
echo "🔧 Setting up development environment..."

if [ ! -f .env ]; then
    cp .env.example .env
    echo "✅ .env file created from example"
else
    echo "⚠️  .env already exists, skipping..."
fi

# Step 4: Install dependencies
echo ""
echo "📦 Installing dependencies..."
echo "Running: composer install"
composer install --no-interaction

echo "Running: npm install"
npm install

# Step 5: Generate key
echo ""
echo "🔑 Generating application key..."
php artisan key:generate

# Step 6: Database setup
echo ""
echo "🗄️  Database Setup"
echo "=================="
echo "Please ensure you have created a MySQL database named 'academy_lms'"
echo "Update your .env file with the correct database credentials"
echo ""
read -p "Press Enter after you've set up the database and updated .env..."

# Step 7: Run migrations
echo ""
echo "🏃 Running migrations..."
php artisan migrate

# Step 8: Seed database (if seeders exist)
echo ""
echo "🌱 Seeding database..."
if [ -f "database/seeders/DatabaseSeeder.php" ]; then
    php artisan db:seed
    echo "✅ Database seeded"
else
    echo "ℹ️  No seeders found, skipping..."
fi

# Step 9: Create storage link
echo ""
echo "🔗 Creating storage link..."
php artisan storage:link

# Step 10: Set permissions (Linux/Mac)
if [[ "$OSTYPE" != "msys" && "$OSTYPE" != "win32" ]]; then
    echo ""
    echo "📁 Setting permissions..."
    chmod -R 755 storage bootstrap/cache
    echo "✅ Permissions set"
fi

# Step 11: Clear caches
echo ""
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo ""
echo "🎉 Setup Complete!"
echo "=================="
echo ""
echo "Next steps:"
echo "1. Update your .env file with your database credentials"
echo "2. Run: php artisan serve"
echo "3. Visit: http://localhost:8000"
echo ""
echo "For your friend to set up:"
echo "1. Clone the repository"
echo "2. Run: ./setup_project.sh"
echo "3. Follow the prompts"
echo ""
echo "Happy coding! 🚀"
EOF
