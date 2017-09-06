# Xortify Server 5
## Module: Ban
## Author: Simon Antony Roberts <wishcraft@users.sourceforge.net>

This module is for the xortify.com server banning module, this is for operations with the open honeypot see: http://sourceforge.net/projects/xortify

# Rewrite: SEO Friendly URLS [.htaccess]

This goes in the XOOPS_ROOT_PATH/.htaccess file listed in the order of occurence required.

    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^ban/latest/([0-9]+)/ipsec.html$ ./modules/ban/index.php?op=latest&num=$1&extra=toliet [L,NC,QSA]
    RewriteRule ^ban/create/ipsec.html$ ./modules/ban/index.php?op=create&extra=shower [L,NC,QSA]
    RewriteRule ^ban/issued/([0-9]+)/(.*?)/(.*?)/ipsec.html$ ./modules/ban/index.php?op=member&member_id=$1&ip=$3 [L,NC,QSA]
    RewriteRule ^ban/index.php(.*?)$ ./modules/ban/index.php$1 [L,NC,QSA]
    RewriteRule ^ban/backend.php(.*?)$ ./modules/ban/backend.php$1 [L,NC,QSA]
    RewriteRule ^ban/$ ./modules/ban/index.php [L,NC,QSA]
