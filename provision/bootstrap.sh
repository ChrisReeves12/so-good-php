#!/usr/bin/env bash

echo "-- Updating Apt-Get --"
sudo apt-get update

echo "-- Downloading and Installing PostgreSQL --"
sudo apt-get -y install postgresql postgresql-contrib libpq-dev

echo "-- Installing necessary dependencies --"
sudo apt-get -y install nodejs-legacy vim lsof git-core default-jre curl zlib1g-dev build-essential libssl-dev libreadline-dev libyaml-dev libsqlite3-dev sqlite3 libxml2-dev libxslt1-dev libcurl4-openssl-dev python-software-properties libffi-dev

echo "-- Installing PHP --"
sudo apt-get -y install php7.0-cli php7.0-curl php7.0-fpm php7.0-gd php7.0-intl php7.0-json php7.0-mcrypt php7.0-mysql php7.0-readline php7.0-tidy php7.0-xml php7.0-mbstring php7.0-bcmath php7.0-bz2 php7.0-imap php7.0-zip php7.0-soap php-pear php-tideways php-apcu php-memcached php-uploadprogress php-geoip php-redis php-solr php-mongodb php7.0-pgsql php7.0-opcache php-zmq php-stomp php-imagick php-xdebug

echo "-- Provisioning Complete! --"





