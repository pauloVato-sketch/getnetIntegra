Como implementar um teste:

- criar uma classe extendendo a classe que você quer testar na pasta ./src/Test/Service;
- declarar a classe criada no arquivo ./services.test.xml copiando todas as injeções da classe original no arquivo ./services.xml;
- criar uma classe de teste na pasta ./tests com o nome Service.%Classe%.test.php;
- implementar os testes.