<?
// Copyright (c) Isaac Gouy 2010-2020

// DATA LAYOUT ///////////////////////////////////////////////////

define('DATA_TEST',0);
define('DATA_LANG',1);
define('DATA_ID',2);
define('DATA_TESTVALUE',3);
define('DATA_GZ',4);
define('DATA_FULLCPU',5);
define('DATA_MEMORY',6);
define('DATA_STATUS',7);
define('DATA_LOAD',8);
define('DATA_ELAPSED',9);
define('DATA_BUSY',10);


define('DATA_TIME',DATA_ELAPSED);

define('PROGRAM_TIMEOUT',-1);


// MENU ///////////////////////////////////////////////////

// No longer used. 
// For old usage see lib/switch.php & websites/linux/benchmark.php

function MkMenuForm($Tests,$SelectedTest,$Langs,$SelectedLang,$Id=NULL){
   echo '<form method="get" action="benchmark.php" id="menus">', "\n";
   echo '<p><select name="test" id="test">', "\n";
   echo '<option value="all">- all ', TESTS_PHRASE, 's -</option>', "\n";

   foreach($Tests as $Row){
      $Link = $Row[TEST_LINK];
      $Name = $Row[TEST_NAME];
      if ($Link==$SelectedTest){
         $Selected = 'selected="selected"';
      } else {
         $Selected = '';
      }
      printf('<option %s value="%s">%s</option>', $Selected,$Link,$Name); echo "\n";
   }
   echo '</select>', "\n";

   echo '<select name="lang" id="lang">', "\n";
   echo '<option value="all">- all ', LANGS_PHRASE, 's -</option>', "\n";
   foreach($Langs as $Row){
      $Link = $Row[LANG_LINK];
      $Name = $Row[LANG_FULL];
      if ($Link==$SelectedLang){
         $Selected = 'selected="selected"';
      } else {
         $Selected = '';
      }
      printf('<option %s value="%s">%s</option>', $Selected,$Link,$Name); echo "\n";
   }
   echo '</select>', "\n";

   if ($Id){
      echo '<input type="hidden" name="id" value="'.$Id.'">', "\n";
   }

   $datasets = array(
      array('linux','linux'),
      array('macbook','macbook')
      );

   echo '<select name="data" id="data">', "\n";
   foreach($datasets as $Row){
      $Link = $Row[0];
      $Name = $Row[1];
      if ($Link==SITE_NAME){
         $Selected = 'selected="selected"';
      } else {
         $Selected = '';
      }
      printf('<option %s value="%s">%s</option>', $Selected,$Link,$Name); echo "\n";
   }
   echo '</select>', "\n";

   echo '<input type="submit" value="Show" />', "\n";
   echo '</p></form>', "\n";
}



// FORMAT ///////////////////////////////////////////////////

function ElapsedTime($d){
   if ($d[DATA_ELAPSED] > 0.0){
      return number_format($d[DATA_ELAPSED],2);
   } else {
      return '';
   }
}

function CpuLoad($d){
   if (strlen($d[DATA_LOAD])>1){
      return str_replace(' ','&nbsp;',$d[DATA_LOAD]);
   } else {
      return '';
   }
}

function IdName($id){
   if ($id>1){ return '&nbsp;#'.$id; } else { return ''; }
}


function MarkTime($PathRoot=''){
   $mtime = filemtime($PathRoot.DATA_PATH.'filtered_measurements.csv');
   $Mark = gmdate("d M Y", $mtime);
   return $Mark;
}

function StatusMessage($i){
   if ($i==0){ $m = ''; }
   elseif ($i==-1){ $m = 'Timed&nbsp;Out'; }
   elseif ($i==-2){ $m = 'Failed'; }
   elseif ($i==-6){ $m = 'No&nbsp;Comparison'; }
   elseif ($i==-10){ $m = 'Bad&nbsp;Output'; }
   elseif ($i==-11){ $m = 'Make&nbsp;Error'; }
   elseif ($i==-12){ $m = 'Empty'; }
   elseif ($i==-13){ $m = 'Partial'; }
   else { $m = ''; }
   return $m;
}

?>
