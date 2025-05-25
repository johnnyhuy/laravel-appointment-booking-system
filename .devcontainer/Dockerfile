FROM php:7.4-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    gnupg \
    wget \
    python3 \
    python3-pip \
    build-essential \
    sudo \
    libzip-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configure Python environment
RUN ln -sf /usr/bin/python3 /usr/bin/python \
    && python3 -m pip install --upgrade pip

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create a non-root user
RUN useradd -ms /bin/bash vscode && \
    usermod -aG www-data vscode && \
    echo "vscode ALL=(ALL) NOPASSWD:ALL" > /etc/sudoers.d/vscode

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Configure Apache
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /workspaces/laravel-appointment-booking-system/public\n\
    <Directory /workspaces/laravel-appointment-booking-system/public>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Set working directory
WORKDIR /workspaces/laravel-appointment-booking-system

# Install Chromium for Dusk testing (works on both amd64 and arm64)
RUN apt-get update && apt-get install -y chromium

# Set proper permissions
RUN chown -R vscode:www-data /workspaces

# Switch to non-root user
USER vscode

# Expose port
EXPOSE 80