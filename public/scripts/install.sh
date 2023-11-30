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


black="\e[30m"
red="\e[31m"
green="\e[32m"
yellow="\e[33m"
blue="\e[34m"
purple="\e[35m"
cyan="\e[36m"
white="\e[37m"
grey="\e[39m"

black_bg="\e[40m"
red_bg="\e[41m"
green_bg="\e[42m"
yellow_bg="\e[43m"
blue_bg="\e[44m"
purple_bg="\e[45m"
cyan_bg="\e[46m"
white_bg="\e[47m"

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
    echo -e "$purple ____   ____   __ _   ____   ____   ____     ____    __   ____   ____   __    __    "
    echo -e "/ ___) (  __) (  ( \ / ___) (  __) / ___)   (  _ \  /  \ (  _ \ (_  _) / _\  (  )   "
    echo -e "\___ \  ) _)  /    / \___ \  ) _)  \___ \    ) __/ (  O ) )   /   )(  /    \ / (_/\ "
    echo -e "(____/ (____) \_)__) (____/ (____) (____/   (__)    \__/ (__\_)  (__) \_/\_/ \____/ "
    echo -e "                                                                                     $reset"
}

scriptDescription() {
    echo -e "This script will install all of the necessary components to allow Senses Portal to "
    echo -e "function as intended.                                                              "
    echo -e "                                                                                   "
    echo -e "If you have any issues or questions, Please get in contact at $boldjack@senses.co.uk$reset"
}

check_for_volumes() {
    echo -e "Checking for volumes in /mnt..."

    VOLUME_COUNT=$(ls /mnt | wc -l)

    if [ "$VOLUME_COUNT" -gt 0 ]; then
        # Server has Volumes
        echo -e "Found $bold$VOLUME_COUNT Volumes$reset"

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
        echo -e "Found $bold$VOLUME_COUNT Volumes$reset"
        return 0
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
        echo -e "$red_bg ERROR $reset" "Unable to validate your API Token"
        newline
        exit 1
    else
        echo -e "$green_bg SUCCESS $reset" "API Token was successfully validated"
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
if [ ! "$(whoami)" == root ]; then
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
if (($(crontab -l | grep -c "Senses Portal") > 0)); then
    echo -e "$blue_bg INFO $reset" "Senses Portal Scraper is already added to the crontab."
else
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
fi

hashline

echo -e "Removing installer script..."
sudo rm /root/install.sh

newline

echo -e "Installation Complete, Thank you for using Senses Portal."

newline
