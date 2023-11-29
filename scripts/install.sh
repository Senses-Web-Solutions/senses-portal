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
    echo -e '###################################################################################'
}

asciiTitle() {
    echo -e " ____   ____   __ _   ____   ____   ____     ____    __   ____   ____   __    __     "
    echo -e "/ ___) (  __) (  ( \ / ___) (  __) / ___)   (  _ \  /  \ (  _ \ (_  _) / _\  (  )    "
    echo -e "\___ \  ) _)  /    / \___ \  ) _)  \___ \    ) __/ (  O ) )   /   )(  /    \ / (_/\  "
    echo -e "(____/ (____) \_)__) (____/ (____) (____/   (__)    \__/ (__\_)  (__) \_/\_/ \____/  "

    newline
}

scriptDescription() {
    hashline
    newline
    echo -e 'This script will install all of the necessary components to allow Senses Portal to'
    echo -e 'function as intended.'
    newline
    echo -e 'If you have any issues or questions, Please get in contact at \e[1mjack@senses.co.uk\e[0m'
    newline
    hashline
    newline
}

check_for_volumes() {
    echo -e "Checking for volumes..."
    echo -e "Found \e[1m2 Volumes\e[0m"

    newline

    printf '%s\n' \
        'Name,Size,Location' \
        '------------------------,------,------------' \
        'volume_iwjs_iwjs,500GB,/dev/sda' \
        'volume_iwjs_iwjs_2,2TB,/dev/sda4' | column -s "," -t

    for volume in volume_iwjs_iwjs volume_iwjs_iwjs_2; do
        newline

        read -p "Would you like to monitor $volume? (y/n) [n]: " confirmed

        if [ "$confirmed" = "y" ]; then
            echo "Added $volume to monitor script."
        fi
    done

    newline
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

# #################################################### The Script #################################################### #

if [ -d "$HOME/senses-portal" ]; then
    rm -r "$HOME/senses-portal"
fi

if [ ! -d "$HOME/senses-portal" ]; then
    mkdir "$HOME/senses-portal"
fi

clear

asciiTitle

scriptDescription


echo -e "Creating API Token file..."
echo -e $KEY > "$HOME/senses-portal/.api_token"

echo -e "Validating API Token..."
wget -q --method POST --body-data='{}' --header="Content-Type: application/json" --header="Authorization: Bearer $KEY" -O- http://dev.portal.senses.co.uk/api/servers/validate &> /dev/null


hashline


check_for_volumes


hashline
newline

echo -e "Installing data scraping script..."
wget -q -P "$HOME/senses-portal" http://dev.portal.senses.co.uk/scripts/scrape.sh && bash "$HOME/senses-portal/scrape.sh"

echo -e "Adding script to Crontab..."
crontab -l | {
    cat;
    printf '%s\n' \
        '' \
        '# Run Senses Portal Scraper Every Minute (With a 30 second offset)' \
        '# This offset is to account for the spike in CPU usage when minutely schedules are run' \
        '' \
        '* * * * * ( sleep 30; bash $HOME/senses-portal/scrape.sh )'\
        '';
} | crontab -

echo -e "Removing installer script..."
rm install.sh

newline
hashline
newline

echo -e "Installation Complete, Thank you for using Senses Portal."

newline
