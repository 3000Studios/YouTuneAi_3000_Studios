# Deployment Guide

## Overview

This guide covers deploying the YouTuneAi Theme Manager in production environments.

## Prerequisites

- Python 3.8 or higher
- Docker (optional, for containerized deployment)
- Git

## Installation Methods

### Method 1: Python Package

```bash
# Clone the repository
git clone https://github.com/3000Studios/YouTuneAi_3000_Studios.git
cd YouTuneAi_3000_Studios

# Create virtual environment
python -m venv venv
source venv/bin/activate  # On Windows: venv\Scripts\activate

# Install dependencies
pip install -r requirements.txt

# Install the package
pip install -e .

# Verify installation
theme-manager --version
```

### Method 2: Docker

```bash
# Build the image
docker build -t youtune-theme-manager:latest .

# Run the container
docker run -v $(pwd)/themes:/app/themes youtune-theme-manager:latest
```

### Method 3: Docker Compose

```bash
# Start services
docker-compose up -d

# Check logs
docker-compose logs -f

# Stop services
docker-compose down
```

## Configuration

### Environment Variables

Create a `.env` file:

```bash
# Logging
LOG_LEVEL=INFO
LOG_FORMAT=json

# Theme settings
THEMES_DIR=/app/themes
MAX_THEME_SIZE=524288000  # 500 MB
MAX_FILE_SIZE=10485760    # 10 MB

# Security
ENABLE_SECURITY_SCAN=true
SCAN_TIMEOUT=300

# Performance
WORKER_THREADS=4
```

### Configuration File

Edit `config/config.yaml` to customize settings:

```yaml
themes:
  directory: "themes"
  max_size: 524288000

security:
  enable_scanning: true
  scan_timeout: 300

logging:
  level: "INFO"
  format: "json"
```

## Production Deployment

### System Requirements

**Minimum:**
- 2 CPU cores
- 2 GB RAM
- 10 GB disk space

**Recommended:**
- 4 CPU cores
- 4 GB RAM
- 50 GB disk space
- SSD storage

### Security Hardening

1. **Run as non-root user**
   ```bash
   useradd -m -s /bin/bash thememanager
   chown -R thememanager:thememanager /opt/theme-manager
   ```

2. **Set file permissions**
   ```bash
   chmod 750 /opt/theme-manager
   chmod 640 /opt/theme-manager/config/*
   ```

3. **Configure firewall**
   ```bash
   # If running a web interface (future feature)
   ufw allow 8000/tcp
   ufw enable
   ```

4. **Enable SELinux/AppArmor**
   ```bash
   # SELinux
   setenforce 1
   
   # AppArmor
   aa-enforce /etc/apparmor.d/*theme-manager*
   ```

### Systemd Service

Create `/etc/systemd/system/theme-manager.service`:

```ini
[Unit]
Description=YouTuneAi Theme Manager
After=network.target

[Service]
Type=simple
User=thememanager
Group=thememanager
WorkingDirectory=/opt/theme-manager
Environment="PATH=/opt/theme-manager/venv/bin"
ExecStart=/opt/theme-manager/venv/bin/theme-manager
Restart=always
RestartSec=10

# Security
NoNewPrivileges=true
PrivateTmp=true
ProtectSystem=strict
ProtectHome=true
ReadWritePaths=/opt/theme-manager/themes
ReadWritePaths=/opt/theme-manager/logs

[Install]
WantedBy=multi-user.target
```

Enable and start:
```bash
systemctl daemon-reload
systemctl enable theme-manager
systemctl start theme-manager
```

### Monitoring

#### Log Management

Configure log rotation in `/etc/logrotate.d/theme-manager`:

```
/opt/theme-manager/logs/*.log {
    daily
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 thememanager thememanager
    sharedscripts
    postrotate
        systemctl reload theme-manager > /dev/null 2>&1 || true
    endscript
}
```

#### Health Checks

