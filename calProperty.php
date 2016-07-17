<?php
require_once 'db.php';


// pr(calLabel());

function calLabel() {
    $datas = getDataFromDB();
    $labelGood = array('child','infant'); //'person'
    $liklyhood = array('UNKNOWN'=>0,'VERY_UNLIKELY'=>0.1,'UNLIKELY'=>0.3, 'POSSIBLE'=>0.5, 'LIKELY'=>0.7, 'VERY_LIKELY'=>1);

    $toReturn = array();
    $filter1 = array();

    // 1st filter
    foreach ($datas as $data) {
        $labels = (!empty($data['label'])) ? json_decode($data['label']) : array();
        $face = json_decode($data['face']);
        $imageProperties = json_decode($data['image_properties']);

        // label
        $description = array();
        foreach ($labels as $label) {
            $description[] =  $label->description;
        }
        $count = count(array_intersect($labelGood, $description));

        // label & face
        if($count > 0 && !empty($face)) {
            // color
            $dominantColorsArray = '<table><tr>';
            $dominantColors = $imageProperties->dominantColors->colors;
            foreach ($dominantColors as $dominantColor) {
                // $colors = $dominantColor->color;
                // $score = $dominantColor->score;
                // $pixel = $dominantColor->pixelFraction;
                $dominantColorsArray .= '<td><div class="colorbox" style="background-color:rgb('.$dominantColor->color->red.','.$dominantColor->color->green.','.$dominantColor->color->blue.')"></div><p>score: '.$dominantColor->score.'</p><p>pixelFraction: '.$dominantColor->pixelFraction.'</p></td>';
            }
            $dominantColorsArray .= '</tr></table>';

            $toReturn[] = array(
                // 'data' => $data,
                'img' => '<img src="upload/'.$data['img_id'].'" width=200/>',
                'joyLikelihood' => $liklyhood[$face[0]->joyLikelihood],
                'sorrowLikelihood' => $liklyhood[$face[0]->sorrowLikelihood],
                'angerLikelihood' => $liklyhood[$face[0]->angerLikelihood],
                'surpriseLikelihood' => $liklyhood[$face[0]->surpriseLikelihood],
                'dominantColors' => $dominantColor,
                'dominantColorsTag' => $dominantColorsArray
            );
        }
    }

    return $toReturn;
}

// debug用
function pr($print) {
    echo '<pre>'; print_r($print); echo '</pre>';
}
?>
<style>.colorbox{width:50px;height:50px;}</style>
