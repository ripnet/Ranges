<?php

require "Ranges.php";

$r = new Ranges();
$r->addRange(1, 5);
$r->addRange(3, 7);
$r->addRange(6, 10);
$r->addRange(11, 15);
$r->addRange(17, 20);
$r->addRange(18, 19);
print_r($r->getAllRanges());
print $r->getCount() . "\n";
print_r($r->getFirst(20)->getAllRanges());
print_r($r->getAllRanges());