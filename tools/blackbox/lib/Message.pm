package Message;

use strict;
use warnings;

use Term::ANSIColor;

my %colourMap = (
	ok => 'green',
	error => 'red',
	warning => 'yellow',
	validation_error => 'magenta',
	validation_warning => 'cyan',
);

sub colourMap
{
	my $type = shift;
	return $colourMap{$type};
}

sub new
{
	my $proto = shift;
	my $class = ref($proto) || $proto;
	my $self = {};
	my ($line, $message) = @_;
	
	$self->{line} = $line;
	$self->{message} = $message;
	
	bless ($self, $class);
	return $self;
}

sub type
{
	my $self = shift;
	my ($newType) = @_;
	if (defined $newType) {
		$self->{type} = $newType;
	}
	return $self->{type};
}

sub colour
{
	my $self = shift;
	my ($newColour) = @_;
	if (defined $newColour) {
		$self->{colour} = $newColour;
	}
	if (defined $self->{colour}) {
		return $self->{colour};
	}
	else {
		return $colourMap{$self->{type}};
	}
}

sub message
{
	my $self = shift;
	return $self->{message};
}

sub line
{
	my $self = shift;
	return $self->{line};
}

sub print
{
	my $self = shift;
	
	my $color = $self->colour;
	if (defined $color) {
		print color($color);
	}
	print "\t".$self->line.":\t".$self->message;
	if (defined $color) {
		print color('reset');
	}
	print "\n";
}


1;

