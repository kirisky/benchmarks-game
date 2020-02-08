<?php
// Copyright (c) Isaac Gouy 2010-2020


// LIBRARIES ////////////////////////////////////////////////

require_once(LIB_PATH.'lib_whitelist.php');
require_once(LIB);
require_once(LIB_PATH.'lib_data.php');


// PAGE ////////////////////////////////////////////////

$Page = new Template(LIB_PATH);



// GET_VARS ////////////////////////////////////////////////

if (TRUE){



// DATA LAYOUT ///////////////////////////////////////////////////

define('STATS_SIZE',8);
define('STAT_MIN',0);
define('STAT_XLOWER',1);
define('STAT_LOWER',2);
define('STAT_MEDIAN',3);
define('STAT_UPPER',4);
define('STAT_XUPPER',5);
define('STAT_MAX',6);
define('STATS_N',7);


// FUNCTIONS ///////////////////////////////////////////

function BoxplotData($FileName,$Tests,$Langs,$Incl,$Excl,$HasHeading=TRUE){

   return FullScores( FullRatios($FileName,$Tests,$Langs,$Incl,$Excl,$HasHeading) );
}

function FullRatios($FileName,$Tests,$Langs,$Incl,$Excl,$HasHeading=TRUE){
   $time_mins = array();
   foreach($Tests as $k => $v){ $time_mins[$k] = 360000.0; } // 100 hours
   $data = array();

   $lines = @file($FileName) or die ('Cannot open $FileName');
   if ($HasHeading){ unset($lines[0]); } // remove header line

   foreach($lines as $line) {
      $row = explode( ',', $line);
      $test = $row[DATA_TEST];
      $lang = $row[DATA_LANG];
      $key = $test.$lang.$row[DATA_ID];

      // accumulate all acceptable datarows, exclude duplicates

      if (isset($Incl[$test]) && isset($Incl[$lang]) && isset($Langs[$lang]) &&
                  !isset($Excl[$key])){

            settype($row[DATA_STATUS],'integer');
            settype($row[DATA_FULLCPU],'double');
            settype($row[DATA_ELAPSED],'double');
            $row_time = $row[DATA_FULLCPU];

               if ($row[DATA_STATUS] == 0) {

                  // The Python timing script only captures child processes which 
                  // causes partial measurements for some (OCaml) programs.
                  // Don't show them as correctly timed!

                  $isPartial = ($row[DATA_FULLCPU] < $row[DATA_ELAPSED]) && 
                     ((($row[DATA_ELAPSED] - $row[DATA_FULLCPU]) / $row[DATA_ELAPSED]) > 0.1);


                  if (!$isPartial && (!isset($data[$lang][$test]) ||
                     ($row_time < $data[$lang][$test][DATA_FULLCPU]))){

                     $data[$lang][$test] = $row;

                     if ($row_time < $time_mins[$test]){
                        $time_mins[$test] = $row_time;
                     }
                   }
            }
      }
   }
   unset($lines);

   $ratios = array();
   foreach($data as $k => $test){
      if (sizeof($test)/sizeof($Tests) > 0.35){
         $s = array();
         foreach($test as $t => $row){
            // wait until now to filter so sizeof($test) is consistent with FullWeightedData
            if ($Tests[$t][TEST_WEIGHT]>0){
                  $s[] = $row[DATA_FULLCPU]/$time_mins[$t];
            }
         }
         if (!empty($s)){ $ratios[$k] = $s; }
      }
   }

   return $ratios;
}


function FullScores($ratios){
  $score = array();
  foreach($ratios as $k => $s){
// calculate GeometricMean just to use for sort
     $score[$k] = array(GeometricMean($s),Percentiles($s));

  }
   uasort($score,'CompareGeometricMean');
//   uasort($score,'CompareMedian');

   $labels = array();
   $stats = array();
   foreach($score as $k => $test){
      $labels[] = $k;
      $gm[] = $test[0];
      $stats[] = $test[1];
   }
   return array($labels,$gm,$stats);
}

function CompareMedian($a, $b){
   return $a[STAT_MEDIAN] < $b[STAT_MEDIAN] ? -1 : 1;
}

function CompareGeometricMean($a, $b){
   return $a[0] < $b[0] ? -1 : 1;
}


function GeometricMean($a){
   $logsum = 0.0;
   foreach($a as $v){
      $logsum += log($v);
   }
   return exp($logsum/sizeof($a));
}


function Percentiles($a){
   sort($a);
   $n = sizeof($a);
   $mid = floor($n / 2);
   if ($n % 2 != 0){
      $median = $a[$mid];
      $lower = Median( array_slice($a,0,$mid+1) ); // include median in both quartiles
      $upper = Median( array_slice($a,$mid) );
   } else {
      $median = ($a[$mid-1] + $a[$mid]) / 2.0;
      $lower = Median( array_slice($a,0,$mid) );
      $upper = Median( array_slice($a,$mid) );
   }
   $maxwhisker = ($upper - $lower) * 1.5;
   $xlower = ($lower - $maxwhisker < $a[0]) ? $a[0]: $lower - $maxwhisker;
   $xupper = ($upper + $maxwhisker > $a[$n-1]) ? $a[$n-1] : $upper + $maxwhisker;

   return array($a[0],$xlower,$lower,$median,$upper,$xupper,$a[$n-1],$n);
}

function Median($a){
   $n = sizeof($a);
   $mid = floor($n / 2);
   return ($n % 2 != 0) ? $a[$mid] : ($a[$mid-1] + $a[$mid]) / 2.0;
}



// PAGE ////////////////////////////////////////////////

$Body = new Template(LIB_PATH);
$TemplateName = 'boxplotcpu.tpl.php';


// GET_VARS ////////////////////////////////////////////////

list($Incl,$Excl) = WhiteListInEx();
$Tests = WhiteListUnique('test.csv',$Incl); // assume test.csv in name order
$Langs = WhiteListUnique('lang.csv',$Incl); // assume lang.csv in name order


// HEADER ////////////////////////////////////////////////

$mark = MarkTime();
$mark = $mark.' '.SITE_NAME;
$Title = 'Private';


// DATA ////////////////////////////////////////////////

$Data = BoxplotData(DATA_PATH.'filtered_measurements.csv',$Tests,$Langs,$Incl,$Excl);


// TEMPLATE VARS ////////////////////////////////////////////////

$Body->set('Data', $Data );
$Body->set('Mark', $mark);

$Page->set('PageBody', $Body->fetch($TemplateName));
$Page->set('PageTitle', 'Private');

echo $Page->fetch('page_standalone.tpl.php');

// 404 Not Found ////////////////////////////////////////////////

} else {

echo $Page->fetch('page404HTML5.tpl.php');
}

?>
