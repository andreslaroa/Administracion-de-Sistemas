#!/usr/bin/perl
use strict;
use warnings;
use CGI;
use DBI;

# Conectar a la base de datos (ajustar según tus credenciales)
my $dbname = 'usuarios';
my $dbuser = 'carldres';
my $dbpass = 'hornazo';
my $dbh = DBI->connect("DBI:mysql:$dbname", $dbuser, $dbpass)
    or die "Error de conexión a la base de datos: $DBI::errstr";

# Obtener datos del formulario
my $cgi = CGI->new;
my $username = $cgi->param('username');
my $password = $cgi->param('password');
my $email = $cgi->param('email');

# Insertar datos en la base de datos
my $insert_sql = "INSERT INTO registros (username, password, email) VALUES (?, ?, ?)";
my $sth = $dbh->prepare($insert_sql);
$sth->execute($username, $password, $email)
    or die "Error al insertar en la base de datos: $DBI::errstr";
$sth->finish;

# Crear usuario en el sistema Debian
 my $useradd_cmd = "sudo useradd -m -s /bin/bash $username";
 system($useradd_cmd) == 0
     or die "Error al crear usuario en el sistema Debian";

# Establecer contraseña del usuario
 my $passwd_cmd = "echo '$username:$password' | sudo chpasswd";
 system($passwd_cmd) == 0
     or die "Error al establecer la contraseña del usuario";

 print "Content-type: text/html\n\n";
 print "Registro exitoso";

# Cerrar la conexión a la base de datos
$dbh->disconnect;
