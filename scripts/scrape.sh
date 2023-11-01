#! /bin/bash

# Ansi color code variables
red="\e[0;91m"
blue="\e[0;94m"
expand_bg="\e[K"
blue_bg="\e[0;104m${expand_bg}"
red_bg="\e[0;101m${expand_bg}"
green_bg="\e[0;102m${expand_bg}"
green="\e[0;92m"
white="\e[0;97m"
bold="\e[1m"
uline="\e[4m"
reset="\e[0m"


# CONNECTED

CONNECTED=$(ping -c 1 google.com &> /dev/null && echo "true" || echo "false")


# HOSTNAME, IP_ADDRESS & OS

HOSTNAME=$(hostname)
IP_ADDRESS=$(hostname -I | awk '{print $1}')
OS=$(uname -o)
DISTRO=$(source /etc/os-release | echo $ID)
DISTRO_VERSION=$(source /etc/os-release | echo $VERSION_ID)
ARCHITECTURE=$(uname -p)
KERNEL=$(uname -s)
KERNEL_VERSION=$(uname -r)


# TIMESTAMP & UPTIME

TIMESTAMP=$EPOCHSECONDS
UPTIME=$(cat /proc/uptime | awk '{print $1}')


# CPU_CORES & THREADS

CPU_CORES=$(lscpu | grep "CPU(s)" | awk 'NR==1 {print $2}')
CPU_THREADS=$(nproc) # Threads per Core X Cores per Socket X Sockets


# CPU_USE & CPU_IDLE

CPU_US=$(top -bn1 -E k | grep "Cpu(s)" | awk '{print $2}')
CPU_SY=$(top -bn1 -E k | grep "Cpu(s)" | awk '{print $4}')
CPU_NI=$(top -bn1 -E k | grep "Cpu(s)" | awk '{print $6}')
CPU_ID=$(top -bn1 -E k | grep "Cpu(s)" | awk '{print $8}')
CPU_WA=$(top -bn1 -E k | grep "Cpu(s)" | awk '{print $10}')
CPU_HI=$(top -bn1 -E k | grep "Cpu(s)" | awk '{print $12}')
CPU_SI=$(top -bn1 -E k | grep "Cpu(s)" | awk '{print $14}')
CPU_ST=$(top -bn1 -E k | grep "Cpu(s)" | awk '{print $16}')

CPU_USE=$(echo "100 - $CPU_ID" | bc)


# LOAD

LOAD_1=$(cat /proc/loadavg | awk '{print $1}')
LOAD_5=$(cat /proc/loadavg | awk '{print $2}')
LOAD_15=$(cat /proc/loadavg | awk '{print $3}')


# RAM

RAM_USED=$(free -k | awk 'NR==2 {print $3}')
RAM_FREE=$(free -k | awk 'NR==2 {print $4}')

RAM_TOTAL=$(cat /proc/meminfo | grep MemTotal | awk '{print $2}')
RAM_FREE=$(cat /proc/meminfo | grep MemFree | awk '{print $2}')
RAM_BUFFER=$(cat /proc/meminfo | grep Buffers | awk '{print $2}')
RAM_CACHE=$(cat /proc/meminfo | grep -we Cached -e SReclaimable | awk '{sum+=$2;}END{print sum;}')
RAM_USED=$(echo "$RAM_TOTAL - $RAM_FREE - $RAM_BUFFER - $RAM_CACHE" | bc)


# SWAP

SWAP_TOTAL=$(cat /proc/meminfo | grep SwapTotal | awk '{print $2}')
SWAP_FREE=$(cat /proc/meminfo | grep SwapFree | awk '{print $2}')
SWAP_CACHE=$(cat /proc/meminfo | grep SwapCached | awk '{print $2}')
SWAP_USED=$(echo "$SWAP_TOTAL - $SWAP_FREE" | bc)


# DISK_USED & DISK_FREE

DISK_TOTAL=$(df / | awk 'NR==2 {print $2*1}')
DISK_USED=$(df / | awk 'NR==2 {print $3*1}')
DISK_FREE=$(df / | awk 'NR==2 {print $4*1}')
DISK_READ=$(iostat | grep -w vda | awk '{print $6}')
DISK_WRITE=$(iostat | grep -w vda | awk '{print $7}')


# FOR THE INSTALLER

# for i in $(ls /mnt); do
#   echo $i
# done

# This will find all of the volumes that are mounted in /mnt and can do something based on that.



# #################################################################################################################### #

# Print JSON Output
OUTPUT="""{
    \"connected\": $CONNECTED,

    \"hostname\": \"$HOSTNAME\",
    \"ip_address\": \"$IP_ADDRESS\",
    \"os\": \"$OS\",
    \"distro\": \"$DISTRO\",
    \"distro_version\": \"$DISTRO_VERSION\",
    \"architecture\": \"$ARCHITECTURE\",
    \"kernel\": \"$KERNEL\",
    \"kernel_version\": \"$KERNEL_VERSION\",

    \"timestamp\": $TIMESTAMP,
    \"uptime\": $UPTIME,

    \"cpu_cores\": $CPU_CORES,
    \"cpu_threads\": $CPU_THREADS,

    \"cpu_use\": $CPU_USE,

    \"cpu_us\": $CPU_US,
    \"cpu_sy\": $CPU_SY,
    \"cpu_ni\": $CPU_NI,
    \"cpu_id\": $CPU_ID,
    \"cpu_wa\": $CPU_WA,
    \"cpu_hi\": $CPU_HI,
    \"cpu_si\": $CPU_SI,
    \"cpu_st\": $CPU_ST,

    \"load_1\": $LOAD_1,
    \"load_5\": $LOAD_5,
    \"load_15\": $LOAD_15,

    \"ram_total\": $RAM_TOTAL,
    \"ram_free\": $RAM_FREE,
    \"ram_buffer\": $RAM_BUFFER,
    \"ram_cache\": $RAM_CACHE,
    \"ram_used\": $RAM_USED,

    \"swap_total\": $SWAP_TOTAL,
    \"swap_free\": $SWAP_FREE,
    \"swap_cache\": $SWAP_CACHE,
    \"swap_used\": $SWAP_USED,

    \"disk_total\": $DISK_TOTAL,
    \"disk_free\": $DISK_FREE,
    \"disk_used\": $DISK_USED,
    \"disk_read\": $DISK_READ,
    \"disk_write\": $DISK_WRITE
}"""


