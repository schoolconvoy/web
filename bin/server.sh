# Login as root
ssh root@your_server_ip

# Add user
adduser sammy

# Add user to sudo group
usermod -aG sudo sammy

# Firewall settings
ufw app list
ufw allow OpenSSH
ufw status

# Install Nginx
sudo apt update
sudo apt install nginx
sudo ufw app list

# Allow Nginx
sudo ufw allow 'Nginx HTTP'
sudo ufw status

# Check Nginx
systemctl status nginx

# Check my IP
curl -4 icanhazip.com

# Visit IP in browser, maybe pipe the above output to curl
# TODO: curl -4 icanhazip.com | xargs -I {} curl http://{}

# Setup server blocks
# TODO: sudo mkdir -p /var/www/your_domain/html

# Set permissions
sudo chown -R $USER:$USER /var/www/your_domain/html

# Setup NGINX configuration
sudo nano /etc/nginx/sites-available/your_domain

# Setup Laravel NGINGX configuration
# server {
#     listen 80;
#     listen [::]:80;
#     server_name interguide.schoolconvoy.com;
#     root /var/www/interguide.schoolconvoy.com/web/public;

#     add_header X-Frame-Options "SAMEORIGIN";
#     add_header X-Content-Type-Options "nosniff";

#     index index.php;

#     charset utf-8;

#     location / {
#         try_files $uri $uri/ /index.php?$query_string;
#     }

#     location = /favicon.ico { access_log off; log_not_found off; }
#     location = /robots.txt  { access_log off; log_not_found off; }

#     error_page 404 /index.php;

#     location ~ \.php$ {
#         fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
#         fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
#         include fastcgi_params;
#         fastcgi_hide_header X-Powered-By;
#     }

#     location ~ /\.(?!well-known).* {
#         deny all;
#     }
# }

# Create symbolink link
sudo ln -s /etc/nginx/sites-available/your_domain /etc/nginx/sites-enabled/

# Increase server buckets
sudo nano /etc/nginx/nginx.conf

# Check configuration is okay
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx

# SSL Installation
sudo snap install --classic certbot
