<?php


namespace app\asm\model;


use app\model\BaseModel;
use app\model\ConfigModel;
use think\facade\Db;
use WpOrg\Requests\Requests;

class Fofa extends BaseModel
{
    public static function discover()
    {
        $user = ConfigModel::value('fofa_user');
        $token = ConfigModel::value('fofa_token');
        $where = ['tool' => 'asm_discover_fofa', 'status' => 0];
        $appList = Db::table('task_scan')->where($where)->select()->toArray();
        foreach ($appList as $appInfo) {
            $extInfo = json_decode($appInfo['ext_info'], true);

            $str = urlencode(base64_encode(html_entity_decode($extInfo['keyword'])));
            $url = "https://fofa.info/api/v1/search/all?email={$user}&key={$token}&size=2000&qbase64=" . $str;
            $field = "ip,port,protocol,country,country_name,region,city,longitude,latitude,as_number,as_organization,host,domain,os,server,icp,title,jarm,header,banner,cert,base_protocol,link,certs_issuer_org,certs_issuer_cn,certs_subject_org,certs_subject_cn,tls_ja3s,tls_version,product,product_category,version,lastupdatetime,cname";
            $fieldArr = explode(",", $field);

            $url = "{$url}&fields={$field}";
            $list = Requests::get($url);
            $list = json_decode($list->body, true)['results'] ?? [];

            $insertCout = 0;
            foreach ($list as $value) {
                $data = array_combine($fieldArr, $value);
                $insertCout += Db::table('tool_fofa')->extra("IGNORE")->insert($data);
            }
            echo "收获结果{$insertCout}\n";

            $where = ['tool' => 'asm_discov', 'status' => 0, 'task_version' => $appInfo['task_version']];
            Db::table('task_scan')->where($where)->update(['status' => 1]);
        }


    }
}