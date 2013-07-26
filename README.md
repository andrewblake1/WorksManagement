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

	DocumentRoot /var/www/dev/melbourne
	<Directory /var/www/dev/melbourne/>
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
apt-get install at
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
find / -name my.cnf. This potential security risk as normally only open to 127.0.0.1 - may require firewall to make safe
#edit my.cnf to allow access from any ip??
3.1/ 
echo "max_sp_recursion_depth = 255">>max_sp_recursion_depth.cnf
#### tidy up the above - could just be put both in 1 file under conf.d


/etc/init.d/mysql restart

#4./ Installing application
# need to install as user www-data which means www-data needs access to the .ssh folder
# www-data needs to update into runtime directory and update assets hence doesn't work if installed as root
# easiest just to chown -R www-data . after pulled down from github as by default www-data has no home directory to store ssh-keys so would need


##### alter the above, everything safer owned by root and only give www-data write access to directories that is needed as below



# to muck around with this whereas already setup for root hence just do as root and chown after
cd /var/www
git init test
cd test
git clone git@github.com:andrewblake1/WorksManagement.git
mv WorksManagement melbourne
# if installed in domain/subdomain i.e. no supdirectory then .htaccess is fine, otherwise need to modify first RewriteRule e.g. /melbourne/ instead of /
cd melbourne
cp template.htaccess .htaccess
# need to create private uploads directory/s below document root that www-data has write access to
mkdir /home/www-data
cd /home/www-data
mkdir /uploads
mkdir /uploads/test
mkdir /uploads/test/melbourne
mkdir /uploads/test/perth
mkdir /uploads/test/melbourne/assembly
mkdir /uploads/test/perth/assembly
mkdir /uploads/melbourne
mkdir /uploads/perth
mkdir /uploads/melbourne/assembly
mkdir /uploads/perth/assembly

mkdir /var/www/melbourne/assets/assembly
mkdir /var/www/perth/assets/assembly
mkdir /var/www/test/melbourne/assets/assembly  
mkdir /var/www/test/perth/assets/assembly
mkdir /var/www/dev/melbourne/assets/assembly
mkdir /var/www/dev/perth/assets/assembly

# need to create public uploads temporary directories
chown -R www-data /var/www/melbourne/assets
chown -R www-data /var/www/perth/assets
chown -R www-data /var/www/test/melbourne/assets
chown -R www-data /var/www/test/perth/assets
chown -R www-data /var/www/dev/melbourne/assets
chown -R www-data /var/www/dev/perth/assets
chown -R www-data /home/www-data/uploads

nano /etc/php5/apache2/php.ini
#edit php.ini 
post_max_size = 40M
upload_max_filesize = 30M
max_file_uploads = 20
session.gc_probability = 1
session.gc_divisor = 1000
# high memory limit needed for phpexcel otherwise it crashes leaving a blank screen with no file. So far with materials
# export of just over 6000 rows, requiring somewhere between 256M and 384M
memory_limit = 384M

exit

# need to set local database access
cd protected/config
cp local_template.php local.php
# set database settings here - also set directories correct

#5./ 

#6./ to update to repository source go to the document root and type
cd /var/www/test/melbourne
git remote update
git stash
git merge origin/master


CLEANING DATA
=============
Cleaning UED & JEN Material catalog for Melbourne

Using sed on Debian as Mac OSX sed doesn't appear to support logical OR (|)
1./ import the data into mysql first before UPDATE Sheet1 SET A = REPLACE(A, '"', ''); to remove speech marks
2./ export to CSV with field delimeter " and seperator ; or whatever suits. NB: no good to export CSV from Excel as issues with CR LF pair vs newline required by sed on unix

sed 's/^"\([0-9]*\) ";" \([0-9]*\)";" \(.*\) \(JAR\|BAL\|CM\|PAA\|LTH\|cl\|SHT\|CAN\|CON\|DRM\|DR\|TBE\|BT\|BR\|RL\|EA\|SET\|CAR\|BOX\|BAG\|ROL\|PAC\|M\)[" ].*/"\1";"\2";"\3";"\4";/' Sheet1.csv > Sheet2.csv


DATABASE IMPORT
===============
Zinfra
INSERT INTO `materialToClient` (`material_id`, `client_id`, `alias`, `deleted`, `updated_by`)
SELECT `material`.id, 8, CONCAT_WS(' ', `JEN/ZNX Item No`), 0, 1
FROM `JEN_ZNX Free Issue`
JOIN `material` ON `material`.`alias` = `JEN_ZNX Free Issue`.`WCE Product No`;


Tenix
INSERT INTO `materialToClient` (`material_id`, `client_id`, `alias`, `deleted`, `updated_by`)
SELECT `material`.id, 12, CONCAT_WS(' ',  `UED Item No`), 0, 1
FROM `JEN_ZNX Free Issue`
JOIN `material` ON `material`.`alias` = `JEN_ZNX Free Issue`.`WCE Product No`;


ZNX
INSERT INTO `materialToClient` (`material_id`, `client_id`, `alias`, `deleted`, `updated_by`)
SELECT `material`.id, 4, CONCAT_WS(' ', `JEN/ZNX Item No`, `UED Item No`), 0, 1
FROM `JEN_ZNX Free Issue`
JOIN `material` ON `material`.`alias` = `JEN_ZNX Free Issue`.`WCE Product No`;

JEN
INSERT INTO `materialToClient` (`material_id`, `client_id`, `alias`, `deleted`, `updated_by`)
SELECT `material`.id, 6, CONCAT_WS(' ', `JEN/ZNX Item No`), 0, 1
FROM `JEN_ZNX Free Issue`
JOIN `material` ON `material`.`alias` = `JEN_ZNX Free Issue`.`WCE Product No`;

SPA 2
INSERT INTO `materialToClient` (`material_id`, `client_id`, `alias`, `deleted`, `updated_by`)
SELECT `material`.id, 2, CONCAT_WS(' ', `SPA Free issue`.`SPA Item No`, `SPA Free issue`.`SPA Old No`), 0, 1
FROM `SPA Free issue`
JOIN `material` ON `material`.`alias` = `SPA Free issue`.`WCE Product No`
group by `SPA Free issue`.`WCE Product No`;

Tenix
INSERT INTO `materialToClient` (`material_id`, `client_id`, `alias`, `deleted`, `updated_by`)
SELECT `material`.id, 12, CONCAT_WS(' ', `SPA Free issue`.`SPA Item No`, `SPA Free issue`.`SPA Old No`), 0, 1
FROM `SPA Free issue`
JOIN `material` ON `material`.`alias` = `SPA Free issue`.`WCE Product No`
group by `SPA Free issue`.`WCE Product No`;

Zinfra
INSERT INTO `materialToClient` (`material_id`, `client_id`, `alias`, `deleted`, `updated_by`)
SELECT `material`.id, 8, CONCAT_WS(' ', `SPA Free issue`.`SPA Item No`, `SPA Free issue`.`SPA Old No`), 0, 1
FROM `SPA Free issue`
JOIN `material` ON `material`.`alias` = `SPA Free issue`.`WCE Product No`
group by `SPA Free issue`.`WCE Product No`;
