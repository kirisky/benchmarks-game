<?   // Copyright (c) Isaac Gouy 2010-2020 ?>

<? 
$Row = $Langs[$SelectedLang];
$LangName = $Row[LANG_FULL];
$LangTag = $Row[LANG_TAG]; 
?>
<article>
  <div>
    <h1>all <?=$LangName;?> programs & measurements</h1>
  </div>
  <section>
    <table>
<? 
// force version info into table

$noSpaceName = str_replace(' ','&nbsp;',$LangName);
echo "      <tbody>\n";

echo "      <tr>\n";
echo '        <td colspan="7" class="message">', $LangVersion, "\n";

$is_start = TRUE;
foreach($Data as $row){
  $test = $row[DATA_TEST];
  $id = $row[DATA_ID];
  $status = $row[DATA_STATUS];

  if (isset($prevTest) && isset($prevId)){
    if ($test != $prevTest || $id != $prevId){ $is_start = TRUE; }
    elseif (isset($prevStatus) && $prevStatus<0) { continue; }
  }
  $prevTest = $test;
  $prevId = $id;
  $prevStatus = $status;

  if ($row[DATA_TESTVALUE]==0){
    $n = '?'; 
  } else { 
    $n = '&nbsp;'.number_format($row[DATA_TESTVALUE]); 
  }

  if ($status<0){
    $e = StatusMessage($row[DATA_STATUS]); 
    $e_message = ' class="message"'; 
    $kb = ''; $gz = ''; $b = ''; $ld = '';
   } else {
    $e = ElapsedTime($row);
    $e_message = ''; 

    $kb = number_format((double)$row[DATA_MEMORY]);
    $gz = $row[DATA_GZ];
    $b = number_format($row[DATA_FULLCPU],2);
    $ld = CpuLoad($row);
  }

  if ($is_start){
      echo "      <tbody>\n";
      if ($status==0){
        echo "        <tr>\n";
        echo "          <th>source\n";
        echo "          <th>secs\n";
        echo "          <th>N\n";
        echo "          <th>mem\n";
        echo "          <th>gz\n";
        echo "          <th>cpu\n";
        echo "          <th>cpu load\n";
      }
      $is_start = FALSE;
  }

  echo "        <tr>\n";
  $TestName = $Tests[$row[DATA_TEST]][TEST_NAME];
  $nav = '"./program.php?test='.$test.'&amp;lang='.$row[DATA_LANG].'&amp;id='.$id.'"';
  echo "          <td><a href=$nav><span>", $TestName, "&nbsp;", IdName($id), "</span></a>\n";
  echo "          <td".$e_message.">", $e, "\n";
  echo "          <td>", $n, "\n";
  echo "          <td>", $kb, "\n";
  echo "          <td>", $gz, "\n";
  echo "          <td>", $b, "\n";
  echo '          <td class="message">', $ld, "\n";
} 

?>
    </table>
  </section>
</article>
<footer>
  <nav>
    <ul>
      <li>
    </ul>
  </nav>
</footer>
