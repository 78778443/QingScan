<?php
declare(strict_types=1);

namespace app\api\controller;

use app\api\BaseController;
use app\scan\CodeAudit;
use app\scan\Dicts;
use app\scan\VulnScan;
use app\scan\WafScan;

/**
 * 规则管理 API
 * 内置规则只读展示（引擎类常量），自定义规则（extend/rules/{type}.php）增删
 * 接口：list / save / delete / types
 */
class Rules extends BaseController
{
    /** 规则类型 => 中文名（前端 Tab） */
    private const TYPES = [
        'code_audit'       => '代码审计（L1 正则）',
        'code_audit_taint' => '代码审计（L2 污点）',
        'fingerprint'      => '指纹识别',
        'vuln'             => '漏洞检测',
        'waf'              => 'WAF 识别',
    ];

    /** 各类型规则字段定义（供前端表单渲染） */
    private const FIELDS = [
        'code_audit' => [
            ['key' => 'id', 'label' => '规则ID', 'type' => 'text', 'required' => true],
            ['key' => 'ext', 'label' => '适用扩展名（逗号分隔，* 为全部）', 'type' => 'text', 'required' => false],
            ['key' => 'pattern', 'label' => '正则表达式（# 定界符）', 'type' => 'text', 'required' => true],
            ['key' => 'message', 'label' => '提示信息', 'type' => 'text', 'required' => true],
            ['key' => 'severity', 'label' => '严重级别', 'type' => 'select', 'options' => ['error', 'warning'], 'required' => true],
        ],
        'code_audit_taint' => [
            ['key' => 'id', 'label' => '规则ID', 'type' => 'text', 'required' => true],
            ['key' => 'sinks', 'label' => '危险函数（逗号分隔）', 'type' => 'text', 'required' => true],
            ['key' => 'message', 'label' => '提示信息', 'type' => 'text', 'required' => true],
            ['key' => 'severity', 'label' => '严重级别', 'type' => 'select', 'options' => ['error', 'warning'], 'required' => true],
        ],
        'fingerprint' => [
            ['key' => 'name', 'label' => '指纹名称', 'type' => 'text', 'required' => true],
            ['key' => 'headers', 'label' => '响应头（key=value 逗号分隔，value 可空）', 'type' => 'text', 'required' => false],
            ['key' => 'body', 'label' => '页面关键字（逗号分隔）', 'type' => 'text', 'required' => false],
        ],
        'vuln' => [
            ['key' => 'name', 'label' => '漏洞名称', 'type' => 'text', 'required' => true],
            ['key' => 'severity', 'label' => '危险等级', 'type' => 'select', 'options' => ['low', 'medium', 'high', 'critical'], 'required' => true],
            ['key' => 'paths', 'label' => '检测路径（逗号分隔）', 'type' => 'text', 'required' => true],
            ['key' => 'keywords', 'label' => '匹配关键字（逗号分隔）', 'type' => 'text', 'required' => true],
            ['key' => 'description', 'label' => '漏洞描述', 'type' => 'text', 'required' => false],
        ],
        'waf' => [
            ['key' => 'headers', 'label' => '响应头特征（key 或 key:value，逗号分隔）', 'type' => 'text', 'required' => false],
            ['key' => 'cookie', 'label' => 'Cookie 名（逗号分隔）', 'type' => 'text', 'required' => false],
            ['key' => 'body', 'label' => '页面关键字（逗号分隔）', 'type' => 'text', 'required' => false],
            ['key' => 'firewall', 'label' => 'WAF 名称', 'type' => 'text', 'required' => true],
            ['key' => 'manufacturer', 'label' => '厂商', 'type' => 'text', 'required' => false],
        ],
    ];

    /** 各类型用于唯一标识规则（删除匹配）的字段 */
    private const ID_KEYS = [
        'code_audit'       => 'id',
        'code_audit_taint' => 'id',
        'fingerprint'      => 'name',
        'vuln'             => 'name',
        'waf'              => 'firewall',
    ];

    /** 各类型中按逗号分隔存储的字段（保存时转数组） */
    private const LIST_KEYS = [
        'code_audit'       => ['ext'],
        'code_audit_taint' => ['sinks'],
        'fingerprint'      => ['body'],
        'vuln'             => ['paths', 'keywords'],
        'waf'              => ['headers', 'cookie', 'body'],
    ];

