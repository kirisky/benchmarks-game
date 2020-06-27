#!/usr/bin/python3

"""Extract measurements from a csv file. Present the
measurements in GFM tables. Sort the table data 4 different
ways and for each make a different file in the current
directory: secs.md, mem.md, gz.md, cpu.md.

"""




import os, csv
from string import Template

csvdata = './data.csv'

# globals
tests = set()
results = {}




def readResultsFromCSV():
    global tests, results
    csvcols = ('test','lang','id','n','size','cpu','mem','status','load','secs')
    with open(csvdata, newline='') as csvfile:
        reader = csv.DictReader(csvfile,csvcols)
        for row in reader:
            if row['test'] != 'name': # exclude header row

                if row['status'] == '0' : # exclude failures
                    tests.add(row['test'])

                    # separate data from different tests
                    if row['test'] not in results:
                        results[ row['test'] ] = []

                    results[ row['test'] ].append( [      # index
                        # convert to numbers for sorting
                        float( row['secs'] ),             # 0
                        int( row['mem'] ),                # 1
                        int( row['size'] ),               # 2
                        float( row['cpu'] ),              # 3

                        row['load'],                      # 4
                        row['lang'],                      # 5
                        row['id']                         # 6

                    ] )




def writeMarkdown(i=0):
                  # 0=secs, 1=mem, 2=gz, 3=cpu

    tableMD = Template("""| $tablesummary | secs | mem | gz | cpu | 
| :------ | -----: | -----: | -----: | -----: |  
$tablerows
""")

    rowMD = Template("""| $rowprogram | $rowsecs | $rowmem | $rowgz | $rowcpu |
""")

    md = ''
    for test in sorted(tests):
        testresults = results[ test ]
        testresults.sort(key=lambda r: r[i]) 

        rows = ''
        for r in testresults:
            s = rowMD.substitute(
                rowprogram = r[5] + '&nbsp;#&nbsp;' + r[6],
                rowsecs = f"{round( r[0], 2 ):.2f}",
                rowmem = f"{r[1]:,}",
                rowgz = r[2],
                rowcpu = f"{round( r[3], 2 ):.2f}",           
                )
            rows += s

        md += tableMD.substitute(tablesummary = test, tablerows = rows)

    f = ['./secs.md', './mem.md', './gz.md', './cpu.md'][i]
    print(md,file=open(f,'w'))




readResultsFromCSV()

for i in range(0,4):
    writeMarkdown(i)




