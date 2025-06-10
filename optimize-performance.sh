#!/bin/bash

echo "🚀 Talent Request System Performance Optimization Script"
echo "========================================================="

# Clear application cache
echo "📝 Clearing application cache..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize application
echo "⚡ Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Database optimizations
echo "🗄️ Running database optimizations..."
php artisan migrate:status

# Generate optimized composer autoloader
echo "📦 Optimizing Composer autoloader..."
composer dump-autoload --optimize

# Clear and warm up caches
echo "🔥 Warming up caches..."
php artisan cache:clear
php artisan config:cache

echo ""
echo "✅ Performance optimization completed!"
echo ""
echo "📊 Performance Tips:"
echo "   - Monitor query logs for N+1 issues"
echo "   - Use debug=1 in URLs to see timing info"
echo "   - Check Redis/cache hit rates"
echo "   - Monitor talent availability cache efficiency"
echo ""
echo "🔧 Configuration:"
echo "   - Pagination: config/talent_performance.php"
echo "   - Cache TTL settings available in .env"
echo "   - Enable query logging: DB_LOG_QUERIES=true"
