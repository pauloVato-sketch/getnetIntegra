if ! [ -f ../backend/environment_bkp.xml ]; then
mv ../backend/app.json ../backend/app_bkp.json
mv ../backend/app_test.json ../backend/app.json
fi