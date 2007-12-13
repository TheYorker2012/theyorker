package js_warnings;

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

sub runTest
{
	my ($self, $ci_root, $file) = @_;
	
	if ($file =~ /\.(?:js|css)$/) {
		# Look for php tags not in quotes
		my $fildes;
		my $fail = 0;
		if (open($fildes, "<$file")) {
			my $lineno = 1;
			while (my $line = <$fildes>) {
				if ($line =~ /<\?/) {
					$fail = 1;
					$self->printError($file, $lineno, "Warning: PHP tags found in non PHP file");
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

