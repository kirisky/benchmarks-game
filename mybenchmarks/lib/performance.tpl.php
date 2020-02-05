<?   // Copyright (c) Isaac Gouy 2004-2020 ?>
<? 
function PTime($d){
  if ($d <= 0.0){ return '?'; }
  if ($d<300.0){ return number_format($d,2); }
  elseif ($d<3600.0){
    $m = floor($d/60); $s = $d-($m*60); $ss = number_format($s,0);
    if (strlen($ss)<2) { $ss = "0".$ss; }
    return number_format($m,0)."&nbsp;min"; }
  else {
    $h = floor($d/3600); $m = floor(($d-($h*3600))/60);
    $mm = number_format($m,0); if (strlen($mm)<2) { $mm = "0".$mm; }
    return number_format($h,0)."h&nbsp;".$mm."&nbsp;min";
  }
}

function PFx($d){
  if ($d>9.9){ return number_format($d); }
  elseif ($d>0.0){ return number_format($d,1); }
  else { return "&nbsp;"; }
}


$Row = $Tests[$SelectedTest];
$TestName = $Row[TEST_NAME];

list($Succeeded,$Failed,$Special,$Labels,$Ratios) = $Data;
unset($Data);

$elapsed_th = '';
$mem_th = '';
$gzbytes_th = '';
$cpu_th = '';
$t_class = ' class="best"';

$sort_nav = '<a href="./performance.php?test='.$SelectedTest.'&amp;sort=';
$elapsed_nav_open = $sort_nav.'elapsed"><span>';
$elapsed_nav_close = '</span></a>';
$mem_nav_open = $sort_nav.'kb"><span>';
$mem_nav_close = '</span></a>';
$gzbytes_nav_open = $sort_nav.'gz"><span>';
$gzbytes_nav_close = '</span></a>';
$cpu_nav_open = $sort_nav.'fullcpu"><span>';
$cpu_nav_close = '</span></a>';

if ($Sort=='elapsed'){
  $elapsed_th = $t_class;
  $elapsed_nav_open = '';
  $elapsed_nav_close = '';
} elseif ($Sort=='kb'){
  $mem_th = $t_class;
  $mem_nav_open = '';
  $mem_nav_close = '';
} elseif ($Sort=='gz'){
  $gzbytes_th = $t_class;
  $gzbytes_nav_open = '';
  $gzbytes_nav_close = '';
} elseif ($Sort=='fullcpu'){ 
  $cpu_th = $t_class;
  $cpu_nav_open = '';
  $cpu_nav_close = '';
}
?>
<article>
  <div>
    <h1><?=$TestName;?></h1>
  </div>
  <section>
    <div>
        <p>Always look at the source code.  
      <h2></h2>
    </div>
    <table>
<?
echo "      <tr>\n";
echo "        <th>&#215;\n";
echo '        <th>source', "\n";
echo "        <th", $elapsed_th,">", $elapsed_nav_open, "secs", $elapsed_nav_close, "\n";
echo "        <th", $mem_th,">", $mem_nav_open, "mem", $mem_nav_close, "\n";
echo "        <th", $gzbytes_th,">", $gzbytes_nav_open, "gz", $gzbytes_nav_close, "\n";
echo "        <th", $cpu_th,">", $cpu_nav_open, "cpu", $cpu_nav_close, "\n";
echo "        <th>cpu load\n";


foreach($Langs as $k => $v){ $No_Program_Langs[$k] = TRUE; }

// TODO search $Succeeded for $first with non-zero values

$better = array();
if (sizeof($Succeeded) > 0){ $first = $Succeeded[0]; }

