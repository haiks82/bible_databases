<?php
// Create MySQL Connection
$mainlink = new PDO('mysql:host=localhost; dbname=bible;charset=utf8', 'bible','bible');
$mainlink->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$mainlink->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

// Viewport
echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0, user-scalable=no\">";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"./style.css\">";

echo "<div class=\"container\">";
// Query Input
echo "<form method=\"get\" action=\"./\">";
echo "<select name=\"translation\" class=\"forminput\">";
foreach($mainlink->query("select * from bible_version_key") as $trsl){
echo "<option value=\"$trsl[sqltable]\">$trsl[abbreviation]</option>";
}
echo "</select>";
echo "<input type=\"text\" name=\"query\" class=\"forminput\">";
echo "<button class=\"forminput\">Search</button>";
echo "</form>";

// Variables
$query = $_REQUEST[query];
$trsl = $_REQUEST[translation];
if ($query != ''){

// Query Formatting
$qtc = preg_replace('/\:+/', ' ', $query);
$qth = preg_replace('/\-+/', ' ', $qtc);
$qts = preg_replace('/\s+/', ' ', $qth);

// Condition Check
if (strpos($query, '-') !== false){
$range = true;
}

$partials = explode(' ', $qts);

// Numbered Book Name and ID
if (is_numeric($partials[0])){
$numberedbook = true;
$numbook = $partials[0]." ".$partials[1];
foreach($mainlink->query("select * from key_english where n like '$numbook%'") as $bookdb){
$b = str_pad($bookdb[b], 2, "0", STR_PAD_LEFT);
$book = $bookdb[n];
}
  // Query Variables
$c = str_pad($partials[2], 3, "0", STR_PAD_LEFT);
$v = str_pad($partials[3], 3, "0", STR_PAD_LEFT);
  // Chapter Condition Check, Query Variables
if (empty($partials[3])){
$r1 = '001';
foreach($mainlink->query("select count(*) as cnt from $trsl where b='$b' and c='$c'") as $versecnt){
$r2 = str_pad($versecnt[cnt], 3, "0", STR_PAD_LEFT);
}
$chapter = true;
}
}else{

// Book Name and ID
foreach($mainlink->query("select * from key_english where n like '$partials[0]%'") as $bookdb){
$b = str_pad($bookdb[b], 2, "0", STR_PAD_LEFT);
$book = $bookdb[n];
}
  // Query Variables
$c = str_pad($partials[1], 3, "0", STR_PAD_LEFT);
$v = str_pad($partials[2], 3, "0", STR_PAD_LEFT);
  // Chapter Condition Check, Query Variables
if (empty($partials[2])){
$r1 = '001';
foreach($mainlink->query("select count(*) as cnt from $trsl where b='$b' and c='$c'") as $versecnt){
$r2 = str_pad($versecnt[cnt], 3, "0", STR_PAD_LEFT);
}
$chapter = true;
}
}

// SQL Queries
if ($chapter === true){
$qrystrr1 = $b.$c.$r1;
$qrystrr2 = $b.$c.$r2;
echo "<div><h2>$book ".ltrim($c, '0')."</h2></div>";
foreach($mainlink->query("select * from $trsl where (id between '$qrystrr1' and '$qrystrr2')") as $bibledb){
echo "<div class=\"vref\"><b><sup>".$bibledb[v]."</sup></b></div> ".$bibledb[t]."&nbsp;&nbsp;";
}
}elseif ($range === true){
if ($numberedbook === true){
$r1 = str_pad($partials[3], 3, "0", STR_PAD_LEFT);
$r2 = str_pad($partials[4], 3, "0", STR_PAD_LEFT);
$qrystrr1 = $b.$c.$r1;
$qrystrr2 = $b.$c.$r2;
echo "<div><h2>$book ".ltrim($c, '0')."</h2></div>";
foreach($mainlink->query("select * from $trsl where (id between '$qrystrr1' and '$qrystrr2')") as $bibledb){
echo "<div class=\"vref\"><b><sup>".$bibledb[v]."</sup></b></div> ".$bibledb[t]."&nbsp;&nbsp;";
}
}else{
$r1 = str_pad($partials[2], 3, "0", STR_PAD_LEFT);
$r2 = str_pad($partials[3], 3, "0", STR_PAD_LEFT);
$qrystrr1 = $b.$c.$r1;
$qrystrr2 = $b.$c.$r2;
echo "<div><h2>$book ".ltrim($c, '0')."</h2></div>";
foreach($mainlink->query("select * from $trsl where (id between '$qrystrr1' and '$qrystrr2')") as $bibledb){
echo "<div class=\"vref\"><b><sup>".$bibledb[v]."</sup></b></div> ".$bibledb[t]."&nbsp;&nbsp;";
}
}
}else{
$qrystr = $b.$c.$v;
echo "<div><h2>$book ".ltrim($c, '0')."</h2></div>";
foreach($mainlink->query("select * from $trsl where id='$qrystr'") as $bibledb){
echo "<div class=\"vref\"><b><sup>".$bibledb[v]."</sup></b></div> ".$bibledb[t]."&nbsp;&nbsp;";
}
}
}
echo "</div>";
?>
