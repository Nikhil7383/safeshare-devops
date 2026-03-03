FROM nginx:alpine

# Remove default config
RUN rm /etc/nginx/conf.d/default.conf

# Copy nginx config
COPY docker/nginx/nginx.conf /etc/nginx/conf.d/default.conf

# Copy frontend files
COPY frontend/ /var/www/frontend/

# Copy backend files (needed for PHP routing/static access)
COPY backend/ /var/www/html/

# Create uploads folder
RUN mkdir -p /var/www/uploads

EXPOSE 80