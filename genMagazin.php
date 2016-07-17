<?php
require_once 'calProperty.php';

$datas = calLabel();
$json = makeJSON($datas);
echo $json;

// TODO send to reader

function makeJSON($datas){
	$best = getBest($datas);
	$contents = makeContents($datas);
	$arr = array(
			'number' => makeNumber(),
			'title' => $best['title'],
			'image' => $best['image'],
			'color' => makeColor($best),
			'pageNum' => count($contents),
			'contents' => $contents
		    );
	return json_encode($arr, JSON_PRETTY_PRINT);
}

function getBest($datas) {
	$joyMax = 0;
	$img;
	foreach ($datas as $data) {
		$joy = $data['joyLikelihood'];
		if($joy > $joyMax){
			$joyMax = $joy;
			$img = $data['img'];
		}
	}

	return array(
		'title' => 'dummy-title', // TODO
		'image' => $img
	);
}

function makeNumber(){
	$year = 2016;
	$month = 7;
	$week = 4;

	return array('year' => $year, 'month' => $month, 'week' => $week);
}

function makeColor($best){
	return 'red'; // TODO
}

function groupByLikelihood($datas){
	$joyArr = $sorrowArr = $angerArr = $surpriseArr = $otherArr = [];
	
	foreach($datas as $data){
		if ($data['joyLikelihood'] >= 0.5){
			$joyArr[] = $data;
		} else if($data['sorrowLikelihood'] >= 0.5){
			$sorrowArr[] = $data;
		} else if($data['angerLikelihood'] >= 0.5){
			$angerArr[] = $data;
		} else if($data['surpriseLikelihood'] >= 0.5){
			$surpriseArr[] = $data;
		} else {
			$otherArr[] = $data;
		}
	}
	return array(
		'joy' => $joyArr,
		'sorrow' => $sorrowArr,
		'anger' => $angerArr,
		'surprise' => $surpriseArr,
		'other' => $otherArr,
	);
}

function makeContents($datas){
	$groups = groupByLikelihood($datas);
	$joyGroup = $groups['joy'];
	$sorrowGroup = $groups['sorrow'];
	$angerGroup = $groups['anger'];
	$surpriseGroup = $groups['surprise'];
	$otherGroup = $groups['other'];
	
	$contentArray = [];
	if(!empty($joyGroup)){
		addContent($contentArray, $joyGroup, 1, "楽");
	}
	if(!empty($sorrowGroup)){
		addContent($contentArray, $sorrowGroup, 1, "悲");
	}
	if(!empty($angerGroup)){
		addContent($contentArray, $angerGroup, 1, "怒");
	}
	if(!empty($surpriseGroup)){
		addContent($contentArray, $surpriseGroup, 1, "驚");
	}
	if(!empty($otherGroup)){
		addContent($contentArray, $otherGroup, 0, "他");
	}
	return $contentArray;
}

function addContent(&$arr, $group, $template, $text){
	$IMG_NUM_PER_PAGE = 3;
	$DEFAULT_TEMPLATE = 0;

	$chunk = array_chunk($group, $IMG_NUM_PER_PAGE);
	$arr[] = array(
		"templateNo" => $template,
		"text" => $text,
		"images" => makeImages($chunk[0]),
	);
	for($i = 1; $i < count($chunk); ++$i){
		$arr[] = array(
			"templateNo" => $DEFAULT_TEMPLATE,
			"text" => $text,
			"images" => makeImages($chunk[$i]),
		);
	}
}

function makeImages($group){
	$array = [];
	foreach($group as $data){
		$array[] = makeImage($data['img'], 'dummy-caption'); // TODO
	}
	return $array;
}

function makeImage($url, $caption){
	return array('url' => $url, 'caption' => $caption);
}
?>
