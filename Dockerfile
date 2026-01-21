FROM php:8.2-apache

# 1. تثبيت المكتبات الضرورية للنظام وقواعد البيانات
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql zip

# 2. تفعيل مود الريرايت في أباتشي (ضروري لروابط لارفيل)
RUN a2enmod rewrite

# 3. تعديل إعدادات أباتشي لتوجيه الزوار لمجلد public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# 4. تحديد مجلد العمل
WORKDIR /var/www/html

# 5. نسخ ملفات المشروع من جهازك للسيرفر
COPY . /var/www/html

# 6. تثبيت Composer وتشغيل التثبيت
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader
# إنشاء الجسر (Symlink) بقوة السلطة
RUN php artisan storage:link
# 7. ضبط صلاحيات الكتابة لمجلدات التخزين
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 8. فتح المنفذ 80
EXPOSE 80

# 9. الأمر السحري: تنفيذ الميجريشن ثم تشغيل السيرفر
CMD bash -c "php artisan migrate --force && apache2-foreground"
# CMD bash -c "php artisan migrate:fresh --seed --force && apache2-foreground"