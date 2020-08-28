cd ../backend
rm vendor -Rf
[ -f composer.lock ] && rm composer.lock || echo "File composer.lock not found"
composer install