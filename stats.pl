#!/usr/bin/perl

use strict;
use warnings;

# Comando para obtener la carga de la CPU
my $cpu_load = `uptime`;

# Comando para obtener el uso de memoria
my $memory_usage = `free -h`;

# Comando para obtener el uso de disco
my $disk_usage = `df -h`;

# Comando para obtener los registros del sistema (últimas 10 líneas)
my $system_logs = `tail -n 10 /var/log/syslog`;

# Construir el cuerpo del correo electrónico
my $body = "Carga de CPU:\n$cpu_load\n\nUso de Memoria:\n$memory_usage\n\nUso de Disco:\n$disk_usage\n\nRegistros del Sistema:\n$system_logs";

# Escribir la información en el archivo stats.txt
open my $fh, '>', '/home/carldres/stats.txt' or die "No se pudo abrir el archivo: $!";
print $fh $body;
close $fh;