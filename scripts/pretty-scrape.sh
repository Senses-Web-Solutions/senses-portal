#! /bin/bash

# KPMs
#
#* RAM_USED
#* RAM_FREE
#* SWAP_USED
#* SWAP_FREE
#
# DISK_USED
# DISK_FREE
#
#* CPU_CORES
#* CPU_THREADS
#
#* LOAD_1
#* LOAD_5
#* LOAD_15
#
#* UPTIME
# TASKS
#
#* CONNECTED
#* HOSTNAME
#* IP_ADDRESS
#* OS
#* TIMESTAMP



# top - 12:33:34 up 170 days,  4:24,  2 users,  load average: 0.18, 0.25, 0.28
# Tasks: 258 total,   1 running, 256 sleeping,   0 stopped,   1 zombie
# %Cpu(s):  1.5 us,  2.9 sy,  0.0 ni, 95.6 id,  0.0 wa,  0.0 hi,  0.0 si,  0.0 st
# KiB Mem :  8148192 total,   921368 free,  2407612 used,  4819212 buff/cache
# KiB Swap:  5242876 total,  4984316 free,   258560 used.  5130848 avail Mem


# COMMANDS

# Basic Calculator

# bc [ -hlwsqv ] [long-options] [ file ... ]



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
echo """{
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
    \"DISK_USED\": $DISK_USED,
}"""
