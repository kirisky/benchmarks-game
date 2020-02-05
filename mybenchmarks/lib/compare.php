<?php
// Copyright (c) Isaac Gouy 2010-2020

// LIBRARIES ////////////////////////////////////////////////

require_once(LIB_PATH.'lib_whitelist.php');
require_once(LIB);
require_once(LIB_PATH.'lib_data.php');


// FUNCTIONS ///////////////////////////////////////////

function BestRows($rows){
   $testvalue = -2; // assume not test value is < 0
   $time = 360000.0; // assume no program was allowed to run for 100 hours
   $id = -2; // assume no id is < 0
   $failed_id = 0;

   // Identify id of fastest row at largest n, or whatever rows there are
   foreach($rows as $row) {
      $row_testvalue = $row[DATA_TESTVALUE];
      if ($row[DATA_STATUS] > PROGRAM_TIMEOUT){
         if ($row_testvalue > $testvalue){
            $testvalue = $row_testvalue;
            $time = $row[DATA_TIME];
            $id = $row[DATA_ID];
         } elseif ($row_testvalue == $testvalue){
            $row_time = $row[DATA_TIME];
            if ($row_time < $time){
               $time = $row_time;
               $id = $row[DATA_ID];
            }
         }
      } else {
         $failed_id = $row[DATA_ID];
      }
   }
   if ($id < 0){ $id = $failed_id; } // assume no id is < 0

   // filter and return the best of the rows
   $best = array();
   foreach($rows as $row) {
      if ($row[DATA_ID] == $id){
         $best[] = $row;
      }
   }
   return $best;
}


function AccumulateComparableRows($rowsL1,$rowsL2,&$comparable){
   $rowL1 = empty($rowsL1) ? NULL :  $rowsL1[0];
   $rowL2 = empty($rowsL2) ? NULL :  $rowsL2[0];
   $time = NO_VALUE; $mem = NO_VALUE; $gz = NO_VALUE;

   $n = min(sizeof($rowsL1),sizeof($rowsL2));
   if ($n > 0){
      for ($i=1;$i<$n;$i++){
         if ($rowsL1[$i][DATA_STATUS]<=PROGRAM_TIMEOUT || $rowsL2[$i][DATA_STATUS]<=PROGRAM_TIMEOUT){ break; }
         $rowL1 = $rowsL1[$i];
         $rowL2 = $rowsL2[$i];
      }
      if ($rowL1[DATA_STATUS] + $rowL2[DATA_STATUS] > PROGRAM_TIMEOUT){
         if ($rowL2[DATA_TIME]>0.0){ $time = $rowL1[DATA_TIME] / $rowL2[DATA_TIME]; }
         if ($rowL2[DATA_MEMORY]>NO_VALUE){ $mem = $rowL1[DATA_MEMORY] / $rowL2[DATA_MEMORY]; } else { $mem = NO_VALUE; }
         if ($rowL2[DATA_GZ]>0.0){ $gz = $rowL1[DATA_GZ] / $rowL2[DATA_GZ]; }
      }
   }
   $test = isset($rowL1) ? $rowL1[DATA_TEST] : $rowL2[DATA_TEST];
   $comparable[$test] = array($rowL1,$rowL2, DATA_TIME => $time, DATA_MEMORY => $mem, DATA_GZ => $gz);
}


