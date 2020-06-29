#!/bin/bash
# Auf Zeilenende LF achten

cp -R /root/plugins/* /var/www/html/wp-content/plugins
#cp -R /root/themes/* /var/www/html/wp-content/themes

cd /var/www/html/

echo "Auf welchem Port soll die WordPress-Installlation hören?"
read PORT

wp core install --url=localhost:$PORT --title=BanaForward --admin_user=ifis --admin_password=sifisifi --admin_email=admin@is.de

#wp theme install ../Avada.zip --activate
#wp theme install ../Avada-Child-Theme.zip --activate
#wp theme delete twentytwenty

#wp plugin delete hello
#wp plugin delete akismet
#wp plugin activate mailhog

wp language core install de_DE
wp site switch-language de_DE
