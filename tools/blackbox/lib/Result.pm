package Result;

use strict;
use warnings;

use Term::ANSIColor;

sub new
{
	my $proto = shift;
	my $class = ref($proto) || $proto;
	my $self = {};
	
	$self->{url} = shift;
	$self->{links} = {};
	$self->{messages} = [];
	
	bless ($self, $class);
	return $self;
}

sub result
{
	my $self = shift;
	my ($newResult) = @_;
	if (defined $newResult) {
		$self->{result} = $newResult;
	}
	return $self->{result};
}

sub message
{
	my $self = shift;
	my ($message) = @_;
	push @{$self->{messages}}, $message;
}

sub messages
{
	my $self = shift;
	return $self->{messages};
}

sub url
{
	my $self = shift;
	return $self->{url};
}

sub valid
{
	my $self = shift;
	my ($newValid) = @_;
	if (defined $newValid) {
		$self->{valid} = $newValid;
	}
	return $self->{valid};
}

sub links
{
	my $self = shift;
	my @newLinks = @_;
	foreach my $link (@newLinks) {
		$self->{links}{$link} = 1;
	}
	return keys %{$self->{links}};
}

sub redirect
{
	my $self = shift;
	my ($newRedirect) = @_;
	if (defined $newRedirect) {
		$self->{redirect} = $newRedirect;
	}
	return $self->{redirect};
}

sub print
{
	my $self = shift;
	
	my $status = "[ ".color('green').'OK'.color('reset')." ]";
	if (!$self->result->is_success) {
		my $statusLin = $self->result->status_line;
		my $colour = 'red';
		$status = "[ ".color($colour).$statusLin.color('reset')." ]";
	}
	if (defined $self->valid) {
		my $validationStatus;
		my $colour;
		if ($self->valid) {
			$validationStatus = 'VALID';
			$colour = Message::colourMap('validation_warning');
		}
		else {
			$validationStatus = 'INVALID';
			$colour = Message::colourMap('validation_error');
		}
		$status .= " [ ".color($colour).$validationStatus.color('reset')." ]";
	}
	else {
		$status .= " [ ".color(Message::colourMap('validation_warning')).'Could not validate'.color('reset')." ]";
	}
	my $url = $self->url." ";
	while (length($url) < 100) {
		$url .= '-';
	}
	print $url." $status\n";
	if ($self->result->is_success) {
		my @messages = @{$self->messages};
		foreach my $message (@messages) {
			$message->print;
		}
	}
}

1;

