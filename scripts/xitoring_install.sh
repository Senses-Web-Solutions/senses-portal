#!/bin/bash

### Default Vars

for i in "$@"; do
    case $i in
    -k=* | --key=*)
        KEY="${i#*=}"
        shift
        ;;
    -c=* | --region=*)
        region="--region=${i#*=}"
        shift
        ;;
    -h | --help)
        echo "Xitogent Installer Arguments"
        echo ""
        echo "--help                            See the manual."
        echo "--key                             Enter your Key e.g. --key={KEY}"
        echo "--region                          Enter region id. (int)"
        echo "--ping                            will create the Ping Check for the server."
        echo "--http                            will create the HTTP(s) Check for the server."
        echo "--ftp                             will create the FTP Check for the server."
        echo "--dns                             will create the DNS Check for the server."
        echo "--imap                            will create the IMAP Check for the server."
        echo "--pop3                            will create the POP3 Check for the server."
        echo "--smtp                            will create the SMTP Check for the server."
        echo "--group                           Specify the Group which you want to add your server to.(string)"
        echo "--subgroup                        Specify the Subgroup which you want to add your server to.(string)"
        echo "--notification                    Specify the Notification role you want to assign to your triggers.(string)"
        echo "--auto_discovery                  Enable Auto Discovery Service."
        echo "--auto_update                     Enable Xitogent Auto Update."
        echo "--auto_trigger                    Enable Automatic creating of recommended triggers."
        shift
        exit 0
        ;;
    -g=* | --group=*)
        GROUP="${i#*=}"
        ;;
    -s=* | --subgroup=*)
        SUBGROUP="${i#*=}"
        ;;
    --auto_discovery)
        auto_discovery="--auto_discovery"
        ;;
    --notification=*)
        NOTIFICATION="${i#*=}"
        ;;
    --ping)
        module_ping="--module_ping"
        ;;
    --http)
        module_http="--module_http"
        ;;
    --ftp)
        module_ftp="--module_ftp"
        ;;
    --dns)
        module_dns="--module_dns"
        ;;
    --imap)
        module_imap="--module_imap"
        ;;
    --pop3)
        module_pop3="--module_pop3"
        ;;
    --smtp)
        module_smtp="--module_smtp"
        ;;
    --auto_update)
        auto_update="--auto_update"
        ;;
    --auto_trigger)
        auto_trigger="--auto_trigger"
        ;;
    --heartbeat)
        heartbeat="--heartbeat"
        ;;
    --ipv4)
        ipv4="--ipv4"
        ;;
    --ipv6)
        ipv6="--ipv6"
        ;;
    *=INVALID_ARG)
        echo -e "\e[41mERROR\e[0m ""Invalid argument $INVALID_ARG"
        exit 1
        ;;
    esac
done

xitoring_url=https://app.xitoring.com

is_root() {
    if [ "$(whoami)" == root ]; then
        return 0
    else
        echo -e "\e[41mERROR\e[0m ""You are not logged in as root, please switch to root user and retry."
        exit 0
    fi
}

validate_user() {
    validation=$(curl -X GET \
        "$xitoring_url"/validate-key/"$KEY" \
        -H 'cache-control: no-cache' \
        -H 'content-type: multipart/form-data;')
    if (($(echo "$validation" | grep -c "invalid_key") > 0)); then
        echo -e "\e[41mERROR\e[0m ""Your API key is invalid."
        exit 1
    elif (($(echo "$validation" | grep -c "no_access") > 0)); then
        echo -e "\e[41mERROR\e[0m ""You don't have access to add a server."
        exit 1
    elif (($(echo "$validation" | grep -c "reached_maximum") > 0)); then
        echo -e "\e[41mERROR\e[0m ""You have reached the maximum number of servers you can add."
        exit 1
    elif (($(echo "$validation" | grep -c "unconfirmed_email") > 0)); then
        echo -e "\e[41mERROR\e[0m ""You have to confirm your accounts Email address first."
        exit 1
    else
        return 0
    fi
}

detect_linux_distro_and_version() {
  if [ -f /etc/os-release ]; then
    source /etc/os-release
    distro=$ID
    version=$VERSION_ID
  elif [ -f /etc/lsb-release ]; then
    source /etc/lsb-release
    distro=$DISTRIB_ID
    version=$DISTRIB_RELEASE
  elif [ -f /etc/redhat-release ]; then
    distro="redhat"
    version=$(awk '{print $(NF-1)}' /etc/redhat-release)
  else
    distro="unknown"
    version="unknown"
  fi

  echo "Detected Linux distribution: $distro"
  echo "Detected Linux version: $version"
}

fail_report() {
    {
    curl -X POST \
    "$xitoring_url"/send_report \
    -H 'cache-control: no-cache' \
    -H 'content-type: application/x-www-form-urlencoded' \
    -H 'key: '"$KEY" \
    -d 'subject=Xitogent Installation Failure&body= '"$result $distro $version"
    } > /dev/null 2>&1
}

