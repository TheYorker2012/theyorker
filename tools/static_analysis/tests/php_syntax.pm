package php_syntax;

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
	print "\tThis simply runs the PHP syntax checker on all PHP files\n";
}

sub runTest
{
	my ($self, $ci_root, $file, $configuration) = @_;
	
	if ($file =~ /\.php$/) {
		my $stdout = `php --syntax-check "$file"`;
		my $result = $? << 8;
		return $result;
	}
	else {
		return 0;
	}
}

1;

