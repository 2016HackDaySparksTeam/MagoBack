<?php
require_once 'kansei/kansei_analyze.php';

$img = 'upload/4e5020f476df46c43b3e4496e0738515.jpg';
$type = 'rgb';
$csv = 'kansei/rgbdata.csv';
analyze($img, $type, $csv);
?>
