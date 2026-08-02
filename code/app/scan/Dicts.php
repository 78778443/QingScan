<?php
declare(strict_types=1);

namespace app\scan;

/**
 * 内置扫描引擎使用的字典数据
 *
 * 社区生态兼容：可在 extend/dicts/ 目录放置同名 .txt 文件追加自定义字典
 * （如 extend/dicts/subdomains.txt、extend/dicts/dirs.txt、extend/dicts/passwords.txt），
 * 每行一个，自动与内置字典合并去重。
 */
class Dicts
{
    /** extend/dicts/ 目录下自定义字典与内置字典合并 */
    private static function mergeDict(string $name, array $default): array
    {
        $path = dirname(__DIR__, 2) . '/extend/dicts/' . $name . '.txt';
        if (is_file($path)) {
            $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if ($lines) {
                foreach ($lines as $line) {
                    $line = trim($line);
                    if ($line !== '') {
                        $default[] = $line;
                    }
                }
            }
        }
        return array_values(array_unique($default));
    }

    /** 常用子域名字典 */
    public static function subdomains(): array
    {
        return self::mergeDict('subdomains', [
            'www', 'mail', 'webmail', 'smtp', 'pop3', 'imap', 'ftp', 'ssh', 'vpn', 'sslvpn', 'webvpn',
            'ns1', 'ns2', 'dns', 'mx', 'mail1', 'mail2', 'news', 'blog', 'bbs', 'forum', 'shop', 'store',
            'oa', 'office', 'erp', 'crm', 'hr', 'mis', 'portal', 'home', 'm', 'mobile', 'wap', 'app', 'api',
            'openapi', 'test', 'dev', 'develop', 'demo', 'stage', 'staging', 'pre', 'preview', 'beta',
            'admin', 'manage', 'manager', 'system', 'backend', 'cms', 'uc', 'passport', 'sso', 'login',
            'pay', 'payment', 'trade', 'order', 'mall', 'malls', 'member', 'user', 'users', 'account',
            'static', 'assets', 'cdn', 'img', 'image', 'images', 'pic', 'video', 'media', 'download',
            'upload', 'file', 'files', 'doc', 'docs', 'help', 'support', 'service', 'services', 'status',
            'monitor', 'monitoring', 'git', 'gitlab', 'svn', 'jenkins', 'ci', 'cd', 'docker', 'k8s',
            'kubernetes', 'grafana', 'prometheus', 'zabbix', 'nagios', 'elasticsearch', 'es', 'kibana',
            'redis', 'mysql', 'db', 'database', 'sql', 'backup', 'bak', 'logs', 'log', 'trace', 'tracing',
            'task', 'cron', 'queue', 'mq', 'kafka', 'rabbitmq', 'rocketmq', 'sms', 'push', 'notify',
            'message', 'msg', 'im', 'chat', 'webchat', 'live', 'stream', 'rtmp', 'play', 'vip', 'card',
            'h5', 'weixin', 'wechat', 'wx', 'alipay', 'zhifubao', 'union', 'partner', 'supplier', 'seller',
            'agent', 'agency', 'finance', 'fund', 'wallet', 'accounting', 'report', 'reports', 'analysis',
            'bi', 'data', 'datas', 'bigdata', 'stat', 'stats', 'statistics', 'sync', 'api2', 'gateway',
            'proxy', 'redirect', 'short', 'urls', 'link', 'links', 'm1', 'm2', 'm3', 'w', 'web', 'web1',
            'web2', 'web3', 'app1', 'app2', 'server', 'server1', 'server2', 'node', 'node1', 'cache',
            'search', 'so', 'suggest', 'recommend', 'feed', 'rss', 'newsletter', 'survey', 'voting',
            'vote', 'activity', 'event', 'game', 'games', 'lottery', 'coupon', 'ticket', 'train', 'bus',
            'travel', 'hotel', 'movie', 'film', 'music', 'radio', 'tv', 'iptv', 'ebook', 'read', 'novel',
            'auth', 'authorization', 'oauth', 'token', 'session', 'verify', 'captcha', 'code', 'codes',
        ]);
    }