// bold the best of each according to the sort criteria
foreach($Succeeded as $d){
  $k = $d[DATA_LANG];

  $HtmlName = $Langs[$k][LANG_FULL];
  $elapsed_td = '';
  $mem_td = '';
  $gzbytes_td = '';
  $cpu_td = '';
  if (!isset($better[$k])){
    $better[$k] = TRUE;

    $HtmlName = $Langs[$k][LANG_HTML];
    if ($Sort=='elapsed'){
      $elapsed_td = $t_class;
    } elseif ($Sort=='kb'){
      $mem_td = $t_class;
    } elseif ($Sort=='gz'){
      $gzbytes_td = $t_class;
    } elseif ($Sort=='fullcpu'){ 
      $cpu_td = $t_class;
    }
  }

  if ($Sort=='fullcpu'){   
    if ($first[DATA_FULLCPU]<=0){ $ratio = 0; }
    else { $ratio = $d[DATA_FULLCPU]/$first[DATA_FULLCPU]; }
  } elseif ($Sort=='kb'){ 
    if ($first[DATA_MEMORY]<=0){ $ratio = 0; }
    else { $ratio = $d[DATA_MEMORY]/$first[DATA_MEMORY]; }
  } elseif ($Sort=='elapsed'){
    if ($first[DATA_ELAPSED]<=0){ $ratio = 0; }
    else { $ratio = $d[DATA_ELAPSED]/$first[DATA_ELAPSED]; }
  } elseif ($Sort=='gz'){
    if ($first[DATA_GZ]<=0){ $ratio = 0; }
    else { $ratio = $d[DATA_GZ]/$first[DATA_GZ]; }
  }
  if ($ratio>750){ $ratio = 0; }


  echo "      <tr>\n";

  $r = PFx($ratio);
  echo "        <td>$r\n";

  $id = $d[DATA_ID];
  $HtmlName = $HtmlName.IdName($d[DATA_ID]);
  $nav = '"./program.php?test='.$SelectedTest.'&amp;lang='.$k.'&amp;id='.$id.'"';
  echo "        <td><a href=$nav><span>", "$HtmlName</span></a>\n";

  $e = PTime($d[DATA_ELAPSED]);
  echo "        <td", $elapsed_td, ">", $e, "\n";

  if ($d[DATA_MEMORY]<=0){ $kb = '?'; } else { $kb = number_format((double)$d[DATA_MEMORY]); }
  echo "        <td", $mem_td, ">", $kb, "\n";

  $gz = $d[DATA_GZ];
  echo "        <td", $gzbytes_td, ">", $gz, "\n";

  $fc = PTime($d[DATA_FULLCPU]);
  echo "        <td", $cpu_td, ">", $fc, "\n";

  $ld = CpuLoad($d);
  echo '        <td class="message">', $ld, "\n";
}
unset($better);


foreach($Langs as $k => $v){
  foreach($Failed as $d){
    if ($d[DATA_LANG]==$k){

      echo "      <tr>\n";
      echo "        <td>\n";

      $id = $d[DATA_ID];
      $HtmlName = $Langs[$k][LANG_FULL].IdName($d[DATA_ID]);
      $nav = '"./program.php?test='.$SelectedTest.'&amp;lang='.$k.'&amp;id='.$id.'"';
      echo "        <td><a href=$nav><span>", "$HtmlName</span></a>\n";

      $message = StatusMessage($d[DATA_STATUS]);

      $elapsed_message = '';
      $elapsed_td = '';
      $mem_message = '';
      $mem_td = '';
      $gzbytes_message = '';
      $gzbytes_td = '';
      $cpu_message = '';
      $cpu_td = '';
      $t_class = ' class="message"';

      if ($Sort=='elapsed'){
        $elapsed_message = $message;
        $elapsed_td = $t_class;
      } elseif ($Sort=='kb'){
        $mem_message = $message;
        $mem_td = $t_class;
      } elseif ($Sort=='gz'){
        $gzbytes_message = $message;
        $gzbytes_td = $t_class;
      } elseif ($Sort=='fullcpu'){ 
        $cpu_message = $message;
        $cpu_td = $t_class;
      }

      echo "        <td", $elapsed_td, ">", $elapsed_message, "\n";
      echo "        <td", $mem_td, ">", $mem_message, "\n";
      echo "        <td", $gzbytes_td, ">", $gzbytes_message, "\n";
      echo "        <td", $cpu_td, ">", $cpu_message, "\n";
      echo "        <td>\n";
    }
  }
}


echo "      </table>", "\n";
echo "      <nav>", "\n";
echo "        <ul>", "\n";
echo "          <li", $elapsed_th,">", $elapsed_nav_open, "by secs", $elapsed_nav_close, "\n";
echo "          <li", $mem_th,">", $mem_nav_open, "by mem", $mem_nav_close, "\n";
echo "          <li", $gzbytes_th,">", $gzbytes_nav_open, "by gz", $gzbytes_nav_close, "\n";
echo "          <li", $cpu_th,">", $cpu_nav_open, "by cpu", $cpu_nav_close, "\n";
echo "        </ul>", "\n";
echo "      </nav>", "\n";
?>
  </section>
</article>
<footer>
  <nav>
    <ul>
    </ul>
  </nav>
</footer>

