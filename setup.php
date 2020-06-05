<?php
/**
 * Free Wallpaper Script
 *
 * Free Wallpaper Script by Vepa Halliyev is licensed under a Creative Commons Attribution-Share Alike 3.0 License.
 *
 * @package		Free Wallpaper Script
 * @author		Vepa Halliyev
 * @copyright	Copyright (c) 2009, Vepa Halliyev, veppa.com.
 * @license		http://www.veppa.com/free-wallpaper-script/
 * @link		http://www.veppa.com/free-wallpaper-script/
 * @since		Version 1.0
 * @filesource
 */
/*
  {DB_NAME}
  {DB_HOST}
  {DB_USER}
  {DB_PASS}
  {TABLE_PREFIX}
  {USE_MOD_REWRITE} true/false
  {USE_PDO}
 */
if (!defined('CORE_ROOT'))
{
    exit('Cannot call setup.php directly. Run <a href="index.php">index.php</a>');
}


$RewriteBase = dirname($_SERVER['SCRIPT_NAME']);

define('DEBUG', true);
// turn error reporting on 
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', '1');

$file = 'sys/config.template.php';
$script_name = 'Veppa Wallpaper Script';

// check if sample exists
if (!file_exists($file))
{
    exit('Sorry, I need a ' . $file . ' file to work from. Please re-upload this file from your Veppa wallpaper script installation.');
}
$configFile = file_get_contents($file);


// Check if config.php has been created
if (file_exists('sys/config.php'))
    exit("The file 'config.php' already exists. If you need to reset any of the configuration items in this file, please edit it manually.");

// check php version 
function phpMinV($v)
{
    $phpV = PHP_VERSION;
    if ($phpV[0] >= $v[0])
    {
        if (empty($v[2]) || $v[2] == '*')
        {
            return true;
        }
        elseif ($phpV[2] >= $v[2])
        {
            if (empty($v[4]) || $v[4] == '*' || $phpV[4] >= $v[4])
            {
                return true;
            }
        }
    }
    return false;
}

if (!phpMinV('5'))
    exit("Sorry, PHP 5 required to run this script. Please upgrade to php 5.");


// check sys folder write permission for config.php	
if (!is_writable('sys/'))
    exit("Sorry, I can't write to the directory. You'll have to either change the permissions on your sys directory or create your config.php manually.");


// wallpaper upload permission
$upload_test_dir = FROG_ROOT . '/user-content/uploads/';
if (!is_writable($upload_test_dir))
    exit("Access permission to /user-content/uploads/ is restricted. Please set folder and subfolder pemission to 0777 in order to upload wallpapers.");



// default values
$dbname = 'wallpaperscript';
$uname = 'dbusername';
$passwrd = 'dbpassword';
$dbhost = 'localhost';
$prefix = 'vws_';
$use_pdo = false;



require_once(CORE_ROOT . '/Framework.php');


