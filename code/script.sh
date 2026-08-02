#!/bin/bash
# 启动 QingScan：Web 服务 + 常驻扫描调度器
php think run &
sleep 5
# 常驻扫描调度器：自动生成并执行扫描任务
php think schedule
