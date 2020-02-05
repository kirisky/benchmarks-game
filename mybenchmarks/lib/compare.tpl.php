<?   // Copyright (c) Isaac Gouy 2004-2020 ?>
<? 

// hardcode comparisons that will be offered

$vs_java = array( "id" => "java", "name" => "Java" );
$vs_php = array( "id" => "php", "name" => "PHP" );
$vs_python = array( "id" => "python", "name" => "CPython" );

$comparisons = array(
  "java" => array( $vs_python ),
  "php" => array( $vs_python ),
  "python" => array( $vs_java, $vs_php ),
);


$Row = $Langs[$SelectedLang];
$LangName = $Row[LANG_FULL];
$LangName2 = $Langs[$SelectedLang2][LANG_FULL];
$LangLink = $Row[LANG_LINK];
$LangLink2 = $Langs[$SelectedLang2][LANG_LINK];
?>
<article>
  <div>
    <h1><?=$LangName;?> versus <?=$LangName2;?> fastest programs</h1>
    <aside>

<? 
if (isset( $comparisons[$SelectedLang] )) {
  $cs = $comparisons[$SelectedLang]; 
  if (count($cs) > 1) {
    $default = $Langs[$SelectedLang][LANG_COMPARE];
    $page = $Langs[$SelectedLang][LANG_SPECIALURL];

    echo "    <nav>", "\n";
    echo "      <ul>", "\n";

    foreach($cs as $c){
      if ($SelectedLang2 == $c["id"]) {
        echo '        <li class="best">vs ', $c["name"], "\n";

      } elseif ($c["id"] == $default) {
          echo '        <li><a href="./compare.php?lang=', $SelectedLang, '&amp;lang2=', $c['id'], '"><span>vs ', $c["name"], "</span></a>\n";

      } else {

        echo '        <li><a href="./compare.php?lang=', $SelectedLang, '&amp;lang2=', $c['id'], '"><span>vs ', $c["name"], "</span></a>\n";

      }
    }

    echo "      </ul>", "\n";
    echo "    </nav>", "\n";
  } 
}
?>
    </aside> 
  </div>
  <section>
    <div>
        <p>Always look at the source code.
      <h2></h2>
    </div>
    <table>
<?
foreach($Data as $k => $rows){
  // Why would $k be NULL? No working programs for a test? 
  if ($k == NULL || $Tests[$k][TEST_WEIGHT]<=0){ continue; }
  $test = $Tests[$k];
  $testname = $test[TEST_NAME];
  $testlink = $test[TEST_LINK];

  if (!empty($rows)){

    echo "      <tbody>\n";
    echo "      <tr>\n";
    echo '        <th colspan="3"><a href="./performance.php?test=', $testlink, '"><span>', $testname, "</span></a>\n";
    echo '        <th colspan="3">', "\n";

    echo "      <tr>\n";
    echo "        <th>source\n";
    echo "        <th>secs\n";
    echo "        <th>mem\n";
    echo "        <th>gz\n";
    echo "        <th>cpu\n";
    echo "        <th>cpu load\n";

    $elapsed_td = '';
    if (isset($rows[0]) && isset($rows[1]) && ($rows[0][DATA_TIME] < $rows[1][DATA_TIME])){
      $elapsed_td = ' class="best"';
    }

    foreach($rows as $row){

      if (is_array($row)){
        $id = $row[DATA_ID];
        $lang = $row[DATA_LANG];
        $name = $Langs[$lang][LANG_FULL];
        $noSpaceName = str_replace(' ','&nbsp;',$name);

        echo "      <tr>\n";

        $nav = '"./program.php?test='.$k.'&amp;lang='.$lang.'&amp;id='.$id.'"';
        echo "        <td><a href=$nav>", "<span>$noSpaceName</span></a>\n";

        if ($row[DATA_STATUS] > PROGRAM_TIMEOUT){

          if ($row[DATA_ELAPSED]>0){ $e = number_format($row[DATA_ELAPSED],2); } else { $e = '?'; }
          echo "        <td", $elapsed_td, ">", $e, "\n";

          $kb = number_format($row[DATA_MEMORY]);
          echo "        <td>", $kb, "\n";

          $gz = $row[DATA_GZ];
          echo "        <td>", $gz, "\n";

          if ($row[DATA_FULLCPU]>0){ $fc = number_format($row[DATA_FULLCPU],2); } else { $fc = '?'; }
          echo "        <td>", $fc, "\n";

          $ld = CpuLoad($row);
          echo '        <td class="message">', $ld, "\n";

        } else {
          echo "        <td>&nbsp;", "\n";
          echo '        <td class="message">', StatusMessage($row[DATA_STATUS]), "\n";
          echo '        <td colspan="3">', "\n";
        }
        $elapsed_td = '';

      } elseif (!isset($row)) {

        echo "      <tr>\n";
        echo "        <td>&nbsp;", "\n";
        echo '        <td class="message">', 'No&nbsp;program', "\n";
        echo '        <td class="message" colspan="4">', '&nbsp;', "\n";
      }
    }

  } else { // empty($rows)     

    echo "      <tbody>\n";
    echo "      <tr>\n";
    echo "        <th>", $testname, "\n";
    echo '        <th colspan="5">', "\n";

    echo "      <tr>\n";
    echo "        <td>&nbsp;", "\n";
    echo '        <td class="message">', 'No&nbsp;programs', "\n";
    echo '        <td class="message" colspan="4">', '&nbsp;', "\n";
  }
}


// force version info into bottom of table

$name = $Langs[$SelectedLang][LANG_FULL];
$noSpaceName = str_replace(' ','&nbsp;',$name);
echo "      <tbody>\n";

echo "      <tr>\n";
echo "        <td>", "$noSpaceName\n";
echo '        <td colspan="5" class="message">', $LangVersion, "\n";

$name = $Langs[$SelectedLang2][LANG_FULL];
$noSpaceName = str_replace(' ','&nbsp;',$name);
echo "      <tr>\n";
echo "        <td>", "$noSpaceName\n";
echo '        <td colspan="5" class="message">', $Lang2Version, "\n";

echo "    </table>", "\n";
?>
    <nav>
      <ul>
        <li><a href="./measurements.php?lang=<?=$LangLink;?>"><span>all <?=$LangName;?> programs & measurements</span></a>
        <li><a href="./measurements.php?lang=<?=$LangLink2;?>"><span>all <?=$LangName2;?> programs & measurements</span></a>
      </ul>
    </nav>
  </section>
</article>
<footer>
  <nav>
    <ul>
    </ul>
  </nav>
</footer>

