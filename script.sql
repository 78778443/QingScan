create table app
(
    id                  int auto_increment
        primary key,
    status              tinyint     default 1                     null,
    name                varchar(150)                              null,
    url                 varchar(100)                              null,
    create_time         timestamp   default CURRENT_TIMESTAMP     not null,
    crawler_time        datetime    default '2000-01-01 00:00:00' null,
    awvs_scan_time      datetime    default '2000-01-01 00:00:00' null,
    subdomain_time      datetime    default '2000-01-01 00:00:00' null,
    is_delete           tinyint(1)  default 0                     not null,
    username            varchar(50) default ''                    null,
    password            char(32)    default ''                    null,
    whatweb_scan_time   datetime    default '2000-01-01 00:00:00' null,
    subdomain_scan_time datetime    default '2000-01-01 00:00:00' null comment 'OneForAll子域名扫描时间',
    screenshot_time     datetime    default '2000-01-01 00:00:00' null comment '截图时间',
    xray_scan_time      datetime    default '2000-01-01 00:00:00' not null,
    dirmap_scan_time    datetime    default '2000-01-01 00:00:00' null,
    user_id             int(10)     default 0                     not null,
    wafw00f_scan_time   datetime    default '2000-01-01 00:00:00' not null,
    jietu_path          varchar(512)                              null,
    is_intranet         tinyint(1)  default 0                     not null,
    nuclei_scan_time    datetime    default '2000-01-01 00:00:00' null,
    dismap_scan_time    datetime    default '2000-01-01 00:00:00' null,
    crawlergo_scan_time datetime    default '2000-01-01 00:00:00' null,
    vulmap_scan_time    datetime    default '2000-01-01 00:00:00' null,
    xray_agent_port     int(10)     default 0                     not null comment 'xray被动代理端口',
    agent_time          datetime    default '2000-01-01 00:00:00' not null,
    agent_start_up      int(1)      default 0                     not null comment 'xray代理是否已启动',
    constraint un_url
        unique (url)
)
    engine = InnoDB
    collate = utf8mb4_bin
    row_format = COMPACT;

create table app_crawlergo
(
    id            int(10) auto_increment
        primary key,
    app_id        int(10)                                not null,
    user_id       int(10)      default 0                 not null,
    url           text                                   null,
    method        varchar(20)  default ''                not null,
    accept        varchar(500) default ''                not null,
    cache_control varchar(255) default ''                not null,
    cookie        varchar(500) default ''                not null,
    referer       varchar(255) default ''                not null,
    spider_name   varchar(100) default ''                not null,
    user_agent    varchar(255) default ''                not null,
    data          varchar(500) default ''                not null,
    source        varchar(255) default ''                not null,
    create_time   timestamp    default CURRENT_TIMESTAMP not null
)
    comment 'url收集' engine = InnoDB
                      collate = utf8mb4_bin
                      row_format = COMPACT;

create table app_dirmap
(
    id          int(10) auto_increment
        primary key,
    app_id      int(10)                               not null,
    code        varchar(10) default ''                not null comment '状态码',
    size        varchar(20)                           not null comment '大小kb',
    type        varchar(100)                          not null comment '类型',
    url         varchar(255)                          not null comment 'url地址',
    create_time timestamp   default CURRENT_TIMESTAMP not null,
    user_id     int(10)     default 0                 not null
)
    engine = InnoDB
    collate = utf8mb4_bin
    row_format = COMPACT;

create table app_dismap
(
    id          int(10) auto_increment
        primary key,
    app_id      int(10)   default 0                 not null,
    user_id     int(10)   default 0                 not null,
    result      text                                null comment '结果',
    create_time timestamp default CURRENT_TIMESTAMP not null
)
    engine = InnoDB
    collate = utf8mb4_bin
    row_format = COMPACT;

create table app_info
(
    app_id         int          default 0  not null
        primary key,
    cms            varchar(255)            null,
    server         varchar(255)            null,
    statuscode     varchar(255)            null,
    title          varchar(255)            null,
    length         int                     null,
    code           int(3)       default 0  not null comment '状态码',
    page_title     varchar(100) default '' not null comment '网页标题',
    header         text                    null comment '网页header',
    icon           varchar(100) default '' not null comment '网页ICON',
    url_screenshot varchar(256) default '' not null comment 'url屏幕截图',
    constraint app_id
        foreign key (app_id) references app (id)
            on update cascade on delete cascade
)
    engine = InnoDB
    collate = utf8mb4_bin
    row_format = COMPACT;

create table app_nuclei
(
    id                int(10) auto_increment
        primary key,
    app_id            int(10)                                not null,
    user_id           int(10)                                not null,
    template          varchar(255) default ''                null,
    template_url      varchar(255) default ''                null,
    template_id       varchar(100) default ''                null,
    name              varchar(255) default ''                null,
    author            varchar(255) default ''                null comment '作者',
    tags              varchar(255) default ''                null comment '标签',
    description       varchar(500) default ''                null comment '描述',
    reference         varchar(255) default ''                null,
    severity          varchar(100) default ''                null,
    type              varchar(20)  default ''                null comment '协议类型',
    host              varchar(50)  default ''                null,
    matched_at        varchar(100) default ''                null,
    extracted_results varchar(255) default ''                null,
    ip                varchar(20)  default ''                null,
    create_time       timestamp    default CURRENT_TIMESTAMP not null,
    curl_command      varchar(500)                           null,
    status            tinyint(1)   default 0                 not null
)
    engine = InnoDB
    collate = utf8mb4_bin
    row_format = COMPACT;

create table app_vulmap
(
    id          int(10) auto_increment
        primary key,
    app_id      int(10)                                not null,
    user_id     int(10)                                not null,
    author      varchar(100) default ''                not null comment '作者',
    description varchar(255) default ''                not null comment '描述',
    host        varchar(50)  default ''                not null comment '主机',
    port        varchar(10)  default ''                not null comment '端口',
    param       varchar(500) default ''                not null comment '参数',
    request     text                                   null,
    payload     varchar(255) default ''                not null,
    response    text                                   null,
    url         varchar(255) default ''                not null,
    plugin      varchar(255) default ''                not null comment '漏洞',
    target      varchar(500) default ''                not null,
    vuln_class  varchar(255) default ''                not null comment '漏洞名称',
    create_time timestamp    default CURRENT_TIMESTAMP not null
)
    comment '漏洞扫描' engine = InnoDB
                       collate = utf8mb4_bin
                       row_format = COMPACT;