# {
#     "connected": true,

#     "hostname": "Teamleaf8-Dev", // uname -n
#     "ip_address": "165.227.225.119", // curl ifconfig.me/ip
#     "os": "GNU/Linux", // uname -o
#     "distro": "Ubuntu", // cat /etc/os-release (Name)
#     "distro_version": "20.04", // cat /etc/os-release (Version ID)
#     "architecture": "x86_64", // uname -p
#     "kernel": "Linux", // uname -s
#     "kernel_version": "5.4.0-148-generic", // uname -r

#     "timestamp": 1698682711,
#     "uptime": 15581366.81,

#     "cpu_cores": 4,
#     "cpu_threads": 4,

#     "cpu_use": 100, // Non Idle

#     "cpu_us": 0,
#     "cpu_sy": 0,
#     "cpu_ni": 0,
#     "cpu_id": 0,
#     "cpu_wa": 0,
#     "cpu_hi": 0,
#     "cpu_si": 0,
#     "cpu_st": 0,

#     "load_1": 0.01,
#     "load_5": 0.03,
#     "load_15": 0.05,

#     "ram_total": 24348189, // MemTotal from /proc/meminfo
#     "ram_free": 867880, // MemFree from /proc/meminfo
#     "ram_buffer": 503456, // Buffers from /proc/meminfo
#     "ram_cache": 503456, // Cached & SReclaimable from /proc/meminfo
#     "ram_used": 2255308, // ram_total - ram_free - ram_buffer - ram_cache

#     "swap_total": 24348189,
#     "swap_free": 4984572,
#     "swap_used": 258304,

#     "disk_total": 24348189, // Will default to whichever drive is mounted on "/"
#     "disk_free": 1456088,
#     "disk_used": 24348189,

#     "disk_read": 130416778, // Can use the difference of this / 60 to get average Kb/s
#     "disk_write": 991372114,

#     "volumes": {
#         "sda": {
#             "disk_free": 1456088,
#             "disk_used": 24348189,
#             "disk_total": 24348189,

#             "disk_read": 435987345, // From iostat
#             "disk_write": 34586345,
#         },
#         "sda1": {
#             "disk_free": 1456088,
#             "disk_used": 24348189,
#             "disk_total": 24348189,

#             "disk_read": 435987345,
#             "disk_write": 34586345,
#         },
#     },
# }

# # Check if bc command is installed and if not then install it
# if command -v bc > /dev/null 2>&1; then
#     echo "bc is installed."
# else
#     sudo apt install bc
# fi

# # Check if iostat command is installed and if not then install it
# if command -v iostat > /dev/null 2>&1; then
#     echo "iostat is installed."
# else
#     sudo apt install sysstat
# fi

wget -q --method=post -O- --body-data="$OUTPUT" --header="Content-Type: application/json" http://dev.portal.senses.co.uk/api/v2/server-metrics --header="Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiOTI5Njg3MjVkYWNjY2QwODZlN2YyNTQ1OGY3ZjkyMDQ2MDdlOWU4NjFiYTRlYWM0OGYzYWM4YmQyOTE2YmY1OTJjZGVmNGQ3OWMyMjZkZGMiLCJpYXQiOjE2OTg4MzkzNzMuOTg1NTIzLCJuYmYiOjE2OTg4MzkzNzMuOTg1NTI2LCJleHAiOjE3MzA0NjE3NzMuOTgxMDk2LCJzdWIiOiI4Iiwic2NvcGVzIjpbXX0.acmePiCPZUH0k8hNlIxnoJG3Mp8CfIlsMe4lGauP7MIM7aDocJ90vKPnU2Womqit3ZV02z_h_7UA8W2UopcuQqbZjktpD5XiREpFThTIeFnEEENVtw74CJb3VZOisxhnrg0pCXcklan1neaO5uOqgRr5rsqlXK_T25ixNXAI-Q7qIjUA-PkkvQBRM5YwnzWUVb0cMccrH97t9m1VvoFOpGEhcZ6lWw4XNT-sXhex0id0snhROXJiKx4vump_ImAfoczXMbJbutiS9KKofYmtkMI4GcUFPjB0wButHemNSm1eNqZ5UxP-xl7beVyJrB7FrKmJFlSgR-_PThalnLei9FYNVouLVUwbDmNs1ttv0lIhJWsSrLvk0XQOGOzV74mgWzN5IbxlpNgBm0tnKSiBpmopHCQDHqc2sduGGcvaYCDDiKJczJ0_vQ-wj2YXl64kh4GG_Ix78Zj7BtzUZzxUbSpmxGiwNf92HBQKjbDgQ-tRrRoQf0cffZ5aotEZTF2AobrUCA4gH-g3MDNDojUdN-TEC4QkKYkORNSPmNRANiLVOYa5WDRO4SXaEiAF-X2ByumXRPR6lt7OMvRTkHoQpAWbyKeeZBSG9kmXOQ4NcTMSjMl5KCvO2IBKRVjWCVUpUGwtSAhYXeAFWZOAmhjran4J0cD0rbPdYisxNA-HvAw"
