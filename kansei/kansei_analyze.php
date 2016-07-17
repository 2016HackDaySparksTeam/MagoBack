<?php
class SenseOfColor
{
    private $filepath;

    public function __construct($filepath)
    {
        $this->filepath = $filepath;
    }
    public function getAverage()
    {
        $kansei_avg = array();
        //.jpeg
        $img  = imagecreatefromjpeg($this->filepath);
        $imgX = imagesx($img);
        $imgY = imagesy($img);
        $imgXY = $imgX*$imgY;
        $rSum = '';
        $gSum = '';
        $bSum = '';
        for ($y = 0; $y < $imgY; $y++) {
            for ($x = 0; $x < $imgX; $x++) {
                $rgb = imagecolorat($img, $x, $y);
                //10進数に
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                //合算
                $rSum += $r;
                $gSum += $g;
                $bSum += $b;
            }
        }
        //合算された赤(R),緑(G),青(B)をそれぞれ画像の合計px数で割り、
        //再度16進数に変換した上で出力
        $kansei_avg['AverageColor'] = '#'.dechex($rSum/$imgXY).dechex($gSum/$imgXY).dechex($bSum/$imgXY);

        return $kansei_avg;
    }
    public function getList()
    {
        $rgb_arr = array();

        //.jpeg
        $img  = imagecreatefromjpeg($this->filepath);

        $imgX = imagesx($img);
        $imgY = imagesy($img);

        for ($y = 0; $y < $imgY; $y++) {
            for ($x = 0; $x < $imgX; $x++) {
                $rgb = imagecolorat($img, $x, $y);
                //10進数に
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                array_push($rgb_arr,$r,$g,$b);
            }
        }
        return $rgb_arr;
    }
}

class SenseOfColorFactory
{
    public static function create($filepath)
    {
        return new SenseOfColor($filepath);
    }
}

/**
 * [analyze image]
 * @param  [string] $img  [image file]
 * @param  [string] $type [rgb, null]
 * @return [type]       [kansei, averageColor]
 */
function analyze($img, $type = '', $csv = '') {
    $target = SenseOfColorFactory::create($img);

    header('Content-type: application/json');

    if($type == 'rgb'){
        $url = 'http://160.16.201.12:49999';

        $data1= ["data"];
        $data = ($target->getList());

        $arr_data = array($data);

        // exec ("echo > ./rgbdata.csv");
        exec('echo > '. $csv);

        $fp = fopen($csv, 'w');
        foreach ($arr_data as $fields) {
            fputcsv($fp, $fields);
        }
        fclose($fp);

        $options = array('http' => array(
            'method' => 'POST',
            'content' => http_build_query($data1),
        ));
        $contents = file_get_contents($url, false, stream_context_create($options));
        print $contents;

    }else{
        print json_encode($target->getAverage());
    }
}
