#! /bin/bash


# Download the scraper script
wget -q http://dev.portal.senses.co.uk/scripts/scrape.sh


# Add the job to the crontab
crontab -l | { cat; echo -e "\n# Run Senses Server Scraper every minute (With a 30 second offset)\n* * * * * ( sleep 30; bash /home/forge/scrape.sh )"; } | crontab -
