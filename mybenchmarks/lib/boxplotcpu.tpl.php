<?   // Copyright (c) Isaac Gouy 2015-2020 ?>
<? 
  list($labels,$gm,$stats) = $Data;
  unset($Data);  
  unset($gm); 

  $chart = 'chartboxSVGcpu.php';
?>
<img src="<?=$chart;?>?<?='s='.Encode($stats);?>&amp;<?='m='.Encode($Mark);?>&amp;<?='w='.Encode($labels);?>">
