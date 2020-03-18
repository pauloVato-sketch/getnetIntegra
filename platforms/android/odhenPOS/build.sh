export http_proxy="http://skype:rosadesaron@192.168.122.3:8080"
export https_proxy="http://skype:rosadesaron@192.168.122.3:8080"
export proxy="http://skype:rosadesaron@192.168.122.3:8080"

export HTTP_PROXY="http://skype:rosadesaron@192.168.122.3:8080"
export HTTPS_PROXY="http://skype:rosadesaron@192.168.122.3:8080"
export PROXY="http://skype:rosadesaron@192.168.122.3:8080"

cd mobile
#rm -rf node_modules
rm -rf package-lock.json
npm install
grunt
cd ..
if [ ! -d androidWebView ]; then
    git clone http://192.168.122.56/zeedhi/androidWebView.git
fi
cd androidWebView
git pull
git checkout odhen_webview
if [ ! -f ./gradle/wrapper/gradle-2.2.1-all.zip ]; then
	cp ./gradle/wrapper/dists/gradle-2.2.1-all.zip ./gradle/wrapper/gradle-2.2.1-all.zip	
fi
./gradlew clean assemble
