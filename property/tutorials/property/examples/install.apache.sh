#!/bin/bash
# $Id$ 

#/**
#  * installscript for APACHE with PHP, IMAP, POSTGRESQL, MYSQL, LIBXML, XSLT, FREEDTS(MSSQL) and EACCELERATOR
#  * 
#  * 
#  * Download all tarballs to one directory(here: '/opt/web') and place this script in the same place
#  * 
#  * NOTE: Do not add spaces after bash variables.
#  *
#  * @author            Sigurd Nes <Sigurdne (inside) online (dot) no>
#  * @version           Release-1.0.0
#  */

##############################
# should be edited
##############################

#/**
#  * Name of the freetds package e.g freetds-stable.tgz
#  * 
#  * @var               string FREETDS, FREETDSTAR
#  * Download: http://www.freetds.org/software.html
#  */
FREETDSTAR="freetds-stable.tgz"
FREETDS="freetds-0.82"

# Download: http://xmlsoft.org/downloads.html
LIBXMLTAR="libxml2-2.7.6.tar.gz"
LIBXML="libxml2-2.7.6"

LIBXSLTAR="libxslt-1.1.26.tar.gz"
LIBXSL="libxslt-1.1.26"

# Download: ftp://ftp.cac.washington.edu/imap/
IMAPTAR="imap-2007e.tar.Z"
IMAP="imap-2007e"

PHP_PREFIX="/usr/local"

#/**
#  * Name of the APACHE tarball e.g httpd-2.2.6.tar.gz
#  * 
#  * @var               string APACHE, APACHETAR
#  * Download: http://php.net/
#  */
APACHETAR="httpd-2.2.14.tar.gz"
APACHE="httpd-2.2.14"

#/**
#  * Name of the PHP tarball e.g php-5.2.tar.gz
#  * 
#  * @var               string PHP, PHPTAR
#  * Download: http://httpd.apache.org/
#  */
PHPTAR="php-5.3.1.tar.bz2"
PHP="php-5.3.1"

#/**
#  * Name of the EACCELERATOR tarball e.g eaccelerator-0.9.5.tar.bz2
#  * 
#  * @var               string EACCELERATOR, EACCELERATORTAR
#  * Download: http://eaccelerator.net/
#  */
EACCELERATORTAR="eaccelerator-svn379.tar.gz"
EACCELERATOR="eaccelerator-svn379"
PHP_PREFIX="/usr/local"

# APC as Alternative:
# Download: http://pecl.php.net/package/APC
# APCTAR="APC-3.1.2.tgz"
# APC="APC-3.1.2"

#/**
#  * Oracle PDO-Support
#  * Download: http://www.oracle.com/technology/software/tech/oci/instantclient/index.html
#  */

ORACLETAR="instantclient-basic-linux32-11.2.0.1.zip"
ORACLE="instantclient_11_2"
ORACLEDEVELTAR="instantclient-sdk-linux32-11.2.0.1.zip"

ORACLE_PDO=""

# include the oracle pdo-driver in the install
function include_oracle()
{
    unzip $1
    rm /opt/$2 -rf
    mv $2 /opt/
    unzip $ORACLEDEVELTAR 
    mv $2/sdk /opt/$2/
    export ORACLE_HOME=/opt/$2/
    ln -s /opt/$2/libclntsh.so.11.1 /opt/$2/libclntsh.so
    ln -s /opt/$2/libocci.so.11.1 /opt/$2/libocci.so
    ln -s /opt/$2/ /opt/$2/lib
}


# clean up from previous

rm $FREETDS -rf &&\
rm $LIBXML -rf &&\
rm $LIBXSL -rf &&\
rm $IMAP -rf &&\
rm $PHP -rf &&\
rm $EACCELERATOR -rf &&\
rm $APACHE -rf &&\
rm $ORACLE -rf &&\

# perform the install

echo -n "Include Oracle-pdo? answere yes or no: "

read svar


if [ $svar = "yes" ];then
    echo "Ok - lets try"
    include_oracle $ORACLETAR $ORACLE $ORACLEDEVELTAR
    ORACLE_PDO=" --with-oci8=instantclient,/opt/$ORACLE/ --with-pdo-oci"
    echo $ORACLE_PDO
    else
    echo "Skipping Oracle"
fi

tar -xzf $FREETDSTAR &&\
tar -xzf $LIBXMLTAR &&\
tar -xzf $LIBXSLTAR &&\
gunzip -c $IMAPTAR | tar xf - &&\
tar -xzf $APACHETAR &&\
bunzip2 -c $PHPTAR | tar xvf -&&\
tar -xzf $EACCELERATORTAR &&\
cd $FREETDS &&\
./configure --prefix=/usr/local/freetds --with-tdsver=8.0 --enable-msdblib\
--enable-dbmfix --with-gnu-ld --enable-shared --enable-static &&\
make &&\
make install &&\
touch /usr/local/freetds/include/tds.h &&\
touch /usr/local/freetds/lib/libtds.a &&\
cd ../$IMAP &&\
make lmd SSLTYPE=unix.nopwd IP6=4 &&\
ln -s c-client include &&\
mkdir lib &&\
cd lib &&\
ln -s ../c-client/c-client.a libc-client.a &&\
cd ../../$LIBXML &&\
./configure &&\
make &&\
make install &&\
cd ../$LIBXSL &&\
./configure &&\
make &&\
make install &&\
cd ../$APACHE/srclib/apr &&\
./configure --prefix=/usr/local/apr-httpd/ &&\
make &&\
make install &&\
# Build and install apr-util 1.2
cd ../apr-util &&\
./configure --prefix=/usr/local/apr-util-httpd/\
 --with-apr=/usr/local/apr-httpd/ &&\
make &&\
make install &&\
# Configure httpd
cd ../../ &&\
./configure --with-apr=/usr/local/apr-httpd/\
 --with-apr-util=/usr/local/apr-util-httpd/\
 --with-mpm=prefork\
 --enable-so\
 --enable-deflate\
 --enable-headers &&\
make &&\
make install &&\
cd ../$PHP &&\
export LDFLAGS=-lstdc++ &&\
./configure --with-imap=/opt/web/$IMAP\
 --with-imap-ssl\
 --with-sybase-ct=/usr/local/freetds\
 --with-apxs2=/usr/local/apache2/bin/apxs\
 --with-xsl\
 --with-zlib\
 --with-pspell\
 --with-jpeg-dir=/usr/lib\
 --with-png-dir=/usr/lib\
 --with-freetype-dir=/usr/lib\
 --with-gd\
 --enable-ftp\
 --with-pgsql\
 --with-mysql\
 --enable-shmop\
 --enable-sysvsem\
 --enable-sysvshm\
 --enable-calendar\
 --enable-pdo\
 --with-pdo-sqlite\
 --with-sqlite\
 --with-pdo-pgsql\
 --with-pdo-mysql\
 --with-openssl\
 --enable-mbstring\
 --with-mcrypt\
 --enable-soap\
 --with-xmlrpc \
 $ORACLE_PDO &&\
make &&\
make install &&\
cd ../$EACCELERATOR &&\
$PHP_PREFIX/bin/phpize &&\
./configure --enable-eaccelerator=shared --with-php-config=$PHP_PREFIX/bin/php-config &&\
make &&\
make install &&\
mkdir /tmp/eaccelerator &&\
chmod 0777 /tmp/eaccelerator


#cd ../$APC &&\
#$PHP_PREFIX/bin/phpize &&\
#./configure --enable-apc-mmap --with-apxs --with-php-config=$PHP_PREFIX/bin/php-config &&\
#make &&\
#make install

# vim: set expandtab :