create table app_wafw00f
(
    id           int(10) auto_increment
        primary key,
    app_id       int(10)                             not null,
    user_id      int(10)   default 0                 not null,
    create_time  timestamp default CURRENT_TIMESTAMP not null,
    url          varchar(100)                        not null,
    detected     varchar(10)                         not null,
    firewall     varchar(100)                        not null comment 'waf防火墙名称',
    manufacturer varchar(100)                        not null comment '厂商'
)
    comment 'waf指纹识别' engine = InnoDB
                          collate = utf8mb4_bin
                          row_format = COMPACT;

create table app_whatweb
(
    id             int(10) auto_increment
        primary key,
    app_id         int(10)                                 not null,
    target         varchar(255)                            null,
    http_status    varchar(255)                            null,
    request_config varchar(255)                            null,
    plugins        text                                    null,
    create_time    timestamp default CURRENT_TIMESTAMP     not null,
    poc_scan_time  datetime  default '2000-01-01 00:00:00' not null,
    user_id        int(10)   default 0                     not null
)
    comment 'web指纹识别' engine = InnoDB
                          collate = utf8mb4_bin
                          row_format = COMPACT;

create table app_whatweb_poc
(
    id          int(10) auto_increment
        primary key,
    whatweb_id  int(10)                                not null,
    url         varchar(255) default ''                not null,
    app_id      int(10)                                not null,
    `key`       varchar(100) default ''                not null,
    value       varchar(100) default ''                not null,
    result      text                                   null,
    create_time timestamp    default CURRENT_TIMESTAMP not null,
    user_id     int(10)                                not null
)
    engine = InnoDB
    collate = utf8mb4_bin
    row_format = COMPACT;

create table app_xray_agent_port
(
    id              int(10) auto_increment
        primary key,
    app_id          int(10)                             not null,
    xray_agent_prot int(10)                             not null comment '代理端口',
    create_time     timestamp default CURRENT_TIMESTAMP not null,
    start_up        int(1)    default 0                 not null comment '是否已启动',
    is_get_result   int(1)    default 0                 not null comment '是否已获取结果'
)
    comment '本地代理' engine = InnoDB
                       collate = utf8mb4_bin
                       row_format = COMPACT;

create table asm_domain
(
    id          int auto_increment
        primary key,
    domain      varchar(50)                        null,
    icp         varchar(255)                       null,
    com_name    varchar(255)                       null,
    create_time datetime default CURRENT_TIMESTAMP null,
    constraint un_domain
        unique (domain)
);

create table asm_finger
(
    id          int auto_increment
        primary key,
    url         varchar(255)                       null,
    cms         varchar(255)                       null,
    title       varchar(255)                       null,
    Server      varchar(255)                       null,
    status      varchar(255)                       null,
    ip          varchar(255)                       null,
    address     varchar(255)                       null,
    isp         varchar(255)                       null,
    create_time datetime default CURRENT_TIMESTAMP null,
    size        int                                null,
    iscdn       int                                null,
    constraint un_url
        unique (url)
)
    engine = InnoDB;

create table asm_host
(
    id                    int auto_increment
        primary key,
    app_id                int         default 0                     null,
    domain                varchar(64)                               null,
    host                  text                                      null,
    status                tinyint     default 1                     not null,
    create_time           timestamp   default CURRENT_TIMESTAMP     not null,
    isp                   varchar(20) default ''                    not null comment '运营商',
    country               varchar(50) default ''                    not null comment '国家',
    region                varchar(50) default ''                    not null comment '省份',
    city                  varchar(50) default ''                    not null comment '市',
    area                  varchar(50) default ''                    not null comment '地区',
    hydra_scan_time       datetime    default '2000-01-01 00:00:00' not null,
    port_scan_time        datetime    default '2000-01-01 00:00:00' not null,
    ip_scan_time          datetime    default '2000-01-01 00:00:00' not null,
    is_delete             int(4)      default 0                     not null,
    user_id               int(10)     default 0                     not null,
    unauthorize_scan_time datetime    default '2000-01-01 00:00:00' not null comment '未授权扫描时间',
    constraint un_host
        unique (domain)
)
    engine = InnoDB
    collate = utf8mb4_bin
    row_format = COMPACT;

create table asm_host_port
(
    id          int auto_increment
        primary key,
    port        int         default 0                     not null,
    host        varchar(30) default '0'                   not null,
    type        char(5)                                   null,
    service     varchar(255)                              null,
    is_close    tinyint     default 0                     null comment '是否关闭',
    create_time timestamp   default CURRENT_TIMESTAMP     not null,
    update_time datetime                                  null on update CURRENT_TIMESTAMP,
    os          varchar(30)                               null comment '操作系统',
    html        text                                      null,
    headers     text                                      null,
    is_delete   tinyint(1)  default 0                     not null,
    scan_time   datetime    default '2000-01-01 00:00:00' not null,
    user_id     int(10)     default 0                     not null,
    app_id      int         default 0                     not null,
    constraint un_port
        unique (host, port, type)
)
    engine = InnoDB
    collate = utf8mb4_bin
    row_format = COMPACT;

create table asm_ip
(
    id          int auto_increment
        primary key,
    ip          char(16)                              null,
    create_time timestamp   default CURRENT_TIMESTAMP not null,
    isp         varchar(20) default ''                not null comment '运营商',
    location    varchar(50) default ''                not null comment '地区'
)
    engine = InnoDB
    collate = utf8mb4_bin
    row_format = COMPACT;

create index un_ip
    on asm_ip (ip);

create table asm_ip_domain
(
    id          int auto_increment
        primary key,
    ip          varchar(16)                        null,
    create_time datetime default CURRENT_TIMESTAMP null,
    sub_domain  varchar(40)                        null,
    domain      varchar(30)                        null,
    constraint un_ip
        unique (ip, sub_domain)
);

