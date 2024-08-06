#!/usr/bin/perl

use strict;
use warnings;
use JSON;

# Ejecutar el comando systemctl y capturar la salida
my $output = `systemctl list-unit-files --type=service`;

# Procesar la salida para obtener los nombres de los servicios y su estado
my @lines = split /\n/, $output;
my %services;

foreach my $line (@lines) {
    next unless $line =~ /\S/;  # Ignorar líneas vacías
    my ($service, $state) = ($line =~ /^\s*([\w\-\.]+)\s+(\w+)/);
    next unless $service && $state;
    $services{$service} = ($state eq 'enabled') ? 'active' : 'inactive';
}

# Preparar los datos para salida JSON
my @active_services = grep { $services{$_} eq 'active' } keys %services;
my @inactive_services = grep { $services{$_} eq 'inactive' } keys %services;

my %result = (
    active   => \@active_services,
    inactive => \@inactive_services
);

# Imprimir los datos como JSON
print "Content-Type: application/json\n\n";
print encode_json(\%result);