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


# TIMESTAMP & UPTIME

TIMESTAMP=$EPOCHSECONDS
UPTIME=$(cat /proc/uptime | awk '{print $1}')


# RAM & SWAP

RAM_USED=$(free -k | awk 'NR==2 {print $3}')
RAM_FREE=$(free -k | awk 'NR==2 {print $4}')

SWAP_USED=$(free -k | awk 'NR==3 {print $3}')
SWAP_FREE=$(free -k | awk 'NR==3 {print $4}')


# LOAD

LOAD_1=$(cat /proc/loadavg | awk '{print $1}')
LOAD_5=$(cat /proc/loadavg | awk '{print $2}')
LOAD_15=$(cat /proc/loadavg | awk '{print $3}')


# CPU_CORES & THREADS

CPU_CORES=$(lscpu | grep "CPU(s)" | awk 'NR==1 {print $2}')
CPU_THREADS=$(nproc) # Threads per Core X Cores per Socket X Sockets


# CPU_USE & CPU_IDLE

CPU_IDLE=$(top -bn1 -E k | grep "Cpu(s)" | awk -F ', ' '{print $4*1}')
CPU_USE=$(echo "100 - $CPU_IDLE" | bc)


# DISK_USED & DISK_FREE

DISK_USED=$(df -BKB / | awk 'NR==2 {print $3*1}')
DISK_FREE=$(df -BKB / | awk 'NR==2 {print $4*1}')


# #################################################################################################################### #

# Print JSON Output
OUTPUT="""{
    \"CONNECTED\": $CONNECTED,

    \"HOSTNAME\": \"$HOSTNAME\",
    \"IP_ADDRESS\": \"$IP_ADDRESS\",
    \"OS\": \"$OS\",

    \"TIMESTAMP\": $TIMESTAMP,
    \"UPTIME\": $UPTIME,

    \"CPU_CORES\": $CPU_CORES,
    \"CPU_THREADS\": $CPU_THREADS,

    \"CPU_USE\": $CPU_USE,
    \"CPU_IDLE\": $CPU_IDLE,

    \"LOAD_1\": $LOAD_1,
    \"LOAD_5\": $LOAD_5,
    \"LOAD_15\": $LOAD_15,

    \"RAM_FREE\": $RAM_FREE,
    \"RAM_USED\": $RAM_USED,

    \"SWAP_FREE\": $SWAP_FREE,
    \"SWAP_USED\": $SWAP_USED,

    \"DISK_FREE\": $DISK_FREE,
    \"DISK_USED\": $DISK_USED
}"""

# echo -e """{
#     \"CONNECTED\": ${green}$CONNECTED${reset},

#     \"HOSTNAME\": \"$HOSTNAME\",
#     \"IP_ADDRESS\": \"$IP_ADDRESS\",
#     \"OS\": \"$OS\",

#     \"TIMESTAMP\": $TIMESTAMP,
#     \"UPTIME\": $UPTIME,

#     \"CPU_CORES\": $CPU_CORES,
#     \"CPU_THREADS\": $CPU_THREADS,

#     \"CPU_USE\": $CPU_USE,
#     \"CPU_IDLE\": $CPU_IDLE,

#     \"LOAD_1\": $LOAD_1,
#     \"LOAD_5\": $LOAD_5,
#     \"LOAD_15\": $LOAD_15,

#     \"RAM_FREE\": $RAM_FREE,
#     \"RAM_USED\": $RAM_USED,

#     \"SWAP_FREE\": $SWAP_FREE,
#     \"SWAP_USED\": $SWAP_USED,

#     \"DISK_FREE\": $DISK_FREE,
#     \"DISK_USED\": $DISK_USED
# }""";

wget --method=post -O- --body-data="$OUTPUT" --header=Content-Type:application/json http://dev.portal.senses.co.uk/api/servers/scrape
