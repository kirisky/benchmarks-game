<?
// Copyright (c) Isaac Gouy 2015-2020

// CONSTANTS ///////////////////////////////////////////////////

define('MARGIN',24);
define('SPACE_BETWEEN_GRIDLINES',10);

// CHART CLASS ///////////////////////////////////////////////////


class Chart {
   var $im,$w,$h,$colour;
   var $xo,$xscale,$xshift,$xaxis;
   var $yo,$yscale,$yshift,$yaxis;


   function Chart(){
      $this->w = $this->defaultWidth();
      $this->h = $this->defaultHeight();

      // chart origin may be temporarily reset to create multiple panels
      $this->xo = $this->defaultOriginX();
      $this->yo = $this->defaultOriginY();

      echo '<?xml version="1.0" encoding="UTF-8" standalone="no"?>', "\n";
      echo '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">', "\n";
      echo '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 ', $this->w, ' ', $this->h, '">', "\n";

      echo "\n", '<!-- Copyright 2015-2020 Isaac Gouy -->', "\n";
      echo '<!-- https://salsa.debian.org/benchmarksgame-team/benchmarksgame/blob/master/LICENSE.md -->', "\n", "\n";
   }

   function defaultWidth(){
      return 480;
   }
   
   function defaultHeight(){
      return 300;
   }
   
   function defaultOriginX(){
      return 58;
   }

   function defaultOriginY(){
      return MARGIN;
   }

   function complete(){
      echo '</svg>', "\n";
   }

   function lineSVG_($x1,$y1,$x2,$y2){
      echo '<line x1="', round($x1,2), '" y1="', round($y1,2), '" x2="', round($x2,2), '" y2="', round($y2,2), '" />', "\n"; 
   }

   function grectSVG_($x1,$y1,$x2,$y2){
      echo '<rect x="', round($x1,2), '" y="', round($y1,2), '" width="', round($x2-$x1,2), '" height="', round($y2-$y1,2), '" />', "\n"; 
   }

   function rectSVG_($x1,$y1,$x2,$y2,$fill,$stroke){
      echo '<rect x="', round($x1,2), '" y="', round($y1,2), '" width="', round($x2-$x1,2), '" height="', round($y2-$y1,2), 
         '" style="fill: ', $fill, '; stroke: ', $stroke, ';" />', "\n"; 
   }

   function frame(){
      echo '<g style="stroke: #333;">', "\n"; 
      $this->lineSVG_($this->xo, MARGIN, $this->xo, $this->h-MARGIN);
      $this->lineSVG_($this->w -1, MARGIN, $this->w -1, $this->h-MARGIN);
      $this->lineSVG_($this->xo, $this->h - $this->yo, $this->w -1, $this->h - $this->yo); 
      echo "</g>\n"; 
   }

   function title($label){
      echo '<text x="', ($this->xo/2.0) + ($this->w/2.0), '" y="', 18, '" style="text-anchor: middle; font-family: Ubuntu,Verdana,sans-serif; font-size: 1.2em;" >', $label, "</text>\n"; 
   }

   function notice($label){ 
      $x = $this->w;
      echo '<g style="text-anchor: end; font-family: Ubuntu,Verdana,sans-serif; font-size: 0.9em;" >', "\n"; 
      echo '<text x="', $x, '" y="', $this->h - 6, '" >', $label, "</text>\n"; 
      echo '<text x="', $this->xo, '" y="', $this->h - 6, '" style="text-anchor: start;" >', "benchmarks game", "</text>\n"; 
      echo "</g>\n";
      return $x;
   }

   function xAxisLegend($label,$size=null){
      $xmiddle = ($this->xo/2.0) + ($this->w/2.0);
      echo '<text x="', $xmiddle, '" y="', $this->h, '" style="text-anchor: middle; font-family: Ubuntu,Verdana,sans-serif; font-size: 0.9em;" >', $label, "</text>\n"; 
   }
   
   function yAxisLegend($label,$size=null){ 
      $ymiddle = $this->h/2.0;
      echo '<text x="', 16, '" y="', $ymiddle, '" transform="rotate(270,', 16,',', $ymiddle, ')" style="text-anchor: middle; font-family: Ubuntu,Verdana,sans-serif; font-size: 0.9em;" >', $label, "</text>\n"; 
   }

   function shiftAndScale(){
      if (isset($this->yaxis)){
         $this->yshift = $this->yaxis[0][0];
         $max = $this->yaxis[ sizeof($this->yaxis) - 1][0];
         $valuerange = ($this->yshift < 0) ? abs($max - $this->yshift) : $max - $this->yshift;
         $this->yscale = ($this->h - (2 * $this->yo)) / $valuerange;
      }
   }

