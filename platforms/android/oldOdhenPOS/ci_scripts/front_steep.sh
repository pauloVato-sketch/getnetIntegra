npm install
bower cache clean
rm -Rf bower_components
bower install
cd ../mobile
npm install
grunt test