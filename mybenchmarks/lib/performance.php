<?php
// Copyright (c) Isaac Gouy 2010-2020

// LIBRARIES ////////////////////////////////////////////////

require_once(LIB_PATH.'lib_whitelist.php');
require_once(LIB);
require_once(LIB_PATH.'lib_data.php');


// FUNCTIONS ///////////////////////////////////////////

function SelectedLangs($Langs){
   $w = array();
   foreach($Langs as $lang){
      $link = $lang[LANG_LINK];
      if (isset($_GET[$link])){ $w[$link] = 1; }
      if ($lang[LANG_SELECT]){ $wd[$link] = 1; }
   }
   if (sizeof($w)<=0){ $w = $wd; }
   return $w;
}

function BenchmarkData($FileName,$Test,$Langs,$Incl,$Excl,$Sort,$SLangs,$HasHeading=TRUE){
   $lines = @file($FileName) or die ('Cannot open $FileName');
   if ($HasHeading){ unset($lines[0]); } // remove header line

   $prefix = substr($Test,1).',';
   $succeeded = array();
   $failed = array();
   $special = array();

   $time_min = 360000.0; // 100 hours
   $mem_min = 1024000000;
   $gz_min = 1048576;
   $DATA_TIME_SORT = $Sort=='fullcpu' ? DATA_FULLCPU : DATA_TIME;

   foreach($lines as $line) {
      if (strpos($line,$prefix)){
         $row = explode( ',', $line);
         $lang = $row[DATA_LANG];

         if (isset($Incl[$lang])){
               //$exclude = $Excl[ $Test.$lang.$row[DATA_ID] ];
            $exclude = NULL;
            if (isset($Excl[ $Test.$lang.$row[DATA_ID] ])){
               $exclude = $Excl[ $Test.$lang.$row[DATA_ID] ];
            }
            if (!$exclude){
               settype($row[DATA_ID],'integer');
               settype($row[DATA_TESTVALUE],'integer');
               settype($row[DATA_FULLCPU],'double');
               settype($row[DATA_GZ],'integer');
               settype($row[DATA_BUSY],'double');
               settype($row[DATA_MEMORY],'integer');
               settype($row[DATA_STATUS],'integer');
               settype($row[DATA_ELAPSED],'double');
   
               if (isset($exclude)){
                  $special[] = $row;
               } elseif ($row[DATA_STATUS]){
                  $failed[] = $row;
               } else {

                  $succeeded[] = $row;
                  
                  $row_time = $row[$DATA_TIME_SORT];
                  if ($row_time > 0.0 && $row_time < $time_min){
                     $time_min = $row_time;
                  }
                  $row_mem = $row[DATA_MEMORY];
                  if ($row_mem > 0 && $row_mem < $mem_min){
                     $mem_min = $row_mem;
                  }
                  $row_gz = $row[DATA_GZ];
                  if ($row_gz > 0 && $row_gz < $gz_min){
                     $gz_min = $row_gz;
                  }
               }

            }
         }
      }
   }

   if ($Sort=='fullcpu'){
      usort($succeeded, 'CompareFullCpuTime');
      usort($special, 'CompareFullCpuTime');
      $assumed_min = 0.0;
      $sort_index = $DATA_TIME_SORT;
      $row_min = $time_min;

   } elseif ($Sort=='kb'){
      usort($succeeded, 'CompareMemoryUse');
      usort($special, 'CompareMemoryUse');
      $assumed_min = 256;
      $sort_index = DATA_MEMORY;
      if ($mem_min < 256){ $mem_min = 256; }
      settype($mem_min,'double');
      $row_min = $mem_min;

   } elseif ($Sort=='gz'){
      usort($succeeded, 'CompareGz');
      usort($special, 'CompareGz');
      $assumed_min = 128;
      $sort_index = DATA_GZ;
      $row_min = $gz_min;

   } elseif ($Sort=='elapsed'){
      usort($succeeded, 'CompareElapsed');
      usort($special, 'CompareElapsed');
      $assumed_min = 0.0;
      $sort_index = $DATA_TIME_SORT;
      $row_min = $time_min;
   }

   $labels = array();
   $ratios = array();
   $count = 0; $max = 15;
   foreach($succeeded as $row){
      $k = $row[DATA_LANG];
      if (isset($SLangs[$k])){
         $labels[] = $k;
         unset($SLangs[$k]);
         $row_value = $row[$sort_index];
         $ratios[] = $row_value > $assumed_min ? $row_value/$row_min : 1.0;
         $count++;
      }
      if ($count == $max){ break; }
   }

   return array($succeeded,$failed,$special,$labels,$ratios);
}


