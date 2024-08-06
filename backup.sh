#!/bin/bash
#Archivo utilizado para realizar las copias de seguridad remotas del sistema

# Variables
REMOTE_USER="carldres"
REMOTE_HOST="34.125.136.211"
REMOTE_DIR="/home/carldres/backup"
LOCAL_DIR="/var/www"

# Respaldar archivos utilizando rsync
sudo -i -u carldres rsync -avz -e ssh $LOCAL_DIR $REMOTE_USER@$REMOTE_HOST:$REMOTE_DIR