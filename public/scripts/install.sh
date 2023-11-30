#! /bin/bash

# ############################################## Inititialise Variables ############################################## #

for i in "$@"; do
    case $i in
        -k=* | --key=*)
            KEY="${i#*=}"
            shift
            ;;
        *=INVALID_ARG)
            echo -e "\e[41mERROR\e[0m ""Invalid argument $INVALID_ARG"
            exit 1
            ;;
    esac
done

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

# ##################################################### Functions #################################################### #

newline() {
    echo -e ""
}

hashline() {
    newline
    echo -e '###################################################################################'
    newline
}

asciiTitle() {
    echo " ____   ____   __ _   ____   ____   ____     ____    __   ____   ____   __    __     "
    echo "/ ___) (  __) (  ( \ / ___) (  __) / ___)   (  _ \  /  \ (  _ \ (_  _) / _\  (  )    "
    echo "\___ \  ) _)  /    / \___ \  ) _)  \___ \    ) __/ (  O ) )   /   )(  /    \ / (_/\  "
    echo "(____/ (____) \_)__) (____/ (____) (____/   (__)    \__/ (__\_)  (__) \_/\_/ \____/  "
    echo "                                                                                     "
}

scriptDescription() {
    echo -e "This script will install all of the necessary components to allow Senses Portal to "
    echo -e "function as intended.                                                              "
    echo -e "                                                                                   "
    echo -e "If you have any issues or questions, Please get in contact at \e[1mjack@senses.co.uk\e[0m"
}

check_for_volumes() {
    echo -e "Checking for volumes in /mnt..."

    VOLUME_COUNT=$(ls /mnt | wc -l)

    if [ VOLUME_COUNT > 0 ] then
        # Server has Volumes
        echo -e "Found \e[1m$VOLUME_COUNT Volumes\e[0m"

        newline

        DF=$(df -h | grep /mnt | awk '{print $6, $2, $3, $4, $5, $1}')

        echo -e "Name Size Used Avail Use% Filesystem\n-------------------------------- ------ ------ ------ ------ ----------------\n$DF" | column -t

        for volume in $(ls /mnt); do
            newline
            read -p "Would you like to monitor $volume? (y/n) [n]: " confirmed

            if [ "$confirmed" = "y" ]; then
                echo "Added $volume to monitor script."
            fi
        done
    else
        # Server does not have Volumes
        echo -e "Found \e[1m$VOLUME_COUNT Volumes\e[0m"
    fi
}

# check_things() {
#     # # Check if bc command is installed and if not then install it
#     # if command -v bc > /dev/null 2>&1; then
#     #     echo "bc is installed."
#     # else
#     #     sudo apt install bc
#     # fi

#     # # Check if iostat command is installed and if not then install it
#     # if command -v iostat > /dev/null 2>&1; then
#     #     echo "iostat is installed."
#     # else
#     #     sudo apt install sysstat
#     # fi
# }

validate_token() {
    validation=$(wget -q --method POST --body-data='{}' --header="Content-Type: application/json" --header="Authorization: Bearer $KEY" -O- http://dev.portal.senses.co.uk/api/servers/validate)
    if (($(echo "$validation" | grep -c "<head>") > 0)); then
        echo -e "\e[41mERROR\e[0m" "Unable to validate your API Token"
        newline
        exit 1
    else
        echo -e "\e[42mSUCCESS\e[0m" "API Token was successfully validated"
        newline
    fi
}

# #################################################### The Script #################################################### #

clear

asciiTitle

hashline

scriptDescription

hashline

# Check if the user is the root user
if [ "$(whoami)" == root ]; then
    return 0
else
    echo -e "\e[41mERROR\e[0m ""You are not logged in as root, please switch to root user and retry."
    newline
    exit 0
fi

echo -e "Validating API Token..."
validate_token

if [ -d "/root/senses-portal" ]; then
    echo -e "Removing previous installation of Senses Portal..."
    rm -r "/root/senses-portal"
fi

if [ ! -d "/root/senses-portal" ]; then
    echo -e "Creating new installation of Senses Portal..."
    mkdir "/root/senses-portal"
fi

echo -e "Storing API Token..."
echo -e $KEY > "/root/senses-portal/.api_token"

hashline

check_for_volumes

hashline

echo -e "Installing data scraping script..."
wget -q -P "/root/senses-portal" http://dev.portal.senses.co.uk/scripts/scrape.sh && sudo bash /root/senses-portal/scrape.sh

echo -e "Adding script to Crontab..."
sudo crontab -l | {
    cat;
    printf '%s\n' \
        '' \
        '# Run Senses Portal Scraper Every Minute (With a 30 second offset)' \
        '# This offset is to account for the spike in CPU usage when minutely schedules are run' \
        '' \
        '* * * * * ( sleep 30; sudo bash /root/senses-portal/scrape.sh )'\
        '';
} | sudo crontab -

hashline

echo -e "Removing installer script..."
sudo rm /root/install.sh

newline

echo -e "Installation Complete, Thank you for using Senses Portal."

newline
