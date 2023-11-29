#! /bin/bash

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

RAM_FREE=$(top -bn1 -E k | grep "Mem :" | awk '{print $6}')
RAM_USED=$(top -bn1 -E k | grep "Mem :" | awk '{print $8}')

SWAP_FREE=$(top -bn1 -E k | grep "Swap:" | awk '{print $5}')
SWAP_USED=$(top -bn1 -E k | grep "Swap:" | awk '{print $7}')


# LOAD

LOAD_1=$(cat /proc/loadavg | awk '{print $1}')
LOAD_5=$(cat /proc/loadavg | awk '{print $2}')
LOAD_15=$(cat /proc/loadavg | awk '{print $3}')


# CPU_CORES & THREADS

CPU_CORES=$(lscpu | grep "CPU(s)" | awk 'NR==1 {print $2}')
CPU_THREADS=$(lscpu | grep "Thread(s)" | awk '{print $4}')


# CPU_USE & CPU_IDLE

CPU_IDLE=$(top -bn1 -E k | grep "Cpu(s)" | awk -F ', ' '{print $4*1}')
CPU_USE=$(echo "100 - $CPU_IDLE" | bc)


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
    \"CPU_THREADS\": $CPU_CORES,

    \"CPU_USE\": $CPU_USE,
    \"CPU_IDLE\": $CPU_IDLE,

    \"LOAD_1\": $LOAD_1,
    \"LOAD_5\": $LOAD_5,
    \"LOAD_15\": $LOAD_15,

    \"RAM_FREE\": $RAM_FREE,
    \"RAM_USED\": $RAM_USED,

    \"SWAP_FREE\": $SWAP_FREE,
    \"SWAP_USED\": $SWAP_USED,
}"""
