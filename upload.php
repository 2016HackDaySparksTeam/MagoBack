<?php
// require_once 'libPuzzle.php';
// $checkPastFile = checkPastFile('upload/821429f1089fa0935761248db17cc749.jpg');
// pr($checkPastFile);
if (!empty($_FILES['image'])) {
    //似ている画像があるかをチェック


    // 写真アップロード
    uploadImage($_FILES['image']);
}

/**
 * 写真uidつけてサーバにアップロード
 * CloudVisionに写真をなげ
 * 返してきたjsonをdbに保存.
 *
 * @param array $file swiftからpostしたデータ
 *
 * @return [type] [description]
 */
function uploadImage($file)
{
    $path = $_SERVER['DOCUMENT_ROOT'].'/upload/';
    $allowedImgType = array('image/png','image/x-png','image/jpg','image/jpeg','image/pjpeg','image/gif');
    if (in_array($file['type'], $allowedImgType) &&
    $file['size'] > 0) {
        switch ($file['type']) {
            case 'image/png':
            case 'image/x-png':
                $ext = 'png';
                break;

            case 'image/jpg':
            case 'image/jpeg':
            case 'image/pjpeg':
                $ext = 'jpg';
                break;

            case 'image/gif':
                $ext = 'gif';
                break;

            default:
                $ext = 'jpg';
                break;
        }

        // $fullname = strtolower($file['name']);
        // $name = substr($fullname, 0, strrpos($fullname, '.'));
        // $ext = substr($fullname, strrpos($fullname, '.') + 1);
        // $newName = $name.date('YmdHis').'.'.$ext;
        $newName = md5(uniqid()).'.'.$ext;

        // アップロード
        if (move_uploaded_file($file['tmp_name'], $path.$newName)) {
            $image_path = 'upload/'.$newName;
            echo 'アップロードできました。<br />';
            echo '<img src="'.$image_path.'" /><br />';

            // CloudVision
            require_once 'getjson.php';
            $json = getJson($image_path);
            $object = json_decode($json);

            // dbに保存するデータの準備
            $face = json_encode($object->responses[0]->faceAnnotations);
            $label = json_encode($object->responses[0]->labelAnnotations);
            $text = json_encode($object->responses[0]->textAnnotations);
            $safeSearch = json_encode($object->responses[0]->safeSearchAnnotation);
            $imageProperties = json_encode($object->responses[0]->imagePropertiesAnnotation);

            require_once 'db.php';
            $res['img_id'] = $newName;
            $res['face'] = $face;
            $res['label'] = $label;
            $res['text'] = $text;
            $res['safe_search'] = $safeSearch;
            $res['image_properties'] = $imageProperties;

            // dbに保存
            insertResult($res);

            exit(0);
        } else {
            die('アップロードできませんでした！：（');
        }
    } else {
        die('ファイルタイプがおかしい！');
    }
}

// debug用
function pr($print) {
    echo '<pre>'; print_r($print); echo '</pre>';
}
?>


<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>" enctype="multipart/form-data">
    <label for="file">画像</label>
    <input type="file" name="image">
    <br />
    <button type="submit">アップロード</button>
</form>
