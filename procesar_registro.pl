#!/usr/bin/perl

use strict;
use warnings;
use CGI;
use DBI;
use Mail::Sendmail;
use UUID::Tiny ':std';

# Establecer la variable PATH para incluir /usr/bin
$ENV{PATH} = "/usr/bin";

# Crear instancia CGI para procesar los datos del formulario
my $cgi = CGI->new;

# Obtener datos del formulario
my $username = $cgi->param('username');
my $email = $cgi->param('email');
my $password = $cgi->param('password');
my $name = $cgi->param('firstname');
my $last_name = $cgi->param('lastname');
my $address = $cgi->param('postal_email');

# Verificar si el usuario o correo electrónico ya existe en la base de datos
my $db_name = 'usuarios';     # Nombre de tu base de datos
my $db_host = 'localhost';    # Host de tu base de datos
my $db_user = 'carldres';     # Usuario de tu base de datos
my $db_pass = 'hornazo';      # Contraseña de tu base de datos

my $dbh = DBI->connect("DBI:mysql:$db_name:$db_host", $db_user, $db_pass)
    or die "Error al conectar a la base de datos: $DBI::errstr";

# Consulta para verificar si el usuario existe y su confirmación es 0
my $check_user_query = $dbh->prepare("SELECT username, email, confirmado FROM registros WHERE username = ? OR email = ?");
$check_user_query->execute($username, $email);

my $user_data = $check_user_query->fetchrow_hashref;

if ($user_data) {
    my $confirmado = $user_data->{'confirmado'};
    
    if ($confirmado == 0) {
        # Usuario existe y no está confirmado: actualizar la información del usuario
        my $token = generate_unique_token();
        my $update_user_query = $dbh->prepare("UPDATE registros SET username = ?, email = ?, password = ?, token = ?, name = ?, last_name = ?, address = ? WHERE username = ? or email = ?");
        $update_user_query->execute($username, $email, $password, $token, $name, $last_name, $address, $username, $email)
            or die "Error al actualizar el usuario en la base de datos: " . $update_user_query->errstr;

        # Enviar correo electrónico de confirmación
        send_confirmation_email($username, $email, $token);

        # Mostrar mensaje de actualización exitosa al usuario
        print $cgi->header('text/html'),
              $cgi->start_html('Actualización de Registro'),
              $cgi->h2('Actualización Exitosa'),
              $cgi->p('La información del usuario ha sido actualizada correctamente. Se ha enviado un correo electrónico de confirmación a tu dirección.'),
              $cgi->end_html;
    } else {
        # Usuario existe y está confirmado: mostrar mensaje de error
        print $cgi->header('text/html'),
              $cgi->start_html('Error de Confirmación'),
              $cgi->h2('Error de Confirmación'),
              $cgi->p('El usuario ya está confirmado en el sistema.'),
              $cgi->end_html;
    }
} else {
    # Insertar un nuevo usuario en la base de datos
    my $token = generate_unique_token();
    my $confirm_url = "http://34.16.182.84:80/confirmar_registro.html";

    # Construir mensaje de correo electrónico
    my $subject = "Confirmación de Registro en Carldres";
    my $message = <<"END_MESSAGE";
Hola $username,

Gracias por registrarte en Carldres. Para completar tu registro, por favor haz clic en el siguiente enlace y pega el token proporcionado:

$confirm_url

Token: $token

Si no has solicitado este registro, por favor ignora este mensaje.

Saludos,
Equipo de Carldres
END_MESSAGE

    # Configurar parámetros de envío de correo electrónico
    my %mail = (
        To      => $email,
        From    => 'noreply@carldres.com',
        Subject => $subject,
        Message => $message
    );

    # Enviar el correo electrónico
    sendmail(%mail) or die "Error al enviar el correo electrónico: $Mail::Sendmail::error";

    # Insertar el usuario en la base de datos
    my $insert_user = $dbh->prepare("INSERT INTO registros (username, email, password, confirmado, token, name, last_name, address) VALUES (?, ?, ?, 0, ?, ?, ?, ?)");
    $insert_user->execute($username, $email, $password, $token, $name, $last_name, $address)
        or die "Error al insertar usuario en la base de datos: " . $insert_user->errstr;

    # Mostrar mensaje de confirmación al nuevo usuario
    print $cgi->header('text/html'),
          $cgi->start_html('Confirmación de Registro'),
          $cgi->h2('Registro Exitoso'),
          $cgi->p('Se ha enviado un correo electrónico de confirmación a tu dirección de correo electrónico. Por favor verifica tu correo electrónico para completar el registro.'),
          $cgi->end_html;
}

# Cerrar la conexión a la base de datos
$dbh->disconnect;

# Función para generar un token único
sub generate_unique_token {
    my $uuid = create_uuid_as_string(UUID_V4);
    return $uuid;
}

# Función para enviar el correo de confirmación
sub send_confirmation_email {
    my ($username, $email, $token) = @_;

    my $confirm_url = "http://34.16.182.84:80/confirmar_registro.html";

    # Construir mensaje de correo electrónico
    my $subject = "Confirmación de Registro en Carldres";
    my $message = <<"END_MESSAGE";
Hola $username,

Gracias por registrarte en Carldres. Para completar tu registro, por favor haz clic en el siguiente enlace y pega el token proporcionado:

$confirm_url

Token: $token

Si no has solicitado este registro, por favor ignora este mensaje.

Saludos,
Equipo de Carldres
END_MESSAGE

    # Configurar parámetros de envío de correo electrónico
    my %mail = (
        To      => $email,
        From    => 'noreply@carldres.com',
        Subject => $subject,
        Message => $message
    );

    # Enviar el correo electrónico
    sendmail(%mail) or die "Error al enviar el correo electrónico: $Mail::Sendmail::error";
}