#!/usr/bin/perl

use strict;
use warnings;
use File::Slurp;

# Leer el contenido del archivo de cambio de contraseña de WordPress
my $wordpress_file = '/var/www/html/login/formularios/gestiones/cambio_contrasena_blog.txt';
my $wordpress_content = read_file($wordpress_file);

# Leer el contenido del archivo de alta en moodle
my $moodle_file = '/var/www/html/login/formularios/gestiones/alta_moodle.txt';
my $moodle_content = read_file($moodle_file);

# Leer el contenido del archivo de cambio de contraseña de Nextcloud
my $nextcloud_file = '/var/www/html/login/formularios/gestiones/cambio_contrasena_nextcloud.txt';
my $nextcloud_content = read_file($nextcloud_file);

# Leer el contenido del archivo de baja del sistema y vaciar el archivo
my $baja_sistema_file = '/var/www/html/login/formularios/gestiones/baja_sistema.txt';
my $baja_sistema_content = read_file($baja_sistema_file);

# Vaciar el archivo de cambio de contraseña wordpres
open(my $fh_wordpress, '>', $wordpress_file) or die "No se pudo abrir el archivo '$wordpress_file' $!";
close($fh_wordpress);

# Vaciar el archivo cambio de contraseña nextcloud                
open(my $fh_nextcloud, '>', $nextcloud_file) or die "No se pudo abrir el archivo '$nextcloud_file' $!";
close($fh_nextcloud);

# Vaciar el archivo de baja del sistema pero no borrarlo
open(my $fh_baja, '>', $baja_sistema_file) or die "No se pudo abrir el archivo '$baja_sistema_file' $!";
close($fh_baja);

# Leer el contenido del archivo de cambio de rol y vaciar el archivo
my $cambio_rol_file = '/var/www/html/login/formularios/gestiones/cambio_rol.txt';
my $cambio_rol_content = read_file($cambio_rol_file);

# Vaciar el archivo de cambio de rol pero no borrarlo
open(my $fh_rol, '>', $cambio_rol_file) or die "No se pudo abrir el archivo '$cambio_rol_file' $!";
close($fh_rol);

# Vaciar el archivo de alta en moodle
open(my $fh_moodle, '>', $moodle_file) or die "No se pudo abrir el archivo '$moodle_file' $!";
close($fh_moodle);

# Leer el contenido del archivo de registro de inicio de sesión
my $login_log_file = '/var/www/html/login.log';
my $login_log_content = read_file($login_log_file);

# Vaciar el archivo de registro de inicio de sesión pero no borrarlo
open(my $fh_login, '>', $login_log_file) or die "No se pudo abrir el archivo '$login_log_file' $!";
close($fh_login);

# Configurar los destinatarios y el contenido de los correos electrónicos
my $to = 'carldres004@gmail.com';
my $from = 'carldres@carldres.local';  # Especifica el remitente aquí
my $subject = 'Solicitudes y registros de cambios en el sistema';

# Correo para WordPress
my $wordpress_message = "Los siguientes usuarios requieren cambio de contraseña en el blog de WordPress:\n\n$wordpress_content";
write_file('/tmp/wordpress_email.txt', $wordpress_message);

# Correo para moodle
my $moodle_message = "Los siguientes usuarios requieren el alta en moodle:\n\n$moodle_content";
write_file('/tmp/moodle_email.txt', $moodle_message);

# Correo para Nextcloud
my $nextcloud_message = "Se han recibido solicitudes de cambio de contraseña para los siguientes usuarios en Nextcloud:\n\n$nextcloud_content";
write_file('/tmp/nextcloud_email.txt', $nextcloud_message);

# Correo para Baja del Sistema
my $baja_sistema_message = "Se han recibido solicitudes de baja del sistema para los siguientes usuarios:\n\n$baja_sistema_content";
write_file('/tmp/baja_sistema_email.txt', $baja_sistema_message);

# Correo para Cambio de Rol
my $cambio_rol_message = "Se han recibido solicitudes de cambio de rol a profesor para los siguientes usuarios:\n\n$cambio_rol_content";
write_file('/tmp/cambio_rol_email.txt', $cambio_rol_message);

# Correo para Registro de Inicio de Sesión
my $login_log_message = "Registro de intentos de inicio de sesión:\n\n$login_log_content";
write_file('/tmp/login_log_email.txt', $login_log_message);

# Enviar correos electrónicos utilizando el comando mail con la opción -r para especificar el remitente
system("mail -r $from -s '$subject' $to < /tmp/wordpress_email.txt");
system("mail -r $from -s '$subject' $to < /tmp/moodle_email.txt");
system("mail -r $from -s '$subject' $to < /tmp/nextcloud_email.txt");
system("mail -r $from -s 'Solicitud de baja del sistema' $to < /tmp/baja_sistema_email.txt");
system("mail -r $from -s 'Solicitud de cambio de rol a profesor' $to < /tmp/cambio_rol_email.txt");
system("mail -r $from -s 'Registro de inicio de sesión' $to < /tmp/login_log_email.txt");

print "Correos electrónicos enviados correctamente.\n";