check_if_installed() {
# Check if package is installed with dpkg
if command -v dpkg-query > /dev/null 2>&1; then
  if dpkg-query -W -f='${Status}' "xitogent" 2>/dev/null | grep -c "ok installed" > /dev/null; then
      echo -e "\e[41mWARNING\e[0m ""Xitogent Package is already installed."
      echo "If you want to reinstall and reregister xitogent on your server you can execute 'apt remove xitogent' first"
  else
      return 0
  fi
# Check if package is installed with yum
elif command -v yum > /dev/null 2>&1; then
  if yum list installed "xitogent" > /dev/null 2>&1; then
      echo -e "\e[41mWARNING\e[0m ""Xitogent Package is already installed."
      echo "If you want to reinstall and reregister xitogent on your server you can execute 'yum remove xitogent' first"
  else
      return 0
  fi
else
  echo -e "\e[41mERROR\e[0m ""Neither dpkg nor yum package managers found. Package manager unknown."
  exit 1
fi


if [ -f /etc/xitogent/xitogent.conf ]; then
    echo -e "\e[41mERROR\e[0m ""File /etc/xitogent/xitogent.conf found. Aborting."
    echo "The xitogent.conf file is exist, it means that your server is already registered on Xitoring. if you want to reinstall and reregister xitogent on your server you need to uninstall xitogent using your package manager first."
    exit 1
else
    return 0
fi
}

package_manager_detection() {
    if type apt &>/dev/null; then
        package_manager="apt"
    elif type yum &>/dev/null; then
        package_manager="yum"
    else
        echo -e "\e[41mERROR\e[0m ""Your package manager is not supported, you can try to install xitogent manually."
        result="Package Manager is Unknown"
        fail_report
        exit 0
    fi
}

add_yum_repo() {
    if [[ $package_manager == "yum" ]]; then
        touch /etc/yum.repos.d/xitogent.repo
        echo "" >/etc/yum.repos.d/xitogent.repo
        {
            echo "[Xitogent]"
            echo "name=Xitoring Agent on your machine"
            echo "baseurl=https://mirror.xitoring.com/centos"
            echo "enabled=1"
            echo "gpgcheck=1"
            echo "gpgkey=https://mirror.xitoring.com/centos/RPM-GPG-KEY-Xitogent"
        } >/etc/yum.repos.d/xitogent.repo
        if [[ $? -ne 0 ]]; then
            echo -e "\e[41mERROR\e[0m ""Failed to write to /etc/yum.repos.d/xitogent.repo" >&2
            result="Failed to write to /etc/yum.repos.d/xitogent.repo"
            fail_report
            exit 1
        fi
    else
        return 0
    fi
}

add_apt_repo() {
    if [[ $package_manager == "apt" ]]; then
        install -m 0755 -d /etc/apt/keyrings
        curl -fs 'https://mirror.xitoring.com/debian/DEB-GPG-KEY-Xitogent' >/etc/apt/keyrings/DEB-GPG-KEY-Xitogent
        chmod a+r /etc/apt/keyrings/DEB-GPG-KEY-Xitogent
        touch /etc/apt/sources.list.d/xitogent.list
        echo "deb [signed-by=/etc/apt/keyrings/DEB-GPG-KEY-Xitogent] https://mirror.xitoring.com/debian ./" >/etc/apt/sources.list.d/xitogent.list
        if [[ $? -ne 0 ]]; then
            echo -e "\e[41mERROR\e[0m ""Failed to write to /etc/apt/sources.list.d/xitogent.list" >&2
            result="Failed to write to /etc/apt/sources.list.d/xitogent.list"
            fail_report
            exit 1
        fi
    else
        return 0
    fi
}

yum_install() {
    if [[ $package_manager == "yum" ]]; then
        yum repolist &>/dev/null
        result=$(yum install xitogent -y 2>&1)
        echo "$result"
        if [[ $? -ne 0 ]]; then
            echo -e "\e[41mERROR\e[0m ""Failed to install the Xitogent package" >&2
            fail_report
            exit 1
        fi
    else
        return 0
    fi
}

apt_install() {
    if [[ $package_manager == "apt" ]]; then
        apt-get update
        result=$(apt-get install xitogent -y 2>&1)
        echo "$result"
        if [[ $? -ne 0 ]]; then
            echo -e "\e[41mERROR\e[0m ""Failed to install the Xitogent package" >&2
            fail_report
            exit 1
        fi
    else
        return 0
    fi
}

register_server() {
    /usr/bin/xitogent register --key "$KEY" $region $auto_discovery $auto_trigger $auto_update $module_ping $module_http $module_dns $module_ftp $module_smtp $module_imap $module_pop3 $heartbeat $ipv4 $ipv6 --notification \"$NOTIFICATION\" --group \"$GROUP\" --subgroup \"$SUBGROUP\"
    systemctl daemon-reload &>/dev/null
    systemctl enable xitogent &>/dev/null
    systemctl restart xitogent &>/dev/null
    if [[ $? -ne 0 ]]; then
        result=$(systemctl status xitogent 2>&1)
        echo "Error: Failed to start the Xitogent service" >&2
        echo "$result"
        fail_report
        exit 1
    fi
}

initial_service_test() {
    sleep 3
    if (("$(systemctl is-active xitogent | grep -wc 'active')" > 0)); then
        echo "Start Service 	systemctl start xitogent"
        echo "Stop Service 		systemctl stop xitogent"
        echo "Restart Service 	systemctl restart xitogent"
        echo "Get Status		systemctl status xitogent"
        echo ""
        echo -e "\e[32mInstallation Successful\e[0m"
        exit 0
    else
        echo -e "\e[41mERROR\e[0m ""Starting Service Failed"
        echo ""
        sleep 3
        systemctl status xitogent
        exit 1
    fi
}

is_root
validate_user
detect_linux_distro_and_version
check_if_installed
package_manager_detection
add_yum_repo
add_apt_repo
yum_install
apt_install
register_server
initial_service_test
