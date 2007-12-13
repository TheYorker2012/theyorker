package php_csi;

use test;

use vars qw(@ISA);
@ISA = ('test');

sub new
{
	my $proto = shift;
	my $class = ref($proto) || $proto;
	my $self = $proto->SUPER::new($proto);
	return $self;
}

# Help information
sub printInformation
{
	my ($self) = @_;
	print "\tThis is the PHP Coding Standards Inspection script\n";
	print "\t\tDetects PHP short tags\n";
}

sub validateConfiguration
{
	my ($self, $configuration) = @_;
	
	my $fail = 0;
	
	if (defined $configuration->{'php_csi:autofix'}) {
		print STDERR "Autofix not implemented\n";
		$fail = 1;
	}
	
	return $fail;
}

sub runTest
{
	my ($self, $ci_root, $file, $configuration) = @_;
	
	if ($file =~ /\.php$/) {
		my $fildes;
		my $fail = 0;
		if (open($fildes, "<$file")) {
			my $lineno = 1;
			while (my $line = <$fildes>) {
				# PHP short tags
				if ($line =~ /<\?(...)?/ && (!defined $1 || $1 ne 'php')) {
					$fail = 1;
					$self->printError($file, $lineno, "Short PHP tags are not permitted");
				}
				
				
				++$lineno;
			}
			close($fildes);
		}
		return $fail;
	}
	else {
		return 0;
	}
}

1;

