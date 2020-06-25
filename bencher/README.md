The Benchmarks Game — bencher
=============================

Overview
--------
* bencher is an example of how to use the benchmarks game Python 2 program measurement scripts to make the content for the mybenchmarks [PHP website scripts](../mybenchmarks).

* Different [.ini files](makefiles) allow different sets of measurements to be separated into different folders.
  You really are expected to copy/edit the ini file.

* These scripts are designed to make content for the benchmarks game website — marked-up program source code files, program log files, multi-second run times, results checking, two-dozen language implementations, a thousand programs — that might not be what you need! (Perhaps something like [hyperfine](https://github.com/sharkdp/hyperfine) would match your needs?)

* OTOH perhaps making something like the benchmarks game website is exactly what you would like to do, for example — [https://pybenchmarks.org/](https://pybenchmarks.org/)
   
Background
----------

* These Python 2 scripts were originally written because there didn't seem to be a way to set processor affinity with the previous Perl scripts (which was a problem for single core measurements on a quad-core machine).

Example
-------
![](/bencher/screenshot.png)


Gotchas
-------

* Even if javac and java and python are on the path — you must say that in the ini file! 
  You are expected to copy/edit the [ini file](makefiles/my.linux.ini) and corresponding [Makefile](makefiles/my.linux.Makefile), and make explicit everything: from - use PATH to find CPython - to how often a test is run.


Usage
-----

1. Try to make new measurements using the example data. 

1. Once you have that working, replace the example data with your data. Delete the contents of [programs](programs), [run_logs](run_logs), [run_markup](run_markup), [summary](summary), [tmp](tmp); and replace with your program folders and programs.

1. Copy one of the [.ini files](makefiles/my.linux.ini) and copy the matching [.Makefile](makefiles/my.linux.Makefile) and edit as required (see the [original detailed instructions](README)).





