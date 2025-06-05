# Academy LMS

A comprehensive Learning Management System built with Laravel, designed for educational institutions and online learning platforms.

## Features

- **Course Management**: Create, manage, and organize courses with modules and lessons
- **User Management**: Support for students, instructors, and administrators
- **Assessment System**: Quizzes, assignments, and grading capabilities
- **Bootcamp System**: Intensive training programs with live classes
- **Blog System**: Content management for educational articles and news
- **Payment Integration**: Multiple payment gateways (Stripe, Razorpay, PayTM)
- **Certificate Generation**: Automated certificate creation upon course completion
- **Live Chat**: Real-time communication between users
- **Mobile Responsive**: Optimized for all devices
- **Multi-language Support**: Internationalization ready

## Requirements

- PHP >= 8.0
- Composer
- Node.js >= 16.x
- NPM or Yarn
- MySQL >= 5.7 or MariaDB >= 10.3
- Apache or Nginx web server

## Installation

### For Collaborative Development

This project includes automated setup scripts for easy collaboration between team members.

#### Windows Users (PowerShell)
```powershell
# Clone the repository
git clone <your-repository-url>
cd Academy-LMS

# Run the setup script
.\setup_project.ps1
```

#### Linux/Mac Users (Bash)
```bash
# Clone the repository
git clone <your-repository-url>
cd Academy-LMS

# Make the script executable and run it
chmod +x setup_project.sh
./setup_project.sh
```

### Manual Installation

1. **Clone the repository**
   ```bash
   git clone <your-repository-url>
   cd Academy-LMS
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database**
   - Create a MySQL database named `academy_lms`
   - Update `.env` file with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=academy_lms
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. **Run migrations and seeders**
   ```bash
   php artisan migrate:fresh --seed
   ```

7. **Fix any migration conflicts (if needed)**
   ```bash
   php fix_migrations.php
   ```

8. **Build assets**
   ```bash
   npm run build
   # Or for development
   npm run dev
   ```

9. **Start the development server**
   ```bash
   php artisan serve
   ```

Visit `http://localhost:8000` to access the application.

## Configuration

### Mail Configuration
Update your `.env` file with mail settings:
```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Academy LMS"
```

### Payment Gateway Configuration
Configure payment gateways in your `.env` file:

**Stripe:**
```env
STRIPE_KEY=your_stripe_publishable_key
STRIPE_SECRET=your_stripe_secret_key
```

**Razorpay:**
```env
RAZORPAY_KEY=your_razorpay_key
RAZORPAY_SECRET=your_razorpay_secret
```

**PayTM:**
```env
PAYTM_MERCHANT_ID=your_merchant_id
PAYTM_MERCHANT_KEY=your_merchant_key
PAYTM_MERCHANT_WEBSITE=your_website
PAYTM_CHANNEL=your_channel
PAYTM_INDUSTRY_TYPE=your_industry_type
```

## File Storage

The application supports multiple file storage options:

- **Local Storage**: Default for development
- **AWS S3**: For production environments
- **Other Cloud Storage**: Configure in `config/filesystems.php`

Update your `.env` for cloud storage:
```env
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=your_region
AWS_BUCKET=your_bucket_name
```

## Default Admin Credentials

After running the seeders, you can log in with:
- **Email**: admin@example.com
- **Password**: password

**⚠️ Important**: Change these credentials immediately after first login!

## Development

### Running in Development Mode
```bash
# Start the Laravel development server
php artisan serve

# In another terminal, start the asset watcher
npm run dev
```

### Code Style
This project follows Laravel coding standards. Run the following commands to maintain code quality:

```bash
# Format code
./vendor/bin/pint

# Run tests
php artisan test
```

### Database

#### Migration Issues
If you encounter migration conflicts (common when collaborating), use the included migration fixer:

```bash
php fix_migrations.php
```

This script resolves conflicts between the `install.sql` file and Laravel migrations.

#### Fresh Installation
To reset the database completely:

```bash
php artisan migrate:fresh --seed
```

## Deployment

### Production Deployment Checklist

1. **Environment Configuration**
   - Set `APP_ENV=production`
   - Set `APP_DEBUG=false`
   - Configure proper database credentials
   - Set up mail and payment gateway configurations

2. **Optimize Application**
   ```bash
   composer install --optimize-autoloader --no-dev
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan queue:restart
   npm run build
   ```

3. **File Permissions**
   ```bash
   chmod -R 755 storage bootstrap/cache
   ```

4. **Web Server Configuration**
   - Point document root to `public/` directory
   - Configure URL rewriting for Laravel

### Docker Deployment
A Docker configuration is available for containerized deployment. See `docker-compose.yml` for details.

## API Documentation

The application includes a comprehensive API for mobile app integration and third-party services. API documentation is available at `/api/documentation` when running in development mode.

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Guidelines

- Follow Laravel conventions and best practices
- Write tests for new features
- Update documentation as needed
- Ensure code passes all existing tests
- Use meaningful commit messages

## Troubleshooting

### Common Issues

**Migration Errors**
- Run `php fix_migrations.php` to resolve conflicts
- Use `php artisan migrate:fresh --seed` for clean installation

**Asset Compilation Issues**
- Clear npm cache: `npm cache clean --force`
- Delete `node_modules` and run `npm install` again
- Check Node.js version compatibility

**File Permission Errors**
- Ensure `storage/` and `bootstrap/cache/` directories are writable
- On Linux/Mac: `chmod -R 755 storage bootstrap/cache`

**Database Connection Issues**
- Verify database credentials in `.env`
- Ensure database server is running
- Check if database exists

### Getting Help

- Check the [Issues](../../issues) section for known problems
- Create a new issue for bugs or feature requests
- Join our community discussions

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Security

If you discover any security-related issues, please email security@yourdomain.com instead of using the issue tracker.

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for details about changes in each release.

## Credits

- Built with [Laravel](https://laravel.com/)
- UI components powered by [Tailwind CSS](https://tailwindcss.com/)
- Icons by [Heroicons](https://heroicons.com/)
- File uploads handled by [Laravel Media Library](https://spatie.be/docs/laravel-medialibrary/)

---

**Academy LMS** - Empowering Education Through Technology