    /** 新建文件时的头部注释 */
    private const FILE_HEADERS = [
        'code_audit'       => "<?php\n/**\n * 自定义代码审计规则（L1 正则规则）\n * 结构：['id' => 规则唯一标识, 'ext' => ['php','java',...], 'pattern' => '#正则#i', 'message' => '描述', 'severity' => 'ERROR'|'WARNING']\n */\n",
        'code_audit_taint' => "<?php\n/**\n * 自定义污点分析规则（L2 语法感知）\n * 结构：['id' => '规则标识', 'sinks' => ['函数名1','函数名2'], 'message' => '描述', 'severity' => 'ERROR'|'WARNING']\n */\n",
        'fingerprint'      => "<?php\n/**\n * 自定义指纹识别规则\n * 结构：['name' => '指纹名称', 'headers' => ['响应头名' => '值(可选)'], 'body' => ['页面关键字']]\n */\n",
        'vuln'             => "<?php\n/**\n * 自定义漏洞检测规则\n * 结构：['name' => '漏洞名称', 'severity' => 'low|medium|high|critical', 'paths' => ['/路径1'], 'keywords' => ['匹配关键字'], 'description' => '描述']\n */\n",
        'waf'              => "<?php\n/**\n * 自定义 WAF 识别规则\n * 结构：['headers' => ['响应头名' => '值(可选)'], 'cookie' => ['cookie名'], 'body' => ['页面关键字'], 'firewall' => 'WAF名称', 'manufacturer' => '厂商']\n */\n",
    ];

    /**
     * 规则类型列表（前端 Tab）
     * GET /api/Rules/types
     */
    public function types()
    {
        $data = [];
        foreach (self::TYPES as $key => $label) {
            $data[] = ['key' => $key, 'label' => $label];
        }
        return $this->apiReturn(1, $data);
    }

    /**
     * 规则列表
     * GET /api/Rules/list?type=code_audit
     * 返回 {builtin, custom, type, fields}
     */
    public function list()
    {
        $type = (string)$this->request->param('type', '', null);
        if (!isset(self::TYPES[$type])) {
            return $this->apiReturn(0, [], 'type 参数不合法');
        }
        return $this->apiReturn(1, [
            'type' => $type,
            'builtin' => $this->builtinRules($type),
            'custom' => $this->customRules($type),
            'fields' => self::FIELDS[$type],
        ]);
    }

    /**
     * 新增自定义规则
     * POST /api/Rules/save  {type, rule: {...}}
     * 注：param 第三参传 null 绕过全局 Request 的 addslashes/htmlspecialchars 过滤器，
     * 否则正则反斜杠会被转义破坏
     */
    public function save()
    {
        $type = (string)$this->request->param('type', '', null);
        if (!isset(self::TYPES[$type])) {
            return $this->apiReturn(0, [], 'type 参数不合法');
        }
        $rule = $this->request->param('rule', [], null);
        if (!is_array($rule) || empty($rule)) {
            return $this->apiReturn(0, [], 'rule 不能为空');
        }

        $normalized = $this->normalizeRule($type, $rule);
        if (!is_array($normalized)) {
            return $this->apiReturn(0, [], $normalized);
        }

        // 正则预检：pattern 无效直接拒绝
        if ($type === 'code_audit' && isset($normalized['pattern'])) {
            if (@preg_match($normalized['pattern'], '') === false) {
                return $this->apiReturn(0, [], '正则表达式无效：' . $normalized['pattern'] . '（' . preg_last_error_msg() . '）');
            }
        }

        $rules = $this->customRules($type);
        $rules[] = $normalized;

        if (!$this->writeRulesFile($type, $rules)) {
            return $this->apiReturn(0, [], '写入规则文件失败');
        }
        return $this->apiReturn(1, ['type' => $type, 'rule' => $normalized], '保存成功');
    }

    /**
     * 删除自定义规则
     * POST /api/Rules/delete  {type, id}
     */
    public function delete()
    {
        $type = (string)$this->request->param('type', '', null);
        if (!isset(self::TYPES[$type])) {
            return $this->apiReturn(0, [], 'type 参数不合法');
        }
        $id = (string)$this->request->param('id', '', null);
        if ($id === '') {
            return $this->apiReturn(0, [], 'id 不能为空');
        }

        $file = $this->rulesFile($type);
        if (!is_file($file)) {
            return $this->apiReturn(0, [], '自定义规则文件不存在');
        }

        $rules = $this->customRules($type);
        $idKey = self::ID_KEYS[$type];
        $found = false;
        foreach ($rules as $i => $rule) {
            if (isset($rule[$idKey]) && (string)$rule[$idKey] === $id) {
                unset($rules[$i]);
                $found = true;
                break;
            }
        }
        if (!$found) {
            return $this->apiReturn(0, [], '规则不存在：' . $id);
        }

        if (!$this->writeRulesFile($type, array_values($rules))) {
            return $this->apiReturn(0, [], '写入规则文件失败');
        }
        return $this->apiReturn(1, [], '删除成功');
    }

