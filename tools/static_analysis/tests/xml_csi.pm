package xml_csi;

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
	print "\tThis is the XML Coding Standards Inspection script\n";
	print "\t\tDetects use of single quotes for attributes\n";
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
				# Single quote tag attributes
				if ($line =~ /^([^']*('([^']|\\')*'))*[^']*<(\w+)(\s+\w+\s*=\s*"[^"]*")*\s+\w+\s*=\s*'[^']*'/) {
					$fail = 1;
					$self->printError($file, $lineno, "Single quote tag attributes are not permitted (in tag $1)");
# 					$self->printError($file, $lineno, "$line");
				}
				# Find attributes with spaces on either side of =
# 				if ($line =~ /^([^']*('([^']|\\')*'))*[^']*<(\w+)(\s+\w+\s*=\s*"[^"]*")*\s+'[^']*'/) {
				
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

