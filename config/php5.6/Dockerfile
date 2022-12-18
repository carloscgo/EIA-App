FROM centos:7

MAINTAINER Alberto J. Ruiz G. <albejoruiz@gmail.com>

RUN yum -y update
RUN yum -y install httpd httpd-tools curl
RUN yum -y update && yum -y install https://github.com/wkhtmltopdf/wkhtmltopdf/releases/download/0.12.5/wkhtmltox-0.12.5-1.centos7.x86_64.rpm

RUN rpm -Uvh https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm \
    && rpm -Uvh http://rpms.remirepo.net/enterprise/remi-release-7.rpm

RUN yum --enablerepo=remi-php56 -y install php php-bcmath php-cli php-common php-gd php-intl php-ldap php-mbstring \
    php-mysqlnd php-pear php-soap php-xml php-xmlrpc php-zip php-apc php-opcache php-mongo php-redis php-curl php-devel gcc && yum clean all

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=1.10.20

RUN sed -E -i -e '/<Directory "\/var\/www\/html">/,/<\/Directory>/s/AllowOverride None/AllowOverride All/' /etc/httpd/conf/httpd.conf
RUN sed -E -i -e 's/DirectoryIndex (.*)$/DirectoryIndex index.php \1/g' /etc/httpd/conf/httpd.conf

RUN rm -f /etc/httpd/conf.d/welcome.conf

RUN chmod -R 777 /var/lib/php/session

RUN usermod -u 1000 apache

RUN chown -R apache:apache /var/www/html
RUN find /var/www/html -type d -exec chmod 775 {} \;

EXPOSE 80

CMD ["/usr/sbin/httpd","-D","FOREGROUND"]
