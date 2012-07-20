<?php

$options['project_type'] = 'php';
$options['project_readable_dirs'] = array();
$options['project_readable_files'] = array('symfttpd.phar'); // readable files by the server in the web dir (robots.txt).
$options['project_readable_phpfiles'] = array('index.php'); // executable php files in the web directory
$options['project_readable_restrict'] = true;
$options['project_web_dir'] = 'web';
$options['project_log_dir'] = 'log';
$options['project_cache_dir'] = 'cache';

$options['server_type']  = 'lighttpd';