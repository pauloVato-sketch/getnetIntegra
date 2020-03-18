tar -zcvf toTest.tar.gz ./*
scp toTest.tar.gz root@192.168.122.54:/var/www/
ssh root@192.168.122.54 rm /var/www/{$1} -Rf
ssh root@192.168.122.54 mkdir /var/www/{$1}
ssh root@192.168.122.54 tar -zxvf /var/www/toTest.tar.gz -C /var/www/{$1}/