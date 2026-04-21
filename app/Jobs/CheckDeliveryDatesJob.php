
<IfModule mod_ssl.c>
    <VirtualHost mega1000.pl:443>
        ServerAdmin www-data@www-data

        DocumentRoot /var/www/front-page

        ProxyPass / http://localhost:3000/
        ProxyPassReverse / http://localhost:3000/

        <Directory /var/www/front-page>
        RewriteEngine On
        RewriteCond %{HTTP_HOST} ^(www\.)(.*) [NC]
        RewriteRule (.*) https://%2%{REQUEST_URI} [L,R=301]
        </Directory>

        ErrorLog ${APACHE_LOG_DIR}/front_error.log
        CustomLog ${APACHE_LOG_DIR}/front_access.log combined

        ServerName www.mega1000.pl
        ServerAlias mega1000.pl

        Include /etc/letsencrypt/options-ssl-apache.conf
        SSLCertificateFile /etc/letsencrypt/live/mega1000.pl/fullchain.pem
        SSLCertificateKeyFile /etc/letsencrypt/live/mega1000.pl/privkey.pem
    </VirtualHost>
    <VirtualHost mega1000.pl:80>
        ServerName www.mega1000.pl
        ServerAlias mega1000.pl
        Redirect permanent / https://mega1000.pl/

        <Directory /var/www/front-page>
        RewriteEngine On
        RewriteCond %{HTTP_HOST} ^(www\.)(.*) [NC]
        RewriteRule (.*) https://mega1000.pl%{REQUEST_URI} [L,R=301]
        </Directory>
        RewriteCond %{SERVER_NAME} =www.mega1000.pl [OR]
        RewriteCond %{SERVER_NAME} =mega1000.pl
        RewriteRule ^ https://%{SERVER_NAME}%{REQUEST_URI} [END,NE,R=permanent]
    </VirtualHost>
    # vim: syntax=apache ts=4 sw=4 sts=4 sr noet
</IfModule>
