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
IP_ADDRESS=$(curl ifconfig.me/ip)
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
    \"swap_used\": $SWAP_USED,

    \"disk_total\": $DISK_TOTAL,
    \"disk_free\": $DISK_FREE,
    \"disk_used\": $DISK_USED,
    \"disk_read\": $DISK_READ,
    \"disk_write\": $DISK_WRITE
}"""


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

wget -q --method=post -O- --body-data="$OUTPUT" --header="Content-Type: application/json" http://dev.portal.senses.co.uk/api/v2/server-metrics --header="Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiMTM1NGRiNGVjNDU4NzVhMTAwNWZiNzJhNGFlZDM5NDZiODZjMTg2NDhiZmM4ODBmNDAzZjNjMjAxN2I5ZWUxMTI0ZTI2YjM1OWYzMGNjNWIiLCJpYXQiOjE2OTg2ODA2MDguMTQ1NjYxLCJuYmYiOjE2OTg2ODA2MDguMTQ1NjY1LCJleHAiOjE3MzAzMDMwMDguMTQwMjc2LCJzdWIiOiI4Iiwic2NvcGVzIjpbXX0.HMX8shZPKWFMu525Cw09YHeyMHPich-MDozg3weDKTgXkNzV7mfpdG2uJlXJqINtowoF6m3Ff8ye7goff318dthq7PDW7MztcayNbBAIkKQ2ED-u82G1VOPyDDV9B35pvm9geFbWwu9vQZ5Vp9zukq0XQFvnfV9T9YBiTxBkjf90N42mmCH7B0K9G4s6Qg1cr7l3KhkmWva_jvxJDedDn4s2h_4GZG22AFsjyMFYyJqmLmjVjaJN-0c9S5SSeRDwO6DgsC3-k-nDcwYo7EktRdQ4dMdiZzIEuge8WT3D0b9YiNHLjQLC8oa8FHQ-T0WeXblqBvUU_qfPvJcFHPz7Wny_XO-GQUIh8pOkfW5zV7gITMSOopE4KYvl3t2b6qH2Zr-2pw4MK16RSwHp10G9uS3P9RolIueI27hbrR7Pe7Wko_24HTqXshjwtpEwrcht9cbuHZXYPwCiXJhJTIz0cQRvgyey_sGaMZsikQAikONCMzh8dlnk6c87RrL7b5kI7dUw83ozDeATO5yffNFiqg8kw3gkkimRnt7hMuWmVJn8Y6qAkjIWxXcOcXZoL5ogRsAk6el9FVRYjTUneT5I5XRhkALZOjP-GLf6e1PZgsshuZwDWlzhk7P5pIHn8JXL3MWYn8sAQeC9DBh1OIYyn53ejxXP5YqB-csGC6yYgr0"
