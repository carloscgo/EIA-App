FROM centos:7

MAINTAINER Alberto J. Ruiz G. <albejoruiz@gmail.com>

RUN yum -y update
RUN yum -y install httpd httpd-tools curl
RUN yum -y update && yum -y install https://github.com/wkhtmltopdf/wkhtmltopdf/releases/download/0.12.5/wkhtmltox-0.12.5-1.centos7.x86_64.rpm

RUN rpm -Uvh https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm \
 && rpm -Uvh http://rpms.remirepo.net/enterprise/remi-release-7.rpm

RUN curl https://packages.microsoft.com/config/rhel/7/prod.repo > /etc/yum.repos.d/mssql-release.repo
RUN yum -y update && yum remove unixODBC-utf16 unixODBC-utf16-devel
RUN ACCEPT_EULA=Y yum -y install msodbcsql
RUN ACCEPT_EULA=Y yum -y install mssql-tools
RUN echo 'export PATH="$PATH:/opt/mssql-tools/bin"' >> ~/.bash_profile
RUN echo 'export PATH="$PATH:/opt/mssql-tools/bin"' >> ~/.bashrc
RUN source ~/.bashrc
RUN yum -y install unixODBC-devel

RUN yum -y update && yum --enablerepo=remi-php74 -y install php php-bcmath php-cli php-common php-gd php-intl php-ldap php-mbstring \
    php-mysqlnd php-pear php-soap php-xml php-xmlrpc php-zip php-apc php-opcache php-redis php-curl php-devel php-pdo php-odbc php-sqlsrv php-pdo-dblib gcc

RUN pear config-set auto_discover 1
RUN pear channel-update pecl.php.net

RUN yum install -y openssl-devel
RUN yum install -y ImageMagick ImageMagick-devel ImageMagick-perl

RUN pecl install mongodb
RUN echo "extension=mongodb.so" > /etc/php.d/mongodb.ini

RUN pecl install imagick
RUN echo "extension=imagick.so" > /etc/php.d/imagick.ini

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=1.10.20

RUN yum -y update && yum -y install nano
RUN yum -y update && yum -y install supervisor && yum clean all

COPY supervisord/*.ini /etc/supervisord.d/

RUN sed -E -i -e '/<Directory "\/var\/www\/html">/,/<\/Directory>/s/AllowOverride None/AllowOverride All/' /etc/httpd/conf/httpd.conf
RUN sed -E -i -e 's/DirectoryIndex (.*)$/DirectoryIndex index.php \1/g' /etc/httpd/conf/httpd.conf

RUN rm -f /etc/httpd/conf.d/welcome.conf

RUN chmod -R 777 /var/lib/php/session

RUN usermod -u 1000 apache
# RUN usermod -G 1000 apache

RUN chown -R apache:apache /var/www/html
RUN find /var/www/html -type d -exec chmod 775 {} \;

EXPOSE 80

CMD exec supervisord -n
