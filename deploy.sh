#!/bin/bash

# Script Deployment untuk Laravel Kos-Kosan H.Kastim
# Usage: ./deploy.sh

echo "🚀 Starting deployment process..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if .env exists
if [ ! -f .env ]; then
    echo -e "${RED}❌ Error: .env file not found!${NC}"
    echo "Please create .env file from .env.example"
    exit 1
fi

echo -e "${GREEN}✓ .env file found${NC}"

# Install/Update dependencies
echo "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader

if [ $? -ne 0 ]; then
    echo -e "${RED}❌ Composer install failed!${NC}"
    exit 1
fi

echo -e "${GREEN}✓ Dependencies installed${NC}"

# Generate app key if not exists
if ! grep -q "APP_KEY=base64:" .env; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
    echo -e "${GREEN}✓ Application key generated${NC}"
fi

# Clear and cache config
echo "⚙️  Optimizing configuration..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

echo -e "${GREEN}✓ Configuration optimized${NC}"

# Run migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force

if [ $? -ne 0 ]; then
    echo -e "${RED}❌ Migration failed!${NC}"
    exit 1
fi

echo -e "${GREEN}✓ Migrations completed${NC}"

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link

echo -e "${GREEN}✓ Storage link created${NC}"

# Set permissions
echo "🔐 Setting permissions..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

echo -e "${GREEN}✓ Permissions set${NC}"

echo ""
echo -e "${GREEN}✅ Deployment completed successfully!${NC}"
echo ""
echo "Next steps:"
echo "1. Make sure your web server is configured correctly"
echo "2. Point document root to: $(pwd)/public"
echo "3. Ensure PHP version >= 8.2"
echo "4. Test your application"

