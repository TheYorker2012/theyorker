package js_warnings;

use strict;
use warnings;

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
	print "\tThis test checks various files for potential mistakes.\n";
	print "\tThis includes:\n";
	print "\t\tChecking certain non-php files for erronous php tags\n";
}

sub runTest
{
	my ($self, $ci_root, $file, $configuration) = @_;
	
	if ($file =~ /\.(?:js|css)$/) {
		# Look for php tags not in quotes
		my $fildes;
		my $fail = 0;
		if (open($fildes, "<$file")) {
			my $lineno = 1;
			my $in_comment = 0;
			while (my $line = <$fildes>) {
				if ($line =~ /<\?/) {
					$fail = 1;
					$self->printError($file, $lineno, "Warning: PHP tags found in non PHP file");
				}
				
				my $temp_line = $line;
				$temp_line =~ s/\/\*(?:\*[^\/]|[^\*])*\*\///g;
				$temp_line =~ s/'*(?:\\'|[^'])*'//g;
				$temp_line =~ s/"*(?:\\"|[^"])*"//g;
				$temp_line =~ s/\/\/.*$//g;
				# Comment ending in this line
				if ($in_comment && $temp_line =~/^(?:\*[^\/]|[^\*])*\*\//) {
					$in_comment = 0;
					$temp_line =~ s/^(?:\*[^\/]|[^\*])*\*\///;
				}
				# Comment to the end of the line
				elsif (!$in_comment && $temp_line =~/\/\*(?:\*[^\/]|[^\*])*$/) {
					$in_comment = 1;
					$temp_line =~ s/\/\*(?:\*[^\/]|[^\*])*$//;
				}
				elsif ($in_comment) {
					$temp_line = '';
				}
				if ($temp_line =~ /\W(class)\W/) {
					if (defined $1) {
						$fail = 1;
						$self->printError($file, $lineno, "Warning: class is a reserved word in ecmascript and may cause it to fail in IE");
						$self->printError($file, $lineno, "line: $line");
					}
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

