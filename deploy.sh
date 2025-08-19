#!/bin/bash

# PHITSOL Project Deployment Script
# This script prepares the project for production deployment

echo "🚀 Starting PHITSOL Project Deployment..."

# Set variables
PROJECT_NAME="phitsol"
DEPLOY_DIR="./deploy"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

# Create deployment directory
echo "📁 Creating deployment directory..."
rm -rf $DEPLOY_DIR
mkdir -p $DEPLOY_DIR

# Copy PHP backend files
echo "📋 Copying PHP backend files..."
cp -r app/ $DEPLOY_DIR/
cp -r config/ $DEPLOY_DIR/
cp -r public/ $DEPLOY_DIR/
cp -r migrations/ $DEPLOY_DIR/
cp -r bin/ $DEPLOY_DIR/
cp -r assets/ $DEPLOY_DIR/
cp composer.json $DEPLOY_DIR/
cp index.php $DEPLOY_DIR/
cp .htaccess $DEPLOY_DIR/

# Copy frontend build files (if built)
if [ -d "frontend/.next" ]; then
    echo "📋 Copying Next.js build files..."
    cp -r frontend/.next $DEPLOY_DIR/frontend/
    cp -r frontend/public $DEPLOY_DIR/frontend/
    cp frontend/package.json $DEPLOY_DIR/frontend/
    cp frontend/next.config.ts $DEPLOY_DIR/frontend/
fi

# Copy admin files
echo "📋 Copying admin files..."
cp -r admin/ $DEPLOY_DIR/

# Create deployment info
echo "📝 Creating deployment info..."
cat > $DEPLOY_DIR/DEPLOYMENT_INFO.txt << EOF
PHITSOL Project Deployment
==========================
Deployment Date: $(date)
Deployment Script: deploy.sh
Project Version: 1.0.0

DEPLOYMENT STEPS:
1. Upload files to production server
2. Run: composer install --no-dev --optimize-autoloader
3. Run: npm install --production (in frontend directory)
4. Run: npm run build (in frontend directory)
5. Set up environment variables
6. Configure web server
7. Set proper file permissions

EXCLUDED FILES:
- node_modules/
- vendor/
- .next/ (will be rebuilt)
- .git/
- Development configuration files
- Log files
- Cache files

For more information, see README.md
EOF

# Create production .htaccess
echo "🔧 Creating production .htaccess..."
cat > $DEPLOY_DIR/.htaccess << 'EOF'
# Production .htaccess for PHITSOL
RewriteEngine On

# Security headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
Header always set Referrer-Policy "strict-origin-when-cross-origin"

# Redirect to public directory
RewriteCond %{REQUEST_URI} !^/public/
RewriteRule ^(.*)$ public/$1 [L]

# Handle API requests
RewriteRule ^api/(.*)$ public/api/index.php [QSA,L]

# Cache static assets
<FilesMatch "\.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$">
    ExpiresActive On
    ExpiresDefault "access plus 1 year"
    Header set Cache-Control "public, immutable"
</FilesMatch>

# Compress text files
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>
EOF

# Create production environment template
echo "🔧 Creating environment template..."
cat > $DEPLOY_DIR/.env.production.template << 'EOF'
# Production Environment Variables
# Copy this file to .env and update with your production values

# Database Configuration
MONGODB_URI=mongodb://localhost:27017
MONGODB_DATABASE=phitsol_production

# Application Configuration
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Session Configuration
SESSION_SECRET=your-production-secret-key-here

# Email Configuration (if using)
SMTP_HOST=smtp.your-provider.com
SMTP_PORT=587
SMTP_USERNAME=your-email@domain.com
SMTP_PASSWORD=your-email-password

# Security
CSRF_TOKEN_SECRET=your-csrf-secret-key-here
EOF

# Create deployment size report
echo "📊 Creating deployment size report..."
du -sh $DEPLOY_DIR/* > $DEPLOY_DIR/SIZE_REPORT.txt
echo "Total deployment size:" >> $DEPLOY_DIR/SIZE_REPORT.txt
du -sh $DEPLOY_DIR >> $DEPLOY_DIR/SIZE_REPORT.txt

# Create deployment package
echo "📦 Creating deployment package..."
cd $DEPLOY_DIR
tar -czf ../${PROJECT_NAME}_deploy_${TIMESTAMP}.tar.gz .
cd ..

echo "✅ Deployment package created: ${PROJECT_NAME}_deploy_${TIMESTAMP}.tar.gz"
echo "📁 Deployment files are in: $DEPLOY_DIR"
echo ""
echo "🚀 NEXT STEPS:"
echo "1. Upload the tar.gz file to your production server"
echo "2. Extract the files"
echo "3. Run: composer install --no-dev --optimize-autoloader"
echo "4. Run: npm install --production (in frontend directory)"
echo "5. Run: npm run build (in frontend directory)"
echo "6. Set up environment variables"
echo "7. Configure web server"
echo ""
echo "📋 See DEPLOYMENT_INFO.txt for detailed instructions"