    /** 常用 Web 路径字典 */
    public static function dirs(): array
    {
        return self::mergeDict('dirs', [
            'admin', 'administrator', 'manage', 'manager', 'management', 'backend', 'system', 'sys',
            'index', 'index.php', 'index.html', 'login', 'login.php', 'login.html', 'logout', 'register',
            'user', 'users', 'member', 'members', 'account', 'passport', 'profile', 'admin/login.php',
            'admin/index.php', 'admin/login', 'admin/index', 'admin.php', 'manage/login.php', 'manager/login',
            'api', 'api/', 'api.php', 'v1', 'v2', 'openapi', 'swagger', 'swagger-ui.html', 'swagger/index.html',
            'api-docs', 'graphql', 'status', 'health', 'healthz', 'ping', 'info.php', 'phpinfo.php', 'test.php',
            'shell.php', 'cmd.php', 'eval.php', 'phpmyadmin', 'pma', 'myadmin', 'adminer', 'mysql', 'db',
            'phpMyAdmin', 'phpmyadmin/index.php', 'navicat', 'upload', 'uploads', 'file', 'files', 'download',
            'downloads', 'attachment', 'attachments', 'images', 'img', 'image', 'css', 'js', 'static', 'assets',
            'public', 'storage', 'media', 'backup', 'backups', 'bak', 'old', 'temp', 'tmp', 'test', 'tests',
            'demo', 'dev', 'develop', 'debug', 'logs', 'log', 'error', 'error.log', 'access.log', 'nginx.conf',
            'web.config', '.git', '.git/', '.git/config', '.svn', '.svn/', '.env', '.env.bak', '.htaccess',
            '.DS_Store', '.gitignore', 'robots.txt', 'sitemap.xml', 'sitemap.php', 'crossdomain.xml',
            'favicon.ico', 'readme', 'readme.txt', 'README.md', 'readme.md', 'install', 'install/', 'setup',
            'install.php', 'upgrade', 'update', 'config', 'config/', 'config.php', 'config.inc.php',
            'configuration.php', 'settings', 'setting', 'wp-admin', 'wp-content', 'wp-includes', 'wp-login.php',
            'wp-config.php', 'wordpress', 'wp', 'wp-admin/', 'wp-content/', 'wp-includes/', 'xmlrpc.php',
            'wp-json', 'drupal', 'joomla', 'administrator', 'templates', 'modules', 'plugins', 'themes',
            'thinkphp', 'index.php?s=', 'public/index.php', 'application', 'app', 'runtime', 'cache',
            'sql', 'data.sql', 'database.sql', 'dump.sql', 'db.sql', 'backup.sql', 'export', 'export/',
            'import', 'cgi-bin', 'cgi-bin/', 'perl', 'php-cgi', 'server-status', 'server-info', 'nginx-status',
            'stub_status', 'apache-status', 'webdav', 'dav', 'manager/html', 'manager/status', 'host-manager',
            'console', 'dashboard', 'panel', 'control', 'controlpanel', 'cpanel', 'plesk', 'webmin', 'zabbix',
            'zabbix/', 'grafana', 'grafana/', 'jenkins', 'jenkins/', 'gitlab', 'gitlab/', 'nexus', 'nexus/',
            'kibana', 'kibana/', 'elasticsearch', 'es', 'redis', 'memcached', 'rabbitmq', 'rabbitmq/',
            'sonar', 'sonarqube', 'artifactory', 'harbor', 'registry', 'rancher', 'k8s', 'kubernetes',
            'prometheus', 'alertmanager', 'consul', 'etcd', 'nacos', 'eureka', 'dubbo', 'sentinel', 'xxl-job',
            'xxl-job-admin', 'seata', 'skywalking', 'zipkin', 'jaeger', 'elastic', 'logstash', 'filebeat',
            'metric', 'metrics', 'actuator', 'actuator/', 'actuator/health', 'actuator/env', 'actuator/heapdump',
            'actuator/mappings', 'actuator/beans', 'vpn', 'sslvpn', 'webvpn', 'mail', 'webmail', 'owa',
            'exchange', 'autodiscover', 'remote', 'rdp', 'vnc', 'teamviewer', 'anydesk', 'sophos', 'fortigate',
            'fortinet', 'pfsense', 'openvpn', 'wireguard', 'ipsec', 'cisco', 'huawei', 'h3c', 'ruijie',
            'sharp', 'kuaipan', 'baidu', 'yun', 'oss', 'cos', 'bucket', 'object', 'minio', 's3', 'storage/',
            'oss/', 'cos/', 'upload/', 'uploads/', 'file/', 'files/', 'doc/', 'docs/', 'wiki', 'wiki/',
            'confluence', 'jira', 'jira/', 'fogbugz', 'trac', 'redmine', 'mantis', 'bugzilla', 'phpmyadmin/',
            'weblogic', 'websphere', 'jboss', 'tomcat', 'tomcat/', 'catalina', 'jsp-examples', 'servlet-examples',
            'manager', 'host-manager/', 'webdav/', 'axis', 'axis2', 'struts', 'struts2', 'spring', 'springboot',
            'druid', 'druid/', 'druid/index.html', 'swagger-ui', 'v2/api-docs', 'v3/api-docs', 'openapi.json',
            'actuator/gateway', 'nacos/', 'nacos/v1', 'eureka/', 'apollo', 'apollo/', 'xxljob', 'ddos',
            'shell', 'hack', 'hacker', 'hackme', 'toy', 'waf', 'waf/',
        ]);
    }

