The Benchmarks Game — bencher
==================================

Overview
--------
* bencher is an example of how to use the benchmarks game Python program measurement scripts to make the content for the mybenchmarks [PHP website scripts](../mybenchmarks).

* Different [.ini files](makefiles) allow different sets of measurements to be separated into different folders.

   
Background
----------

* These Python scripts were originally written because there didn't seem to be a way to set processor affinity with the previous Perl scripts (which was a problem for single core measurements on a quad-core machine).

* These scripts are designed to make content for the benchmarks game website — marked-up program source code files, program log files, multi-second run times, results checking, two-dozen language implementations, a thousand programs — that might not be what you need! 


Example
-------
![](/bencher/screenshot.png)


Gotchas
-------



Usage
-----

1. Try to make new measurements using the example data. 

1. Once you have that working, replace the example data with your data. Delete the contents of [programs](programs), [run_logs](run_logs), [run_markup](run_markup), [summary](summary), [tmp](tmp); and replace with your program folders and programs.

1. Copy one of the [.ini files](makefiles) and copy the matching [.Makefile](makefiles) and edit as required (see the [original detailed instructions](README)).





