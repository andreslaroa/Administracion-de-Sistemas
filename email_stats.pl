#!/usr/bin/perl

use strict;
use warnings;

# Leer el contenido del archivo stats.txt
open(my $stats_fh, '<', '/home/carldres/stats.txt') or die "No se pudo abrir el archivo stats.txt: $!";
my $stats_content = do { local $/; <$stats_fh> };
close($stats_fh);

# Configurar destinatario y asunto del correo electrónico
my $to = 'carldres004@gmail.com';
my $subject = 'Estadísticas del sistema';

# Enviar el correo electrónico con el contenido del archivo stats.txt
open (MAIL, "|/usr/sbin/sendmail -t");
print MAIL "To: $to\n";
print MAIL "Subject: $subject\n\n";
print MAIL $stats_content;
close (MAIL);

# Vaciar el archivo stats.txt sin borrarlo
open(my $fh, '>', '/home/carldres/stats.txt') or die "No se pudo abrir el archivo stats.txt: $!";
print $fh "";  # Escribir una cadena vacía
close($fh);