create table asm_ip_port
(
    id          int auto_increment
        primary key,
    ip          varchar(16)                        null,
    create_time datetime default CURRENT_TIMESTAMP null,
    port        int                                null,
    finger      varchar(255)                       null,
    protocol    varchar(20)                        null,
    location    varchar(255)                       null,
    isp         varchar(50)                        null,
    constraint un_ip
        unique (ip, port)
);

create table asm_sub_domain
(
    id          int auto_increment
        primary key,
    domain      varchar(50)                        null,
    icp         varchar(255)                       null,
    com_name    varchar(255)                       null,
    create_time datetime default CURRENT_TIMESTAMP null,
    sub_domain  varchar(50)                        null,
    ip          varchar(255)                       null,
    localtion   varchar(255)                       null,
    finger      varchar(255)                       null,
    constraint un_domain
        unique (sub_domain)
);

create table asm_urls
(
    id              int auto_increment
        primary key,
    method          varchar(20)  default 'get'             not null,
    app_id          int          default 0                 null,
    url             varchar(180)                           null,
    create_time     timestamp    default CURRENT_TIMESTAMP not null,
    header          varchar(1024)                          null,
    content         longtext                               null,
    response_header text                                   null,
    hash            varchar(32)                            null,
    scheme          varchar(10)                            null,
    host            varchar(255)                           null,
    query           varchar(1024)                          null,
    title           varchar(1024)                          null,
    keywords        varchar(1024)                          null,
    description     varchar(1024)                          null,
    content_type    varchar(30)                            null,
    extension       varchar(20)                            null,
    icp             text                                   null,
    user_id         int(10)      default 0                 not null,
    isp             varchar(255) default ''                null,
    status          int                                    null,
    server          varchar(255) default ''                null,
    ip              varchar(255) default ''                null,
    address         varchar(255) default ''                null,
    sub_domain      varchar(100)                           null,
    domain          varchar(100)                           null,
    constraint un_url
        unique (url)
)
    engine = InnoDB
    collate = utf8mb4_bin
    row_format = COMPACT;

create table auth_group
(
    auth_group_id int unsigned auto_increment comment '用户组表'
        primary key,
    title         char(100)  default '' not null comment 'title:用户组中文名称',
    status        tinyint(1) default 1  not null comment '为1正常，为0禁用',
    rules         text                  null comment 'rules：用户组拥有的规则id， 多个规则","隔开',
    update_time   int(10)    default 0  not null comment '修改时间',
    is_delete     tinyint(1) default 0  not null,
    created_at    int(10)    default 0  not null comment '添加时间'
)
    charset = utf8
    row_format = DYNAMIC;

create table auth_rule
(
    auth_rule_id int unsigned auto_increment comment '自增id'
        primary key,
    href         char(127)    default '' not null comment '地址   控制器/方法',
    title        char(20)     default '' not null comment '名称',
    is_delete    tinyint(1)   default 0  not null comment '删除状态  1删除  0正常',
    is_open_auth tinyint(2)   default 1  not null comment '是否验证权限',
    pid          int(5)       default 0  not null comment '父栏目ID',
    sort         int          default 0  not null comment '排序',
    created_at   int          default 0  not null comment '添加时间',
    menu_status  tinyint(1)   default 1  not null comment '菜单状态  1显示  0隐藏',
    update_time  int(10)      default 0  not null comment '更新时间',
    level        tinyint      default 1  not null comment '等级  1顶级，一级，2.二级，3.三级',
    delete_time  int(10)      default 0  not null,
    icon_url     varchar(255) default '' not null comment '图标样式',
    constraint un_href
        unique (href)
)
    comment '权限列表' charset = utf8
                       row_format = DYNAMIC;

create table awvs_app
(
    id          int auto_increment
        primary key,
    app_id      int          null,
    address     varchar(255) null,
    criticality varchar(255) null,
    description varchar(255) null,
    fqdn        varchar(255) null,
    type        varchar(255) null,
    domain      varchar(255) null,
    target_id   char(36)     null,
    target_type varchar(255) null,
    user_id     int(10)      not null,
    constraint un_target
        unique (target_id)
)
    engine = InnoDB
    collate = utf8mb4_bin
    row_format = COMPACT;

create table awvs_vuln
(
    id               int auto_increment
        primary key,
    affects_detail   varchar(255)                         null,
    affects_url      varchar(255)                         null,
    app              varchar(255)                         null,
    confidence       int                                  null,
    criticality      int                                  null,
    last_seen        varchar(255)                         null,
    loc_id           int                                  null,
    severity         int                                  null,
    status           varchar(255)                         null,
    tags             varchar(255)                         null,
    target_id        varchar(36)                          null,
    vt_created       varchar(255)                         null,
    vt_id            varchar(36)                          null,
    vt_name          varchar(255)                         null,
    vt_updated       varchar(32)                          null,
    vuln_id          bigint(32)                           null,
    cvss2            varchar(255)                         null,
    cvss_score       varchar(255)                         null,
    description      varchar(255)                         null,
    details          varchar(255)                         null,
    highlights       varchar(255)                         null,
    impact           varchar(255)                         null,
    long_description varchar(255)                         null,
    recommendation   varchar(255)                         null,
    `references`     varchar(255)                         null,
    request          text                                 null,
    response_info    varchar(255)                         null,
    source           varchar(255)                         null,
    create_time      timestamp  default CURRENT_TIMESTAMP not null,
    check_status     tinyint(1) default 0                 not null comment '审核状态',
    is_delete        tinyint(1) default 0                 not null,
    user_id          int(10)    default 0                 not null,
    app_id           int        default 0                 null,
    constraint un_vuln_id
        unique (vuln_id)
)
    engine = InnoDB
    collate = utf8mb4_bin
    row_format = COMPACT;

create table bug
(
    id           int auto_increment
        primary key,
    status       tinyint    default 1                 null,
    name         varchar(150)                         null,
    create_time  timestamp  default CURRENT_TIMESTAMP not null,
    detail       text                                 null comment '漏洞详情',
    category     varchar(255)                         null,
    host         varchar(255)                         null,
    check_status tinyint(1) default 0                 not null
)
    engine = InnoDB
    collate = utf8mb4_bin
    row_format = COMPACT;

