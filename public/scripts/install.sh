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

# ##################################################### Functions #################################################### #

asciiTitle() {
    echo -e " ____   ____   __ _   ____   ____   ____     ____    __   ____   ____   __    __     "
    echo -e "/ ___) (  __) (  ( \ / ___) (  __) / ___)   (  _ \  /  \ (  _ \ (_  _) / _\  (  )    "
    echo -e "\___ \  ) _)  /    / \___ \  ) _)  \___ \    ) __/ (  O ) )   /   )(  /    \ / (_/\  "
    echo -e "(____/ (____) \_)__) (____/ (____) (____/   (__)    \__/ (__\_)  (__) \_/\_/ \____/  "

    newline
}

scriptDescription() {
    echo -e '###################################################################################'
    echo -e ''
    echo -e 'This script will install all of the necessary components to allow Senses Portal to'
    echo -e 'function as intended.'
    echo -e ''
    echo -e 'If you have any issues or questions, Please get in contact at \e[1mjack@senses.co.uk\e[0m'
    echo -e ''
    echo -e '###################################################################################'

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

        read -p " Would you like to monitor $volume? (y/n) [n]: " confirmed

        if [ "$confirmed" = "y" ]; then
            echo " Added $volume to monitor script."
        fi
    done

    newline
}

newline() {
    echo -e ""
}

hashline() {
    echo -e '###################################################################################'
}

# download() {
#     # Download a file from a url
#     wget -q $1
# }

download_scraper() {
    # Download the scraping script
    newline
    wget -q -P "$HOME/senses-portal" --show-progress http://dev.portal.senses.co.uk/scripts/scrape.sh && sudo bash "$HOME/senses-portal/scrape.sh"
}

create_key_file() {
    if [ ! -d "$HOME/senses-portal" ]; then
        mkdir "$HOME/senses-portal"
    fi

    echo -e $KEY > "$HOME/senses-portal/api_token"
}

validate_server() {
    wget -q --method POST --body-data="{}" --header="Content-Type: application/json" --header="Authorization: Bearer $KEY" -O- http://dev.portal.senses.co.uk/api/servers/validate &> /dev/null
}

add_to_crontab() {
    # Add the job to the crontab
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
}

check_things() {
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
}

# validateServerModel() {
#     # Checks to see if there is a server model with the API key that you have provided in the install url.
#     # Also check to see if the API can post correctly to the main server by sending a POST that will set a "validated_at" column.
# }


# #################################################### The Script #################################################### #

clear

asciiTitle

scriptDescription

create_key_file
validate_server

check_for_volumes

hashline

download_scraper

hashline
