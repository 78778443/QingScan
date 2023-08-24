<?php


namespace app\model;


use think\facade\Db;
use function Sodium\add;

class GithubNoticeModel extends BaseModel
{
    public static function getNotice()
    {

            $url = 'https://github.com/advisories?page=';
            $shouye_temp = file_get_contents($url);
            if ($shouye_temp == false) {
            addlog("获取GitHub公告首页失败....");
            return false;
            }
            // 获取总页数
            $regex = '/data-total-pages=\"(\d+)\"/';
            preg_match($regex, $shouye_temp, $matches);
            if (isset($matches[1]) == false) {
            addlog("获取GitHub公告总页数失败....");
            return false;
            }
            $i = 1;
            while ($i <= $matches[1]) {
                $shouye = file_get_contents($url . $i);
                if (empty($shouye)) {
                addlog("获取GitHub公告首页内容失败....");
                    continue;
                }
                $regex = '/<a href="(.*?)" class="Link--primary v-align-middle no-underline h4 js-navigation-open" data-pjax="">/';
                preg_match_all($regex, $shouye, $data);
                if (isset($data[1]) == false) {
                addlog("获取GitHub公告第一页失败....");
                    continue;
                }
                foreach ($data[1] as $K => $v) {
                    $details = file_get_contents($v);
                    // 获取标题
                    $title_reg = '/<h2 data-view-component="true" class="lh-condensed Subhead-heading">(.*?)\n<\/h2>/';
                    preg_match($title_reg, $details, $title);
                    $title = trim($title[1]);
                    // 获取等级
                    $level_reg = '/<span title="Severity label" data-view-component="true" class="Label Label--danger text-bold mr-1 v-align-middle">\s(.*?)\n<\/span>/';
                    preg_match($level_reg, $details, $level);
                    $level = trim($level[1]);
                    // 发布时间
                    $github_release_date_reg = '/<relative-time datetime="(.*?)" class="no-wrap" title=".*?">.*?<\/relative-time>/';
                    preg_match($github_release_date_reg, $details, $github_release_date);
                    $github_release_date = date('Y-m-d H:i:s', strtotime($github_release_date[1]));
                    // CVE ID
                    $cve_id_reg = '/<div class="color-fg-muted pt-2">(.*?)<\/div>/';
                    preg_match($cve_id_reg, $details, $cve_id);
                    $cve_id = $cve_id[1];
                    // cwes
                    $cwes_reg = '/<a data-hovercard-type="cwe" data-hovercard-url=".*?" href=".*?" data-view-component="true" class="Label Label--secondary mt-2 mr-1 text-normal no-underline">\s(.*?)\s<\/a>/';
                    preg_match_all($cwes_reg, $details, $cwes);
                    $cwes = json_encode(array_map('trim', $cwes[1]));
                    // CVSS Score
                    /*$cvss_score_reg = '/<a data-hovercard-type="cwe" data-hovercard-url=".*?" href=".*?" data-view-component="true" class="Label Label--secondary mt-2 mr-1 text-normal no-underline">\s(.*?)\s<\/a>/';
                    preg_match_all($cvss_score_reg, $details, $cvss_score);
                    var_dump($cwes);*/
                    $cvss_score = '';
                    // references
                    $ul_reg = '/<ul>\s(.*?)\s<\/ul>/is';
                    preg_match($ul_reg, $details, $ul);
                    $references = "<ul>{$ul[1]}</ul>";
                    // desc
                    $desc_reg = '/<div class="markdown-body comment-body p-0">.*?<p>(.*?)<\/p>.*?<\/div>/is';
                    preg_match($desc_reg, $details, $desc);
                    $desc = $desc[1];
                    // package
                    $package_reg = '/<.*? class="f4 color-text-primary text-bold">(.*?)<\/.*?>/';
                    preg_match_all($package_reg, $details, $package);
                    $package = [
                        $package[1][0], $package[1][1], $package[1][2],
                    ];
                    $hash = md5($title . $cve_id);
                    if (Db::name('github_notice')->where('hash', $hash)->count('id')) {
                        addlog("获取GitHub漏洞信息重复:{$hash}");
                        continue;
                    }
                    $data = [
                        'title' => $title,
                        'level' => $level,
                        'cve_id' => $cve_id,
                        'cwes' => $cwes,
                        'cvss_score' => $cvss_score,
                        'github_release_date' => $github_release_date,
                        'references' => $references,
                        'desc' => $desc,
                        'package' => $package,
                        'hash' => $hash,
                        'create_time' => date('Y-m-d H:i:s', time()),
                    ];
                    //var_dump($data);exit;
                    Db::name('github_notice')->insert($data);
                }
                $i++;
            }

    }
}