function HeadToHeadData($FileName,$Tests,$Langs,$Incl,$Excl,$L1,$L2,$HasHeading=TRUE){
   $measurements = array();
   $lines = file($FileName);

   $prefixL1 = ','.$L1.',';
   $prefixL2 = ','.$L2.',';
   $rowsL1 = array();
   $rowsL2 = array();
   foreach($lines as $line) {
      $isLang1 = strpos($line,$prefixL1);
      if ($isLang1 || strpos($line,$prefixL2)){
         $row = explode( ',', $line);
         $test = $row[DATA_TEST];
// do we need to check $test here?  
//         if (isset($Incl[$test])){
            if (isset($previous) && $previous != $test){ // assume ndata.csv is sorted by test
               AccumulateComparableRows(BestRows($rowsL1),BestRows($rowsL2),$measurements);
               $rowsL1 = array();
               $rowsL2 = array();
            }
            $previous = $test;

            $key = $test.$row[DATA_LANG].$row[DATA_ID];
            settype($row[DATA_ID],'integer');
            // $L1 and $L2 have already been checked
            if (!isset($Excl[$key])){
               settype($row[DATA_STATUS],'integer');
               settype($row[DATA_TESTVALUE],'integer');
               settype($row[DATA_TIME],'double');
               settype($row[DATA_GZ],'double');
               settype($row[DATA_MEMORY],'double');

               if ($isLang1){
                  $rowsL1[] = $row;
               } else {
                  $rowsL2[] = $row;
               }
            }
//         }
      }
   }
   AccumulateComparableRows(BestRows($rowsL1),BestRows($rowsL2),$measurements);

   // collect values for chart and stats
   $times = array();
   $mismatches = array();
   foreach($measurements as $v){
      $test = $v[0][DATA_TEST]; 
      if (($test!=NULL && $Tests[$test][TEST_WEIGHT]<=0) || $v[DATA_TIME] == NO_VALUE){ continue; }
      $times[] = $v[DATA_TIME];
   }

   // sort by x times faster
   uasort($measurements,'CompareTimeRatio');

   $sorted = array();
   foreach($measurements as $rows){
      $test = isset($rows[0]) ? $rows[0][DATA_TEST] : $rows[1][DATA_TEST];
      $sorted[$test] = $rows;
   }
   foreach($Tests as $k => $v){
      if (!isset($sorted[$k])){ $sorted[$k] = array(); }
   }

   return $sorted;
}


function CompareTimeRatio($a, $b){
   return $a[DATA_TIME] == NO_VALUE ? 1 :
         ($b[DATA_TIME] == NO_VALUE ? -1 :
         ($a[DATA_TIME] < $b[DATA_TIME] ? -1 : 1));
}


// PAGE ////////////////////////////////////////////////

$Page = new Template(LIB_PATH);


// GET_VARS ////////////////////////////////////////////////

list($Incl,$Excl) = WhiteListInEx();
$Tests = WhiteListUnique('test.csv',$Incl); // assume test.csv in name order
$Langs = WhiteListUnique('lang.csv',$Incl); // assume lang.csv in name order


// if compare.php was requested directly get the request parameters
if (isset($_GET['lang'])
      && strlen($_GET['lang']) && (strlen($_GET['lang']) <= NAME_LEN)){
   $X = $_GET['lang'];
   if (preg_match("/^[a-z0-9]+$/",$X)){ $L = $X; }
}
if (isset($L)){
   $Available = isset($Langs[$L]) && isset($Incl[$L]);
   // specific request which is not available
   if (!$Available){ 
      $L = 'java'; 
   }
} else {
   // no specific request
   $L = 'java';
}

if (isset($_GET['lang2'])
      && strlen($_GET['lang2']) && (strlen($_GET['lang2']) <= NAME_LEN)){
   $X = $_GET['lang2'];
   if (preg_match("/^[a-z0-9]+$/",$X)){ $L2 = $X; }
}
if ($Available && !empty($L2)){ 
   $Available = isset($Langs[$L2]) && isset($Incl[$L2]) && ($L2 != $L);
   if (!$Available){ 
      // assume LANG_COMPARE is always available in every data set
      $L2 = $Langs[$L][LANG_COMPARE];
   }
} else {
      // assume LANG_COMPARE is always available in every data set
      $L2 = $Langs[$L][LANG_COMPARE];
}


// 200 OK ////////////////////////////////////////////////