function CompareFullCpuTime($a, $b){
   return  $a[DATA_FULLCPU] < $b[DATA_FULLCPU] ? -1 : 1;
}

function CompareMemoryUse($a, $b){
   return  $a[DATA_MEMORY] < $b[DATA_MEMORY] ? -1 : 1;
}

function CompareGz($a, $b){
   return  $a[DATA_GZ] < $b[DATA_GZ] ? -1 : 1;
}

function CompareElapsed($a, $b){
   return  $a[DATA_ELAPSED] < $b[DATA_ELAPSED] ? -1 : 1;
}


// PAGE ////////////////////////////////////////////////

$Page = new Template(LIB_PATH);


// GET_VARS ////////////////////////////////////////////////

list($Incl,$Excl) = WhiteListInEx();
$Tests = WhiteListUnique('test.csv',$Incl); // assume test.csv in name order
$Langs = WhiteListUnique('lang.csv',$Incl); // assume lang.csv in name order

$SLangs = SelectedLangs($Langs);

if (isset($_GET['test'])
      && strlen($_GET['test']) && (strlen($_GET['test']) <= NAME_LEN)){
   $X = $_GET['test'];
   if (preg_match("/^[a-z]+$/",$X)){ $T = $X; }
}
$Available = isset($T) && isset($Tests[$T]) && isset($Incl[$T]);
if (!$Available){ $T = 'nbody'; }


if (isset($_GET['sort'])
      && strlen($_GET['sort']) && (strlen($_GET['sort']) <= 7)){
   $X = $_GET['sort'];
   if (preg_match("/^[a-z]+$/",$X) && ($X == 'fullcpu' || $X == 'kb' || $X == 'gz' || $X == 'elapsed')){  
      $S = $X; 
   }
}
if (!isset($S)){
   $S = 'elapsed'; 
}


// 200 OK ////////////////////////////////////////////////

