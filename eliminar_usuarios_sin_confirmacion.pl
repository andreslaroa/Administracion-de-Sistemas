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

# Establecer la conexión a la base de datos
my $dbh = DBI->connect("DBI:mysql:$db_name;host=$db_host;port=$db_port", $db_user, $db_pass)
    or die "Error de conexión a la base de datos: $DBI::errstr";

# Consulta para eliminar usuarios no confirmados
my $sql = "DELETE FROM registros WHERE confirmado = 0";
my $rows_affected = $dbh->do($sql);

# Cerrar la conexión a la base de datos
$dbh->disconnect();

# Imprimir el número de registros eliminados (opcional)
print "Se han eliminado $rows_affected usuarios no confirmados.\n";