    /** 指纹规则：match 任一命中即算识别成功 */
    public static function fingerprints(): array
    {
        return [
            ['name' => 'nginx', 'headers' => ['server' => 'nginx'], 'body' => []],
            ['name' => 'apache', 'headers' => ['server' => 'apache'], 'body' => []],
            ['name' => 'IIS', 'headers' => ['server' => 'iis'], 'body' => []],
            ['name' => 'tomcat', 'headers' => ['server' => 'tomcat'], 'body' => ['tomcat']],
            ['name' => 'jetty', 'headers' => ['server' => 'jetty'], 'body' => []],
            ['name' => 'openresty', 'headers' => ['server' => 'openresty'], 'body' => []],
            ['name' => 'PHP', 'headers' => ['x-powered-by' => 'php'], 'body' => ['php']],
            ['name' => 'ASP.NET', 'headers' => ['x-powered-by' => 'asp.net', 'x-aspnet-version' => ''],
                'body' => ['__viewstate', 'asp.net']],
            ['name' => 'Java', 'headers' => ['x-powered-by' => 'java'], 'body' => ['java.servlet', 'jsp']],
            ['name' => 'thinkphp', 'headers' => [], 'body' => ['thinkphp', 'tp5', 'think\\']],
            ['name' => 'laravel', 'headers' => [], 'body' => ['laravel', 'csrf-token']],
            ['name' => 'wordpress', 'headers' => [], 'body' => ['wp-content', 'wp-includes', 'wordpress']],
            ['name' => 'drupal', 'headers' => [], 'body' => ['drupal', 'drupal.settings']],
            ['name' => 'joomla', 'headers' => [], 'body' => ['joomla', 'com_content']],
            ['name' => 'discuz', 'headers' => [], 'body' => ['discuz', 'powered by discuz']],
            ['name' => 'phpwind', 'headers' => [], 'body' => ['phpwind']],
            ['name' => 'dedecms', 'headers' => [], 'body' => ['dedecms', 'powered by dedecms']],
            ['name' => '帝国CMS', 'headers' => [], 'body' => ['ecms', 'empirecms']],
            ['name' => '织梦CMS', 'headers' => [], 'body' => ['dedecms']],
            ['name' => 'phpMyAdmin', 'headers' => [], 'body' => ['phpmyadmin', 'pma_theme', 'pma_logout']],
            ['name' => 'Jenkins', 'headers' => [], 'body' => ['jenkins', 'jenkins-ci.org']],
            ['name' => 'GitLab', 'headers' => [], 'body' => ['gitlab', 'gitlab-logo']],
            ['name' => 'Grafana', 'headers' => [], 'body' => ['grafana', 'grafana-app']],
            ['name' => 'Zabbix', 'headers' => [], 'body' => ['zabbix', 'zabbix.js']],
            ['name' => 'SonarQube', 'headers' => [], 'body' => ['sonarqube', 'sonar']],
            ['name' => 'Kibana', 'headers' => [], 'body' => ['kibana']],
            ['name' => 'Elasticsearch', 'headers' => [], 'body' => ['elasticsearch']],
            ['name' => 'Swagger', 'headers' => [], 'body' => ['swagger-ui', 'swagger']],
            ['name' => 'Druid', 'headers' => [], 'body' => ['druid', 'druid-console']],
            ['name' => 'Nacos', 'headers' => [], 'body' => ['nacos']],
            ['name' => 'XXL-JOB', 'headers' => [], 'body' => ['xxl-job']],
            ['name' => 'Django', 'headers' => ['server' => 'django', 'x-frame-options' => 'deny'], 'body' => ['django']],
            ['name' => 'Flask', 'headers' => ['server' => 'werkzeug'], 'body' => []],
            ['name' => 'Node.js', 'headers' => ['x-powered-by' => 'express'], 'body' => ['node.js']],
            ['name' => 'Shiro', 'headers' => ['set-cookie' => 'rememberme'], 'body' => []],
            ['name' => 'Spring', 'headers' => [], 'body' => ['spring', 'whitelabel error']],
            ['name' => 'Shiro权限框架', 'headers' => [], 'body' => ['shiro']],
            ['name' => 'fastjson', 'headers' => [], 'body' => ['fastjson']],
            ['name' => 'log4j', 'headers' => [], 'body' => ['log4j']],
            ['name' => 'WebLogic', 'headers' => ['server' => 'weblogic'], 'body' => ['weblogic']],
            ['name' => 'WebSphere', 'headers' => ['server' => 'websphere'], 'body' => []],
            ['name' => 'JBoss', 'headers' => ['server' => 'jboss'], 'body' => ['jboss']],
            ['name' => 'Struts2', 'headers' => [], 'body' => ['struts2', 'org.apache.struts']],
            ['name' => 'RabbitMQ', 'headers' => [], 'body' => ['rabbitmq']],
            ['name' => 'Redis', 'headers' => [], 'body' => ['redis']],
            ['name' => 'Memcached', 'headers' => [], 'body' => ['memcached']],
            ['name' => 'MinIO', 'headers' => [], 'body' => ['minio']],
            ['name' => 'Harbor', 'headers' => [], 'body' => ['harbor']],
            ['name' => 'Nexus', 'headers' => [], 'body' => ['nexus']],
            ['name' => 'ALIBABA', 'headers' => [], 'body' => ['alibaba']],
            ['name' => 'Bootstrap', 'headers' => [], 'body' => ['bootstrap']],
            ['name' => 'jQuery', 'headers' => [], 'body' => ['jquery']],
            ['name' => 'Vue.js', 'headers' => [], 'body' => ['vue.js', 'id="app"']],
            ['name' => 'React', 'headers' => [], 'body' => ['react', '__nxtg']],
            ['name' => 'Angular', 'headers' => [], 'body' => ['angular']],
        ];
    }

