<?php


namespace app\model;

use think\facade\App;
use think\facade\Db;

class GoogleModel extends BaseModel
{
    // 谷歌截图
    public static function screenshot()
    {
        while (true) {
            $list = Db::name('app')->whereTime('screenshot_time', '<=', date('Y-m-d H:i:s', time() - (86400 * 15)))->where('is_delete', 0)->orderRand()->limit(5)->field('id,url')->select();
            $file_path = App::getRootPath() . 'public/screenshot/';
            foreach ($list as $v) {
                if (empty($v['url'])) {
                    addlog(["获取APP基础信息失败，没有找到URL", $v]);
                    //更新扫描时间
                    self::updateScanTime($v['id']);
                    continue;
                }

                $result = curl_get_url_head($v['url']);
                if (empty($result)) {
                    addlog(["获取APP基础信息失败，没有获得头信息", $v['url']]);
                    //更新扫描时间
                    self::updateScanTime($v['id']);
                    continue;
                }
                $data['statuscode'] = $result['code'];
                $data['header'] = json_encode($result['header']);
                preg_match("/<head.*>(.*)<\/head>/smUi", $result['content'], $htmlHeaders);
                if (count($htmlHeaders)) {
                    // 取得 <head> 中 meta 设置的编码格式
                    if (preg_match("/<meta[^>]*http-equiv[^>]*charset=(.*)(\"|')/Ui", $htmlHeaders[1], $results)) {
                        $charset = $results[1];
                    } else {
                        $charset = "GBK";
//                    $charset = "None";
                    }
                    // 取得 <title> 中的文字
                    if (preg_match("/<title>(.*)<\/title>/Ui", $htmlHeaders[1], $htmlTitles)) {
                        if (count($htmlTitles)) {
                            // 将  <title> 的文字编码格式转成 UTF-8
                            if ($charset == "None") {
                                $title = $htmlTitles[1];
                            } else {
                                $title = iconv($charset, "UTF-8", $htmlTitles[1]);
                            }
                            $data['page_title'] = $title;
                        }
                    }
                }
                $icon = curl_get($v['url'] . '/favicon.ico');
                $host = parse_url($v['url'])['host'];
                $filename = "icon/{$host}.ico";
                file_exists(dirname($filename)) ? true : mkdir(dirname($filename), 0777, true);
                file_put_contents($filename, $icon);
                $data['icon'] = $filename;

//                $host = parse_url($v['url'])['host'];
//                $filename = "{$file_path}{$host}.png";
//                $cmd = "/usr/bin/google-chrome --headless --screenshot='{$filename}' '{$v['url']}'";
//                echo $cmd.PHP_EOL;
//                exec($cmd);
//                $data['url_screenshot'] = $filename;


                if (Db::name('app_info')->where(['app_id' => $v['id']])->find()){
                    Db::name('app_info')->where(['app_id' => $v['id']])->update($data);
                }else{
                    $data['app_id'] = $v['id'];
                    Db::name('app_info')->insert($data);
                }

                //更新扫描时间
                self::updateScanTime($v['id']);

            }
            echo "Google浏览器获取基础信息完成，休息5秒" . PHP_EOL;
            sleep(5);
        }

    }

    public static function updateScanTime($id)
    {
        $data = ['screenshot_time' => date('Y-m-d H:i:s', time())];
        Db::name('app')->where('id', $id)->update($data);
    }
}