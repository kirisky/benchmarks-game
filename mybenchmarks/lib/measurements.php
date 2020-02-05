<?php
// Copyright (c) Isaac Gouy 2010-2017

// LIBRARIES ////////////////////////////////////////////////

require_once(LIB_PATH.'lib_whitelist.php');
require_once(LIB);
require_once(LIB_PATH.'lib_data.php');


// FUNCTIONS ///////////////////////////////////////////

function LanguageData($FileName,$Langs,$Incl,$Excl,$L,$HasHeading=TRUE){
   $rows = array();
   $lines = file($FileName);

   $prefixL = ','.$L.',';
   foreach($lines as $line) {
      if (strpos($line,$prefixL)){
         $row = explode( ',', $line);
         $test = $row[DATA_TEST];
         $key = $test.$L.$row[DATA_ID];

         // $L has already been checked
         if (isset($Incl[$test]) && isset($Incl[$L]) && !isset($Excl[$key])){
            settype($row[DATA_STATUS],'integer');
            settype($row[DATA_ELAPSED],'double');
            $rows[] = $row;
         }

      }
   } 
   return $rows;
}


// PAGE ////////////////////////////////////////////////

$Page = new Template(LIB_PATH);


// GET_VARS ////////////////////////////////////////////////

list($Incl,$Excl) = WhiteListInEx();
$Tests = WhiteListUnique('test.csv',$Incl); // assume test.csv in name order
$Langs = WhiteListUnique('lang.csv',$Incl); // assume lang.csv in name order

if (isset($_GET['lang'])
      && strlen($_GET['lang']) && (strlen($_GET['lang']) <= NAME_LEN)){
   $X = $_GET['lang'];
   if (preg_match("/^[a-z0-9]+$/",$X)){ $L = $X; }
}
$Available = isset($L) && isset($Langs[$L]) && isset($Incl[$L]);
if (!$Available){ $L = 'java'; }


// 200 OK ////////////////////////////////////////////////

if ($Available){

$Body = new Template(LIB_PATH);
$TemplateName = 'measurements.tpl.php';


// HEADER ////////////////////////////////////////////////

$LangName = $Langs[$L][LANG_FULL];
$Title = $LangName.' measurements';


// DATA ////////////////////////////////////////////////

$Data = LanguageData(DATA_PATH.'fastest_measurements.csv',$Langs,$Incl,$Excl,$L);

$v = HtmlFragment(VERSION_PATH.$L.'-version.php');


// META ////////////////////////////////////////////////

$keywords = '<meta name="description" content="Performance measurements for all the '.$LangName.' toy benchmark programs." />';

$robots = '<meta name="robots" content="all" />';

$style = '<style><!--
a{color:black;text-decoration:none}article,footer{padding:0 0 2.9em}article,div,footer,header{margin:auto;width:92%}body{font:100% Droid Sans, Ubuntu, Verdana, sans-serif;margin:0;-webkit-text-size-adjust:100%}code, h3, h1, h2, li a{font-family:Ubuntu Mono,Consolas,Menlo,monospace}div,footer,header{max-width:31em}h3{font-size:1.4em;font-weight:bold;margin:0;padding: .4em}h3, h3 a{color:white}h3 small{font-size:0.64em}h1,h2{margin:1.5em 0 0}h1{font-size:1.4em;font-weight:normal}h2{font-size:1.2em}li{list-style-type:none;vertical-align:top}li a{display:block;font-size:1.2em;margin: .5em .5em 0;padding: .5em .5em .3em}nav p{margin:0 .5em}ul{clear:left;margin:-0.3em 0 1.5em;text-align:center}p{color:#333;line-height:1.4;margin: .3em 0 0}p a,span{border-bottom: .1em solid #333;padding-bottom: .1em}#linux{background-color:green}#macbook{background-color:brown}.best{font-weight:bold}.message{font-size: .8em}table{color:#333;margin:1.3em auto 0;text-align:right}tbody::after{content:"-";display:block;line-height:2.6em;visibility:hidden}td{border-bottom: .15em dotted #eee;padding: .7em 0 0 1em}td a, th a{display:block}td:first-child,th:first-child{text-align:left;padding-left:0}th{font-weight:normal;padding:0 0 0 1em}@media only screen{th:nth-child(3),td:nth-child(3),th:nth-child(4),td:nth-child(4),th:nth-child(5),td:nth-child(5),th:nth-child(6),td:nth-child(6),th:nth-child(7),td:nth-child(7){display:none}}@media only screen and (min-width: 27em){th:nth-child(3),td:nth-child(3){display:table-cell}}@media only screen and (min-width: 38em){th:nth-child(4),td:nth-child(4),th:nth-child(5),td:nth-child(5),th:nth-child(6),td:nth-child(6){display:table-cell}}@media only screen and (min-width: 47em){th:nth-child(7),td:nth-child(7){display:table-cell}}@media only screen and (min-width: 60em){article,footer,header{font-size:1.25em}}
--></style>';


// TEMPLATE VARS ////////////////////////////////////////////////

$Body->set('Data', $Data );
$Body->set('Langs', $Langs);
$Body->set('LangVersion', $v);
$Body->set('SelectedLang', $L);
$Body->set('Tests', $Tests);
$Body->set('Title', $Title);

$Page->set('Keywords', $keywords);
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