    /** 常见弱口令密码字典 */
    public static function passwords(): array
    {
        return self::mergeDict('passwords', [
            '123456', 'password', 'admin', '12345678', '123456789', '12345', '1234', '111111',
            '1234567', 'password1', 'qwerty', 'abc123', 'admin123', 'root', 'toor', 'test',
            'guest', '123123', '654321', '11111111', 'admin888', 'admin666', 'admin@123',
            'admin123456', 'root123', 'root123456', '1234567890', '1qaz2wsx', 'qwe123',
            'p@ssw0rd', 'passw0rd', '1234qwer', 'zaq12wsx', 'woaini1314', 'woaini520',
            'a123456', 'a123456789', 'abcd1234', 'abcd123', '123qwe', 'qwer1234', 'asdf1234',
            'zxcv1234', '!@#$%^&*', '666666', '888888', '000000', '112233', '1314520',
            '5201314', 'a1b2c3', 'abc', 'administrator', 'manager', 'system', '123321',
            '147258369', 'abcabc', 'iloveyou', 'wang123', 'zhang123',
        ]);
    }

    /** 常见用户名/账号字典 */
    public static function usernames(): array
    {
        return self::mergeDict('usernames', [
            'admin', 'root', 'test', 'guest', 'administrator', 'user', 'oracle', 'mysql',
            'postgres', 'ftp', 'www', 'web', 'tomcat', 'jenkins', 'redis', 'default',
            'sa', 'system', 'manager', 'operator',
        ]);
    }

