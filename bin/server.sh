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
