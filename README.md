WorksManagement
===============

Project/Works management system for Northpower's Westcoast Energy

check apache error log if not working for unknown reason. Might be a few instruction missing so need to fix in next install


#1./ Server installation (apache):
#Ensure mod_rewrite is on in virtual host
a2enmod rewrite

Sample virtual host:
<VirtualHost *:80>

	ServerName test.melbourne.wcewm.co.nz

	DocumentRoot /var/www/test/melbourne
	<Directory /var/www/test/melbourne/>
		Options Indexes FollowSymLinks MultiViews
		AllowOverride All
		Order allow,deny
		allow from all
	</Directory>

</VirtualHost>

#restart apache once all changes made

#2./ Debian install after following setup including extra stuff on phpmyadmin from http://www.howtoforge.com/installing-apache2-with-php5-and-mysql-support-on-debian-lenny-lamp
apt-get update
apt-get install git-core
git config --global user.name "Andrew Blake"
git config --global user.email admin@newzealandfishing.com

apt-get install curl
cd /usr/bin
curl -s -O http://github-media-downloads.s3.amazonaws.com/osx/git-credential-osxkeychain
chmod u+x git-credential-osxkeychain
git config --global credential.helper osxkeychain
# might be missing some steps here in creating ssh keys
# set group access read to 
cat /root/.ssh/id_rsa.pub
#copy key into ssh keys in your github account
eval $(ssh-agent)	#runs ssh agent
# installing yii
cd /var
git init yii
cd yii
git clone git@github.com:yiisoft/yii
cd yii
git checkout 1.1.12

3./ MySQL - to upload databases straight from workbench need to open mysql to other ip's
find / -name my.cnf
#edit my.cnf to allow access from any ip??
/etc/init.d/mysql restart

#4./ Installing application
# need to install as user www-data which means www-data needs access to the .ssh folder
# www-data needs to update into runtime directory and update assets hence doesn't work if installed as root
# easiest just to chown -R www-data . after pulled down from github as by default www-data has no home directory to store ssh-keys so would need
# to muck around with this whereas already setup for root hence just do as root and chown after
cd /var/www
git init test
git clone git@github.com:andrewblake1/WorksManagement.git
mv WorksManagement melbourne
chown -R www-data .
# if installed in domain/subdomain i.e. no supdirectory then .htaccess is fine, otherwise need to modify first RewriteRule e.g. /melbourne/ instead of /
cd melbourne
cp template.htaccess .htaccess
# need to set local database access
cd protected/config
cp local_template.php local.php
# set database settings here

#5./ update index.php with correct domains/subdomains in the switch statement that determines the type of environment to run i.e. developmen or production
