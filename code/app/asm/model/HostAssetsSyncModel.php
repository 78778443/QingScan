<?php

namespace app\asm\model;

use app\asm\model\HidsQingtengModel;
use app\model\BaseModel;
use GuzzleHttp\Client;
use think\console\Output;
use think\facade\Db;
use Volcengine\Common\Configuration;
use Volcengine\Common\HeaderSelector;
use Volcengine\Ecs\Api\ECSApi;
use Volcengine\Ecs\Model\DescribeInstancesRequest;
use AlibabaCloud\SDK\Ecs\V20140526\Ecs;
use AlibabaCloud\Credentials\Credential;
use AlibabaCloud\SDK\Ecs\V20140526\Models\DescribeInstancesRequest as AliyunDescribeInstancesRequest;
use AlibabaCloud\Tea\Utils\Utils\RuntimeOptions;

class HostAssetsSyncModel extends BaseModel
{
    /**
     * 从火山云拉取主机资产
     */
    public static function importFromHuoshan(Output $output): void
    {
        $output->writeln("正在从火山云API拉取主机资产...");


        // 从配置获取火山云AK/SK
        if (empty(env('HUOSHAN.AK')) || empty(env('HUOSHAN.SK'))) {
            $output->writeln("<error>火山云AK/SK配置缺失</error>");
            return;
        }

        // 初始化火山云SDK
        $configuration = Configuration::getDefaultConfiguration()
            ->setAk(env('HUOSHAN.AK'))
            ->setSk(env('HUOSHAN.SK'))
            ->setRegion(env('HUOSHAN.REGION') ?? 'cn-beijing');

        // 解决PHP 8.1+兼容性问题
        $client = new Client();
        $configInstance = $configuration;
        $selector = new HeaderSelector();

        // 临时降低错误报告级别，忽略弃用警告
        $errorReporting = error_reporting();
        error_reporting($errorReporting & ~E_DEPRECATED);

        // 创建ECSApi实例和请求对象
        $apiInstance = new ECSApi($client, $configInstance, $selector);

        // 恢复原来的错误报告级别
        error_reporting($errorReporting);

        // 调用API获取实例列表，实现分页逻辑
        try {
            $allInstances = [];
            $maxResults = 100; // 每次获取100条记录
            $nextToken = null;
            $pageNumber = 1;

            do {
                // 创建请求对象，设置max_results和next_token参数
                $describeInstancesRequest = new DescribeInstancesRequest([
                    'max_results' => $maxResults,
                    'next_token' => $nextToken
                ]);

                $response = $apiInstance->describeInstances($describeInstancesRequest);

                // 检查API响应是否成功
                if (!isset($response)) {
                    $output->writeln("<error>火山云API无响应</error>");
                    return;
                }

                // 根据响应类型处理数据
                if (is_object($response)) {
                    // 使用对象的getter方法直接获取数据
                    if (method_exists($response, 'getInstances')) {
                        $pageInstances = $response->getInstances();
                        $pageCount = is_array($pageInstances) ? count($pageInstances) : 0;
                        
                        if ($pageCount > 0) {
                            $allInstances = array_merge($allInstances, $pageInstances);
                            
                            // 获取总实例数和下一页标记
                            $totalCount = method_exists($response, 'getTotalCount') ? $response->getTotalCount() : count($allInstances);
                            $nextToken = method_exists($response, 'getNextToken') ? $response->getNextToken() : null;
                            
                            $output->writeln("<info>已获取第 " . $pageNumber . " 页，共 " . count($allInstances) . "/" . $totalCount . " 台主机</info>");
                        }
                    } else {
                        $output->writeln("<error>火山云API响应对象没有getInstances方法</error>");
                        return;
                    }
                } else if (is_array($response)) {
                    // 如果是数组，保持原处理方式
                    if (!isset($response['Result']) || !is_array($response['Result'])) {
                        $output->writeln("<error>火山云API响应中缺少Result字段</error>");
                        return;
                    }

                    if (!isset($response['Result']['Instances']) || !is_array($response['Result']['Instances'])) {
                        $output->writeln("<error>火山云API响应中缺少Instances字段</error>");
                        return;
                    }

                    $pageInstances = $response['Result']['Instances'];
                    $pageCount = count($pageInstances);
                    
                    if ($pageCount > 0) {
                        $allInstances = array_merge($allInstances, $pageInstances);
                        
                        // 获取总实例数和下一页标记
                        $totalCount = isset($response['Result']['TotalCount']) ? $response['Result']['TotalCount'] : count($allInstances);
                        $nextToken = isset($response['Result']['NextToken']) ? $response['Result']['NextToken'] : null;
                        
                        $output->writeln("<info>已获取第 " . $pageNumber . " 页，共 " . count($allInstances) . "/" . $totalCount . " 台主机</info>");
                    }
                } else {
                    $output->writeln("<error>火山云API响应格式未知</error>");
                    return;
                }

                $pageNumber++;
            } while ($nextToken);

            $totalInstances = count($allInstances);
            $output->writeln("<info>成功获取火山云主机实例列表，共 " . $totalInstances . " 台主机</info>");

            // 检查是否有数据需要导入
            if ($totalInstances > 0) {
                // 构建最终的响应格式
                $formattedResponse = null;
                
                if (is_object($response)) {
                    // 使用反射和getter方法将所有实例转换为数组
                    $instancesArray = [];
                    foreach ($allInstances as $instance) {
                        $instanceArray = [];
                        $reflection = new \ReflectionClass($instance);
                        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);



                        foreach ($methods as $method) {
                            $methodName = $method->getName();
                            // 只处理getter方法
                            if (strpos($methodName, 'get') === 0 && $methodName !== 'getModelName') {
                                // 调用getter方法获取属性值
                                $value = $method->invoke($instance);
                                // 将getter方法名转换为驼峰式的属性名
                                $propertyName = lcfirst(substr($methodName, 3));
                                $instanceArray[$propertyName] = $value;
                            }
                        }

                        $instancesArray[] = $instanceArray;
                    }

                    // 创建符合HostAssetsModel预期格式的数组
                    $formattedResponse = [
                        'Result' => [
                            'Instances' => $instancesArray
                        ]
                    ];
                } else if (is_array($response)) {
                    // 如果是数组响应，直接使用合并后的实例
                    $formattedResponse = [
                        'Result' => [
                            'Instances' => $allInstances
                        ]
                    ];
                }

                if ($formattedResponse) {
                    HostAssetsModel::importFromHuoshanApi($formattedResponse);
                }
            }

            $output->writeln("<info>火山云主机资产导入数据库完成</info>");
        } catch (Throwable $e) {
            $output->writeln("<error>调用火山云API失败: " . $e->getMessage() . "</error>");
            throw $e; // 重新抛出异常，让上层处理
        }
    }

    /**
     * 从阿里云拉取主机资产
     */
    public static function importFromAliyun(Output $output): void
    {
        $output->writeln("正在从阿里云API拉取主机资产...");

        // 从配置获取阿里云AK/SK/区域
        if (empty(env('ALIYUN.AK')) || empty(env('ALIYUN.SK'))) {
            $output->writeln("<error>阿里云AK/SK配置缺失</error>");
            return;
        }

        $accessKeyId = env('ALIYUN.AK');
        $accessKeySecret = env('ALIYUN.SK');
        $regionId = env('ALIYUN.REGION_ID') ?? 'cn-jiangsu-1';

            // 初始化阿里云ECS客户端
            $config = new \Darabonba\OpenApi\Models\Config([
                "accessKeyId" => $accessKeyId,
                "accessKeySecret" => $accessKeySecret,
                "regionId" => $regionId,
                "endpoint" => "ecs.{$regionId}.aliyuncs.com"
            ]);
            $client = new Ecs($config);

            // 调用API获取实例列表，实现分页逻辑
            $allInstances = [];
            $pageSize = 100; // 每次获取100条记录
            $pageNumber = 1;
            $totalCount = 0;

            do {
                // 创建请求对象
                $describeInstancesRequest = new AliyunDescribeInstancesRequest([
                    "regionId" => $regionId,
                    "pageSize" => $pageSize,
                    "pageNumber" => $pageNumber
                ]);

                // 直接调用API，不需要RuntimeOptions
                $response = $client->describeInstances($describeInstancesRequest);

                // 添加调试输出查看响应结构
                $output->writeln("<info>API响应类型: " . gettype($response) . "</info>");
                if (is_object($response)) {
                    $output->writeln("<info>响应对象属性: " . implode(', ', array_keys(get_object_vars($response))) . "</info>");
                    if (isset($response->body)) {
                        $output->writeln("<info>body属性类型: " . gettype($response->body) . "</info>");
//                        $output->writeln("<info>body内容: " . json_encode($response->body, JSON_UNESCAPED_UNICODE) . "</info>");
                    }
                }

                // 检查响应结构并正确处理
                $pageInstances = [];
                if (is_object($response) && isset($response->body)) {
                    $body = $response->body;
                    if (is_object($body)) {
                        // 根据调试输出，阿里云API返回的结构是 instances.instance 数组
                        if (property_exists($body, 'instances') && is_object($body->instances) && property_exists($body->instances, 'instance')) {
                            $pageInstances = $body->instances->instance;
                        } elseif (property_exists($body, 'instances') && is_array($body->instances)) {
                            $pageInstances = $body->instances;
                        } elseif (property_exists($body, 'Instances') && is_object($body->Instances) && property_exists($body->Instances, 'Instance')) {
                            $pageInstances = $body->Instances->Instance;
                        } elseif (is_array($body) && isset($body['Instances']) && isset($body['Instances']['Instance'])) {
                            $pageInstances = $body['Instances']['Instance'];
                        } elseif (is_object($body) && property_exists($body, 'Instances') && is_array($body->Instances)) {
                            $pageInstances = $body->Instances;
                        }
                    }
                } elseif (is_array($response) && isset($response['body']) && is_array($response['body'])) {
                    $body = $response['body'];
                    if (isset($body['Instances']) && isset($body['Instances']['Instance'])) {
                        $pageInstances = $body['Instances']['Instance'];
                    } elseif (isset($body['instances']) && isset($body['instances']['instance'])) {
                        $pageInstances = $body['instances']['instance'];
                    } elseif (isset($body['instances'])) {
                        $pageInstances = $body['instances'];
                    }
                }

                // 检查是否成功获取到实例列表
                if (empty($pageInstances)) {
                    $output->writeln("<info>未能获取到实例数据，响应结构可能已改变</info>");
                    break;
                }

                $pageCount = count($pageInstances);

                if ($pageCount > 0) {
                    $allInstances = array_merge($allInstances, $pageInstances);
                    
                    // 获取总数量
                    if (is_object($response) && isset($response->body)) {
                        if (property_exists($response->body, 'totalCount')) {
                            $totalCount = $response->body->totalCount;
                        } elseif (property_exists($response->body, 'TotalCount')) {
                            $totalCount = $response->body->TotalCount;
                        } elseif (property_exists($response->body, 'total_count')) {
                            $totalCount = $response->body->total_count;
                        } else {
                            // 如果无法获取总数，则设为当前页面数量以避免无限循环
                            $totalCount = count($allInstances);
                        }
                    } elseif (is_array($response) && isset($response['body'])) {
                        $body = $response['body'];
                        if (isset($body['totalCount'])) {
                            $totalCount = $body['totalCount'];
                        } elseif (isset($body['TotalCount'])) {
                            $totalCount = $body['TotalCount'];
                        } elseif (isset($body['total_count'])) {
                            $totalCount = $body['total_count'];
                        } else {
                            $totalCount = count($allInstances);
                        }
                    } else {
                        $totalCount = count($allInstances);
                    }
                    
                    $output->writeln("<info>已获取第 " . $pageNumber . " 页，共 " . count($allInstances) . "/" . $totalCount . " 台主机</info>");
                }

                $pageNumber++;
            } while (count($allInstances) < $totalCount);

            $totalInstances = count($allInstances);
            $output->writeln("<info>成功获取阿里云主机实例列表，共 " . $totalInstances . " 台主机</info>");

            // 检查是否有数据需要导入
            if ($totalInstances > 0) {
                // 转换实例数据格式
                $instancesArray = [];
                foreach ($allInstances as $index => $instance) {
                    $instanceArray = [];
                    
                    // 检查实例数据类型并进行相应处理
                    if (is_object($instance)) {
                        // 如果是对象，使用多种方法获取属性
                        if (method_exists($instance, 'toMap')) {
                            // 尝试使用toMap方法（如果可用）
                            $instanceArray = $instance->toMap();
                        } else {
                            // 使用反射获取属性
                            $reflection = new \ReflectionClass($instance);
                            $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
                            
                            // 首先直接获取公共属性
                            foreach ($properties as $property) {
                                $propertyName = $property->getName();
                                $propertyValue = $property->getValue($instance);
                                
                                // 转换属性名为小写开头的驼峰命名
                                $camelCaseName = lcfirst($propertyName);
                                $instanceArray[$camelCaseName] = $propertyValue;
                            }
                            
                            // 然后尝试通过getter方法获取值
                            $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
                            foreach ($methods as $method) {
                                $methodName = $method->getName();
                                // 只处理getter方法
                                if (strpos($methodName, 'get') === 0 && $methodName !== 'getModelName') {
                                    // 调用getter方法获取属性值
                                    try {
                                        $value = $method->invoke($instance);
                                        // 将getter方法名转换为驼峰式的属性名
                                        $propertyName = lcfirst(substr($methodName, 3));
                                        
                                        // 只有当属性数组中还没有该值时才设置，避免覆盖
                                        if (!array_key_exists($propertyName, $instanceArray)) {
                                            $instanceArray[$propertyName] = $value;
                                        }
                                    } catch (\Exception $e) {
                                        // 忽略getter调用错误
                                        continue;
                                    }
                                }
                            }
                        }
                    } elseif (is_array($instance)) {
                        // 如果是数组，直接使用
                        $instanceArray = $instance;
                    } else {
                        // 其他类型跳过
                        $output->writeln("<warning>跳过非对象/数组类型的实例，索引: {$index}</warning>");
                        continue;
                    }

                    // 确保至少有一些基本数据
                    if (!empty($instanceArray)) {
                        $instancesArray[] = $instanceArray;
                    } else {
                        $output->writeln("<warning>无法从实例对象中提取数据，索引: {$index}</warning>");
                    }
                }

                // 调试：打印$instancesArray的长度
                $output->writeln("<info>实例数组长度: " . count($instancesArray) . "</info>");
                
                // 创建符合HostAssetsModel预期格式的数组
                $formattedResponse = [
                    'Instances' => $instancesArray
                ];

                $result = HostAssetsModel::importFromAliyunApi($formattedResponse);
                $output->writeln("<info>导入结果: " . ($result ? "成功" : "失败") . "</info>");
            }

            $output->writeln("<info>阿里云主机资产导入数据库完成</info>");

    }

    /**
     * 从移动云拉取主机资产
     */
    public static function importFromYidong(Output $output): void
    {
        $output->writeln("正在从移动云拉取主机资产...");

        // Python脚本路径
        $scriptPath = app()->getRootPath() . 'extend/tools/yidongApi/getYiDongHostList.py';
        $jsonFilePath = app()->getRootPath() . 'extend/tools/yidongApi/server_list.json';

        try {
            // 执行Python脚本
            $output->writeln("执行Python脚本: {$scriptPath}");
            $command = "cd " . dirname($scriptPath) . " && python3 getYiDongHostList.py";
//            exec($command, $outputLines, $returnCode);
//
//            if ($returnCode !== 0) {
//                $output->writeln("<error>Python脚本执行失败，返回码: {$returnCode}</error>");
//                $output->writeln("<error>输出: " . implode(PHP_EOL, $outputLines) . "</error>");
//                return;
//            }

            // 读取生成的JSON文件
            $output->writeln("读取JSON文件: {$jsonFilePath}");
            if (!file_exists($jsonFilePath)) {
                $output->writeln("<error>JSON文件不存在: {$jsonFilePath}</error>");
                return;
            }

            // 解析JSON数据
            $jsonContent = file_get_contents($jsonFilePath);
            $apiData = json_decode($jsonContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $output->writeln("<error>JSON解析失败: " . json_last_error_msg() . "</error>");
                return;
            }

            // 打印获取到的数据信息
            $output->writeln("<info>成功获取移动云主机实例数据</info>");
            $output->writeln("<info>数据结构: " . print_r(array_keys($apiData), true) . "</info>");

            // 导入数据到数据库
            HostAssetsModel::importFromYidongApi($apiData);

            // 删除临时JSON文件
//            unlink($jsonFilePath);
//            $output->writeln("<info>临时JSON文件已删除</info>");

        } catch (Throwable $e) {
            $output->writeln("<error>执行移动云资产获取失败: " . $e->getMessage() . "</error>");
            $output->writeln("<error>错误位置: " . $e->getFile() . ":" . $e->getLine() . "</error>");
        }
    }

    /**
     * 从百度云拉取主机资产
     */
    public static function importFromBaidu(Output $output): void
    {
        $output->writeln("正在从百度云API拉取主机资产...");

        // 获取百度云命令行工具路径
        $baiduToolPath = env('BAIDU.TOOL_PATH') ?? app()->getRootPath() . 'extend/tools/baiduCloud/main.py';

        if (!file_exists($baiduToolPath)) {
            $output->writeln("<error>百度云命令行工具不存在: {$baiduToolPath}</error>");
            return;
        }

        try {
            // 执行百度云命令行工具
            $command = "python3 {$baiduToolPath}";
            $output->writeln("<info>执行命令: {$command}</info>");
            $executionResult = shell_exec($command);

            // 检查命令执行结果
            if (empty($executionResult)) {
                $output->writeln("<error>百度云命令行工具无输出</error>");
                return;
            }

            // 解析JSON字符串
            // Python脚本现在直接返回有效的JSON格式，简化解析逻辑
            $apiData = json_decode($executionResult, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $output->writeln("<error>JSON解析失败: " . json_last_error_msg() . "</error>");
                $output->writeln("<error>原始输出: {$executionResult}</error>");
                return;
            }

            // 打印获取到的数据信息
            $output->writeln("<info>成功获取百度云主机实例数据</info>");
            $output->writeln("<info>数据结构: " . print_r(array_keys($apiData), true) . "</info>");

            // 导入数据到数据库
            HostAssetsModel::importFromBaiduApi($apiData);

        } catch (Throwable $e) {
            $output->writeln("<error>执行百度云资产获取失败: " . $e->getMessage() . "</error>");
            $output->writeln("<error>错误位置: " . $e->getFile() . ":" . $e->getLine() . "</error>");
        }
    }

    /**
     * 从天翼云拉取主机资产
     */
    public static function importFromTianyi(Output $output, $teamId = 'A'): void
    {
        $output->writeln("正在从天翼云API拉取主机资产 (团队: {$teamId})...");

        // 根据团队ID选择对应的配置
        $configPrefix = $teamId === 'B' ? 'TIANYI_B' : 'TIANYI';
        
        // 从配置获取天翼云相关参数
        if (empty(env("{$configPrefix}.AK")) || empty(env("{$configPrefix}.SK"))) {
            $output->writeln("<error>天翼云{$teamId}团队AK/SK配置缺失</error>");
            return;
        }

        // 获取天翼云配置
        $accessKeyId = env("{$configPrefix}.AK");
        $secretAccessKey = env("{$configPrefix}.SK");
        $regionId = env("{$configPrefix}.REGION_ID"); // 默认区域ID
        $endpoint = env("{$configPrefix}.ENDPOINT") ?? 'ctecs-global.ctapi.ctyun.cn';
        $resourcePath = env("{$configPrefix}.RESOURCE_PATH") ?? '/v4/ecs/list-instances';

        // 初始化实例数组
        $instances = [];
        
        // 设置分页参数
        $pageSize = 50; // 每页获取50条记录，减少单次请求压力
        $currentPage = 1;
        $totalPages = 1;
        $totalCount = 0;
        
        // 循环获取所有记录
        do {
            // 使用天翼云API客户端类进行请求，添加分页参数
            $response = self::callTianyiApi($accessKeyId, $secretAccessKey, $regionId, $endpoint, $resourcePath, [
                "pageNo" => $currentPage, // 当前页码
                "pageSize" => $pageSize   // 每页记录数
            ]);

            if ($response['error']) {
                $output->writeln("<error>天翼云API请求失败: " . $response['error'] . "</error>");
                return;
            }

            if ($response['status_code'] != 200) {
                $output->writeln("<error>天翼云API响应错误，状态码: " . $response['status_code'] . "</error>");
                return;
            }

            // 解析API响应
            $apiResponse = json_decode($response['body'], true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $output->writeln("<error>解析天翼云API响应失败: " . json_last_error_msg() . "</error>");
                return;
            }

            // 提取实例数据
            if (isset($apiResponse['returnObj']['results']) && is_array($apiResponse['returnObj']['results'])) {
                $instances = array_merge($instances, $apiResponse['returnObj']['results']);
            }
            
            // 更新分页信息
            if ($currentPage == 1) {
                $totalCount = isset($apiResponse['returnObj']['totalCount']) ? $apiResponse['returnObj']['totalCount'] : 0;
                $totalPages = isset($apiResponse['returnObj']['totalPage']) ? $apiResponse['returnObj']['totalPage'] : 1;
                $output->writeln("<info>发现天翼云主机实例总数: " . $totalCount . " 台</info>");
            }
            
            $output->writeln("<info>已获取第 " . $currentPage . "/" . $totalPages . " 页，共 " . count($instances) . " 台主机</info>");
            
            // 进入下一页
            $currentPage++;
            
        } while ($currentPage <= $totalPages);

        $instanceCount = count($instances);
        $output->writeln("<info>成功获取天翼云主机实例列表，共 " . $instanceCount . " 台主机</info>");

        if ($instanceCount > 0) {
            // 准备天翼云资源数据并导入数据库
            $tianyiResources = [];
            $hosts = [];

            foreach ($instances as $instance) {
                // 处理实例数据，映射到系统所需字段
                $resourceId = $instance['instanceID'] ?? $instance['id'] ?? $instance['uuid'] ?? uniqid('tianyi_');
                $resourceName = $instance['instanceName'] ?? $instance['name'] ?? $instance['displayName'] ?? 'Unknown';
                $resourceType = $instance['resourceType'] ?? $instance['type'] ?? $instance['instanceType'] ?? '云主机';
                $region = $instance['region'] ?? env('TIANYI.REGION_ID') ?? 'default';
                $status = strtoupper($instance['status'] ?? $instance['instanceStatus'] ?? 'unknown');
                // 获取所有公网IP
                $publicIps = [];
                if (!empty($instance['floatingIP'])) {
                    $publicIps[] = $instance['floatingIP'];
                }
                if (!empty($instance['publicIp'])) {
                    $publicIps[] = $instance['publicIp'];
                }
                if (!empty($instance['publicIPAddress'])) {
                    $publicIps[] = $instance['publicIPAddress'];
                }
                if (!empty($instance['eip'])) {
                    $publicIps[] = $instance['eip'];
                }
                // 去重
                $publicIps = array_unique(array_filter($publicIps));
                $publicIp = !empty($publicIps) ? reset($publicIps) : '';
                
                // 获取所有私有IP
                $privateIps = [];
                if (!empty($instance['privateIP'])) {
                    $privateIps[] = $instance['privateIP'];
                }
                if (isset($instance['addresses']) && is_array($instance['addresses'])) {
                    foreach ($instance['addresses'] as $address) {
                        if (isset($address['addressList']) && is_array($address['addressList'])) {
                            foreach ($address['addressList'] as $addrInfo) {
                                if (isset($addrInfo['type']) && $addrInfo['type'] === 'fixed') {
                                    if (isset($addrInfo['ipAddress'])) {
                                        $privateIps[] = $addrInfo['ipAddress'];
                                    } elseif (isset($addrInfo['addr'])) {
                                        $privateIps[] = $addrInfo['addr'];
                                    }
                                }
                            }
                        }
                    }
                }
                // 去重
                $privateIps = array_unique(array_filter($privateIps));
                $privateIp = !empty($privateIps) ? reset($privateIps) : '0.0.0.0';
                // 使用convertDatetime方法处理创建时间
                $createTime = self::convertDatetime($instance['createTime'] ?? $instance['create_time'] ?? $instance['createdTime'] ?? null) ?? date('Y-m-d H:i:s');

                // 天翼云资源表数据
                // 构建完整的资源数据
                $resourceData = [
                    'resource_id' => $resourceId,
                    'resource_name' => $resourceName,
                    'resource_type' => $resourceType,
                    'region' => $region,
                    'status' => $status,
                    'public_ip' => $publicIp,
                    'private_ip' => $privateIp,
                    'public_ips' => json_encode($publicIps),
                    'private_ips' => json_encode($privateIps),
                    'create_time' => $createTime,
                    'update_time' => date('Y-m-d H:i:s'),
                    'original_json' => json_encode($instance, JSON_UNESCAPED_UNICODE),
                ];
                
                $tianyiResources[] = $resourceData;

                // 主机资产表数据
                $hosts[] = [
                    'instance_id' => $resourceId,
                    'instance_name' => $resourceName,
                    'display_name' => $resourceName,
                    'cloud_platform' => 'tianyi',
                    'status' => $status,
                    'public_ip' => $publicIp,
                    'private_ip' => $privateIp,
                    'mac_address' => $instance['macAddress'] ?? $instance['mac'] ?? '',
                    'os_type' => $instance['osType'] ?? $instance['os'] ?? 'Unknown',
                    'os_name' => $instance['osName'] ?? $instance['imageName'] ?? 'Unknown',
                    'cpu' => $instance['cpu'] ?? $instance['vCPU'] ?? 0,
                    'memory' => $instance['memory'] ?? $instance['ram'] ?? 0,
                    'instance_type' => $resourceType,
                    'vpc_id' => $instance['vpcId'] ?? $instance['vpcID'] ?? '',
                    'vpc_name' => $instance['vpcName'] ?? '',
                    'security_groups' => json_encode($instance['securityGroups'] ?? $instance['securityGroupList'] ?? []),
                    'create_time' => $createTime,
                    'update_time' => date('Y-m-d H:i:s'),
                    // 处理过期时间格式，将ISO 8601格式转换为MySQL datetime格式
                    'expire_time' => self::convertDatetime($instance['expireTime'] ?? $instance['expiredTime'] ?? null),
                    'hids_installed' => 0,
                ];
            }

            // 批量导入或更新天翼云资源数据
            foreach ($tianyiResources as $resource) {
                // 添加团队标识
                $resource['team_id'] = $teamId;
                
                $existing = Db::table('asm_cloud_tianyi')->where('resource_id', $resource['resource_id'])->find();
                if ($existing) {
                    // 更新现有记录
                    unset($resource['create_time']); // 不更新创建时间
                    Db::table('asm_cloud_tianyi')->where('id', $existing['id'])->update($resource);
                } else {
                    // 添加新记录
                    Db::table('asm_cloud_tianyi')->insertGetId($resource);
                }
            }

            // 导入到主机资产总表
            foreach ($hosts as $host) {
                try {
                    $existing = HostAssetsModel::getByInstanceIdAndPlatform($host['instance_id'], $host['cloud_platform']);
                    if ($existing) {
                        // 更新现有记录
                        unset($host['create_time']); // 不更新创建时间
                        HostAssetsModel::updateByInstanceIdAndPlatform($host['instance_id'], $host['cloud_platform'], $host);
                    } else {
                        // 添加新记录
                        HostAssetsModel::addHostAssets($host);
                    }
                } catch (\Exception $e) {
                    // 打印具体的错误信息
                    $output->writeln("<error>处理主机资产失败: " . $e->getMessage() . "</error>");
                    $output->writeln("<error>主机数据: " . json_encode($host, JSON_UNESCAPED_UNICODE) . "</error>");
                    // 继续处理其他主机
                    continue;
                }
            }
        }

        $output->writeln("<info>天翼云{$teamId}团队主机资产导入数据库完成</info>");

    }

    /**
     * 从天翼云B团队拉取主机资产
     */
    public static function importFromTianyiB(Output $output): void
    {
        self::importFromTianyi($output, 'B');
    }

    /**
     * 转换日期时间格式
     * 将ISO 8601格式转换为MySQL datetime格式
     */
    public static function convertDatetime($datetime)
    {
        if (empty($datetime)) {
            return null;
        }

        // 尝试解析常见的ISO 8601格式
        $formats = [
            'Y-m-d\TH:i:s.u\Z', // 带微秒的UTC格式
            'Y-m-d\TH:i:s\Z',   // 不带微秒的UTC格式
            'Y-m-d\TH:i:s.uP',  // 带微秒的时区偏移格式
            'Y-m-d\TH:i:sP',    // 不带微秒的时区偏移格式
            'Y-m-d H:i:s.u',     // 带微秒的普通格式
            'Y-m-d H:i:s',       // 普通格式
        ];

        foreach ($formats as $format) {
            $dt = \DateTime::createFromFormat($format, $datetime);
            if ($dt !== false) {
                return $dt->format('Y-m-d H:i:s');
            }
        }

        // 最后的尝试：使用strtotime
        $timestamp = strtotime($datetime);
        if ($timestamp === false) {
            return null;
        }

        return date('Y-m-d H:i:s', $timestamp);
    }

    /**
     * 调用天翼云API
     */
    public static function callTianyiApi($ak, $sk, $regionId, $endpoint, $resourcePath, $additionalParams = [])
    {
        // 生成32位的ctyun-eop-request-id（UUID4）
        $requestId = sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );

        // 生成EOP日期
        date_default_timezone_set('Asia/Shanghai');
        $eopDate = date('Ymd\THis\Z');

        // 构建请求体
        $requestBody = array_merge([
            "regionID" => $regionId
        ], $additionalParams);

        // 计算数据的SHA256摘要
        $bodySha256Hex = hash('sha256', json_encode($requestBody, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        // 构造待签名字符串
        $headerStr = "ctyun-eop-request-id:{$requestId}\n" . "eop-date:{$eopDate}\n";
        $encodedQuery = "";
        $signatureBase = "{$headerStr}\n{$encodedQuery}\n{$bodySha256Hex}";

        // 计算动态密钥
        $ktime = hash_hmac('sha256', $eopDate, $sk, true);
        $kAk = hash_hmac('sha256', $ak, $ktime, true);
        $datePart = substr($eopDate, 0, 8); // 提取日期部分
        $kdate = hash_hmac('sha256', $datePart, $kAk, true);

        // 计算签名
        $signature = hash_hmac('sha256', $signatureBase, $kdate, true);
        $signatureB64 = base64_encode($signature);

        // 构造Eop-Authorization
        $eopAuthorization = "{$ak} Headers=ctyun-eop-request-id;eop-date Signature={$signatureB64}";

        // 构建完整URL
        $fullUrl = "https://{$endpoint}{$resourcePath}";

        // 构建请求头
        $headers = [
            "host: {$endpoint}",
            "User-Agent: Mozilla/5.0(QingScan)",
            "Eop-date: {$eopDate}",
            "ctyun-eop-request-id: {$requestId}",
            "Eop-Authorization: {$eopAuthorization}",
            "Content-Type: application/json;charset=UTF-8"
        ];

        // 初始化cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $fullUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        // 生产环境建议开启SSL验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // 执行请求
        $responseBody = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        return [
            'status_code' => $statusCode,
            'body' => $responseBody,
            'error' => $error
        ];
    }


    public static function syncFromQingTengHids(Output $output): void
    {
        $output->writeln("正在从青藤云HIDS API拉取已安装主机列表...");

        // 青藤云HIDS API配置，则从.env文件获取

        $hidsUrl = env('QINGTENG_HIDS.URL', '');
        $token = env('QINGTENG_HIDS.TOKEN', '');
        $timeout = env('QINGTENG_HIDS_TIMEOUT', 30);


        // 验证配置
        if (empty($hidsUrl) || empty($token)) {
            $output->writeln("<error>青藤云HIDS API配置缺失</error>");
            return;
        }

        try {
            // 初始化Guzzle客户端
            $client = new \GuzzleHttp\Client([
                'timeout' => $timeout,
                'verify' => false, // 禁用SSL验证（根据实际情况调整）
            ]);

            // 构建请求参数
            $requestData = [
                'query' => new \stdClass(),
                'size' => 1000, // 一次获取较多数据
                'page' => 0,
                'sort' => []
            ];

            // 发送请求
            $response = $client->post($hidsUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ],
                'json' => $requestData,
            ]);

            // 解析响应
            $responseBody = $response->getBody()->getContents();
            $data = json_decode($responseBody, true);

            if (!isset($data['data']) || !is_array($data['data'])) {
                $output->writeln("<error>青藤云HIDS API响应格式错误</error>");
                return;
            }

            $installedHosts = $data['data'];
            $output->writeln("<info>成功获取青藤云HIDS已安装主机列表，共 " . count($installedHosts) . " 台主机</info>");

            // 1. 首先将所有主机的HIDS状态标记为未安装
            HostAssetsModel::updateAll([
                'hids_installed' => 0
            ]);

            // 2. 然后更新已安装HIDS的主机状态并保存原始数据到asm_hids_qingteng表
            $count = 0;
            foreach ($installedHosts as $host) {
                // 获取主机IP和名称
                $hostname = $host['hostname'] ?? '';
                $ip = $host['ip'] ?? '';
                $version = $host['version'] ?? '';

                // 查找匹配的主机资产
                // 使用闭包来实现OR连接的IP匹配条件
                $where = function ($query) use ($ip, $hostname) {
                    if ($ip) {
                        // IP匹配条件使用OR连接
                        $query->where(function ($q) use ($ip) {
                            $q->where('private_ip', '=', $ip)
                              ->whereOr('public_ip', '=', $ip)
                              ->whereOr('private_ips', 'like', '%"' . $ip . '"%')
                              ->whereOr('public_ips', 'like', '%"' . $ip . '"%');
                        });
                    }
                    
                    if ($hostname) {
                        // 主机名匹配条件
                        $query->where('instance_name', 'like', '%' . $hostname . '%');
                    }
                };
                
                // 直接使用Db查询，因为HostAssetsModel::getHostAssetsList可能不支持闭包条件
                $hostAssets = Db::table('asm_host_assets')
                    ->where($where)
                    ->limit(10)
                    ->select();
                $hostId = null;
                foreach ($hostAssets as $asset) {
                    HostAssetsModel::updateHidsStatus(
                        $asset['id'],
                        1, // 已安装
                        $version,
                        date('Y-m-d H:i:s') // 最后检查时间
                    );
                    $hostId = $asset['id'];
                    $count++;
                }

                // 保存原始数据到asm_hids_qingteng表
                try {
                    $saveData = $host;
                    if ($hostId) {
                        $saveData['host_id'] = $hostId;
                    }
                    HidsQingtengModel::saveOriginalJson($saveData);
                } catch (Throwable $e) {
                    $output->writeln("<error>保存青藤云HIDS数据失败: " . $e->getMessage() . "</error>");
                    // 继续处理其他主机
                    continue;
                }
            }

            $output->writeln("<info>共更新 " . $count . " 台主机的HIDS状态为已安装</info>");

            // 3. 统计未安装HIDS的主机数量
            $notInstalledCount = HostAssetsModel::getHostAssetsCount(['hids_installed' => 0]);
            $output->writeln("<info>当前未安装HIDS的主机数量: " . $notInstalledCount . " 台</info>");

        } catch (Throwable $e) {
            $output->writeln("<error>调用青藤云HIDS API失败: " . $e->getMessage() . "</error>");
            throw $e; // 重新抛出异常，让上层处理
        }
    }
}