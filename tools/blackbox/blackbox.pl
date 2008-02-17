#!/usr/bin/perl

use strict;
use warnings;

use Getopt::Long;
use LWP::UserAgent;
use HTML::Entities;
use Term::ANSIColor;
use WebService::Validator::HTML::W3C;

use FindBin qw($Bin);
use lib "$Bin/lib";

use Result;
use Message;

use constant USER_AGENT_ID => 'YorkerTestBot/0.1';

# Set up user agent
my $userAgent = LWP::UserAgent->new;
$userAgent->agent(USER_AGENT_ID);
# Allow redirect from post data
push @{ $userAgent->requests_redirectable }, 'POST';

# Set up validation
my $validator = WebService::Validator::HTML::W3C->new(
	'validation_uri' => 'http://localhost/w3c-validator/check',
	'detailed' => 1,
	'ua' => $userAgent,
);

sub processUrl
{
	my ($host, $url, $result, $lineno) = @_;
	
	if ($url !~ /^([\w]+):\/\//) {
		if ($url !~ /^[\/#]/) {
			if (defined $result) {
				my $msg = Message->new($lineno, "Relative href found: $url");
				$msg->type('warning');
				$result->message($msg);
			}
			$url = '';
		}
		$url = "http://$host$url";
	}
	$url =~ s/^([^#]*)#.*$/$1/;
	return $url;
}

sub testUrl
{
	my ($host, $url, $validate) = @_;
	
	my $req = HTTP::Request->new(POST => "$url");
	
	my $res = $userAgent->request($req);
	
	my $result = Result->new($url);
	
	$result->result($res);
	
	if ($res->is_success) {
		my $xhtml = $res->content;
		
		# Get links from this page.
		my @xhtml = split("\n", $xhtml);
		my $lineno = 1;
		foreach my $line (@xhtml) {
			# Get links
			my @hrefs = ($line =~ /<a href="([^"]*)">/g);
			my @links;
			foreach my $link (@hrefs) {
				if ($link =~ /^mailto:/i) {
				}
				else {
					$link = decode_entities($link);
					$link = processUrl($host, $link, $result, $lineno);
					push @links, $link;
				}
			}
			$result->links(@links);
			
			# Get double escapings
			if ($line =~ /&amp;amp/) {
				my $msg = Message->new($lineno, "Escaped XML entity");
				$msg->type('error');
				$result->message($msg);
			}
			
			++$lineno;
		}
		
		# Validate
		if ( $validate and $validator->validate_markup($xhtml) ) {
			$result->valid($validator->is_valid);
			my $errors = $validator->errors;
			if (ref $errors) {
				foreach my $error ( @$errors ) {
					my $msg = Message->new($error->line, $error->msg);
					$msg->type('validation_error');
					$result->message($msg);
				}
			}
			my $warnings = $validator->warnings;
			if (ref $warnings) {
				foreach my $warning ( @$warnings ) {
					my $msg = Message->new($warning->line, $warning->msg);
					$msg->colour('validation_warning');
					$result->message($msg);
				}
			}
		}
	}
	
	$result->print;
	
	return $result;
}

sub linkCheck
{
	my ($host, $starts, $excludes, $maxDepth, $validate) = @_;
	
	my %testedLinks;
	my @pendingLinks = @$starts;
	foreach my $link (@pendingLinks) {
		$link = processUrl($host, $link);
	}
	for my $depthLevel (0..$maxDepth) {
		print "\n".color('blue')."STARTING DEPTH $depthLevel (".scalar(@pendingLinks).")".color('reset')."\n";
		my %newLinks;
		while (@pendingLinks) {
			my $link = shift @pendingLinks;
			my $acceptable = 1;
			foreach my $exclusion (@$excludes) {
				if ($link =~ $exclusion) {
					$acceptable = 0;
					last;
				}
			}
			if ($acceptable) {
				my $result = testUrl($host, $link, $validate);
				$testedLinks{$link} = $result;
				my $redirect = $result->redirect;
				if (defined $redirect) {
					if (!defined $testedLinks{$redirect}) {
						unshift(@pendingLinks, $redirect);
					}
				}
				else {
					if ($link =~ /^https?:\/\/([^\/]*)(.*)$/) {
						my $linkHost = $1;
						if ($linkHost eq $host) {
							foreach my $newLink ($result->links) {
								if (!defined $testedLinks{$newLink}) {
									$newLinks{$newLink} = 1;
								}
							}
						}
					}
				}
			}
		}
		@pendingLinks = sort keys %newLinks;
	}
	
}

sub print_usage
{
	print "The Yorker Blackbox Testing Script\n";
	print "  Written by James Hogan <james_hogan\@theyorker.co.uk>\n";
	print "  Copyright (C) 2008 The Yorker Ltd.\n";
	print "Usage:\n";
	print "  ./blackbox.pl\n";
	print "    -h|--host <host>      - Hostname to test\n";
	print "    {initial paths}       - A set of initial paths to start the test\n";
	print "    [-d|--depth <depth>]  - Set the maximum depth to search\n";
	print "    [-v|--validate]       - W3C validation using http://localhost/w3c-validator/check\n";
	print "Examples:\n";
	print "  ./blackbox.pl --validate --depth=2 --host=release.dev.theyorker.co.uk /calendar\n";
	print "  ./blackbox.pl --validate --host=www.theyorker.co.uk /charity\n";
}

sub main
{
	my $configDepth = 0;
	my $configValidate = 0;
	my $configHost;
	
	# Get configuration
	if (!GetOptions(
		'd|depth=i'   => \$configDepth,
		'v|validate!' => \$configValidate,
		'h|host=s'     => \$configHost,
	)) {
		# Invalid configuration
		print_usage;
		return 1;
	}
	
	# No host?
	if (!defined $configHost) {
		print_usage;
		exit 1;
	}
	# Valid depth?
	if ($configDepth < 0 || $configDepth > 10) {
		print STDERR "Depth out of range\n";
		print_usage;
		exit 1;
	}
	
	# No arguments?
	my @startPaths = @ARGV;
	if (!@startPaths) {
		@startPaths = ('/');
	}
	
	linkCheck(
		$configHost,
		\@startPaths,
		[qr/(static|yorkipedia)\.theyorker\.co\.uk/],
		$configDepth,
		$configValidate
	);
}

exit main;

