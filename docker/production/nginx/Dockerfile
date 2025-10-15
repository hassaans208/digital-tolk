# Use the official Nginx image as the base
FROM nginx:alpine

# Remove the default nginx configuration and any conflicting files
RUN rm -f /etc/nginx/nginx.conf /etc/nginx/conf.d/default.conf

# Copy custom nginx configuration
COPY docker/production/nginx/nginx.conf /etc/nginx/nginx.conf

# Copy Laravel application files
COPY . /var/www

# Create necessary directories and set proper permissions
RUN mkdir -p /var/cache/nginx /var/log/nginx /var/run && \
    touch /var/log/nginx/access.log && \
    touch /var/log/nginx/error.log && \
    chown -R nginx:nginx /var/cache/nginx /var/log/nginx && \
    chown nginx:nginx /var/run && \
    chown nginx:nginx /var/log/nginx/access.log && \
    chown nginx:nginx /var/log/nginx/error.log && \
    chown -R nginx:nginx /var/www/storage && \
    chmod 755 /var/run && \
    chmod 755 /var/log/nginx/access.log && \
    chmod 755 /var/log/nginx/error.log && \
    chmod 755 /var/run && \
    chmod 755 /var/www/storage

# Create entrypoint script to ensure proper permissions at runtime
RUN echo '#!/bin/sh' > /entrypoint.sh && \
    echo 'mkdir -p /var/run /var/log/nginx' >> /entrypoint.sh && \
    echo 'chown nginx:nginx /var/run /var/log/nginx' >> /entrypoint.sh && \
    echo 'chmod 755 /var/run /var/log/nginx' >> /entrypoint.sh && \
    echo 'exec nginx -g "daemon off;"' >> /entrypoint.sh && \
    chmod +x /entrypoint.sh

# Expose port 80
EXPOSE 80

# Use entrypoint to ensure permissions (runs as root)
ENTRYPOINT ["/entrypoint.sh"]