    /** 端口默认服务映射（无 banner 时兜底） */
    public static function portServices(): array
    {
        return [
            21 => 'ftp', 22 => 'ssh', 23 => 'telnet', 25 => 'smtp', 53 => 'dns', 80 => 'http',
            81 => 'http', 110 => 'pop3', 111 => 'rpcbind', 123 => 'ntp', 135 => 'msrpc',
            137 => 'netbios-ns', 139 => 'netbios-ssn', 143 => 'imap', 161 => 'snmp', 389 => 'ldap',
            443 => 'https', 445 => 'microsoft-ds', 465 => 'smtps', 500 => 'isakmp', 515 => 'printer',
            523 => 'ibm-db2', 548 => 'afp', 623 => 'ipmi', 636 => 'ldaps', 873 => 'rsync',
            902 => 'vmware-auth', 993 => 'imaps', 995 => 'pop3s', 1080 => 'socks', 1099 => 'rmiregistry',
            1433 => 'mssql', 1521 => 'oracle', 1604 => 'sap', 1645 => 'radius', 1701 => 'l2tp',
            1883 => 'mqtt', 1900 => 'upnp', 2049 => 'nfs', 2181 => 'zookeeper', 2375 => 'docker',
            2379 => 'etcd', 2425 => 'openvpn', 3000 => 'grafana', 3128 => 'squid', 3306 => 'mysql',
            3389 => 'rdp', 4369 => 'epmd', 4730 => 'ganglia', 4848 => 'glassfish', 5000 => 'upnp',
            5001 => 'synology', 5060 => 'sip', 5222 => 'xmpp', 5353 => 'mdns', 5432 => 'postgresql',
            5555 => 'adb', 5601 => 'kibana', 5672 => 'amqp', 5683 => 'coap', 5900 => 'vnc',
            5938 => 'teamviewer', 5984 => 'couchdb', 5985 => 'winrm', 5986 => 'winrm', 6000 => 'x11',
            6379 => 'redis', 6443 => 'kubernetes', 7001 => 'weblogic', 7002 => 'weblogic',
            7077 => 'cloudera', 8000 => 'http-alt', 8001 => 'http-alt', 8009 => 'ajp', 8080 => 'http-proxy',
            8081 => 'http-proxy', 8082 => 'http-proxy', 8083 => 'http-proxy', 8084 => 'http-proxy',
            8085 => 'http-proxy', 8086 => 'influxdb', 8087 => 'http-proxy', 8088 => 'http-proxy',
            8089 => 'http-proxy', 8090 => 'http-proxy', 8161 => 'activemq', 8200 => 'consul',
            8443 => 'https-alt', 8545 => 'ethereum', 8686 => 'npd', 8888 => 'http-alt',
            9000 => 'php-fpm', 9001 => 'supervisord', 9002 => 'http-alt', 9042 => 'cassandra',
            9090 => 'prometheus', 9092 => 'kafka', 9100 => 'printer', 9200 => 'elasticsearch',
            9300 => 'elasticsearch', 9418 => 'git', 9999 => 'http-alt', 10000 => 'webmin',
            11211 => 'memcached', 15672 => 'rabbitmq', 27017 => 'mongodb', 28017 => 'mongodb',
            37777 => 'dahua', 50000 => 'sap', 50070 => 'hadoop', 61616 => 'activemq',
        ];
    }
}