```bash
# Check service status
systemctl status theme-manager

# View logs
journalctl -u theme-manager -f

# Test functionality
theme-manager list
```

### Backup Strategy

```bash
# Backup script
#!/bin/bash
BACKUP_DIR="/backup/theme-manager"
DATE=$(date +%Y%m%d_%H%M%S)

# Backup themes
tar -czf "$BACKUP_DIR/themes-$DATE.tar.gz" /opt/theme-manager/themes

# Backup config
tar -czf "$BACKUP_DIR/config-$DATE.tar.gz" /opt/theme-manager/config

# Keep only last 7 days
find "$BACKUP_DIR" -type f -mtime +7 -delete
```

Schedule with cron:
```cron
0 2 * * * /opt/theme-manager/scripts/backup.sh
```

## High Availability

### Load Balancing

For high-traffic deployments, use multiple instances behind a load balancer:

```nginx
upstream theme_managers {
    server tm1.internal:8000;
    server tm2.internal:8000;
    server tm3.internal:8000;
}

server {
    listen 80;
    server_name theme-manager.example.com;

    location / {
        proxy_pass http://theme_managers;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }
}
```

### Database (Future Enhancement)

For shared state across instances, consider:
- PostgreSQL for metadata
- Redis for caching
- S3/MinIO for theme storage

## Kubernetes Deployment

### Deployment Configuration

```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: theme-manager
spec:
  replicas: 3
  selector:
    matchLabels:
      app: theme-manager
  template:
    metadata:
      labels:
        app: theme-manager
    spec:
      containers:
      - name: theme-manager
        image: ghcr.io/3000studios/youtuneai_3000_studios:latest
        ports:
        - containerPort: 8000
        volumeMounts:
        - name: themes
          mountPath: /app/themes
        resources:
          requests:
            memory: "256Mi"
            cpu: "250m"
          limits:
            memory: "512Mi"
            cpu: "500m"
        securityContext:
          runAsNonRoot: true
          runAsUser: 1000
          readOnlyRootFilesystem: true
          allowPrivilegeEscalation: false
      volumes:
      - name: themes
        persistentVolumeClaim:
          claimName: theme-storage
```

### Service Configuration

```yaml
apiVersion: v1
kind: Service
metadata:
  name: theme-manager
spec:
  selector:
    app: theme-manager
  ports:
  - port: 80
    targetPort: 8000
  type: LoadBalancer
```

## Troubleshooting

### Common Issues

**Issue: Permission denied**
```bash
# Fix permissions
chown -R thememanager:thememanager /opt/theme-manager/themes
chmod -R 750 /opt/theme-manager/themes
```

**Issue: Out of disk space**
```bash
# Clean old themes
find /opt/theme-manager/themes -type f -mtime +30 -delete

# Check disk usage
df -h /opt/theme-manager
```

**Issue: High memory usage**
```bash
# Check processes
ps aux | grep theme-manager

# Restart service
systemctl restart theme-manager
```

## Updates and Maintenance

### Updating the Application

```bash
# Backup first
./scripts/backup.sh

# Pull latest changes
cd /opt/theme-manager
git pull origin main

# Update dependencies
pip install -r requirements.txt

# Restart service
systemctl restart theme-manager
```

### Security Updates

```bash
# Check for vulnerabilities
safety check

# Update dependencies
pip install --upgrade -r requirements.txt

# Run security scan
bandit -r src/
```

## Performance Tuning

### Optimization Tips

1. **Use SSD storage** for theme directories
2. **Enable caching** for frequently accessed themes
3. **Adjust worker threads** based on CPU cores
4. **Monitor memory usage** and adjust limits
5. **Use content delivery network (CDN)** for theme distribution

### Metrics to Monitor

- CPU usage
- Memory usage
- Disk I/O
- Network throughput
- Request latency
- Error rates

## Support

For deployment issues:
- Check logs: `journalctl -u theme-manager -f`
- Review documentation: `/docs`
- Open an issue: GitHub Issues
- Contact support: security@3000studios.com