if (isset($_POST['submit']))
{

    // detect PDO
    //echo 'pdo echeck;';
    if (class_exists('PDO',false))
    {
        foreach (@PDO::getAvailableDrivers() as $driver)
        {
            if ($driver == 'mysql')
            {
                $use_pdo = true;
            }
        }
    }
    //echo $use_pdo?'use_pdo':'no_pdo';
    //echo 'pdo end;';
    // check db connection and create config file
    $dbname = trim($_POST['dbname']);
    $uname = trim($_POST['uname']);
    $passwrd = trim($_POST['pwd']);
    $dbhost = trim($_POST['dbhost']);
    $prefix = trim($_POST['prefix']);
    if (empty($prefix))
        $prefix = 'vws_';

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $password_repeat = trim($_POST['password_repeat']);


    $connections = array(
        'master' => array('DB_DSN' => 'mysql:dbname=' . $dbname . ';host=' . $dbhost,
            'DB_USER' => $uname,
            'DB_PASS' => $passwrd)
    );

    // use pdo 
    if (!defined('USE_PDO'))
    {
        define('USE_PDO', $use_pdo);
    }

    // connection is ok 
    $setup = true;

    Record::$__CONNECTIONS__ = $connections;
    $connected = Record::getConnection('master');
    if (!$connected)
    {
        $error = 'Database connection problem.';
        $setup = false;
    }

    // create admin for the site
    if ($setup && (!strlen($email) || !strlen($password) || !strlen($password_repeat)))
    {
        $error = 'Admin email and password values required.';
        $setup = false;
    }


    if ($setup && (strpos($email, '@') === false || strpos($email, '.') === false))
    {
        $error = 'Admin email is not valid.';
        $setup = false;
    }

    if ($setup && (strlen($password) < 4 || strlen($password) > 32))
    {
        $error = 'Admin password must be between 4-32 characters.';
        $setup = false;
    }

    if ($setup && $password !== $password_repeat)
    {
        $error = 'Admin password ans password repeat is not matching.';
        $setup = false;
    }

    if ($setup)
    {
        // write information to database

        /* Table structure for table `g2_category` */
        $sql[] = "DROP TABLE IF EXISTS `g2_category`";
        $sql[] = "CREATE TABLE `g2_category` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `name` varchar(100) DEFAULT NULL,
				  `added_at` int(11) DEFAULT NULL,
				  `added_by` int(11) DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC";

        /* Data for the table `g2_category` */
        $sql[] = "insert  into `g2_category`(`id`,`name`,`added_at`,`added_by`) values (1,'Nature',1257683601,0),(2,'Love',1258824533,0),(3,'Animals',1258824540,0),(4,'Cars',1258824546,0)";

        /* Table structure for table `g2_comment` */
        $sql[] = "DROP TABLE IF EXISTS `g2_comment`";
        $sql[] = "CREATE TABLE `g2_comment` (
		  `cm_id` int(11) NOT NULL AUTO_INCREMENT,
		  `cm_wid` int(11) DEFAULT NULL,
		  `cm_name` varchar(50) DEFAULT NULL,
		  `cm_website` varchar(250) DEFAULT NULL,
		  `cm_body` text,
		  `cm_active` tinyint(1) NOT NULL DEFAULT '0',
		  `cm_ip` varchar(20) DEFAULT NULL,
		  `cm_added_at` int(11) DEFAULT NULL,
		  `cm_added_by` int(11) DEFAULT NULL,
		  PRIMARY KEY (`cm_id`),
		  KEY `cm_wid` (`cm_wid`,`cm_id`),
		  KEY `cm_active` (`cm_active`,`cm_wid`,`cm_id`)
		) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC";

        /* Data for the table `g2_comment` */
        $sql[] = "insert  into `g2_comment`(`cm_id`,`cm_wid`,`cm_name`,`cm_website`,`cm_body`,`cm_active`,`cm_ip`,`cm_added_at`,`cm_added_by`) values (2,2,'test','http://test.com','test site',1,'127.0.0.1',1257686073,NULL)";

        /* Table structure for table `g2_config` */
        $sql[] = "DROP TABLE IF EXISTS `g2_config`";
        $sql[] = "CREATE TABLE `g2_config` (
		  `name` varchar(250) NOT NULL,
		  `val` text,
		  `is_editable` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:not editable,1:editable,2:editable can delete',
		  PRIMARY KEY (`name`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC";

        /* Data for the table `g2_config` */
        $sql[] = "insert  into `g2_config`(`name`,`val`,`is_editable`) values 
                    ('con_last_date_reset','2009-11-19',0), 
                    ('site_version','1.2.5',0), 
                    ('site_description','Free desktop wallpapers',1), 
                    ('site_title','Wallpapers',1), 
                    ('template','base',1),
                    ('news_from_veppa_on','1',1),
                    ('approve_user_comments','1',1),
                    ('wallpaper_sizes','1024x768,1600x1200,2048x1536,1400x1050,1152x864,1280x960,1920x1440,1280x1024,1280x720,1680x1050,1440x900,1920x1200,1280x800',1)";

        /* Table structure for table `g2_tag` */
        $sql[] = "DROP TABLE IF EXISTS `g2_tag`";
        $sql[] = "CREATE TABLE `g2_tag` (
		  `t_id` int(11) NOT NULL AUTO_INCREMENT,
		  `t_name` varchar(250) DEFAULT NULL,
		  `t_banned` tinyint(1) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`t_id`),
		  UNIQUE KEY `t_name` (`t_name`),
		  KEY `t_banned` (`t_banned`,`t_id`)
		) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC";

        /* Data for the table `g2_tag` */
        $sql[] = "insert  into `g2_tag`(`t_id`,`t_name`,`t_banned`) values (1,'tree',0),(2,'grass',0),(3,'sun',0),(4,'autumn',0),(9,'nature',0),(10,'color',0),(11,'abstract',0),(12,'blue',0),(13,'lights',0),(15,'bugatti',0),(16,'car',0),(17,'veyron',0),(18,'love',0),(19,'kiss',0),(20,'shadow',0),(22,'tiger',0),(23,'white',0),(24,'water',0),(25,'swimming',0),(26,'animals',0),(27,'cars',0)";

        /* Table structure for table `g2_tag_relation` */
        $sql[] = "DROP TABLE IF EXISTS `g2_tag_relation`";
        $sql[] = "CREATE TABLE `g2_tag_relation` (
		  `r_wid` int(11) NOT NULL DEFAULT '0',
		  `r_tid` int(11) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`r_wid`,`r_tid`),
		  KEY `r_tid_wid` (`r_tid`,`r_wid`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC";

        /* Data for the table `g2_tag_relation` */
        $sql[] = "insert  into `g2_tag_relation`(`r_wid`,`r_tid`) values (2,1),(2,2),(2,3),(2,4),(2,9),(3,10),(3,11),(4,11),(4,12),(4,13),(5,15),(5,17),(5,27),(6,1),(6,18),(6,19),(6,20),(7,22),(7,23),(7,24),(7,25),(7,26)";

        /* Table structure for table `g2_user` */
        $sql[] = "DROP TABLE IF EXISTS `g2_user`";
        $sql[] = "CREATE TABLE `g2_user` (
		  `id` INT(11) NOT NULL AUTO_INCREMENT,
		  `username` VARCHAR(100) NOT NULL,
		  `email` VARCHAR(100) NOT NULL,
		  `password` VARCHAR(50) NOT NULL,
		  `level` TINYINT(2) NOT NULL DEFAULT '0' COMMENT '0:pending;1:admin;2:moderator;3:user;',
		  `ip` VARCHAR(32) NOT NULL,
		  `activation` VARCHAR(15) NOT NULL DEFAULT '0' COMMENT 'activation number to verify email address',
		  `logged_at` INT(11) NOT NULL COMMENT 'last login time',
		  `added_at` INT(11) NOT NULL COMMENT 'registration time',
		  `added_by` INT(11) NOT NULL,
		  `web` VARCHAR(100) NOT NULL COMMENT 'user website address',
		  PRIMARY KEY (`id`),
		  UNIQUE KEY `email` (`email`),
		  KEY `level` (`level`),
		  KEY `activation` (`activation`),
		  KEY `added_at` (`added_at`)
		) ENGINE=INNODB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC";

        /* Data for the table `g2_user` */
        //$sql[] = "insert  into `g2_user`(`id`,`username`,`email`,`password`,`level`,`ip`,`activation`,`logged_at`,`added_at`,`added_by`,`web`) values (1,'vepa','test@test.com','098f6bcd4621d373cade4e832627b4f6',1,'127.0.0.1','0',1258822370,1258564165,8,'')";

        /* Table structure for table `g2_vote` */
        $sql[] = "DROP TABLE IF EXISTS `g2_vote`";
        $sql[] = "CREATE TABLE `g2_vote` (
		  `v_wid` int(11) NOT NULL DEFAULT '0',
		  `v_ip` varchar(32) NOT NULL DEFAULT '',
		  `v_vote` tinyint(4) DEFAULT NULL COMMENT '0:-1;1:+1',
		  `v_time` int(11) DEFAULT NULL COMMENT 'timestamp of vote',
		  PRIMARY KEY (`v_wid`,`v_ip`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC";

        /* Data for the table `g2_vote` */
        $sql[] = "insert  into `g2_vote`(`v_wid`,`v_ip`,`v_vote`,`v_time`) values (2,'127.0.0.1',1,1257939416)";

        /* Table structure for table `g2_wallpaper` */
        $sql[] = "DROP TABLE IF EXISTS `g2_wallpaper`";
        $sql[] = "CREATE TABLE `g2_wallpaper` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `img` varchar(250) DEFAULT NULL COMMENT 'image and thumb same name',
		  `name` varchar(250) DEFAULT NULL,
		  `artist` varchar(250) DEFAULT NULL COMMENT 'artist name',
		  `site` varchar(250) DEFAULT NULL COMMENT 'artists site',
		  `description` varchar(250) DEFAULT NULL,
		  `tags` varchar(250) DEFAULT NULL COMMENT 'comma seprated tags for easy reading',
		  `size` varchar(20) DEFAULT NULL COMMENT '1024x800 size',
		  `dwn` int(11) NOT NULL DEFAULT '0',
		  `rank` int(11) NOT NULL DEFAULT '0',
		  `public` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:pending;1:public;',
		  `votes` int(11) NOT NULL DEFAULT '0' COMMENT 'number of votes',
		  `num_comments` int(11) DEFAULT '0' COMMENT 'nuber of approved comments',
		  `num_all_comments` int(11) DEFAULT '0' COMMENT 'number of all comments, pending and approved for checking comment limit',
		  `d1` int(11) NOT NULL,
		  `d2` int(11) NOT NULL,
		  `d3` int(11) NOT NULL,
		  `d4` int(11) NOT NULL,
		  `d5` int(11) NOT NULL,
		  `d6` int(11) NOT NULL,
		  `d7` int(11) NOT NULL,
		  `week` int(11) NOT NULL,
		  `ip` varchar(32) DEFAULT NULL,
		  `added_at` int(11) DEFAULT NULL,
		  `added_by` int(11) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`id`),
		  KEY `w_state` (`public`,`id`),
		  KEY `w_dwn` (`dwn`),
		  KEY `w_rank` (`rank`),
		  KEY `w_week` (`week`)
		) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC";

        /* Data for the table `g2_wallpaper` */
        $sql[] = "insert  into `g2_wallpaper`(`id`,`img`,`name`,`artist`,`site`,`description`,`tags`,`size`,`dwn`,`rank`,`public`,`votes`,`num_comments`,`num_all_comments`,`d1`,`d2`,`d3`,`d4`,`d5`,`d6`,`d7`,`week`,`ip`,`added_at`,`added_by`) values (2,'7/Autumn_Leaves.jpg','Autumn','','','','tree,grass,sun,autumn,nature','1024x768',3,1,1,1,1,1,0,0,0,1,1,0,1,0,'127.0.0.1',1257684842,8),(3,'3/abstract.jpg','Color blast','','','','color,abstract','1600x1200',0,0,1,0,0,0,0,0,0,0,0,0,0,0,'127.0.0.1',1258824380,10),(4,'35/Blue_Light_1680_x_10.jpg','Blue ligths','','','','blue,lights,abstract','1600x1000',0,0,1,0,0,0,0,0,0,0,0,0,0,0,'127.0.0.1',1258824413,10),(5,'1/bugatti-veyron.jpg','Bugatti veyron','','','','bugatti,cars,veyron','1024x768',0,0,1,0,0,0,0,0,0,0,0,0,0,0,'127.0.0.1',1258824438,10),(6,'41/love_kiss.jpg','Love kiss','','','','love,kiss,tree,shadow','1920x1080',0,0,1,0,0,0,0,0,0,0,0,0,0,0,'127.0.0.1',1258824473,10),(7,'30/white-tiger-swimming.jpg','White tiger swimming','','','','tiger,white,water,swimming,animals','2048x1536',0,0,1,0,0,0,0,0,0,0,0,0,0,0,'127.0.0.1',1258824514,10)";


        foreach ($sql as $s)
        {
            if ($setup)
            {
                $s = str_replace('g2_', $prefix, $s);
                if (!Record::query($s))
                {
                    $error = 'Error adding initial database records. Maybe given database not exists. Please create database or check that it matches database in mysql.';
                    $setup = false;
                }
            }
        }
    }

    if ($setup)
    {
        if (!defined('TABLE_PREFIX'))
        {
            define('TABLE_PREFIX', $prefix);
        }

        list($username, ) = explode('@', $email);
        $sql = "INSERT INTO " . $prefix . "user (`username`,`email`,`password`,`level`,`ip`,`activation`,`added_at`) 
			VALUES (?, ?, ?,'1','127.0.0.1','0','0')";


        if (!Record::query($sql, array($username, $email, md5($password))))
        {
            $error = 'Error creating admin for the site.';
            $setup = false;
        }
    }



    if ($setup)
    {
        // create config file	
        $find = array(
            '{DB_NAME}',
            '{DB_HOST}',
            '{DB_USER}',
            '{DB_PASS}',
            '{TABLE_PREFIX}',
            "'{USE_MOD_REWRITE}'",
            "'{USE_PDO}'");

        $replace = array(
            $dbname,
            $dbhost,
            $uname,
            $passwrd,
            $prefix,
            'true',
            $use_pdo ? 'true' : 'false');

        $configFile = str_replace($find, $replace, $configFile);
        if (!file_put_contents('sys/config.php', $configFile))
        {
            exit('Error writing config file.');
        }
        chmod('sys/config.php', 0666);


        // write htaccess file 
        $RewriteBase = dirname($_SERVER['SCRIPT_NAME']) . '/';
        $htaccess = '#Options +FollowSymLinks
AddDefaultCharset UTF-8
<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteBase ' . $RewriteBase . '
  
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteCond %{REQUEST_FILENAME} !-l
  # Main URL rewriting.
  RewriteRule ^(.*)$ index.php?$1 [L,QSA]
</IfModule>';


        if (!file_put_contents('.htaccess', $htaccess))
        {
            echo 'Error writing .htaccess file.';

            echo 'Please create .htaccess file manually with following content:';
            echo '<textarea rows="10" cols="40">' . $htaccess . '</textarea>';
        }

        $msg = '<p>Congratulations you installed ' . $script_name . '. <a href="' . get_url() . '">View your website</a>.</p>';
        exit($msg);
    }
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Veppa Wallpaper Script &rsaquo; Setup Configuration File</title>
        <link rel="stylesheet" href="public/css/screen.css" type="text/css" />
    </head>
    <body class="setup">
        <div class="content">
            <h1>Veppa Wallpaper Script</h1>


            <p>Welcome to Veppa Wallpaper Script. Before getting started, we need some information on the database.</p>

            <form method="post" action="">
                <p>Below you should enter your database connection details. If you're not sure about these, contact your host. </p>
