#!/bin/bash

# 死循环执行命令
while true; do
  php think scan xray -vvv
  php think scan awvs -vvv
  php think scan rad -vvv
  php think scan host -vvv
  php think scan port -vvv
  php think scan nmap -vvv
  php think scan subdomain -vvv
  php think scan cve -vvv
  php think scan google -vvv
  php think scan jietu -vvv
  php think scan upadteRegion -vvv
  php think scan whatweb -vvv
  php think scan subdomainScan -vvv
  php think scan hydra -vvv
  php think scan sqlmapScan -vvv
  php think scan fofa -vvv
  php think scan dirmapScan -vvv
  php think scan getNotice -vvv
  php think scan reptile -vvv
  php think scan backup -vvv
  php think scan whatwebPocTest -vvv
  php think scan wafw00fScan -vvv
  php think scan nucleiScan -vvv
  php think scan vulmapPocTest -vvv
  php think scan crawlergoScan -vvv
  php think scan dismapScan -vvv
  php think scan fortify -vvv
  php think scan semgrep -vvv
  php think scan getProjectComposer -vvv
  php think scan code_python -vvv
  php think scan code_java -vvv
  php think scan code_webshell_scan -vvv
  php think scan mobsfscan -vvv
  php think scan murphysecScan -vvv
  php think scan unauthorizeScan -vvv
  php think scan deleteDir -vvv
    php think scan domainFindIp -vvv
    php think scan scanWebPort -vvv
    php think scan domainFindUrl -vvv
    php think scan ip_location -vvv
    php think scan finger -vvv
    php think scan semgrep -vvv

    echo '休息15秒'
    sleep 15;
done
