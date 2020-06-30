# Webview Requests


## Descrição

* Interface criada para chamar os métodos disponíveis na webview.

## Instalação

 - Verifique se o seu `.bowerrc` está configurado
 - Caso necessário, configure também o proxy

 ```json
{
    "registry": {
        "search": [
            "http://192.168.122.56:5678/",
            "https://bower.herokuapp.com"
        ]
    }
}
```
 - Instale via bower

```bash
$ bower install --save zeedhi-webview-requests
```

- Chame a dependência no index.html:

```html
<script src="bower_components/jQuery/dist/jquery.min.js"></script>
<script src="bower_components/zeedhi-webview-requests/dist/zeedhi-webview-requests.min.js"></script>
```

## Como usar

### Login com Facebook

* Para criar um perfil de desenvolvedor e cadastrar seu aplicativo no serviço de login com o Facebook acesse: [Facebook Developer](https://developers.facebook.com/docs/facebook-login).
* Insira o Facebook App ID no build.properties citado no tópico "CONFIGURANDO O BUILD.PROPERTIES E GERANDO APK".
* Para comunicar entre o aplicativo do Facebook e a webview utilize o código javascript descrito abaixo.
* Recomenda-se que valide os dados no back-end, para exemplos, veja o seguinte repositório [BipFun](http://code.zeedhi.com/zeedhi-projects/bippop/tree/master)(backend>src>Controller,Service>Auth).

```js
this.loginFacebook = function() {
	webViewInterface.loginFacebook()
	.then(function(user) {
		// put your code here 
	})
	.catch(function(err) {
		//put your code here
	});
};
```

* Propriedades do objeto `user` (As propriedades podem mudar de acordo com as autorizações do usuário)

| Propriedade   | Exemplo |
|---------------|---------|
| id            | 0123 |
| name          | Lucas Lacerda | 
| email         | lucas.lacerda@teknisa.com | 
| gender        | male | 
| accessToken   | ytUTutuTYTutUYtuUtgUGUYBnKLJNlmlkmKLjhlNkjlKLnKLBNlbLJJHNjlBJl | 

### Selecionando imagem da galeria

```js
this.uploadImage = function() {
	webViewInterface.uploadImage()
	.then(function(img) {
		//put your code here  
	})
	.catch(function(err) {
		//put your code here
	});
};
```

* Propriedades do objeto `img`

| Propriedade   | Exemplo |
|---------------|---------|
| name            | image.png |
| type            | image/png | 
| size            | 758120 | 
| b64File         | data:image/png;base64,tIgFGTTFuibBonUhguHPkmOPJopigujBPIHHPijkmP== | 

### Ler cartão de crédito com a camera

```js
this.scanCreditCard = function() {
	webViewInterface.scanCreditCard()
	.then(function(creditCard) {
		//put your code here
	})
	.catch(function(err) {
		//put your code here
	});
};
```

* Propriedades do objeto `creditCard`

| Propriedade   | Exemplo |
|---------------|---------|
| number            | 4444 4444 4444 4444g |
| holderName        | LUCAS L C LACERDA | 
| cvv               | 000 | 
| month             | 03 | 
| year              | 2021 | 

### Ler código QR

```js
this.scanQRCode = function() {
	webViewInterface.scanQRCode()
	.then(function(qrCode) {
		//put your code here(Qrcode is already the value)
	})
	.catch(function(err) {
		//put your code here
	});
};
```

### Abrir aplicativos de navegação(Google Maps, Waze, etc).

* Insira o endereço que deseja ir como uma string. Ex.: "Rua Pernambuco, 1000 - Savassi, BH, MG, 30130-151, Brasil".
* Insira a chave da Api do seu aplicativo. 
* Para criar uma chave acesse: https://console.developers.google.com/apis/credentials?project=_
    * Assim que logar e entrar no dasboard vá até o lado esquerdo e clique na seção "Credenciais".
    * Clique em "Criar credenciais". Clique em "Chave de API".
    * No pop-up que surgir clique em "FECHAR".
    * Sua chave está criada, para renomear clique no lápis a direita da chave e edite para o nome que deseja.
* Não existe campo obrigatório, a pesquisa retorna sempre o primeiro resultado fornecido pelo Google.
* Quanto mais informações fornecidas mais precisa é a busca.

```js
this.navigationApps = function(address, myApiKey) {
	webViewInterface.navigationApps(address, myApiKey)
	.then(function() {
		//put your code here
	})
	.catch(function(err) {
		//put your code here
	});
};
```

### Obter a localização atual do dispositivo.
* Para criar uma chave acesse: https://console.developers.google.com/apis/credentials?project=_
    * Assim que logar e entrar no dasboard vá até o lado esquerdo e clique na seção "Credenciais".
    * Clique em "Criar credenciais". Clique em "Chave de API".
    * No pop-up que surgir clique em "FECHAR".
    * Sua chave está criada, para renomear clique no lápis a direita da chave e edite para o nome que deseja.

```js
this.currentLocation = function(myApiKey) {
	webViewInterface.currentLocation(myApiKey)
	.then(function(address) {
		//put your code here
	})
	.catch(function(err) {
		//put your code here
	});
};
```

* Propriedades do objeto `address`

```json
{
   "results" : [
      {
         "address_components" : [
            {
               "long_name" : "1000",
               "short_name" : "1000",
               "types" : [ "street_number" ]
            },
            {
               "long_name" : "Rua Pernambuco",
               "short_name" : "R. Pernambuco",
               "types" : [ "route" ]
            },
            {
               "long_name" : "Savassi",
               "short_name" : "Savassi",
               "types" : [ "political", "sublocality", "sublocality_level_1" ]
            },
            {
               "long_name" : "Belo Horizonte",
               "short_name" : "Belo Horizonte",
               "types" : [ "administrative_area_level_2", "political" ]
            },
            {
               "long_name" : "Minas Gerais",
               "short_name" : "MG",
               "types" : [ "administrative_area_level_1", "political" ]
            },
            {
               "long_name" : "Brasil",
               "short_name" : "BR",
               "types" : [ "country", "political" ]
            },
            {
               "long_name" : "30130",
               "short_name" : "30130",
               "types" : [ "postal_code", "postal_code_prefix" ]
            }
         ],
         "formatted_address" : "R. Pernambuco, 1000 - Savassi, Belo Horizonte - MG, Brasil",
         "geometry" : {
            "bounds" : {
               "northeast" : {
                  "lat" : -19.9356348,
                  "lng" : -43.9349862
               },
               "southwest" : {
                  "lat" : -19.9360402,
                  "lng" : -43.935465
               }
            },
            "location" : {
               "lat" : -19.9358375,
               "lng" : -43.9352256
            },
            "location_type" : "ROOFTOP",
            "viewport" : {
               "northeast" : {
                  "lat" : -19.9344885197085,
                  "lng" : -43.9338766197085
               },
               "southwest" : {
                  "lat" : -19.9371864802915,
                  "lng" : -43.9365745802915
               }
            }
         },
         "partial_match" : true,
         "place_id" : "ChIJYYT0QtuZpgARMxzuHuRFj8M",
         "types" : [ "premise" ]
      }
   ],
   "status" : "OK"
}
```

### Realizar transação no terminal Poynt
* A variável `type` precisa ser um dos tipo abaixo:
    * `C`: Crédito
    * `D`: Débito
    * `V`: Voucher

```js
this.doPayment = function(value, transaction_id, type) {
    webViewInterface.redePoyntDoPayment(value, transaction_id, type)
    .then(function(success) {
        //put your code here
    })
    .catch(function(err) {
        //put your code here
    });
};
```

### Realizar reimpressão no terminal Poynt

```js
this.onReprint = function() {
    webViewInterface.redePoyntOnReprint()
    .then(function(success) {
        //put your code here
    })
    .catch(function(err) {
        //put your code here
    });
};
```

### Realizar estorno no terminal Poynt

```js
this.onReversal = function() {
    webViewInterface.redePoyntOnReversal()
    .then(function(success) {
        //put your code here
    })
    .catch(function(err) {
        //put your code here
    });
};
```

### Realizar impressão no terminal Poynt

```js
this.print = function(content, qrCode, footer) {
    webViewInterface.redePoyntPrint(content, qrCode, footer)
    .then(function(success) {
        //put your code here
    })
    .catch(function(err) {
        //put your code here
    });
};
```

### Minimizar o aplicativo no terminal Poynt

```js
this.hideApp = function() {
    webViewInterface.redePoyntHideApp();
};
```