   function yAxis($yaxis,$scale=null,$shift=null,$axistype=null){
      $this->yaxis = $yaxis;

      if (isset($scale)){
         $this->yscale = $scale;
         $this->yshift = $shift;
      } else {
         $this->shiftAndScale();
      }
      if (isset($axistype)){ $dir = -1.0; } else { $dir = 1.0; }

      echo '<g style="stroke: #ccc;" >', "\n"; 
      foreach($this->yaxis as $v){
         $y = $this->h - $this->yo - $dir*($v[0] * $this->yscale - $this->yshift * $this->yscale);
         if (!isset($firsty)){ $firsty = $y; }
         if (!isset($prev)||abs($prev-$y) > SPACE_BETWEEN_GRIDLINES){
            $this->lineSVG_($this->xo -7, $y, $this->w-1, $y);
            $prev = $y;
         }
      }
      echo "</g>\n"; 

      echo '<g style="text-anchor: end; font-family: Ubuntu,Verdana,sans-serif; font-size: 0.9em;" >', "\n"; 
      unset($firsty);
      foreach($this->yaxis as $v){
         $y = $this->h - $this->yo - $dir*($v[0] * $this->yscale - $this->yshift * $this->yscale);
         if (!isset($firsty)){ $firsty = $y; }
         if (!isset($prev)||abs($prev-$y) > SPACE_BETWEEN_GRIDLINES){
            echo '<text x="', round($this->xo -7,2), '" y="', round($y-2,2), '" >', $v[1], "</text>\n";
            $prev = $y;
         }
      }
      echo "</g>\n"; 
   }
   
   function xAxis($xaxis,$scale,$shift=null){
      echo '<g style="stroke: #ddd;">', "\n"; 
      $this->xaxis = $xaxis;
      $this->xscale = $scale;
      if (isset($shift)){ $this->xshift = $shift; } else { $this->xshift = 0; }

      foreach($this->xaxis as $v){
         $x = $this->xo + ($v[0] * $this->xscale + $this->xshift * $this->xscale);
//         ImageStringUp($this->im, 3, $x, $this->h - $this->yo - 0.5*CHAR_WIDTH_3, $v[1], $this->colour[LIGHT_GRAY]);
            $this->lineSVG_($x, $this->h - $this->yo, $x, $this->yo);
      }
      echo "</g>\n"; 
   }

}


// BOXCHART CLASS ///////////////////////////////////////////////////


// assume vertical box

class BoxChart extends Chart {

   var $boxwidth,$boxspace;

   function BoxChart(){
      Chart::Chart();
//      $this->boxwidth = 21;
//      $this->boxspace = 7;
//      $this->boxmiddle = 10;
      $this->boxwidth = 20;
      $this->boxspace = 5;
      $this->boxmiddle = 10;
//      $this->boxwidth = 18;
//      $this->boxspace = 5;
//      $this->boxmiddle = 9;

      $this->maxboxes = 20;
   }

   function boxAndWhiskers($d){
      $this->boxes($d);
      $this->whiskers($d);
   }

   function boxes($d){
      $n = sizeof($d);
      if ($n % STATS_SIZE == 0){
         $x = $this->xo + 4;
         $ys = abs($this->yshift) * $this->yscale;
         $count = 0;
         echo '<g style="fill: black; stroke: black;">', "\n"; 
         for ($i=0; $i<$n; $i+=STATS_SIZE){
            $xlower = $this->h - ($this->yo + $d[$i+STAT_XLOWER] * $this->yscale + $ys);
            $xupper = $this->h - ($this->yo  + $d[$i+STAT_XUPPER] * $this->yscale + $ys);
            $this->grectSVG_($x+$this->boxmiddle, $xupper, $x+$this->boxmiddle+1, $xlower);
            $x = $x + $this->boxwidth + $this->boxspace;
            if ($count == $this->maxboxes){ break; } else { $count++;  }
         }
         echo "</g>\n"; 

         echo '<g style="fill: white; stroke: black;">', "\n";
         $x = $this->xo + 4;
         $count = 0;
         for ($i=0; $i<$n; $i+=STATS_SIZE){
            $lower = $this->h - ($this->yo  + $d[$i+STAT_LOWER] * $this->yscale + $ys);
            $upper = $this->h - ($this->yo  + $d[$i+STAT_UPPER] * $this->yscale + $ys);

            $this->grectSVG_($x, $upper, $x+$this->boxwidth, $lower);

            $x = $x + $this->boxwidth + $this->boxspace;
            if ($count == $this->maxboxes){ break; } else { $count++;  }
         }
         echo "</g>\n"; 
      }
   }


