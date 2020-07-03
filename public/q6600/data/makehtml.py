#!/usr/bin/python3

"""Extract measurements from a csv file. Present the 
measurements within a collapsible summary/details structure,
with details containing a HTML table. Sort the table data
4 different ways and for each make a different file in
the current directory: secs.html, mem.html, gz.html, cpu.html.

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




def writeCollapsibleHTML(i=0):
                         # 0=secs, 1=mem, 2=gz, 3=cpu

    tableHTML = Template("""    <article style="font:100% Droid Sans, Ubuntu, Verdana, sans-serif">
    <details>
      <summary><strong>$tablesummary</strong></summary>
      <table style="color:#333;text-align:right;width:80%">
        <tr>
          <th style="text-align:left;padding-left:0">program
          <th>secs
          <th>mem
          <th>gz
          <th>cpu
$tablerows
      </table>
    </details>
  </article>
""")

    rowHTML = Template("""        <tr>
          <td style="text-align:left;padding-left:0">$rowprogram
          <td>$rowsecs
          <td>$rowmem
          <td>$rowgz
          <td>$rowcpu
""")

    html = ''
    for test in sorted(tests):
        testresults = results[ test ]
        testresults.sort(key=lambda r: r[i]) 

        rows = ''
        for r in testresults:
            s = rowHTML.substitute(
                rowprogram = r[5] + '&nbsp;#&nbsp;' + r[6],
                rowsecs = f"{round( r[0], 2 ):.2f}",
                rowmem = f"{r[1]:,}",
                rowgz = r[2],
                rowcpu = f"{round( r[3], 2 ):.2f}",           
                )
            rows += s

        html += tableHTML.substitute(tablesummary = test, tablerows = rows)

    f = ['./secs.html', './mem.html', './gz.html', './cpu.html'][i]
    print(html,file=open(f,'w'))




readResultsFromCSV()

for i in range(0,4):
    writeCollapsibleHTML(i)




