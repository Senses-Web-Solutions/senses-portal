#! /bin/bash

# The command given to the user will look something like this
wget -q --show-progress http://dev.portal.senses.co.uk/scripts/install.sh && sudo bash install.sh --key=ddf67e8a6661c3a1444b8542f59c98e0



clear

newline() {
    echo -e "\n"
}

newline

echo "  ____   ____   __ _   ____   ____   ____     ____    __   ____   ____   __    __     "
echo " / ___) (  __) (  ( \ / ___) (  __) / ___)   (  _ \  /  \ (  _ \ (_  _) / _\  (  )    "
echo " \___ \  ) _)  /    / \___ \  ) _)  \___ \    ) __/ (  O ) )   /   )(  /    \ / (_/\  "
echo " (____/ (____) \_)__) (____/ (____) (____/   (__)    \__/ (__\_)  (__) \_/\_/ \____/  "

newline

echo -e """ ###################################################################################

 This script will install all of the necessary components to allow Senses Portal to
 function as intended.

 If you have any issues or questions, Please get in contact at \e[1mjack@senses.co.uk\e[0m

 ###################################################################################

"""

check_for_volumes() {
    echo -e " Checking for volumes..."
    echo -e " Found \e[1m2 Volumes\e[0m"

    newline

    echo -e """ Name,Size,Location
 ------------------------,------,------------
 volume_iwjs_iwjs,500GB,/dev/sda
 volume_iwjs_iwjs_2,2TB,/dev/sda4
""" | column -s "," -t

    newline

    for volume in volume_iwjs_iwjs volume_iwjs_iwjs_2; do

        read -p " Would you like to monitor $volume? (y/n) [n]: " confirmed

        if [ "$confirmed" = "y" ]; then
            echo " Added $volume to monitor script."
        fi
    done
}

check_for_volumes

newline


download_scraper() {
    # Download the scraping script
    wget -q http://dev.portal.senses.co.uk/scripts/scrape.sh
}

add_to_crontab() {
    # Add the job to the crontab
    crontab -l | { cat; echo -e "\n# Run Senses Portal Scraper Every Minute (With a 30 second offset)\n# This offset is to account for the spike in CPU usage when minutely schedules are run\n* * * * * ( sleep 30; bash /home/forge/scrape.sh )"; } | crontab -
}