create table code
(
    id                  int auto_increment
        primary key,
    name                varchar(255)                              null,
    ssh_url             varchar(128)                              null,
    `desc`              varchar(255)                              null,
    hash                varchar(255)                              null,
    scan_time           datetime    default '2000-01-01 00:00:00' not null,
    sonar_scan_time     datetime    default '2000-01-01 00:00:00' not null,
    kunlun_scan_time    datetime    default '2000-01-01 00:00:00' not null,
    semgrep_scan_time   datetime    default '2000-01-01 00:00:00' not null,
    pulling_mode        char(10)    default ''                    not null comment '拉取方式（支持SSH、HTTPS）',
    is_private          tinyint(1)  default 0                     not null,
    username            varchar(50) default ''                    not null comment '用户名',
    password            varchar(50) default ''                    not null comment '密码',
    private_key         text                                      null,
    is_delete           tinyint(1)  default 0                     not null,
    composer_scan_time  datetime    default '2000-01-01 00:00:00' not null,
    java_scan_time      datetime    default '2000-01-01 00:00:00' not null,
    python_scan_time    datetime    default '2000-01-01 00:00:00' not null,
    user_id             int(10)     default 0                     not null,
    star                char(10)    default ''                    not null,
    webshell_scan_time  datetime    default '2000-01-01 00:00:00' not null,
    create_time         timestamp   default CURRENT_TIMESTAMP     not null,
    is_online           int(10)     default 1                     not null comment '1线上   2本地',
    mobsfscan_scan_time datetime    default '2000-01-01 00:00:00' not null,
    project_type        tinyint(1)  default 0                     not null comment '1php 2java 3python 4go 5app 6其他',
    status              tinyint(1)  default 1                     not null comment '1启用 2禁用',
    murphysec_scan_time datetime    default '2000-01-01 00:00:00' not null,
    constraint un_url
        unique (ssh_url, user_id)
)
    engine = InnoDB
    collate = utf8mb4_bin
    row_format = COMPACT;

create table code_check
(
    id           int auto_increment
        primary key,
    app_id       int       default 0                 null,
    content      text charset utf8mb4                null,
    status       tinyint   default 1                 null,
    create_time  timestamp default CURRENT_TIMESTAMP not null,
    files        text                                null,
    author       varchar(255)                        null,
    project_hash varchar(255)                        null,
    version      varchar(255)                        null,
    project_id   int                                 null
)
    engine = InnoDB
    collate = utf8mb4_bin
    row_format = COMPACT;

create table code_check_notice
(
    id       int auto_increment
        primary key,
    check_id varchar(255) null,
    end      varchar(255) null,
    extra    text         null,
    path     varchar(255) null,
    start    varchar(255) null,
    version  varchar(255) null
)
    engine = InnoDB
    row_format = COMPACT;

create table code_composer
(
    id               int auto_increment
        primary key,
    name             varchar(2048)                       null,
    version          varchar(255)                        null,
    source           varchar(255)                        null,
    dist             text                                null,
    `require`        text                                null,
    conflict         varchar(255)                        null,
    require_dev      text                                null,
    suggest          varchar(2550)                       null,
    type             varchar(255)                        null,
    extra            varchar(255)                        null,
    autoload         varchar(255)                        null,
    notification_url varchar(255)                        null,
    license          varchar(255)                        null,
    authors          text                                null,
    description      varchar(255)                        null,
    keywords         text                                null,
    funding          text                                null,
    time             varchar(255)                        null,
    code_id          int                                 null,
    user_id          int                                 null,
    create_time      timestamp default CURRENT_TIMESTAMP not null
)
    engine = InnoDB
    row_format = COMPACT;

create table code_hook
(
    id          int auto_increment
        primary key,
    create_time timestamp default CURRENT_TIMESTAMP not null,
    update_time datetime                            null,
    status      varchar(255)                        null,
    path        varchar(255)                        null,
    gitlab_id   int                                 null,
    project     varchar(255)                        null
)
    engine = InnoDB
    row_format = COMPACT;

create table code_java
(
    id           int(10) auto_increment
        primary key,
    code_id      int(10)                             not null,
    modelVersion varchar(100)                        null,
    groupId      varchar(100)                        null,
    artifactId   varchar(100)                        null,
    version      varchar(100)                        null,
    modules      text                                null,
    packaging    varchar(100)                        null,
    name         varchar(255)                        null,
    comment      varchar(255)                        null,
    url          varchar(100)                        null,
    properties   text                                null,
    dependencies text                                null,
    build        text                                null,
    create_time  timestamp default CURRENT_TIMESTAMP not null,
    user_id      int(10)   default 0                 not null
)
    engine = InnoDB
    collate = utf8mb4_bin
    row_format = COMPACT;

create table code_python
(
    id          int(10) auto_increment
        primary key,
    code_id     int(10)                             not null,
    name        varchar(100)                        not null,
    create_time timestamp default CURRENT_TIMESTAMP not null,
    user_id     int(10)   default 0                 not null
)
    engine = InnoDB
    collate = utf8mb4_bin
    row_format = COMPACT;

create table code_webshell
(
    id           int(10) auto_increment
        primary key,
    code_id      int(10)                                not null,
    create_time  timestamp    default CURRENT_TIMESTAMP not null,
    check_status int(1)       default 0                 not null comment '审核状态',
    user_id      int(10)      default 0                 not null,
    type         varchar(50)  default ''                not null comment '类型',
    filename     varchar(255) default ''                not null comment '文件路径'
)
    engine = InnoDB
    collate = utf8mb4_bin
    row_format = COMPACT;

create table config
(
    id          int auto_increment
        primary key,
    name        varchar(255)                        null comment '配置名称',
    status      tinyint   default 1                 null comment '状态',
    type        tinyint   default 1                 null comment '类型',
    data        text                                null comment '配置内容',
    create_time timestamp default CURRENT_TIMESTAMP not null
)
    engine = InnoDB
    collate = utf8mb4_bin
    row_format = COMPACT;

