<?php
function libPuzzle($file1, $file2){
	$cvec1 = puzzle_fill_cvec_from_file($file1);
	$cvec2 = puzzle_fill_cvec_from_file($file2);

	// ２つのシグネチャから、近似度を判定する
	$d = puzzle_vector_normalized_distance($cvec1, $cvec2);
	return $d;
}

function checkPastFile($checkImage){
    $imageDir = "upload/" ;
    $images = array();
	$differences = array();
    if( is_dir( $imageDir ) && $handle = opendir( $imageDir ) ) {
        while( ($file = readdir($handle)) !== false ) {
            if( filetype( $path = $imageDir . $file ) == "file" ) {
                //同じと判断する閾値
                $libPuzzle = libPuzzle($checkImage,$path);
				$differences[] = $libPuzzle;
                // if($libPuzzle < 0.2) {
                //     return false;
                // }
                array_push($images, $path);
            }
        }
    }
	return array($images, $differences);
    // return true;
}

// if (checkPastFile("apple1.jpg")) {
//     echo "新規画像です。";
// } else {
//     echo "すでに存在する画像です。";
// }
?>
