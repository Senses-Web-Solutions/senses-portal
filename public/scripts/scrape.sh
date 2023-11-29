#! /bin/bash

KEY=$(<"$HOME/senses-portal/api_token")

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
DISTRO=$(source /etc/os-release; echo $ID)
DISTRO_VERSION=$(source /etc/os-release; echo $VERSION_ID)
ARCHITECTURE=$(uname -p)
KERNEL=$(uname -s)
KERNEL_VERSION=$(uname -r)


# TIMESTAMP & UPTIME

TIMESTAMP=$EPOCHSECONDS
UPTIME=$(cat /proc/uptime | awk -F "." '{print $1}')


# CPU_CORES & THREADS

CPU_CORES=$(lscpu | grep "CPU(s)" | awk 'NR==1 {print $2}')
CPU_THREADS=$(nproc) # Threads per Core X Cores per Socket X Sockets


# CPU_USE & CPU_IDLE

CPU_METRICS=$(top -bn1 -E k | grep "Cpu(s)" | awk -F ":" '{print $2}')

CPU_US=$(echo "$CPU_METRICS" | awk -F "," '{print $1*1}')
CPU_SY=$(echo "$CPU_METRICS" | awk -F "," '{print $2*1}')
CPU_NI=$(echo "$CPU_METRICS" | awk -F "," '{print $3*1}')
CPU_ID=$(echo "$CPU_METRICS" | awk -F "," '{print $4*1}')
CPU_WA=$(echo "$CPU_METRICS" | awk -F "," '{print $5*1}')
CPU_HI=$(echo "$CPU_METRICS" | awk -F "," '{print $6*1}')
CPU_SI=$(echo "$CPU_METRICS" | awk -F "," '{print $7*1}')
CPU_ST=$(echo "$CPU_METRICS" | awk -F "," '{print $8*1}')

CPU_USE=$(echo "100 - $CPU_ID" | bc)


# LOAD

LOADAVG=$(cat /proc/loadavg)

LOAD_1=$(echo "$LOADAVG" | awk '{print $1}')
LOAD_5=$(echo "$LOADAVG" | awk '{print $2}')
LOAD_15=$(echo "$LOADAVG" | awk '{print $3}')


# RAM

MEMINFO=$(cat /proc/meminfo)


RAM_CACHED=$(echo "$MEMINFO" | grep -w Cached | awk '{print $2}')
RAM_RECLAIM=$(echo "$MEMINFO" | grep SReclaimable | awk '{print $2}')
RAM_CACHE=$(echo "$RAM_CACHED + $RAM_RECLAIM" | bc)

RAM_TOTAL=$(echo "$MEMINFO" | grep MemTotal | awk '{print $2}')
RAM_FREE=$(echo "$MEMINFO" | grep MemFree | awk '{print $2}')
RAM_BUFFER=$(echo "$MEMINFO" | grep Buffers | awk '{print $2}')
RAM_USED=$(echo "$RAM_TOTAL - $RAM_FREE - $RAM_BUFFER - $RAM_CACHE" | bc)

# SWAP

SWAP_TOTAL=$(echo "$MEMINFO" | grep SwapTotal | awk '{print $2}')
SWAP_FREE=$(echo "$MEMINFO" | grep SwapFree | awk '{print $2}')
SWAP_CACHE=$(echo "$MEMINFO" | grep SwapCached | awk '{print $2}')
SWAP_USED=$(echo "$SWAP_TOTAL - $SWAP_FREE" | bc)

# DISK_USED & DISK_FREE

DISK_TOTAL=$(df / | awk 'NR==2 {print $2*1}')
DISK_USED=$(df / | awk 'NR==2 {print $3*1}')
DISK_FREE=$(df / | awk 'NR==2 {print $4*1}')
DISK_READ=$(iostat | grep -w vda | awk '{print $6}')
DISK_WRITE=$(iostat | grep -w vda | awk '{print $7}')


# #################################################################################################################### #

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

wget -q --method POST --body-data="$OUTPUT" --header="Content-Type: application/json" --header="Authorization: Bearer $KEY" -O- http://dev.portal.senses.co.uk/api/server-metrics &> /dev/null