create table fortify
(
    id               int auto_increment
        primary key,
    create_time      timestamp  default CURRENT_TIMESTAMP     not null,
    Category         varchar(255)                             null,
    Folder           varchar(255)                             null,
    Kingdom          varchar(255)                             null,
    Abstract         text                                     null,
    Friority         varchar(255)                             null,
    `Primary`        longtext                                 null,
    Source           longtext                                 null,
    code_id          int                                      null,
    comment          varchar(255)                             null,
    check_status     int(5)     default 0                     null comment '0 未处理   1 有效漏洞  2 无效漏洞',
    Source_filename  varchar(255)                             null,
    Primary_filename varchar(255)                             null,
    hash             varchar(32)                              null,
    is_delete        tinyint(1) default 0                     not null,
    user_id          int(10)    default 0                     not null,
    update_time      datetime   default '2000-01-01 00:00:00' not null,
    constraint un_hash
        unique (hash)
)
    engine = InnoDB
    collate = utf8mb4_bin
    row_format = COMPACT;

create table github_keyword_monitor
(
    id          int(10) auto_increment
        primary key,
    app_id      int(10)      default 0                     not null,
    user_id     int(10)      default 0                     not null,
    title       varchar(255) default ''                    not null comment '关键字',
    create_time timestamp    default CURRENT_TIMESTAMP     not null,
    update_time datetime     default '2000-01-01 00:00:00' null,
    scan_time   datetime     default '2000-01-01 00:00:00' not null
)
    comment 'github关键字设置' engine = InnoDB
                               collate = utf8mb4_bin
                               row_format = COMPACT;

create table github_keyword_monitor_notice
(
    id          int(10) auto_increment
        primary key,
    parent_id   int(10)      default 0                 not null comment '关键字表id',
    app_id      int(10)      default 0                 not null,
    user_id     int(10)      default 0                 not null,
    keyword     varchar(255) default ''                not null,
    name        varchar(255) default ''                not null,
    path        varchar(255) default ''                not null,
    html_url    varchar(255) default ''                not null,
    create_time timestamp    default CURRENT_TIMESTAMP not null
)
    comment 'github关键字监控结果' engine = InnoDB
                                   collate = utf8mb4_bin
                                   row_format = COMPACT;

create table github_notice
(
    id                  int(10) auto_increment
        primary key,
    title               varchar(255) default ''                not null,
    level               varchar(30)                            not null,
    cve_id              varchar(30)                            not null,
    cwes                varchar(30)                            not null,
    cvss_score          varchar(100)                           not null,
    github_release_date datetime                               null,
    `references`        text                                   null comment '参考资料',
    `desc`              text                                   null,
    package             text                                   null,
    hash                varchar(255)                           null,
    create_time         timestamp    default CURRENT_TIMESTAMP not null
)
    engine = InnoDB
    collate = utf8mb4_bin
    row_format = COMPACT;

create table `group`
(
    id int auto_increment
        primary key
)
    engine = InnoDB
    collate = utf8mb4_bin
    row_format = COMPACT;

create table host_hydra_scan_details
(
    id          int(10) auto_increment
        primary key,
    host_id     int(10)                               not null,
    type        char(10)    default 'ssh'             not null comment '类型  如：ssh、mysql等',
    username    varchar(50) default ''                not null,
    password    varchar(50) default ''                not null,
    create_time timestamp   default CURRENT_TIMESTAMP not null,
    app_id      int(10)     default 0                 not null,
    user_id     int(10)                               not null
)
    engine = InnoDB
    collate = utf8mb4_bin
    row_format = COMPACT;

create table host_unauthorized
(
    id          int(10) auto_increment
        primary key,
    host_id     int(10)                                           not null,
    ip          char(20) charset latin1 default ''                not null,
    port        int(5)                  default 0                 not null,
    text        varchar(255)            default ''                not null,
    user_id     int(10)                 default 0                 not null,
    is_delete   tinyint(1)              default 0                 not null,
    status      tinyint(1)              default 1                 not null,
    create_time timestamp               default CURRENT_TIMESTAMP not null
)
    engine = InnoDB
    row_format = DYNAMIC;

create table log
(
    id          int(10) auto_increment
        primary key,
    content     text                                null,
    app         varchar(50)                         not null,
    create_time timestamp default CURRENT_TIMESTAMP not null
)
    engine = InnoDB
    collate = utf8mb4_bin
    row_format = COMPACT;

create index time
    on log (create_time);

create table mobsfscan
(
    id           int(10) auto_increment
        primary key,
    code_id      int(10)      default 0                     not null,
    user_id      int(10)      default 0                     not null,
    type         varchar(255) default ''                    not null,
    files        text                                       null,
    cwe          varchar(255) default ''                    not null,
    description  varchar(500) default ''                    not null,
    input_case   varchar(255) default ''                    not null,
    masvs        varchar(255) default ''                    not null,
    owasp_mobile varchar(255) default ''                    not null,
    reference    varchar(500) default ''                    not null,
    severity     varchar(255) default ''                    not null,
    create_time  timestamp    default CURRENT_TIMESTAMP     not null,
    check_status tinyint(1)   default 0                     not null comment '0未审核  1有效漏洞  2无效漏洞',
    update_time  datetime     default '2000-01-01 00:00:00' not null
)
    row_format = DYNAMIC;

create table murphysec
(
    id                int(10) auto_increment
        primary key,
    user_id           int(10)      default 0                     not null,
    code_id           int(10)      default 0                     null,
    comp_name         varchar(100) default ''                    not null comment '缺陷组件名称',
    version           varchar(50)  default ''                    not null comment '当前版本',
    min_fixed_version varchar(50)                                not null comment '最小修复版本',
    show_level        tinyint(1)                                 not null comment '修复建议等级 1强烈建议修复 2建议修复 3可选修复',
    language          varchar(20)  default ''                    not null comment '语言',
    solutions         text                                       null comment '修复方案',
    repair_status     tinyint(1)   default 1                     not null comment '修复状态 1未修复 2已修复',
    create_time       timestamp    default CURRENT_TIMESTAMP     not null,
    update_time       datetime     default '2000-01-01 00:00:00' not null
)
    engine = InnoDB
    row_format = DYNAMIC;

