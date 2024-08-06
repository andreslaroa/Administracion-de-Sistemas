#!/usr/bin/perl
use strict;
use warnings;
use DBI;

my $file = '/var/www/html/login/formularios/gestiones/cambio_contrasena_sistema.txt';
my $dsn = 'DBI:mysql:database=usuarios;host=localhost';
my $db_user = 'carldres';
my $db_password = 'hornazo';

# Conectar a la base de datos
my $dbh = DBI->connect($dsn, $db_user, $db_password, { RaiseError => 1, AutoCommit => 1 });

# Abrir el archivo
open(my $fh, '<', $file) or die "No se pudo abrir el archivo '$file' $!";

# Procesar cada línea del archivo
while (my $line = <$fh>) {
    chomp $line;
    my ($user, $old_password, $new_password) = split /:/, $line;

    # Cambiar la contraseña del sistema
    my $cmd = `echo "$user:$new_password" | chpasswd`;
    if ($? != 0) {
        warn "Error cambiando la contraseña del usuario del sistema $user";
        next;
    }

    # Actualizar la contraseña en la base de datos
    my $sth = $dbh->prepare('UPDATE registros SET password = ? WHERE username = ?');
    $sth->execute($new_password, $user);
}

# Cerrar el archivo
close($fh);

# Vaciar el archivo
open($fh, '>', $file) or die "No se pudo abrir el archivo '$file' $!";
close($fh);

# Cerrar la conexión a la base de datos
$dbh->disconnect;