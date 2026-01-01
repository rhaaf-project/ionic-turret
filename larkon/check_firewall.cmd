@echo off
REM Check firewall status on 172
echo === Check iptables INPUT ===
ssh root@103.154.80.172 "iptables -L INPUT -n | head -20"
echo.
echo === Check UFW status ===
ssh root@103.154.80.172 "ufw status 2>/dev/null || echo 'ufw not found'"
echo.
echo === Check if 5060 is listening ===
ssh root@103.154.80.172 "ss -ulnp | grep 5060"
echo.
echo === Test tcpdump for incoming SIP ===
ssh root@103.154.80.172 "timeout 2 tcpdump -i any port 5060 -c 5 2>&1 || echo 'No packets or timeout'"
