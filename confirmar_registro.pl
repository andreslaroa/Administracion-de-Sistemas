#!/usr/bin/perl

use strict;
use warnings;
use CGI;
use DBI;

# Crear instancia CGI para procesar la solicitud HTTP
my $cgi = CGI->new;

# Obtener el token ingresado desde el formulario HTML
my $token = $cgi->param('token');

# Conectar a la base de datos
my $db_name = 'usuarios';   # Nombre de tu base de datos
my $db_host = 'localhost';  # Host de tu base de datos
my $db_user = 'carldres';   # Usuario de tu base de datos
my $db_pass = 'hornazo';    # Contraseña de tu base de datos

my $dbh = DBI->connect("DBI:mysql:$db_name:$db_host", $db_user, $db_pass)
    or die "Error al conectar a la base de datos: $DBI::errstr";

# Verificar si el token existe en la base de datos y el usuario está sin confirmar
my $sth = $dbh->prepare("SELECT * FROM registros WHERE token = ? AND confirmado = 0");
$sth->execute($token);

if (my $user_data = $sth->fetchrow_hashref) {
    # Usuario encontrado: actualizar el estado de confirmación
    my $username = $user_data->{'username'};

    # Actualizar el campo confirmado a 1
    my $update_sth = $dbh->prepare("UPDATE registros SET confirmado = 1 WHERE token = ?");
    $update_sth->execute($token);

    # Mostrar mensaje de confirmación
    print $cgi->header('text/html'),
          $cgi->start_html('Registro Confirmado'),
          $cgi->h2('Registro Confirmado'),
          $cgi->p("¡Felicidades, $username! Tu registro ha sido confirmado."),
          $cgi->end_html;
} else {
    # No se encontró ningún usuario con el token dado o ya confirmado
    print $cgi->header('text/html'),
          $cgi->start_html('Error de Confirmación'),
          $cgi->h2('Error de Confirmación'),
          $cgi->p('No se pudo confirmar el registro. El token proporcionado es inválido o ya ha sido utilizado.'),
          $cgi->end_html;
}

# Cerrar la conexión a la base de datos
$dbh->disconnect;