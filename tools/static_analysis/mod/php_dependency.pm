package php_dependency;

use test;

use vars qw(@ISA);
@ISA = ('test');

my $load_handlers = {
  library => \&checkForLibrary,
  model   => \&checkForModel,
  helper  => \&checkForHelper,
  view    => \&checkForView,
};

my $load_paths = {
  library => [ 'system/application/libraries', 'system/libraries', ],
  model   => [ 'system/application/models',    ],
  helper  => [ 'system/application/helpers',   'system/helpers',   ],
  view    => [ 'system/application/views',     ],
};

my $load_exceptions = {
  library => { 'Database'=>1, },
  model   => {  },
  helper  => {  },
  view    => {  },
};

sub checkForLibrary
{
  my ($base, $name) = @_;
  $name = ucfirst lc $name;
  if (defined $load_exceptions->{library}->{$name}) {
    return 0;
  }
  $name .= ".php";
  foreach my $path (@{$load_paths->{library}}) {
    if (-e "$base/$path/$name") {
      return 0;
    }
  }
  return 1;
}

sub checkForModel
{
  my ($base, $name) = @_;
  $name = lc $name;
  if (defined $load_exceptions->{model}->{$name}) {
    return 0;
  }
  $name .= ".php";
  foreach my $path (@{$load_paths->{model}}) {
    if (-e "$base/$path/$name") {
      return 0;
    }
  }
  return 1;
}

sub checkForHelper
{
  my ($base, $name) = @_;
  $name = lc $name;
  if (defined $load_exceptions->{helper}->{$name}) {
    return 0;
  }
  my $filename .= $name."_helper.php";
  foreach my $path (@{$load_paths->{helper}}) {
    if (-e "$base/$path/$filename") {
      return 0;
    }
  }
  return 1;
}

sub checkForView
{
  my ($base, $name) = @_;
  if (defined $load_exceptions->{view}->{$name}) {
    return 0;
  }
  $name .= ".php";
  foreach my $path (@{$load_paths->{view}}) {
    if (-e "$base/$path/$name") {
      return 0;
    }
  }
  return 1;
}

# Constructor
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
  print "\tThis test searches php files for CI load statements.\n";
  print "\tAny attempts to load files that do not exist cause the test to fail.\n";
  print "\t\n";
  print "\tThe test supports:\n";
  print "\t\tCode igniter load->(library|helper|view|model) statements with string literals.\n";
  print "\t\tMainFrame SetContentSimple statements with string literals.\n";
}

# Main test function
sub runTest
{
  my ($self, $ci_root, $file) = @_;
  
  if ($file =~ /\.php$/) {
    # Look for CI loads
    my $fildes;
    my $fail = 0;
    if (open($fildes, "<$file")) {
      my $lineno = 1;
      while (my $line = <$fildes>) {
        my $type;
        my $module;
        if ($line =~ /->load->(\w+)\(([^),]*)\)/) {
          $type = $1;
          $module = $2;
        }
        elsif ($line =~ /->SetContentSimple\(([^),]*)/) {
          $type = 'view';
          $module = $1;
        }
        if (defined $type && defined $module) {
          if (defined $load_paths->{$type}) {
            if (my @strings = $module =~ /^\s*(?:'([^']*)'|"([^"]*)")\s*$/) {
              my $module_name;
              foreach my $string (@strings) {
                if (defined $string) {
                  $module_name = $string;
                  last;
                }
              }
              if (defined $module_name) {
                my $no_exist = $load_handlers->{$type}->($ci_root, $module_name);
                if ($no_exist) {
                  $self->printError($file, $lineno, "Reference to non-existant $type \"$module_name\"");
                  $fail = 1;
                }
              }
            }
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