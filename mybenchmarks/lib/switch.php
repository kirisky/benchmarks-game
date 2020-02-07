<?php
// Copyright (c) Isaac Gouy 2010-2020

// No longer used. For old usage see lib/lib_data.php MkMenuForm

// DATA LAYOUT ///////////////////////////////////////////////////

$NAME_LEN = 16;
$LANG_COMPARE = 8;

// GET_VARS ////////////////////////////////////////////////

if (isset($_GET['test'])
      && strlen($_GET['test']) && (strlen($_GET['test']) <= $NAME_LEN)){
   $X = $_GET['test'];
   if (preg_match("/^[a-z]+$/",$X)){ $T = $X; }
}
if (!isset($T)){ $T = 'all'; }

if (isset($_GET['lang'])
      && strlen($_GET['lang']) && (strlen($_GET['lang']) <= $NAME_LEN)){
   $X = $_GET['lang'];
   if (preg_match("/^[a-z0-9]+$/",$X)){ $L = $X; }
}
if (!isset($L)){ $L = 'all'; }

if (isset($_GET['lang2'])
      && strlen($_GET['lang2']) && (strlen($_GET['lang2']) <= $NAME_LEN)){
   $X = $_GET['lang2'];
   if (preg_match("/^[a-z0-9]+$/",$X)){ $L2 = $X; }
}
if (!isset($L2)){
   if ($L!='all'){ $L2 = ''; }
}


// PAGES ///////////////////////////////////////////////////


if ($T=='all'){
   if ($L=='all'){
      // should go to some kind-of summary page
      require_once(LIB_PATH.'performance.php');

   } else {
      if ($L!=$L2){
         require_once(LIB_PATH.'compare.php');

      } else {
        require_once(LIB_PATH.'measurements.php');
      }
   }
} elseif ($L=='all'){

   require_once(LIB_PATH.'performance.php');

} else {

   require_once(LIB_PATH.'program.php');
}
?>



