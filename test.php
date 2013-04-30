<?php
require 'vendor/autoload.php';

$test = new ArrayChain\Chain(range(1, 10));
$result = $test->filter(function($i) {
    return $i % 2 == 0;
  })
  ->map(function($i) {
    return 'i: '.$i;
  })
  ->join('; ');

var_dump($result instanceof ArrayChain\Chain ? $result->get() : $result);