   function whiskers($d){
      $n = sizeof($d);
      if ($n%STATS_SIZE == 0){
         $x = $this->xo + 4;
         $ys = abs($this->yshift) * $this->yscale;
         $count = 0;
         $whisk = ($this->boxwidth - $this->boxmiddle)/2;
         for ($i=0; $i<$n; $i+=STATS_SIZE){
            $xlower = $this->h - ($this->yo + $d[$i+STAT_XLOWER] * $this->yscale + $ys);
            $median = $this->h - ($this->yo + $d[$i+STAT_MEDIAN] * $this->yscale + $ys);
            $xupper = $this->h - ($this->yo + $d[$i+STAT_XUPPER] * $this->yscale + $ys);

            echo '<g style="fill: black; stroke: black;">', "\n"; 
            $this->lineSVG_($x+$whisk+0.5, $xlower, $x+$this->boxwidth-$whisk, $xlower);
            $this->lineSVG_($x+$whisk+0.5, $xupper, $x+$this->boxwidth-$whisk, $xupper);

            if ($d[$i+STAT_MIN] < $d[$i+STAT_XLOWER]){
               $y = $this->h - ($this->yo + $d[$i+STAT_MIN] * $this->yscale + $ys);

               $this->lineSVG_($x+$whisk+0.5, $y, $x+$this->boxwidth-$whisk, $y);
               $this->grectSVG_($x+$this->boxmiddle, $y-5, $x+$this->boxmiddle+1, $y);
            }
            if ($d[$i+STAT_MAX] > $d[$i+STAT_XUPPER]){
               $y = $this->h -($this->yo + $d[$i+STAT_MAX] * $this->yscale + $ys);

               $this->lineSVG_($x+$whisk+0.5, $y, $x+$this->boxwidth-$whisk, $y);
               $this->grectSVG_($x+$this->boxmiddle, $y, $x+$this->boxmiddle+1, $y+5);
            }
            echo "</g>\n"; 
            echo '<g style="fill: black; stroke: black;">', "\n"; 
            $this->grectSVG_($x+1, $median-1, $x+$this->boxwidth-1, $median);
            echo "</g>\n"; 

            $x = $x + $this->boxwidth + $this->boxspace;
            if ($count == $this->maxboxes){ break; } else { $count++;  }
         }
      }
   }
   
   function backgroundText($a){
      $chartArea_x = $this->xo;
      $chartArea_y = MARGIN;
      $chartArea_w = $this->w -2 -$this->xo;
      $chartArea_h = $this->h;

      echo "<defs>\n";

// Unfortunately clip-path is not supported well enough to use
//      echo '<clipPath id="chartArea">', "\n";
//      echo '<rect x="', $chartArea_x, '" y="', $chartArea_y, '" width="', $chartArea_w, '" height="', $chartArea_h, '" style="fill: none;" />', "\n";
//      echo "</clipPath>\n";

      echo '<linearGradient id="grade" x1="0" y1="0" x2="0" y2="1">', "\n";
      echo '<stop offset="0" style="stop-color: white" stop-opacity="0.6"/>', "\n";
      echo '<stop offset="1" style="stop-color: white" stop-opacity="0.8"/>', "\n";
      echo "</linearGradient>\n";

      echo '<mask id="fade">', "\n";
      echo '<rect x="', $chartArea_x,'" y="', $chartArea_y, '" width="', $chartArea_w,'" height="', $chartArea_h,'" fill="url(#grade)"  />', "\n";
      echo "</mask>\n";

      echo '<g id="backgroundText" style="text-anchor: end; font-family: Ubuntu,Verdana,sans-serif; font-size: 1em;">', "\n"; 
      $x = round($this->boxspace + ($this->boxwidth)/2.0,2);
      $yend = 0;

      foreach($a as $s){
         // ad-hoc string-sizing to fit chart height
         // because IE9 shows unclipped text below chart
         $repeat = ceil(30 / (1+strlen($s)));
         $s = str_repeat("   ".$s, $repeat);
         $n = (strlen($s) > 60) ? -60 : 0;
         $s = substr($s,$n);

         echo '<text x="', $x, '" y="', $yend, '" transform="rotate(270,', $x,',', $yend, ')" xml:space="preserve">', $s, "</text>\n"; 

         $x = round($x + $this->boxwidth + $this->boxspace,2);
         if ($count == $this->maxboxes){ break; } else { $count++; }
      }
      echo "</g>\n"; 
      echo "</defs>\n";

      // Unfortunately clip-path is not supported well enough to use
//      echo '<use x="', $this->xo+2, '" y="', MARGIN+3, '" xlink:href="#backgroundText" style="clip-path: url(#chartArea);"/>', "\n";

      echo '<use x="', $chartArea_x +2, '" y="', MARGIN+3, '" xlink:href="#backgroundText"/>', "\n";
      echo '<rect x="', $chartArea_x,'" y="', $chartArea_y,'" width="', $chartArea_w, '" height="', $chartArea_h,'" fill="white" mask="url(#fade)" />', "\n";

      // Big white rectangle
      // because IE9 shows unclipped text below chart
      echo '<rect x="', $chartArea_x,'" y="', $chartArea_h -MARGIN,'" width="', $chartArea_w, '" height="', $chartArea_h,'" style="fill: white; stroke: white;" />', "\n";
   }

}



// KDECHART CLASS ///////////////////////////////////////////////////


class KDEChart extends BoxChart {

   function KDEChart(){
      BoxChart::BoxChart();
   }

   function defaultWidth(){
      return 512;
   }

   function defaultHeight(){
      return 288;
   }
}


// AXIS DEFINITIONS ///////////////////////////////////////////////////

function axis500(){
   return array(
      array(1,"1"), array(3,"3"), array(5,"5"),
      array(10,"10"), array(30,"30"), array(50,"50"),
      array(100,"100"), array(300,"300"), array(500,"")
      );
}


function log10axis($a){
   $log10a = array();
   foreach($a as $v){ $log10a[] = array(log10($v[0]),$v[1]); }
   return $log10a;
}

?>
