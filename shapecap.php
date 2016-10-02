<?php
session_start();

sleep(2); // do not directly give the image to slow down a brute-force or dictionairy attack

$xmax = 1000;   // image resolution x
$ymax = 300;    // image resolution y
$angles_used = [3, 4, 5, 15];   // triangle, rectangle, 5pt polygon, circle
$shape_count = 10;      // maximum number of shapes
$noise_px = 3;          // maximum noise that is added (pixel offset)

function getCoords($x, $y, $radius, $angles, $max_noise = 10){
  $offset = rand(0 ,360)*pi()/180;    // random rotation
  $coordinates = array();
  for($i=0; $i<$angles; $i++){
    $c_index = 2*$i;
    $x_noise = rand(-$max_noise, $max_noise); // adds a few pixels of random offset
    $y_noise = rand(-$max_noise, $max_noise);
    $coordinates[$c_index]   = $x + ($radius * cos((2/$angles)*pi()*$i+$offset))+$x_noise;
    $coordinates[$c_index+1] = $y + ($radius * sin((2/$angles)*pi()*$i+$offset))+$y_noise;
  }
  return $coordinates ;
}

$im = imagecreate( $xmax, $ymax );
$white = imagecolorallocate($im, 255, 255, 255);
$black = imagecolorallocate( $im, 51, 122, 183);

// $max_r = 2*sqrt(pow($xmax, 2)+pow($ymax, 2))/($shape_count);  // make the radius such that all shapes can be on the diagonal of the image
$max_r = max($xmax,$ymax)/($shape_count);   // This is good enough for the maximum radius (and less computationally intensive)
$max_r = min($max_r, $xmax/2, $ymax/2);     // Make sure that the figures fit in the image
$min_r = $max_r/2;
$numangles = sizeof($angles_used);

$x = []; $y = [];
$angles = [];
$radius = [];
for($i=0; $i<$shape_count; $i++){
  $angles[$i] = $angles_used[rand(0, $numangles-1)];   // determines the shape
  $radius[$i] = rand($min_r, $max_r);
  $x[$i] = rand($radius[$i], $xmax-$radius[$i]);
  $y[$i] = rand($radius[$i], $ymax-$radius[$i]);

  // if too close to other shape then don't draw it
  $too_close = 0;
  for($j=0; $j<$i; $j++){
    if (($angles[$i]>0) && (abs($x[$i]-$x[$j])<$radius[$j]) && (abs($y[$i]-$y[$j])<$radius[$j])) $too_close=1;
  }

  if ($too_close==0){ // only draw the shape if not too close to other shape
    $values = getCoords($x[$i], $y[$i], $radius[$i], $angles[$i], $noise_px);
    imagefilledpolygon($im, $values, $angles[$i], $black);
  } else{ // reset all values because this shape is not drawn
    $x[$i] = 0; $y[$i] = 0;
    $angles[$i] = 0;
    $radius[$i] = 0;
  }
  $_SESSION['captcha_angles'] = $angles;  // store the angles array. Later this can be used to verify the user input
}

header( "Content-type: image/png" );
imagepng( $im );
imagedestroy( $im );
?>
