

Contents: 
	Install instructions
	Update instructions
	Change Log
	FAQ
	
	
INSTALL INSTRUCTIONS
-----------------------------
- Upload script files to your server via ftp
- Visit  website in your browser and follow on page install instructions


UPDATE INSTRUCTIONS
-----------------------------
- Backup /sys/config.php file 
- Override /sys/ and /public/ folders
- Copy backed config.php file to /sys/ folder
- Open your website in browser and login to admin panel
- Click upgrade on admin panel notification


CHANGE LOG 
-----------------------------
Version: 1.2.5  (16.06.2012)
	+ Removed unused calls to author and category links in wallpaper.php template.
	+ Added option to auto approve comments by registered users. Default value is set to false when updating, true on new installs.

Version: 1.2.4  (29.05.2012)
	+ Fixed bug for non PDO users when installing script. Checking for pdo without calling autoloader.
	
Version: 1.2.3  (03.04.2012)
	+ renamed ParseCsv.php helper file to ParseCSV.php

Version: 1.2.2  (02.04.2012)
	+ fixed editing category, was adding new category instead of updating it. 
	+ fixed category URL bug where non latin characters ware changed by strtolower, resulting in wrond category links. Fixes problem with cyrillic category names.
	+ fixed case sensitive file name requirement for linux servers. fixed problems with use_helper('ParseCSV');

Version: 1.2.1  (03.06.2011)
	+ fixed php short tag problem. Some hosts do not suppert them. Replaced all short tags with long. 
	+ fixed bulk upload where ParseCsv was not loading properly on some hosts

Version: 1.2  (05.04.2011)
	+ added no pdo option check on install
	+ added check for directory permission for uploads on install
	+ added check for PHP version 5 on install
	+ added tags to use as page meta keywords for seo
	+ added display template select tool
	+ added bulk upload feature. uload via ftp csv and images to folder. then use some custom page to import them. 
	+ added language support (added turkish translation)	
	+ added script related news feed to admin dashboard.
	+ added option to disable script related news feed.
	+ added option for custom wallpaper sizes. Leave this option empty to use default sizes.
	+ added option for upgrade databse. admin/upgrade/
	+ added jquery version 1.5
	+ fixed 404 problem if category has no wallpaper
	+ fixed new users automaticly verified because they are added by admin
	+ fixed duplicate user emails
	+ removed default template in application core and used custom templates in user-content/ folder

Version: 1.1 
	+ Fixed category selection when uploading new wallpaper

Version: 1.0
	Initial release: wallpapers, categories, users, comments.
	
	
	
FAQ:
-------------------------------
- How to translate to other language?
	Copy and rename locale file located at /sys/app/i18n/tr-message.php to your desired language. If you want translation for spanish copy file as es-message.php. Then edit file and write spanish trasnlation instead of turkish. After finished with language file edit index.php located in root directory.  After directive use_helper('I18n'); add I18n::setLocale('es'); . Then upload updated index and es-message.php files. You can use any other language. use your language 2 letter code instead of es. 
- Can I use script in multilanguage mode? 
	Short answer is no because every wallpaper has its name and desription in one language. Even if you manage to use multilanguage for interface, wallpapers will be only in one language. 