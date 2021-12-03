'''
思路:
	使用wireshark抓包, 抓取redis服务器发送info命令的payload
	使用Socket模块, 将payload发送到目标主机和端口, 然后对比获取的信息, 是否存在redis版本信息
	对比, 返回信息中若存在redis版本信息, 输出存在redis未授权漏洞, 反之无此漏洞
	判断输入IP是否为某一段地址(如 192.168.0.1-10), 使用str字符串方法, 判断是否存在('-'), 若存在遍历主机地址

'''

import socket
import argparse

# 这是帮助页面那句话
parser = argparse.ArgumentParser(description='Redis未授权访问测试 from  Frieza')

# 三个参数, <命令, 传入类型, 提示, default='默认值'>
parser.add_argument('-u', type=str, help='传入IP')
parser.add_argument('-p', type=str, help='目标端口')

# 对传入参数进行赋值, 没他上边白干
args = parser.parse_args()


# 检测未授权
def redis(ip, port):
    s = socket.socket()
    payload = "\x2a\x31\x0d\x0a\x24\x34\x0d\x0a\x69\x6e\x66\x6f\x0d\x0a"
    socket.setdefaulttimeout(10)
    # print('############################################')
    # print('################## Frieza ##################')
    # print('############################################')
    # print('当前目标: ', ip, ': ', port, '\n')
    try:
        s.connect((ip, int(port)))
        s.sendall(payload.encode())
        a = s.recv(1024).decode()
        if 'redis_version' in a:
            print('存在Redis未授权访问')
    except Exception:
        print('不存在Redis未授权访问')


if __name__ == '__main__':
    redis(args.u, args.p)