create table murphysec_vuln
(
    id                int(10) auto_increment
        primary key,
    user_id           int(10)                 not null,
    code_id           int(10)                 not null,
    murphysec_id      int(10)                 not null,
    title             varchar(255) default '' not null,
    cve_id            varchar(20)  default '' not null comment 'CVE编号',
    suggest_level     varchar(20)  default '' not null comment '处置建议',
    poc               tinyint(1)   default 0  not null,
    description       text                    null comment '漏洞描述',
    affected_version  varchar(255) default '' not null comment '影响版本',
    min_fixed_version varchar(100) default '' not null comment '最小修复版本',
    solutions         text                    null comment '修复建议',
    influence         int(3)       default 0  not null comment '影响指数',
    level             varchar(10)  default '' not null comment '漏洞级别',
    vuln_path         text                    null,
    publish_time      int                     not null comment '发布时间'
)
    engine = InnoDB
    row_format = DYNAMIC;

create table node
(
    id          int auto_increment
        primary key,
    userid      int(10)     default 0                 not null,
    status      tinyint(1)  default 0                 not null,
    hostname    varchar(50) default ''                not null,
    create_time timestamp   default CURRENT_TIMESTAMP not null
)
    engine = InnoDB
    collate = utf8mb4_bin
    row_format = COMPACT;

create table one_for_all
(
    id          int(10) auto_increment
        primary key,
    app_id      int(10)     default 0                 not null,
    alive       varchar(20) default ''                null,
    request     varchar(20) default ''                null,
    resolve     varchar(20) default ''                null,
    url         varchar(50)                           null,
    subdomain   varchar(50)                           null,
    port        varchar(10) default ''                null,
    level       varchar(4)  default ''                null,
    cname       varchar(255)                          null,
    ip          varchar(20)                           null,
    public      char(10)    default ''                null,
    cdn         char(3)     default ''                null,
    status      char(3)     default ''                null,
    reason      varchar(255)                          null,
    title       varchar(255)                          null,
    banner      varchar(255)                          null,
    header      varchar(255)                          null,
    history     varchar(255)                          null,
    response    varchar(255)                          null,
    ip_times    varchar(255)                          null,
    cname_times varchar(255)                          null,
    ttl         varchar(255)                          null,
    cidr        varchar(255)                          null,
    asn         varchar(255)                          null,
    org         varchar(255)                          null,
    addr        varchar(255)                          null,
    isp         varchar(255)                          null,
    resolver    varchar(255)                          null,
    module      varchar(255)                          null,
    source      varchar(255)                          null,
    elapse      varchar(255)                          null,
    find        char(10)    default ''                null,
    create_time timestamp   default CURRENT_TIMESTAMP not null,
    user_id     int(10)     default 0                 not null
)
    engine = InnoDB
    collate = utf8mb4_bin
    row_format = COMPACT;

create table plugin
(
    id          int(10) auto_increment
        primary key,
    user_id     int(10)      default 0                 not null,
    name        varchar(100) default ''                not null comment '插件名称',
    cmd         varchar(255)                           not null comment '插件执行命令',
    result_file varchar(255)                           null comment '结果文件存放位置',
    create_time timestamp    default CURRENT_TIMESTAMP not null,
    status      tinyint(1)   default 0                 not null comment '0禁用  1启用',
    is_delete   tinyint(1)   default 0                 not null,
    result_type varchar(10)  default ''                null comment 'json、csv、txt',
    update_time datetime                               null on update CURRENT_TIMESTAMP,
    tool_path   varchar(255)                           null comment '工具存放位置',
    scan_type   int(4)       default 0                 null comment '0 app 1 host 2 code  3 url',
    type        tinyint(1)   default 1                 not null comment '1执行插件扫描   2结果分析',
    constraint un_name
        unique (name, scan_type)
)
    comment '自定义插件' engine = InnoDB
                         collate = utf8mb4_bin
                         row_format = COMPACT;

create table plugin_scan_log
(
    id           int(10) auto_increment
        primary key,
    app_id       int(10)                              not null,
    plugin_id    int(10)                              not null,
    user_id      int(10)    default 0                 not null,
    content      varchar(5000)                        null comment '扫描结果内容',
    create_time  timestamp  default CURRENT_TIMESTAMP not null,
    check_status tinyint(1)                           not null comment '审核状态',
    plugin_name  varchar(50)                          null comment '插件名称',
    scan_type    int(255)                             null comment '0 app 1 host 2 code  3 url',
    log_type     int        default 0                 null comment '进度  0 开始扫描   1 完成   2 失败',
    file_content varchar(5000)                        null,
    is_read      tinyint(1) default 1                 not null comment '结果是否已读取   1未读  2已读取',
    is_custom    int(10)    default 1                 not null comment '是否为自定义插件 1否  2是',
    constraint un_id
        unique (app_id, plugin_name, log_type, scan_type)
)
    comment '自定义插件扫描结果' engine = InnoDB
                                 collate = utf8mb4_bin
                                 row_format = COMPACT;

create table plugin_store
(
    id          int(11) unsigned auto_increment comment '自增id'
        primary key,
    status      tinyint unsigned                       default 1                 not null comment '状态 1 开启  0 禁用',
    create_time timestamp                              default CURRENT_TIMESTAMP not null,
    name        varchar(50)                            default ''                not null comment '插件标识名,英文字母(惟一)',
    title       varchar(50) collate utf8mb4_unicode_ci default ''                not null comment '插件名称',
    version     varchar(20)                            default ''                not null comment '插件版本号',
    description varchar(255)                                                     not null comment '插件描述',
    code        char(32)                               default ''                not null comment '兑换码'
)
    comment '插件表' engine = InnoDB
                     row_format = COMPACT;

create table pocs_file
(
    id          int auto_increment
        primary key,
    cve_num     varchar(20)                          null,
    name        varchar(100) collate utf8mb4_bin     null,
    create_time timestamp  default CURRENT_TIMESTAMP not null,
    status      tinyint(1) default 1                 not null,
    tool        tinyint(1) default 0                 not null comment '0 pocsuite3 1 xray 2 其他',
    content     longtext                             null comment 'POC内容',
    vul_id      int                                  null,
    constraint cve_poc
        unique (cve_num, name)
)
    engine = InnoDB
    row_format = COMPACT;

