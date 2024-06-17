#!/bin/bash

# 死循环执行命令
while true; do
  php think scan task_scan -vvv
  php think scan start_task -vvv

done
