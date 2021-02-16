Public Website
--------------

https://benchmarksgame-team.pages.debian.net/benchmarksgame/

Toy-program performance measurements for ~24 language implementations.


The Benchmarks Game
===================

Previously new source-code was usually measured and shown on the website within a few days. Not now. Probably not in the future.

Now new source-code might be measured and shown on the website after several months; or not measured and not shown.

As-of July 2020, new measurements were made on a 2012 i5-3330.

On that UEFI / Secure Boot hardware, it has proved very difficult to keep Ubuntu working as a dual-boot. After using Windows 10, Ubuntu often fails to boot. So far the only reliable remedy is to re-install Ubuntu from DVD — that really isn't practical.


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