create table pocsuite3
(
    id          int auto_increment
        primary key,
    url         varchar(255)         null,
    name        varchar(255)         null,
    ssv_id      varchar(255)         null,
    cms         varchar(255)         null,
    version     varchar(255)         null,
    is_max      int(255)   default 0 null,
    tel         varchar(30)          null comment '电话号码',
    regaddress  varchar(255)         null comment '公司注册地址',
    ip          varchar(30)          null comment 'IP地址',
    CompanyName varchar(255)         null comment '公司名字',
    SiteLicense varchar(255)         null,
    CompanyType varchar(255)         null,
    regcapital  varchar(255)         null comment '注册资金',
    is_delete   tinyint(1) default 0 not null,
    user_id     int(10)    default 0 not null
)
    engine = InnoDB
    collate = utf8mb4_bin
    row_format = COMPACT;

create table process_safe
(
    id          int auto_increment
        primary key,
    `key`       varchar(255)     null,
    value       varchar(255)     null,
    status      int(4) default 1 not null comment '0 失效   1启用',
    note        varchar(255)     null comment '描述',
    update_time datetime         null on update CURRENT_TIMESTAMP,
    type        int    default 0 null comment '工具分类  0  黑盒扫描   1  白盒审计  2  专项扫描  3  系统其他'
)
    engine = InnoDB
    collate = utf8mb4_bin
    row_format = COMPACT;

create table project_tools
(
    id          int(10) auto_increment
        primary key,
    type        tinyint(1)  default 1                 not null comment '1app 2code',
    project_id  int(10)     default 9                 not null comment '项目id',
    tools_name  varchar(50) default ''                not null comment '工具名称',
    create_time timestamp   default CURRENT_TIMESTAMP not null
)
    engine = InnoDB
    row_format = DYNAMIC;

create table proxy
(
    id          int(10) auto_increment
        primary key,
    host        varchar(255)                        not null comment 'ip地址',
    port        varchar(255)                        not null comment '端口号',
    status      int(4)    default 1                 not null comment '1 有效  0 无效',
    create_time timestamp default CURRENT_TIMESTAMP not null
)
    comment '代理表' engine = InnoDB
                     collate = utf8mb4_bin
                     row_format = COMPACT;

create table semgrep
(
    id                int auto_increment
        primary key,
    check_id          varchar(100)                             null,
    code_id           int                                      null comment '项目ID',
    create_time       timestamp  default CURRENT_TIMESTAMP     not null,
    end_col           varchar(512)                             null,
    end_line          varchar(512)                             null comment '代码行号',
    end_offset        varchar(512)                             null,
    extra_is_ignored  varchar(512)                             null,
    extra_lines       text                                     null,
    extra_message     varchar(512)                             null,
    extra_metadata    text                                     null,
    extra_metavars    text                                     null,
    extra_severity    varchar(512)                             null comment '危险等级',
    path              varchar(512)                             null comment '污染来源',
    start_col         varchar(512)                             null,
    start_line        varchar(512)                             null,
    start_offset      varchar(512)                             null,
    check_status      tinyint(1) default 0                     not null comment '审核状态',
    is_delete         tinyint(1) default 0                     not null,
    user_id           int(10)    default 0                     not null,
    update_time       datetime   default '2000-01-01 00:00:00' not null,
    extra_engine_kind text                                     null
)
    engine = InnoDB
    collate = utf8mb4_bin
    row_format = COMPACT;

create table svn_project
(
    id        int auto_increment
        primary key,
    name      varchar(255)                           null,
    command   varchar(255)                           null,
    scan_time datetime default '2000-01-01 14:14:14' null
)
    engine = InnoDB
    row_format = COMPACT;

create table system_config
(
    id        int auto_increment
        primary key,
    name      varchar(30)          null comment '配置名称 如：百度token',
    `key`     varchar(30)          null,
    value     varchar(512)         null,
    is_delete tinyint(1) default 0 not null
)
    engine = InnoDB
    collate = utf8mb4_bin
    row_format = COMPACT;

create table system_zanzhu
(
    id      int auto_increment
        primary key,
    name    varchar(255) null,
    amount  float        null,
    time    date         null,
    message varchar(512) null
)
    engine = InnoDB
    collate = utf8mb4_bin
    row_format = COMPACT;

create table task_host_scan
(
    id          int auto_increment
        primary key,
    app_id      int                                 null,
    url         varchar(255)                        null,
    status      tinyint   default 0                 not null,
    create_time timestamp default CURRENT_TIMESTAMP not null,
    update_time datetime                            null on update CURRENT_TIMESTAMP
)
    engine = InnoDB
    collate = utf8mb4_bin
    row_format = COMPACT;

create table task_url_crawler
(
    id          int auto_increment
        primary key,
    app_id      int                                 null,
    url         varchar(255)                        null,
    status      tinyint   default 0                 not null,
    create_time timestamp default CURRENT_TIMESTAMP not null,
    update_time datetime                            null on update CURRENT_TIMESTAMP
)
    engine = InnoDB
    collate = utf8mb4_bin
    row_format = COMPACT;

create table text
(
    hash        varchar(64)                         not null
        primary key,
    content     longtext                            not null,
    create_time timestamp default CURRENT_TIMESTAMP not null
)
    engine = InnoDB
    row_format = COMPACT;

create table urls_sqlmap
(
    id          int(10) auto_increment
        primary key,
    urls_id     int(10)                             not null,
    create_time timestamp default CURRENT_TIMESTAMP not null,
    type        varchar(255)                        null comment '注入类型',
    title       varchar(255)                        null,
    payload     text                                null,
    app_id      int(10)   default 0                 not null,
    `system`    varchar(255)                        null,
    application varchar(255)                        null,
    dbms        varchar(255)                        null,
    user_id     int(10)   default 0                 not null
)
    engine = InnoDB
    collate = utf8mb4_bin
    row_format = COMPACT;

create table user
(
    id              int(10) auto_increment
        primary key,
    username        varchar(20)  default '' not null comment '帐号',
    password        char(32)     default '' not null comment '密码',
    salt            varchar(20)  default '' not null comment '密码盐值',
    nickname        varchar(20)  default '' not null comment '昵称',
    auth_group_id   int(10)      default 0  not null comment '用户组id',
    created_at      int(10)      default 0  not null comment '添加时间',
    last_login_ip   char(20)     default '' not null comment '最后登录ip地址',
    last_login_time int(10)      default 0  not null comment '最后登陆时间',
    status          tinyint(1)   default 0  not null comment '状态 1正常  2禁用',
    update_time     int(10)      default 0  not null comment '修改时间',
    is_delete       tinyint(1)   default 0  not null,
    sex             tinyint(1)   default 0  not null comment '性别',
    phone           char(11)     default '' not null comment '手机号码',
    dd_token        varchar(100) default '' not null comment '钉钉token',
    email           char(50)     default '' not null comment '邮箱',
    token           char(32)     default '' not null,
    url             varchar(255) default '' not null comment '主页url'
)
    engine = InnoDB
    collate = utf8mb4_bin
    row_format = COMPACT;

