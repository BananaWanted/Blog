## For Machines with Public IP Directly on NIC
```bash
NIC="eth0"
ip -o address list dev $NIC | tr -s ' ' '\\' | cut -d '\' -f 4
```
## For Machines without Public IP on NIC
### Outside China:
```bash
IP=$(curl -sL checkip.dyndns.org | grep -Po '\d+\.\d+\.\d+\.\d+')
```
### China Mainland:
```bash
IP=$(curl -sL https://myip.ipip.net | grep -Po '\d+\.\d+\.\d+\.\d+')
```
Note: `ip.cn` was popular in favor of `myip.ipip.net` but at the time this post written `ip.cn` is not available through curl due to DDOS protection provided by [Cloudflare](cloudflare.com).
<!--stackedit_data:
eyJoaXN0b3J5IjpbMTIwMzc4NDE0N119
-->