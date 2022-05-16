<?php


namespace app\model;

use think\facade\App;
use think\facade\Db;

class GoogleModel extends BaseModel
{

    // 谷歌截图
    public static function jietu()
    {
        $file_path = App::getRootPath() . 'public/screenshot/';
        while (true) {
            processSleep(1);
            $list = self::getAppStayScanList('screenshot_time',5);
            foreach ($list as $v) {
                self::scanTime('app', $v['id'], 'screenshot_time');
                PluginModel::addScanLog($v['id'], __METHOD__, 0);
                $host = parse_url($v['url'])['host'];
                $filename = "{$file_path}{$host}.png";
                $cmd = "/usr/bin/google-chrome --headless --screenshot='{$filename}' '{$v['url']}'";
                echo $cmd . PHP_EOL;
                exec($cmd);
                $data['jietu_path'] = $filename;

                if (Db::name('app_info')->where(['app_id' => $v['id']])->find()) {
                    Db::name('app_info')->where(['app_id' => $v['id']])->update($data);
                } else {
                    $data['app_id'] = $v['id'];
                    Db::name('app_info')->insert($data);
                }
                PluginModel::addScanLog($v['id'], __METHOD__, 0,1);
            }
            echo "Google浏览器获取基础信息完成，休息5秒" . PHP_EOL;
            sleep(5);
        }

    }

    // 获取网页基本信息
    public static function getBaseInfo()
    {
        while (true) {
            processSleep(1);
            $list = self::getAppStayScanList('screenshot_time');
            foreach ($list as $v) {
                //更新扫描时间
                self::scanTime('app', $v['id'], 'screenshot_time');

                PluginModel::addScanLog($v['id'], __METHOD__, 0);
                if (empty($v['url'])) {
                    PluginModel::addScanLog($v['id'], __METHOD__, 0,2);
                    addlog(["获取APP基础信息失败，没有找到URL", $v]);
                    continue;
                }

                $result = curl_get_url_head($v['url']);
                if (empty($result)) {
                    PluginModel::addScanLog($v['id'], __METHOD__,0, 2);
                    addlog(["获取APP基础信息失败，没有获得头信息", $v['url']]);
                    continue;
                }
                $data['statuscode'] = $result['code'];
                $data['header'] = json_encode($result['header']);
                $content = file_get_contents($v['url']);
                preg_match("/<head.*>(.*)<\/head>/smUi", $content, $htmlHeaders);

                if (empty($htmlHeaders)) {
                    PluginModel::addScanLog($v['id'], __METHOD__,0,2);
                    addlog(["未匹配到head信息", $v['url'], $content]);
                    continue;
                }
                // 取得 <title> 中的文字
                preg_match("/<title>(.*)<\/title>/Us", $htmlHeaders[1], $htmlTitles);
                if (empty($htmlTitles)) {
                    PluginModel::addScanLog($v['id'], __METHOD__,0, 2);
                    addlog(["未匹配到title信息", $v['url'], $htmlTitles]);
                    continue;
                }
                $title = $htmlTitles[1];
                $data['page_title'] = trim($title);

                $icon = curl_get($v['url'] . '/favicon.ico');
                $host = parse_url($v['url'])['host'];
                $filename = App::getRootPath()."public/icon/{$host}.ico";
                file_exists(dirname($filename)) ? true : mkdir(dirname($filename), 0777, true);
                file_put_contents($filename, $icon);
                $data['icon'] = $filename;

                addlog(["获取一个站点的基础信息完成", $data]);
                if (Db::name('app_info')->where(['app_id' => $v['id']])->find()) {
                    Db::name('app_info')->where(['app_id' => $v['id']])->update($data);
                } else {
                    $data['app_id'] = $v['id'];
                    Db::name('app_info')->extra("IGNORE")->insert($data);
                }

                PluginModel::addScanLog($v['id'], __METHOD__, 0,1);
            }
            echo "Google浏览器获取基础信息完成，休息30秒" . PHP_EOL;
            sleep(30);
        }

    }
}