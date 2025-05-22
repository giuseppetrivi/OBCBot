<?php

require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/project_autoloader.php';
require_once __DIR__ . "/control/states_autoloader.php";

/** file to do some tests */



/** TROVARE LA LOCALITA' */
$localita_eu = file_get_contents("local_db/localita_eu.json");
$localita_eu = json_decode($localita_eu, true);
$count_localita_eu = count($localita_eu);

$string_to_search = "foggia  ";
$assoc_array_perc = [];
for ($i=0; $i<$count_localita_eu; $i++) {
  $similarity_perc = 0;
  $nome_localita = $localita_eu[$i]["denominazione"];

  /* Toglie gli spazi bianchi da inizio e fine stringa e mette tutto in minuscolo */
  $s1 = trim(strtolower($nome_localita));
  $s2 = trim(strtolower($string_to_search));
  similar_text($s1, $s2, $similarity_perc);

  $assoc_array_perc[$nome_localita] = $similarity_perc;
}

$start = microtime(true);
/* Funzione che ordina in ordine descrescente, mantenendo l'associatività dell'array (quindi gli indici testuali) */
arsort($assoc_array_perc);
$end = microtime(true);
$total = $end - $start;
echo "asort: " . $start . " - " . $end . " = " . $total . "<br><br>";


foreach ($assoc_array_perc as $k => $v) {
  echo $k . " ==> " . $v . "<br>";
}




?>