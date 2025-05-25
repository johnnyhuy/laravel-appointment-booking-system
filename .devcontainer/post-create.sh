#!/bin/bash

echo "🚀 Setting up Laravel development environment..."

# Verify Python installation
echo "🐍 Configuring Python environment..."
python3 --version
python --version

# Ensure python command points to python3
sudo ln -sf /usr/bin/python3 /usr/bin/python
echo "✅ Python configured (python3 as default)"

# Install PHP dependencies
composer install

# Install Node.js dependencies
yarn install

# Setup environment file
if [ ! -f .env ]; then
    cp .env.example .env
    echo "✅ Created .env file from .env.example"
fi

# Generate application key
php artisan key:generate

# Update database path in .env for dev container
sed -i 's|DB_DATABASE=.*|DB_DATABASE=/workspaces/laravel-appointment-booking-system/database/dev.database.sqlite|g' .env

# Create SQLite database if it doesn't exist
touch database/dev.database.sqlite

# Set proper permissions
sudo chown -R vscode:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Run database migrations if needed
if [ -f database/dev.database.sqlite ]; then
    php artisan migrate --force
fi

# Compile assets
npm run dev

echo "✅ Laravel development environment setup complete!"
echo "🌐 Run 'php artisan serve' to start the development server"
echo "🧪 Run 'vendor/bin/phpunit' to run tests"