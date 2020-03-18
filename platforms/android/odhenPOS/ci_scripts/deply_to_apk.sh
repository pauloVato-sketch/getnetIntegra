rm -f androidWebView.tar.gz
rm toProduction -Rf 2> /dev/null
tar -zcvf toProduction.tar.gz ./*
tar -zxvf ./toProduction.tar.gz
wget http://cdn.zeedhi.com/deploy/commonFiles/androidWebView.tar.gz
rm -Rf androidWebView/
tar -zxvf androidWebView.tar.gz
mv androidWebView toProduction/
cd toProduction/
chmod +x gradlew
./gradlew assemble
