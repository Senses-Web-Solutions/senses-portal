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

# validate_user() {
#     validation=$(curl -X GET \
#         "$xitoring_url"/validate-key/"$KEY" \
#         -H 'cache-control: no-cache' \
#         -H 'content-type: multipart/form-data;')
#     if (($(echo "$validation" | grep -c "invalid_key") > 0)); then
#         echo -e "\e[41mERROR\e[0m ""Your API key is invalid."
#         exit 1
#     elif (($(echo "$validation" | grep -c "no_access") > 0)); then
#         echo -e "\e[41mERROR\e[0m ""You don't have access to add a server."
#         exit 1
#     elif (($(echo "$validation" | grep -c "reached_maximum") > 0)); then
#         echo -e "\e[41mERROR\e[0m ""You have reached the maximum number of servers you can add."
#         exit 1
#     elif (($(echo "$validation" | grep -c "unconfirmed_email") > 0)); then
#         echo -e "\e[41mERROR\e[0m ""You have to confirm your accounts Email address first."
#         exit 1
#     else
#         return 0
#     fi
# }

# #################################################### The Script #################################################### #

clear

asciiTitle

hashline

scriptDescription

hashline

if [ -d "$HOME/senses-portal" ]; then
    echo -e "Removing previous installation of Senses Portal..."
    rm -r "$HOME/senses-portal"
fi

if [ ! -d "$HOME/senses-portal" ]; then
    echo -e "Creating new installation of Senses Portal..."
    mkdir "$HOME/senses-portal"
fi

echo -e "Creating API Token file..."
echo -e $KEY > "$HOME/senses-portal/.api_token"

echo -e "Validating API Token..."
wget -q --method POST --body-data='{}' --header="Content-Type: application/json" --header="Authorization: Bearer $KEY" -O- http://dev.portal.senses.co.uk/api/servers/validate &> /dev/null

hashline

check_for_volumes

hashline

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

hashline

echo -e "Removing installer script..."
rm install.sh

newline

echo -e "Installation Complete, Thank you for using Senses Portal."

newline
