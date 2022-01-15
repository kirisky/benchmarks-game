Once-upon-a-time new source-code was measured and shown on the website within a few days. Not any more.

Now the benchmarks game might be updated after 6 or 12 months. Now it might not be updated.


(On 2012 i5-3330 UEFI / Secure Boot hardware, it has proved very difficult to keep Ubuntu working as a dual-boot with MS Windows 10. The current measurements were made by first re-installing Ubuntu from DVD and re-installing the development tools and development libraries and … and that's way too much aggravation.)

If in-spite of that warning you are intent on adding something to the issue tracker, please follow these guidelines.


Contribute source-code for measurement
--------------------------------------

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

Try to have a little patience.


