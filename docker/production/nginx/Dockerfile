# Use the official Nginx image as the base
FROM nginx:alpine

# Copy custom nginx configuration
COPY docker/production/nginx/nginx.conf /etc/nginx/nginx.conf

# Create necessary directories
RUN mkdir -p /var/cache/nginx /var/run /var/log/nginx

# Set proper permissions (nginx user already exists in the base image)
RUN chown -R nginx:nginx /var/cache/nginx /var/run /var/log/nginx

# Switch to non-root user (nginx user already exists)
USER nginx

# Expose port 80
EXPOSE 80

# Start nginx
CMD ["nginx", "-g", "daemon off;"]