# Container starten
- docker-compose up -d

# Überblick verschaffen
- docker-compose images
- docker-compose ps
- docker images
- docker rmi <ID>
- docker ps
- docker stop <ID>

# Herunterfahren
- docker-compose down -v 

# CLI auführen
- dokcer-compose ps
- docker run -it --rm  --volumes-from projekta_wordpress_1 --network container:projekta_wordpress_1  wordpress:cli user list

- docker run -it --rm  --volumes-from projekta_wordpress_1 --network container:projekta_wordpress_1  wordpress:cli /root/install.sh


# Hilfen
- docker exec -it projekta_wordpress_1 /bin/bash
