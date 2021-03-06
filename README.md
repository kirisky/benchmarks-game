Public Website
--------------

https://benchmarksgame-team.pages.debian.net/benchmarksgame/

Toy-program performance measurements for ~24 language implementations.


The Benchmarks Game
===================

STOPPED. Updates will be infrequent. Once or twice a year.

Please, people ask to see more "idiomatic" programs —

* we already have enough exhaustively optimized Rust and C programs.
* we already have enough hand-written vector SIMD and "unsafe" programs. 

Thank you.

We still need transliterations of existing straightforward programs. For example, Ruby programs that use Ractors. For example, Go programs that use generics. 

If you are intent on adding something to the issue tracker, [please follow the guidelines](/CONTRIBUTING.md).


What else?
----------

* Where can I get the program source code? 
 — [zip'd program source code](/public/download/benchmarksgame-sourcecode.zip)

* Where can I get the measurement scripts? 
 — [bencher Python measurement scripts](/bencher)
![](/bencher/screenshot.png)

* Where can I get the program measurements? 
 — [.csv data files](/public/data/README.md) — with example conversion scripts for GFM and HTML

| spectralnorm | secs | mem | gz | cpu | 
| :------ | -----: | -----: | -----: | -----: |  
| java&nbsp;#&nbsp;1 | 5.03 | 36,964 | 514 | 5.14 |
| java&nbsp;#&nbsp;3 | 1.44 | 37,820 | 756 | 5.28 |
| java&nbsp;#&nbsp;2 | 1.44 | 36,880 | 950 | 5.29 |
| python&nbsp;#&nbsp;6 | 193.53 | 9,116 | 504 | 193.46 |
| python&nbsp;#&nbsp;7 | 52.17 | 50,092 | 619 | 205.93 |

&nbsp;  
* Where can I get the public website scripts? 
 — [mybenchmarks PHP website scripts](/mybenchmarks)
  
![](/mybenchmarks/performance.png)
![](/mybenchmarks/boxplotcpu.png)
![](/mybenchmarks/compare.png)
![](/mybenchmarks/measurements.png)
![](/mybenchmarks/program.png)

* Where can I get the old archived program source code? 
 — [Alioth program source code](https://salsa.debian.org/benchmarksgame-team/archive-alioth-benchmarksgame)

* If you have other questions then please open a new [Question](https://salsa.debian.org/benchmarksgame-team/benchmarksgame/issues/new?issuable_template=Question) issue.

Suggestions?
------------

If you wish to suggest some kind of change then please open a new [Change](https://salsa.debian.org/benchmarksgame-team/benchmarksgame/issues/new?issuable_template=Change) issue.



"The best way to complain is to make things."
---------------------------------------------



