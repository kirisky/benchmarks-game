STOPPED. Updates will be infrequent. Once or twice a year.


Contribute source-code for measurement
--------------------------------------

Please, people ask to see more "idiomatic" programs —

* we already have enough exhaustively optimized Rust and C programs.
* we already have enough hand-written vector SIMD and "unsafe" programs. 

Thank you.

We still need transliterations of existing straightforward programs. For example, transliterations of multicore JavaScript into type-annotated TypeScript. 

What to contribute
------------------

Programs for programming language implementations that are already shown on the website. We are unlikely to add other language implementations.

Complete tested source-code for one program: in one uncompressed source-code file, attached to one tracker issue.

Please do not contribute multiple programs or multiple versions of a program in one source-code file — they will not be accepted.

Please do not contribute multiple source-code files attached to one tracker issue — they will not be accepted.

Please do not contribute patch files — they will not be accepted.


Style Guide
-----------

Write narrow 80 column programs — plenty of people try to read these programs on a phone. 

You will probably come across people saying that the programs are not idiomatic ("enough"). So read the [description](https://benchmarksgame-team.pages.debian.net/benchmarksgame/description/summary.html) and write your own idiomatic program, without programming tricks. 

Please use code comments to demonstrate that the algorithm is comparable to programs that are currently shown, and matches the provided description.


Test your program!
------------------
Check that program-output matches expected-output before upload.

Use `diff` to check that whitespace characters match.

`diff` program-output with the output-file provided in the [description](https://benchmarksgame-team.pages.debian.net/benchmarksgame/description/summary.html).


Upload a complete tested source-code file
-----------------------------------------

Open a new [Contribute Source Code](https://salsa.debian.org/benchmarksgame-team/benchmarksgame/issues/new?issuable_template=Contribute%20Source%20Code) issue.

Attach your complete tested source-code file.


STOPPED. Updates will be infrequent. Once or twice a year.
