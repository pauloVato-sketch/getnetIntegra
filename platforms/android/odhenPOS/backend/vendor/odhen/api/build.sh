cat composer.lock | sed 's/git@192.168.122.56:/http:\/\/192.168.122.56\//g' > composerTmp.lock
mv composer.lock composer.bkp.lock
mv composerTmp.lock composer.lock
echo "Rodar a rotina de instalação das dependências do composer"
composer install
