package php_csi;

use test;

use vars qw(@ISA);
@ISA = ('test');

sub new
{
	my $proto = shift;
	my $class = ref($proto) || $proto;
	my $self = $proto->SUPER::new($proto);
	
	$self->{autofix} = 0;
	
	return $self;
}

# Help information
sub printInformation
{
	my ($self) = @_;
	print "\tThis is the PHP Coding Standards Inspection script\n";
	print "\t\tDetects PHP short tags\n";
	print "\tConfig options (use -c or --config)\n";
	print "\t\tphp_csi:autofix     Automatically fix CSI problems where possible\n";
}

sub validateConfiguration
{
	my ($self, $configuration) = @_;
	
	my $fail = 0;
	
	if (defined $configuration->{'php_csi:autofix'}) {
		$self->{autofix} = 1;
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
			# Load in the entire file
			my @lines = <$fildes>;
			close $fildes;
			my $modified = 0;
			
			# Do checks, potentially modify the memory copy
			my $lineno = 1;
			foreach my $line (@lines) {
				# PHP short tags
				if ($line =~ /<\?=?[\s>\$]/) {
					$fail = 1;
					if ($self->{autofix}) {
						$line =~ s/<\?=\s*/<?php echo /g;
						$line =~ s/<\?([\s])/<?php$1/g;
						$modified = 1;
						$self->printError($file, $lineno, "Short PHP tags are not permitted. FIXED");
					} else {
						$self->printError($file, $lineno, "Short PHP tags are not permitted");
					}
				}
				
				
				++$lineno;
			}
			
			# If modificiations have been made, write them to the file
			if ($modified) {
				if (open($fildes, ">$file")) {
					print $fildes join('', @lines);
					close($fildes);
				}
			}
		}
		return $fail;
	}
	else {
		return 0;
	}
}

1;

