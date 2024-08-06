#!/usr/bin/perl
use strict;
use warnings;
use DBI;

# Configuración de la conexión a la base de datos
my $db_name = 'usuarios';
my $db_user = 'carldres';
my $db_pass = 'hornazo';
my $db_host = 'localhost';  # Cambiar si la base de datos está en otro host
my $db_port = '3306';        # Puerto de la base de datos (MySQL por ejemplo)

# Obtener el nombre de usuario desde la línea de comandos
my $username = shift @ARGV;

# Verificar que se haya proporcionado un nombre de usuario
die "Uso: $0 <nombre_usuario>\n" unless defined $username;

# Establecer la conexión a la base de datos
my $dbh = DBI->connect("DBI:mysql:$db_name;host=$db_host;port=$db_port", $db_user, $db_pass, { RaiseError => 1 })
    or die "Error de conexión a la base de datos: $DBI::errstr";

# Consulta SQL para actualizar el campo 'rol' para el usuario dado
my $sql = "UPDATE registros SET rol = 'profesor' WHERE username = ?";
my $sth = $dbh->prepare($sql);

# Ejecutar la consulta SQL con el nombre de usuario proporcionado
$sth->execute($username) or die "Error al ejecutar la consulta: $DBI::errstr";

# Verificar si se realizó la actualización correctamente
if ($sth->rows > 0) {
    print "Se ha actualizado el rol del usuario '$username' a 'profesor'.\n";
} else {
    print "No se encontró el usuario '$username' en la base de datos.\n";
}

# Cerrar la conexión a la base de datos
$dbh->disconnect();