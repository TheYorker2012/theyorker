package xml_csi;

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
	
	$self->{autofix} = 0;
	$self->{pedantic} = 0;
	
	return $self;
}

# Commonly used illegal attributes.
my @illegalAttributes = (
	[	'form',	'name'	],
	[	'td',	'width|height'	],
);

# Help information
sub printInformation
{
	my ($self) = @_;
	print "\tThis is the XML Coding Standards Inspection script\n";
	print "\t\tDetects use of single quotes for attributes\n";
}

sub validateConfiguration
{
	my ($self, $configuration) = @_;
	
	my $fail = 0;
	
	if (defined $configuration->{'xml_csi:autofix'}) {
		$self->{autofix} = 1;
	}
	if (defined $configuration->{'pedantic'}) {
		$self->{pedantic} = 1;
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
				# Single quote tag attributes
				if ($self->{pedantic} && $line =~ /^(?:[^']*(?:'(?:[^']|\\')*'))*[^']*<(\w+)(?:\s+\w+\s*=\s*"[^"]*")*\s+\w+\s*=\s*'[^']*'/) {
					$fail = 1;
					$self->printError($file, $lineno, "Single quote tag attributes are not permitted (in tag $1)");
# 					$self->printError($file, $lineno, "$line");
				}
				# Link to \ should be /
				if ($line =~ /href="\\"/) {
					$fail = 1;
					my $message = "Link to '\\'";
					if ($self->{autofix}) {
						$modified = 1;
						$line =~ s/href="\\"/href="\/"/g;
						$message .= ". FIXED";
					}
					$self->printError($file, $lineno, $message);
				}
				# Illegal attributes
				foreach my $illegalInfo (@illegalAttributes) {
					my ($tag, $attr) = @$illegalInfo;
					if ($line =~ /<($tag)(?:\s+\w+=(?:"[^"]*"|'[^']*'))*\s+($attr)=(?:"([^"]*)"|'([^']*)')/) {
						my $tag = $1;
						my $attr = $2;
						$fail = 1;
						my $value = defined($3) ? $3 : $4;
						$self->printError($file, $lineno, "there is no attribute \"$attr\" in the \"$tag\" tag (value given: '$value')");
					}
				}
				# &apos; xml entity unknown to internet explorer.
				if ($line =~ /&apos;/) {
					$fail = 1;
					my $message = "&apos; entity is not known to Internet Explorer";
					if ($self->{autofix}) {
						$modified = 1;
						$line =~ s/&apos;/&#039;/g;
						$message .= ". FIXED";
					}
					$self->printError($file, $lineno, $message);
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

