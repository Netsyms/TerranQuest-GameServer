FROM php:5-apache
MAINTAINER Skylar Ittner <admin@netsyms.com>
RUN apt-get update && apt-get upgrade -y
RUN apt-get install git openssh-server
# nuke the webroot
WORKDIR /var/www
RUN rm -rf html && mkdir html
WORKDIR /var/www/html
# install the server crap (private repo for now, thx GitHub Student)
RUN git clone https://skylarmt-script-account:scriptb0t@github.com/skylarmt/TerranQuestServer.git .
# 0wn3d
RUN cd .. && chown -R www-data:www-data html