if ($Available){

$Body = new Template(LIB_PATH);
$TemplateName = 'compare.tpl.php';


// HEADER ////////////////////////////////////////////////

$LangName = $Langs[$L][LANG_FULL];
$LangName2 = $Langs[$L2][LANG_FULL];
$Title = $LangName.'&nbsp;vs&nbsp;'.$LangName2;


// DATA ////////////////////////////////////////////////

// People seem more confused than helped by comparisons at different workloads, so
// just use the comparison at the largest workload.

$Data = HeadToHeadData(DATA_PATH.'filtered_measurements.csv',$Tests,$Langs,$Incl,$Excl,$L,$L2);

$v = HtmlFragment(VERSION_PATH.$L.'-version.php');
$v2 = HtmlFragment(VERSION_PATH.$L2.'-version.php');


// META ////////////////////////////////////////////////

$keywords = '<meta name="description" content="'.$LangName.' '.$LangName2.' - Which programs are fastest?" />'; 

$robots = '<meta name="robots" content="all" />';


$style = '<style><!--
a{color:black;text-decoration:none}article,footer{padding:0 0 2.9em}article,div,footer,header{margin:auto;width:92%}body{font:100% Droid Sans, Ubuntu, Verdana, sans-serif;margin:0;-webkit-text-size-adjust:100%}h3, h1, h2, nav li a{font-family:Ubuntu Mono,Consolas,Menlo,monospace}div,footer,header{max-width:31em}h3{font-size:1.4em;font-weight:bold;margin:0;padding: .4em}h3, h3 a{color:white}h3 small{font-size:0.64em}h1,h2{margin:1.5em 0 0}h1{font-size:1.4em;font-weight:normal}h2{font-size:1.2em}li{display:inline-block}nav li{list-style-type:none}nav li a{display:block;font-size:1.2em;margin: .5em .5em 0;padding: .5em .5em .3em}nav ul{clear:left;margin:-0.3em 0 1.5em;padding-left:0;text-align:center}p{color:#333;line-height:1.4;margin: .3em 0 0}p a,span{border-bottom: .1em solid #333;padding-bottom: .1em}#linux{background-color:green}#macbook{background-color:brown}.best{font-weight:bold}.message{font-size: .8em}table{color:#333;margin:1.3em auto 0;text-align:right}tbody::after{content:"-";display:block;line-height:2.6em;visibility:hidden}tbody:last-child{text-align:left}td{border-bottom: .15em dotted #eee;padding: .7em 0 0 1em}td a, th a{display:block}td:first-child,th:first-child{text-align:left;padding-left:0}td:nth-child(6),th:nth-child(6){display:table-cell}th{font-weight:normal;padding: .7em 0 0 1em}@media only screen{th:nth-child(3),td:nth-child(3),th:nth-child(4),td:nth-child(4),th:nth-child(5),td:nth-child(5),th:nth-child(6),td:nth-child(6){display:none}h2::after{content:" (too narrow: mem, gz, cpu, cpu-load columns are hidden)";font-weight:normal;font-size: .9em}}@media only screen and (min-width: 28em){th:nth-child(3),td:nth-child(3),th:nth-child(4),td:nth-child(4),th:nth-child(5),td:nth-child(5){display:table-cell}h2::after{content:" (cpu-load column is hidden)"}}@media only screen and (min-width: 41em){th:nth-child(6),td:nth-child(6){display:table-cell}h2::after{display:none}}@media only screen and (min-width: 60em){article,footer,header{font-size:1.25em}}
--></style>';



// TEMPLATE VARS ////////////////////////////////////////////////

$Body->set('Data', $Data );
$Body->set('Langs', $Langs);
$Body->set('SelectedLang', $L);
$Body->set('SelectedLang2', $L2);
$Body->set('LangVersion', $v);
$Body->set('Lang2Version', $v2);
$Body->set('Tests', $Tests);
$Body->set('Title', $Title.' | My Benchmarks');

$Page->set('Keywords', $keywords);
if (isset($LinkRelCanonical)) { $Page->set('LinkCanonical', $LinkRelCanonical); }
$Page->set('PageBody', $Body->fetch($TemplateName));
$Page->set('PageTitle', $Title.' | '.PLATFORM_NAME.' | My Benchmarks');
$Page->set('Robots', $robots);
$Page->set('Style', $style);

echo $Page->fetch('pageHTML5.tpl.php');


// 404 Not Found ////////////////////////////////////////////////

} else {

echo $Page->fetch('page404HTML5.tpl.php');
}
?>