<?php
if ($error)
{
    echo '<p style="color:red;"><b>' . $error . '</b></p>';
}
?>
                <table class="grid">
                    <tr>
                        <th><label for="dbname">Database Name</label></th>
                        <td><input name="dbname" id="dbname" type="text" size="25" value="<?php echo htmlspecialchars($dbname) ?>" /></td>
                        <td>The name of the database you want to run wallpaper script in. </td>
                    </tr>
                    <tr>
                        <th><label for="uname">User Name</label></th>
                        <td><input name="uname" id="uname" type="text" size="25" value="<?php echo htmlspecialchars($uname) ?>" /></td>
                        <td>Your MySQL username</td>
                    </tr>
                    <tr>
                        <th><label for="pwd">Password</label></th>
                        <td><input name="pwd" id="pwd" type="text" size="25" value="<?php echo htmlspecialchars($passwrd) ?>" /></td>
                        <td>...and MySQL password.</td>
                    </tr>
                    <tr>
                        <th><label for="dbhost">Database Host</label></th>
                        <td><input name="dbhost" id="dbhost" type="text" size="25" value="<?php echo htmlspecialchars($dbhost) ?>" /></td>
                        <td>99% chance you won't need to change this value.</td>
                    </tr>
                    <tr>
                        <th><label for="prefix">Table Prefix</label></th>
                        <td><input name="prefix" id="prefix" type="text" size="25" value="<?php echo htmlspecialchars($prefix) ?>" /></td>
                        <td>If you want to run multiple <?php echo $script_name; ?> installations in a single database, change this.</td>
                    </tr>
                    <tr>
                        <td colspan="3"><h3>Create admin for the site</h3></td>
                    </tr>
                    <tr>
                        <th><label for="email">Admin email</label></th>
                        <td><input name="email" id="email" type="text" size="25" value="<?php echo htmlspecialchars($email) ?>" /></td>
                        <td>This will be used to login to admin panel.</td>
                    </tr>
                    <tr>
                        <th><label for="password">Admin password</label></th>
                        <td><input name="password" id="password" type="password" size="25" /></td>
                        <td>This will be used to login to admin panel. Password must be between 4-32 characters.</td>
                    </tr>
                    <tr>
                        <th><label for="password_repeat">Repeat password</label></th>
                        <td><input name="password_repeat" id="password_repeat" type="password" size="25" /></td>
                        <td></td>
                    </tr>
                </table>
                <p class="step"><input name="submit" type="submit" value="Submit" class="button" /></p>
            </form>
        </div>
    </body>
</html>
<?php
exit;
?>