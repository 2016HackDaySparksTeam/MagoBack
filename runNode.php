<?php
// $file = 'kansei/app.js';
$file = 'kansei/color_analyze_3.js';

exec("pgrep node", $pid);
if(empty($pid)) {
    // node is not running!
    exec('node ' . $file . ' >/dev/null 2>&1 & echo $!');

    exec("pgrep node", $newpid);
    echo 'node is running on ' . implode(',', $newpid) . PHP_EOL;
} else {
    echo 'node is already running on by pid ' . implode(',', $pid) . PHP_EOL;
    echo 'you can use `kill -9 '. implode(',', $pid) . '` to stop the service.' . PHP_EOL;
}
?>
