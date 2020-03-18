rm toProduction.tar.gz 2> /dev/null
rm toTest.tar.gz -Rf 2> /dev/null
rm toProduction/ -Rf 2> /dev/null
tar -zcvf toProduction.tar.gz ./*
scp toProduction.tar.gz root@192.168.122.53:/var/www/
ssh root@192.168.122.53 rm /var/www/{$1} -Rf
ssh root@192.168.122.53 mkdir /var/www/{$1}
ssh root@192.168.122.53 tar -zxvf /var/www/toProduction.tar.gz -C /var/www/{$1}/