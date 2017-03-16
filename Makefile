all: docker-compose.yml webapp/config/cookie-secret.php
	docker-compose build

docker-compose.yml: docker-compose.in.yml secrets/github-token.txt
	cat $< | \
	  sed \
	    -e s/%GITHUB_TOKEN%/$(shell cat secrets/github-token.txt)/ \
	    -e s/%GIT_REVISION%/$(shell git log -n 1 --format=%H)/ \
	  > $@

webapp/config/cookie-secret.php:
	echo '<?php' > $@
	echo "return '"$(shell head -c 32 /dev/urandom | base64 | tr '+/=' '-_.')"';" >> $@
