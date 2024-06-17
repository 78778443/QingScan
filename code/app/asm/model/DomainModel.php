<?php


namespace app\asm\model;


use app\model\BaseModel;
use think\facade\Db;

class DomainModel extends BaseModel
{
    public static function insertTarget(array $lines)
    {
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $urlPattern = '/^https?:\/\/\S+/i';
            $domainPattern = '/^((?!-)[A-Za-z0-9-]{1,63}(?<!-)\\.)+[A-Za-z]{2,6}$/';
            $ipPattern = '/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/';

            if (preg_match($urlPattern, $line)) {
                $data = ['url' => $line];
                Db::table('asm_urls')->extra('IGNORE')->insert($data);
                 $mainDomain = getMainDomain(parse_url($line)['host']);
                if ($mainDomain != parse_url($line)['host']) {
                    Db::table('asm_domain')->strict(false)->extra('IGNORE')->insert($data);
                }
            } elseif (preg_match($domainPattern, $line)) {
                $data = ['domain' => getMainDomain($line), 'sub_domain' => $line];
                Db::table('asm_domain')->strict(false)->extra('IGNORE')->insert($data);

            } elseif (preg_match($ipPattern, $line)) {
                $data = ['ip' => $line];
                Db::table('asm_ip')->strict(false)->extra('IGNORE')->insert($data);
            }

        }
    }


}