create table user_log
(
    id          int(10) auto_increment
        primary key,
    username    char(30)     default ''                not null,
    create_time timestamp    default CURRENT_TIMESTAMP not null,
    content     varchar(255) default ''                not null comment '详情',
    type        varchar(50)  default ''                not null comment '操作类型',
    ip          varchar(50)  default ''                not null
)
    engine = InnoDB
    row_format = COMPACT;

create table vul_target
(
    id          int auto_increment
        primary key,
    addr        varchar(100)                        null,
    ip          varchar(32)                         null,
    port        int(10)                             null,
    query       varchar(255)                        null,
    create_time timestamp default CURRENT_TIMESTAMP not null,
    is_vul      int       default 0                 not null comment '是否存在漏洞 0 位置 1 存在 2 不存在',
    vul_id      int                                 null comment '缺陷ID',
    user_id     int(10)   default 0                 not null,
    constraint un_addr
        unique (ip, port, vul_id)
)
    engine = InnoDB
    collate = utf8mb4_bin
    row_format = COMPACT;

create table vulnerable
(
    id                  int auto_increment
        primary key,
    nature              varchar(255) collate utf8mb4_bin         null,
    name                varchar(255) collate utf8mb4_bin         null,
    vul_num             varchar(255) collate utf8mb4_bin         null,
    cve_num             varchar(255) collate utf8mb4_bin         null,
    cnvd_num            varchar(255) collate utf8mb4_bin         null,
    cnnvd_num           varchar(255) collate utf8mb4_bin         null,
    src_num             varchar(255) collate utf8mb4_bin         null,
    vul_level           varchar(255) collate utf8mb4_bin         null,
    vul_type            varchar(255) collate utf8mb4_bin         null,
    cwe                 varchar(255) collate utf8mb4_bin         null,
    vul_cvss            varchar(255) collate utf8mb4_bin         null,
    cvss_vector         varchar(255) collate utf8mb4_bin         null,
    open_time           varchar(255) collate utf8mb4_bin         null,
    vul_repair_time     varchar(255) collate utf8mb4_bin         null,
    vul_source          varchar(255) collate utf8mb4_bin         null,
    temp_plan           varchar(512) collate utf8mb4_bin         null,
    temp_plan_s3        varchar(255) collate utf8mb4_bin         null,
    formal_plan         varchar(512) collate utf8mb4_bin         null,
    patch_s3            varchar(255) collate utf8mb4_bin         null,
    patch_url           varchar(255) collate utf8mb4_bin         null,
    patch_use_func      varchar(255) collate utf8mb4_bin         null,
    cpe                 varchar(255) collate utf8mb4_bin         null,
    product_name        varchar(255)                             null,
    product_open        varchar(255) collate utf8mb4_bin         null,
    product_store       varchar(255) collate utf8mb4_bin         null,
    store_website       varchar(255) collate utf8mb4_bin         null,
    assem_name          varchar(255) collate utf8mb4_bin         null,
    affect_ver          varchar(255) collate utf8mb4_bin         null,
    ver_open_date       varchar(255) collate utf8mb4_bin         null,
    sub_update_url      varchar(255) collate utf8mb4_bin         null,
    git_url             varchar(255) collate utf8mb4_bin         null,
    git_commit_id       varchar(255) collate utf8mb4_bin         null,
    git_fixed_commit_id varchar(255) collate utf8mb4_bin         null,
    product_cate        varchar(255) collate utf8mb4_bin         null,
    product_field       varchar(255) collate utf8mb4_bin         null,
    product_type        varchar(255) collate utf8mb4_bin         null,
    fofa_max            varchar(255) collate utf8mb4_bin         null,
    fofa_con            varchar(255) collate utf8mb4_bin         null,
    created_at          datetime                                 null,
    updated_at          datetime                                 null,
    user_id             int(10)                                  null,
    is_pass             int(10)                                  null,
    user_name           varchar(255) collate utf8mb4_bin         null,
    is_sub_attack       int(10)                                  null,
    temp_plan_s3_hash   varchar(255) collate utf8mb4_bin         null,
    patch_s3_hash       varchar(255) collate utf8mb4_bin         null,
    is_pass_attack      varchar(255) collate utf8mb4_bin         null,
    auditor             varchar(255) collate utf8mb4_bin         null,
    cause               varchar(255) collate utf8mb4_bin         null,
    scan_time           datetime   default '2000-01-01 00:00:00' null,
    is_poc              int(10)    default 0                     null comment '是否有POC',
    is_delete           tinyint(1) default 0                     not null,
    target_scan_time    datetime                                 null
)
    engine = InnoDB
    row_format = COMPACT;

create table xray
(
    id           int auto_increment
        primary key,
    app_id       int                                    null,
    create_time  timestamp    default CURRENT_TIMESTAMP not null,
    detail       text                                   null,
    plugin       varchar(100)                           null,
    target       varchar(255)                           null,
    check_status tinyint(1)   default 0                 not null comment '审核状态',
    hazard_level tinyint(1)   default 0                 not null comment '危险等级',
    url_source   varchar(255) default ''                not null comment 'url来源',
    is_delete    tinyint(1)   default 0                 not null,
    user_id      int(10)      default 0                 not null
)
    engine = InnoDB
    collate = utf8mb4_bin
    row_format = COMPACT;

create table zhiwen
(
    id       int(10) auto_increment
        primary key,
    add_by   varchar(255) null,
    add_time varchar(255) null,
    filters  varchar(255) null,
    keyword  varchar(255) null,
    md5      varchar(255) null,
    supplier varchar(255) null,
    tags     varchar(255) null,
    title    varchar(255) null
)
    engine = InnoDB
    row_format = COMPACT;


