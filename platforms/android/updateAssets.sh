rm -rf app/src/main/assets/www
git submodule update
cp -r odhenPOS/mobile app/src/main/assets/www
cp -r platform_www/* app/src/main/assets/www/
