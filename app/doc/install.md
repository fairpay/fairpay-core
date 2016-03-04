Installation
============

Apache configuration
--------------------

Add thoses lines at the end of the `httpd.conf` file. For Wamp users the file is probably located at `C:\wamp\bin\apache\apache2.4.9\conf`
```
<VirtualHost *:80>
  ServerName fairpay.local
  ServerAlias *.fairpay.local

  DocumentRoot "c:/wamp/www/fairpay/fairpay-core/web"
  <Directory "c:/wamp/www/fairpay/fairpay-core/web">
    AllowOverride All
    Allow from All
  </Directory>
</VirtualHost>
```

Hosts file
----------

Add this line at the end of the `hosts` file. For Windows users the hosts file is located at `C:\Windows\System32\drivers\etc`.
```
127.0.0.1       fairpay.local api.fairpay.local esiee.fairpay.local fake.fairpay.local
```