if ($Available){

$Body = new Template(LIB_PATH);
$TemplateName = 'performance.tpl.php';


// HEADER ////////////////////////////////////////////////

$TestName = $Tests[$T][TEST_NAME];
$Title = $TestName;


// DATA ////////////////////////////////////////////////

$Data = BenchmarkData(DATA_PATH.'filtered_measurements.csv',$T,$Langs, $Incl,$Excl,$S,$SLangs);


// META ////////////////////////////////////////////////

$keywords = '<meta name="description" content="In different programming languages '.$Tests[$T][TEST_META].'.">';


// last field of test.csv has linefeed
$keywords = str_replace(array("\r", "\n"), '', $keywords); 


$canonicalPage = FALSE;
if (!isset($LinkRelCanonical)){
  $canonicalPage = SITE_NAME == 'u64q';
    if ($canonicalPage) {
      $robots = '<meta name="robots" content="all">';
  } else {
    $LinkRelCanonical = '<link rel="canonical" href="../u64q/performance.php?test='.$T.'">';
    $robots = '<meta name="robots" content="all">';
  }
} 


// custom style to hide/show all except chosen column

$elapsed_hide_show = '
@media only screen{th:nth-child(1),td:nth-child(1),th:nth-child(4),td:nth-child(4),th:nth-child(5),td:nth-child(5),th:nth-child(6),td:nth-child(6),th:nth-child(7),td:nth-child(7){display:none}}
';

$mem_hide_show = '
@media only screen{th:nth-child(1),td:nth-child(1),th:nth-child(3),td:nth-child(3),th:nth-child(5),td:nth-child(5),th:nth-child(6),td:nth-child(6),th:nth-child(7),td:nth-child(7){display:none}}
';

$gz_hide_show = '
@media only screen{th:nth-child(1),td:nth-child(1),th:nth-child(3),td:nth-child(3),th:nth-child(4),td:nth-child(4),th:nth-child(6),td:nth-child(6),th:nth-child(7),td:nth-child(7){display:none}}
';

$cpu_hide_show = '
@media only screen{th:nth-child(1),td:nth-child(1),th:nth-child(3),td:nth-child(3),th:nth-child(4),td:nth-child(4),th:nth-child(5),td:nth-child(5),th:nth-child(7),td:nth-child(7){display:none}}
';

$pre_hide_show = '<style><!--
a{color:black;text-decoration:none}article,footer{padding:0 0 2.9em}article,div,footer,header{margin:auto;width:92%}body{font:100% Droid Sans, Ubuntu, Verdana, sans-serif;margin:0;-webkit-text-size-adjust:100%}h3, h1, h2, li a{font-family:Ubuntu Mono,Consolas,Menlo,monospace}div,footer,header{max-width:31em}h3{font-size:1.4em;font-weight:bold;margin:0;padding: .4em}h3, h3 a{color:white}h3 small{font-size:0.64em}h1,h2{margin:1.5em 0 0}h1{font-size:1.4em;font-weight:normal}h2{font-size:1.2em}li{list-style-type:none}li a{display:block;font-size:1.2em;margin: .5em .5em 0;padding: .5em .5em .3em}ul{clear:left;margin:-0.3em 0 1.5em;padding-left:0;text-align:center}p{color:#333;line-height:1.4;margin: .3em 0 0}p a,span{border-bottom: .1em solid #333;padding-bottom: .1em}#linux{background-color:green}#macbook{background-color:brown}.best{font-weight:bold}.message{font-size: .8em}table{color:#333;margin:1.3em auto 0;text-align:right}tbody::after{content:"-";display:block;line-height:2.6em;visibility:hidden}td{border-bottom: .15em dotted #eee;padding: .7em 0 0 1em}td a, th a{display:block}th{font-weight:normal;padding:0 0 0 1em}td:first-child{padding-left:0}td:nth-child(2),th:nth-child(2){text-align:left}
';

$post_hide_show = '@media only screen and (min-width:23em){th:nth-child(1),td:nth-child(1){display:table-cell}}@media only screen and (min-width:37em){th:nth-child(3),td:nth-child(3),th:nth-child(4),td:nth-child(4),th:nth-child(5),td:nth-child(5),th:nth-child(6),td:nth-child(6){display:table-cell}li{display:inline-block}}@media only screen and (min-width:43em){th:nth-child(7),td:nth-child(7){display:table-cell}}@media only screen and (min-width:60em){article,footer,header{font-size:1.25em}}
--></style>';


if ($S=='elapsed'){
  $style = $pre_hide_show.$elapsed_hide_show.$post_hide_show;
} elseif ($S=='kb'){
  $style = $pre_hide_show.$mem_hide_show.$post_hide_show;
} elseif ($S=='gz'){
  $style = $pre_hide_show.$gz_hide_show.$post_hide_show;
} elseif ($S=='fullcpu'){ 
  $style = $pre_hide_show.$cpu_hide_show.$post_hide_show;
}


// TEMPLATE VARS ////////////////////////////////////////////////

$Body->set('Data', $Data );
$Body->set('Langs', $Langs);
$Body->set('SelectedTest', $T);
$Body->set('Sort', $S);
$Body->set('Tests', $Tests);
$Body->set('Title', $Title);

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
