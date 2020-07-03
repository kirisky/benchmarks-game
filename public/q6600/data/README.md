data files
==========

The program measurements are stored in plain text, comma separated value, spreadsheet compatible files - with a column-header row:

- name — the label used to identify one of the tasks
- lang — the label used to identify one of the language implementations
- id — the label used to identify a specific program that implements a task for a language implementation
- n — the workload
- size(B) — the size in bytes of the GZip compressed source-code file
- cpu(s) — the `usr+sys rusage` seconds
- mem(KB) — `GLIBTOP_PROC_MEM_RESIDENT` 
- status — negative values indicate the program failed in some way
- load — the proportion of `GTop cpu not-idle` to `GTop cpu total` for each core
- elapsed(s) — the elapsed `time.time()` seconds
- busy(s) — total `GTop cpu not-idle` elapsed seconds from all cores


There are example Python conversion scripts for [GFM](/public/data/makemd.py) and [HTML](/public/data/makehtml.py).

| spectralnorm | secs | mem | gz | cpu | 
| :------ | -----: | -----: | -----: | -----: |  
| python&nbsp;#&nbsp;6 | 193.53 | 9,116 | 504 | 193.46 |
| java&nbsp;#&nbsp;2 | 1.44 | 36,880 | 950 | 5.29 |
| java&nbsp;#&nbsp;1 | 5.03 | 36,964 | 514 | 5.14 |
| java&nbsp;#&nbsp;3 | 1.44 | 37,820 | 756 | 5.28 |
| python&nbsp;#&nbsp;7 | 52.17 | 50,092 | 619 | 205.93 |


data.csv
--------

Just the fastest measurements at the largest measured workloads.

ndata.csv
---------

Just the fastest measurements at all measured workloads.


alldata.csv
-----------

All measurements, including repeated measurements.


How programs are measured
-------------------------

Please refer to the benchmarks game website for [more details](https://benchmarksgame-team.pages.debian.net/benchmarksgame/how-programs-are-measured.html) of the measurement process, [task descriptions](https://benchmarksgame-team.pages.debian.net/benchmarksgame/description/summary.html), and comparisons of [program measurements](https://benchmarksgame-team.pages.debian.net/benchmarksgame/which-programs-are-fastest.html).


