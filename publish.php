<?php
function git(){
    $cmd = "git status ";
    exec($cmd);
    $cmd = "git add .  ";
    exec($cmd);
    $cmd = "git commit . -m 'update'";
    exec($cmd);
    $cmd = "git push";
    exec($cmd);

}

//git();
$pwd = trim(`pwd`);
$cmd = "rsync -avz --delete --exclude-from '{$pwd}/exclude.txt'   {$pwd}/* root@huawei.ssssec.cn:/www/wwwroot/qingscan.songboy.site/";
echo $cmd . "\n";
exec($cmd);