    // ============================================================
    // 私有辅助
    // ============================================================

    /** 读取内置规则（按类型分发） */
    private function builtinRules(string $type): array
    {
        switch ($type) {
            case 'code_audit':
                return CodeAudit::rules();
            case 'code_audit_taint':
                return CodeAudit::taintRules();
            case 'fingerprint':
                return Dicts::builtinFingerprints();
            case 'vuln':
                return VulnScan::builtinRules();
            case 'waf':
                return WafScan::builtinRules();
        }
        return [];
    }

    /** 读取自定义规则（extend/rules/{type}.php） */
    private function customRules(string $type): array
    {
        $file = $this->rulesFile($type);
        if (is_file($file)) {
            $rules = @include $file;
            if (is_array($rules)) {
                return $rules;
            }
        }
        return [];
    }

    /** 自定义规则文件路径 */
    private function rulesFile(string $type): string
    {
        return dirname(__DIR__, 3) . '/extend/rules/' . $type . '.php';
    }

    /**
     * 校验必填字段并把表单字符串转换为引擎所需结构
     * @return array|string 成功返回规则数组，失败返回错误消息
     */
    private function normalizeRule(string $type, array $rule)
    {
        $out = [];
        foreach (self::FIELDS[$type] as $field) {
            $key = $field['key'];
            $value = trim((string)($rule[$key] ?? ''));
            if ($field['required'] && $value === '') {
                return $field['label'] . '不能为空';
            }
            if ($value !== '') {
                $out[$key] = $this->castValue($type, $key, $value);
            }
        }
        // code_audit 缺省 ext 时适用全部文件
        if ($type === 'code_audit' && !isset($out['ext'])) {
            $out['ext'] = ['*'];
        }
        // 自定义规则 id 加 custom. 前缀，避免与内置冲突
        if (in_array($type, ['code_audit', 'code_audit_taint'], true)
            && isset($out['id']) && strpos($out['id'], 'custom.') !== 0) {
            $out['id'] = 'custom.' . $out['id'];
        }
        return $out;
    }

    /** 按字段类型转换值：列表字段转数组、指纹 headers 转 key=>value、severity 统一小写 */
    private function castValue(string $type, string $key, string $value)
    {
        if (isset(self::LIST_KEYS[$type]) && in_array($key, self::LIST_KEYS[$type], true)) {
            return $this->splitList($value);
        }
        if ($type === 'fingerprint' && $key === 'headers') {
            return $this->parseKeyValue($value);
        }
        if ($key === 'severity') {
            return strtolower($value);
        }
        return $value;
    }

    /** 逗号分隔字符串 → 数组（去空项） */
    private function splitList(string $value): array
    {
        $items = [];
        foreach (explode(',', $value) as $item) {
            $item = trim($item);
            if ($item !== '') {
                $items[] = $item;
            }
        }
        return $items;
    }

    /** "k=v, k2=v2" → ['k' => 'v', 'k2' => 'v2']（无 = 时 value 为空，表示只要头存在即命中） */
    private function parseKeyValue(string $value): array
    {
        $pairs = [];
        foreach (explode(',', $value) as $item) {
            $item = trim($item);
            if ($item === '') {
                continue;
            }
            $pos = strpos($item, '=');
            if ($pos !== false) {
                $k = trim(substr($item, 0, $pos));
                $v = trim(substr($item, $pos + 1));
            } else {
                $k = $item;
                $v = '';
            }
            if ($k !== '') {
                $pairs[$k] = $v;
            }
        }
        return $pairs;
    }

    /**
     * 重写规则文件：保留文件头注释（return [ 之前的内容），规则体用 var_export 输出
     */
    private function writeRulesFile(string $type, array $rules): bool
    {
        $file = $this->rulesFile($type);
        $header = self::FILE_HEADERS[$type] ?? "<?php\n";

        if (is_file($file)) {
            $raw = @file_get_contents($file);
            if ($raw !== false) {
                $pos = strpos($raw, 'return [');
                if ($pos !== false) {
                    $header = substr($raw, 0, $pos);
                } elseif (preg_match('/^<\?php\b[^\n]*\n/', $raw, $m)) {
                    $header = $m[0];
                }
            }
        }

        $content = $header . 'return ' . var_export($rules, true) . ";\n";
        $ok = @file_put_contents($file, $content) !== false;
        if ($ok && function_exists('opcache_invalidate')) {
            @opcache_invalidate($file, true);
        }
        return $ok;
    }
}
