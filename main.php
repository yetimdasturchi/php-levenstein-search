<?php
function levenshtein_distance($s,$t) {
  $m = strlen($s);
  $n = strlen($t);

  for($i=0;$i<=$m;$i++) $d[$i][0] = $i;
  for($j=0;$j<=$n;$j++) $d[0][$j] = $j;

  for($i=1;$i<=$m;$i++) {
    for($j=1;$j<=$n;$j++) {
      $c = ($s[$i-1] == $t[$j-1])?0:1;
      $d[$i][$j] = min($d[$i-1][$j]+1,$d[$i][$j-1]+1,$d[$i-1][$j-1]+$c);
    }
  }

  return $d[$m][$n];
}

function extract_words($text)
{
  $text = trim( preg_replace( array( '/\s{2,}/', '/[\t\n]/' ), ' ', $text ) );
  return preg_split("/[^\w]*([\s]+[^\w]*|$)/", $text, -1, PREG_SPLIT_NO_EMPTY);;
}

function search_dis($query, $text, $minlev=4)
{
    $words = extract_words($text);
    $diss = 0;
    foreach($words as $word){
      $lev = levenshtein_distance($query, $word);  
      if($lev < $minlev) $diss++;
    }
    return $diss;
}


$search_word = "primnyra";

$database = file_get_contents('database.json');
$database = json_decode($database, TRUE);
$temp = [];
$x = 0;
foreach ($database as $item) {
  $diss = search_dis( $search_word, $item['title']);
  if($diss > 0){
    $temp[] = [
      'title' => $item['title'],
      'url' => $item['url'],
      'distance' => $diss
    ];
    if ($x==1) {
    break;
  }
  $x++;
  }
  
  
}
usort($temp, function($a, $b) {
    if($a['distance']==$b['distance']) return 0;
    return $a['distance'] > $b['distance']?1:-1;
});

print_r($temp);
