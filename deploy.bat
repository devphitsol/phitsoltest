@echo off
REM PHITSOL Project Deployment Script for Windows
REM This script prepares the project for production deployment

echo 🚀 Starting PHITSOL Project Deployment...

REM Set variables
set PROJECT_NAME=phitsol
set DEPLOY_DIR=.\deploy
set TIMESTAMP=%date:~10,4%%date:~4,2%%date:~7,2%_%time:~0,2%%time:~3,2%%time:~6,2%
set TIMESTAMP=%TIMESTAMP: =0%

REM Create deployment directory
echo 📁 Creating deployment directory...
if exist %DEPLOY_DIR% rmdir /s /q %DEPLOY_DIR%
mkdir %DEPLOY_DIR%

REM Copy PHP backend files
echo 📋 Copying PHP backend files...
xcopy app %DEPLOY_DIR%\app /E /I /Y
xcopy config %DEPLOY_DIR%\config /E /I /Y
xcopy public %DEPLOY_DIR%\public /E /I /Y
xcopy migrations %DEPLOY_DIR%\migrations /E /I /Y
xcopy bin %DEPLOY_DIR%\bin /E /I /Y
xcopy assets %DEPLOY_DIR%\assets /E /I /Y
copy composer.json %DEPLOY_DIR%\
copy index.php %DEPLOY_DIR%\
copy .htaccess %DEPLOY_DIR%\

REM Copy frontend build files (if built)
if exist "frontend\.next" (
    echo 📋 Copying Next.js build files...
    xcopy frontend\.next %DEPLOY_DIR%\frontend\.next /E /I /Y
    xcopy frontend\public %DEPLOY_DIR%\frontend\public /E /I /Y
    copy frontend\package.json %DEPLOY_DIR%\frontend\
    copy frontend\next.config.ts %DEPLOY_DIR%\frontend\
)

REM Copy admin files
echo 📋 Copying admin files...
xcopy admin %DEPLOY_DIR%\admin /E /I /Y

REM Create deployment info
echo 📝 Creating deployment info...
(
echo PHITSOL Project Deployment
echo ==========================
echo Deployment Date: %date% %time%
echo Deployment Script: deploy.bat
echo Project Version: 1.0.0
echo.
echo DEPLOYMENT STEPS:
echo 1. Upload files to production server
echo 2. Run: composer install --no-dev --optimize-autoloader
echo 3. Run: npm install --production ^(in frontend directory^)
echo 4. Run: npm run build ^(in frontend directory^)
echo 5. Set up environment variables
echo 6. Configure web server
echo 7. Set proper file permissions
echo.
echo EXCLUDED FILES:
echo - node_modules/
echo - vendor/
echo - .next/ ^(will be rebuilt^)
echo - .git/
echo - Development configuration files
echo - Log files
echo - Cache files
echo.
echo For more information, see README.md
) > %DEPLOY_DIR%\DEPLOYMENT_INFO.txt

REM Create production .htaccess
echo 🔧 Creating production .htaccess...
(
echo # Production .htaccess for PHITSOL
echo RewriteEngine On
echo.
echo # Security headers
echo Header always set X-Content-Type-Options nosniff
echo Header always set X-Frame-Options DENY
echo Header always set X-XSS-Protection "1; mode=block"
echo Header always set Referrer-Policy "strict-origin-when-cross-origin"
echo.
echo # Redirect to public directory
echo RewriteCond %%{REQUEST_URI} !^/public/
echo RewriteRule ^^(.*^)$ public/$1 [L]
echo.
echo # Handle API requests
echo RewriteRule ^api/^(.*^)$ public/api/index.php [QSA,L]
echo.
echo # Cache static assets
echo ^<FilesMatch "\.^(css^|js^|png^|jpg^|jpeg^|gif^|ico^|svg^|woff^|woff2^|ttf^|eot^)$"^>
echo     ExpiresActive On
echo     ExpiresDefault "access plus 1 year"
echo     Header set Cache-Control "public, immutable"
echo ^</FilesMatch^>
echo.
echo # Compress text files
echo ^<IfModule mod_deflate.c^>
echo     AddOutputFilterByType DEFLATE text/plain
echo     AddOutputFilterByType DEFLATE text/html
echo     AddOutputFilterByType DEFLATE text/xml
echo     AddOutputFilterByType DEFLATE text/css
echo     AddOutputFilterByType DEFLATE application/xml
echo     AddOutputFilterByType DEFLATE application/xhtml+xml
echo     AddOutputFilterByType DEFLATE application/rss+xml
echo     AddOutputFilterByType DEFLATE application/javascript
echo     AddOutputFilterByType DEFLATE application/x-javascript
echo ^</IfModule^>
) > %DEPLOY_DIR%\.htaccess

REM Create production environment template
echo 🔧 Creating environment template...
(
echo # Production Environment Variables
echo # Copy this file to .env and update with your production values
echo.
echo # Database Configuration
echo MONGODB_URI=mongodb://localhost:27017
echo MONGODB_DATABASE=phitsol_production
echo.
echo # Application Configuration
echo APP_ENV=production
echo APP_DEBUG=false
echo APP_URL=https://your-domain.com
echo.
echo # Session Configuration
echo SESSION_SECRET=your-production-secret-key-here
echo.
echo # Email Configuration ^(if using^)
echo SMTP_HOST=smtp.your-provider.com
echo SMTP_PORT=587
echo SMTP_USERNAME=your-email@domain.com
echo SMTP_PASSWORD=your-email-password
echo.
echo # Security
echo CSRF_TOKEN_SECRET=your-csrf-secret-key-here
) > %DEPLOY_DIR%\.env.production.template

REM Create deployment size report
echo 📊 Creating deployment size report...
dir /s %DEPLOY_DIR% > %DEPLOY_DIR%\SIZE_REPORT.txt

echo ✅ Deployment files are in: %DEPLOY_DIR%
echo.
echo 🚀 NEXT STEPS:
echo 1. Upload the files to your production server
echo 2. Run: composer install --no-dev --optimize-autoloader
echo 3. Run: npm install --production ^(in frontend directory^)
echo 4. Run: npm run build ^(in frontend directory^)
echo 5. Set up environment variables
echo 6. Configure web server
echo.
echo 📋 See DEPLOYMENT_INFO.txt for detailed instructions
pause
