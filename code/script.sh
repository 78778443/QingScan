#!/bin/bash
php think run &
sleep 30
# 死循环执行命令
while true; do
  php think scan create_task -vvv
  php think scan start_task -vvv

done
