#!/usr/bin/perl

use strict;
use warnings;

# The tests available
my $all_tests = {
	php_syntax      => "Perform a syntax check of all php files.",
	php_dependency  => "Perform a CI dependency check on php files.",
	php_csi         => "Perform a a coding standards inspection on all php files.",
	js_warnings     => "Show warnings for potential JS problems.",
};

my $all_test_sets = {
	#name  => [ description,
	#           [ subtests ],
	#           [ subsets  ]
	#         ],
	php    => [ "All PHP tests.",
	            [ 'php_syntax',
	              'php_dependency',
	              'php_csi', ],
	            [  ],
	          ],
	js     => [ "All Javascript tests.",
	            [ 'js_warnings',   ],
	            [  ],
	          ],
};

use Getopt::Long;
use FindBin qw($Bin);
use lib "$Bin/tests";

sub print_usage
{
	print "Usage:\n";
	print "  ./analyser.pl\n";
	print "    <branch>\n";
	print "    [-h|--help]               Show this help screen.\n";
	print "    [-l|--list-sets]          List test sets.\n";
	print "    [-L|--list-tests]         List tests.\n";
	print "    [-i|--information]        Show info about the specified tests.\n";
	print "    [-a|--all]                Run all tests.\n";
	print "    [-t|--test=<test>]        Specify a test to run.\n";
	print "    [-s|--set=<testset>]      Specify a test set to run.\n";
	print "    {-c|--config key[=val]}   Specify a custom option.\n";
	print "    [<<files>>]               Specify files.\n";
	return shift;
}

sub main
{
	my $opt_help        = 0;
	my $opt_list_sets   = 0;
	my $opt_list_tests  = 0;
	my $opt_information = 0;
	my $opt_all = 0;
	my @opt_tests;
	my @opt_sets;
	my @opt_configs;

	if (!GetOptions('h|help'        => \$opt_help,
	                'l|list-sets'   => \$opt_list_sets,
	                'm|list-tests'  => \$opt_list_tests,
	                'i|information' => \$opt_information,
	                'a|all'         => \$opt_all,
	                't|test=s'      => \@opt_tests,
	                's|set=s'       => \@opt_sets,
	                'c|config=s'    => \@opt_configs,))
	{
		exit print_usage 1;
	}
	
	if ($opt_help) {
		exit print_usage 0;
	}

	# List test sets
	if ($opt_list_sets) {
		my @test_set_ids = sort keys %$all_test_sets;
		print "Test sets (" . scalar @test_set_ids . ")\n";
		foreach my $test_set_id (@test_set_ids) {
			my $test_set = $all_test_sets->{$test_set_id};
			print "\t$test_set_id\t" . $test_set->[0] . "\n";
		}
	}

	# List tests
	if ($opt_list_tests) {
		my @test_ids = sort keys %$all_tests;
		print "Tests (" . scalar @test_ids . ")\n";
		foreach my $test_id (@test_ids) {
			print "\t$test_id\t" . $all_tests->{$test_id} . "\n";
		}
	}
	
	my $done_something = $opt_list_sets || $opt_list_tests;
	
	my $branch_path;
	if (!$opt_information) {
		if (!@ARGV) {
			if ($done_something) {
				exit 0;
			}
			else {
				print STDERR "Please specify the branch path\n";
				exit print_usage 0;
			}
		}
		else {
			$branch_path = shift @ARGV;
		}

		# branch path directory must exist
		$branch_path =~ s/^(.*[^\/])\/*$/$1/;
		if (!-d $branch_path) {
			print STDERR "Branch path \"$branch_path\" doesn't exist\n";
			exit print_usage 1;
		}
	}

	my $fail = 0;
	my %tests;
	my %test_sets;
	
	if (!@opt_tests && !@opt_sets) {
		$opt_all = 1;
	}
	
	if ($opt_all) {
		push @opt_tests, keys %$all_tests;
	}
	
	# Go through explicitly specified tests
	foreach my $test_id (@opt_tests) {
		if (defined $all_tests->{$test_id}) {
			$tests{$test_id} = 1;
		}
		else {
			$fail = 1;
			print STDERR "The test called \"$test_id\" could not be found\n";
		}
	}
	# Go through explicitly specified test sets
	# Allow for more to be pushed on the end
	while (@opt_sets) {
		my $test_set_id = shift @opt_sets;
		
		if (defined $all_test_sets->{$test_set_id}) {
			if (!defined $test_sets{$test_set_id}) {
				$test_sets{$test_set_id} = 1;
				# Add contained tests
				foreach my $test_id (@{$all_test_sets->{$test_set_id}->[1]}) {
					if (defined $all_tests->{$test_id}) {
						$tests{$test_id} = 1;
					}
					else {
						die("Test set \"$test_set_id\" contains the test \"$test_id\" which cannot be found.");
					}
				}
				# Add sub test sets
				foreach my $sub_test_set_id (@{$all_test_sets->{$test_set_id}->[2]}) {
					if (defined $all_test_sets->{$sub_test_set_id}) {
						push @opt_sets, $sub_test_set_id;
					}
					else {
						die("Test set \"$test_set_id\" uses the test set \"$sub_test_set_id\" which cannot be found.");
					}
				}
			}
		}
		else {
			$fail = 1;
			print STDERR "The test set called \"$test_set_id\" could not be found\n";
		}
	}
	
	# Perform the tests.
	if (!$fail) {
		# Read config options
		my $configuration = {};
		foreach my $config (@opt_configs) {
			if ($config =~ /([^=]*)=(.*)/) {
				$configuration->{$1} = $2;
			} else {
				$configuration->{$config} = 1;
			}
		}
		
		# Get files if they weren't specified
		my @files_to_analyse;
		if (@ARGV) {
			@files_to_analyse = @ARGV;
		}
		elsif (!$opt_information) {
			@files_to_analyse = `find "$branch_path"`;
			foreach my $filename (@files_to_analyse) {
				chomp($filename);
			}
		}
		
		foreach my $test_id (sort keys %tests) {
			require "$test_id.pm";
			my $test_mod = $test_id->new();
			if ($opt_information) {
				print "Information about test \"$test_id\":\n";
				$test_mod->printInformation;
				print "\n";
			}
			else {
				print "**************** $test_id ****************\n\n";
				if (!$test_mod->validateConfiguration($configuration)) {
					foreach my $file (@files_to_analyse) {
						if ($test_mod->runTest($branch_path, $file, $configuration)) {
							$fail = 1;
						}
					}
				}
				print "\n";
			}
		}
	}
	
	exit $fail;
}

main;

