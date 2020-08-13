package com.odhen.stoneintegration.pos;

//import br.com.softwareexpress.sitef.android.CliSiTef;
//import br.com.softwareexpress.sitef.android.CliSiTefI;
//import br.com.softwareexpress.sitef.android.ICliSiTefListener;
//
//import org.apache.cordova.CallbackContext;
//
//import android.content.Context;
//
//import com.teknisa.tef.PaymentData;
//import com.teknisa.tef.TransactionMessenger;
//import com.teknisa.tef.com.odhen.stoneintegration.printer.OldGertecPrinter;
//import com.teknisa.tef.com.odhen.stoneintegration.printer.Printer;
//
//import org.json.JSONArray;
//import org.json.JSONException;
//import org.json.JSONObject;

public class SitefIntegration /* implements ICliSiTefListener */ {

//    protected static SitefIntegration instance;
//    protected PaymentData paymentData;
//    protected CallbackContext callbackContext = null;
//    protected CliSiTef sitef  = null;
//    protected Context context = null;
//    static TransactionMessenger transactionMessenger;
//    protected Printer com.odhen.stoneintegration.printer;
//
//    protected String givenCommands = "";
//    protected String couponText    = "";
//    protected boolean isFinishing  = false;
//
//    protected String documentId = "";
//    protected String cardNumber = "";
//    protected String cardBrand  = "";
//    protected String authorizationCode = "";
//    protected String cardHolderName = "";
//
//    public static void init(Context mContext, TransactionMessenger transactionMessenger) {
//        getInstance().fixVirtualPinpadOnCliSiTef(mContext);
//        getInstance().initOldPrinter(mContext);
//        SitefIntegration.transactionMessenger = transactionMessenger;
//    }
//
//    /**
//     * Correção para que o pinpad virtual funcione na implementação SitefIntegration
//     * Obs: Precisa ser chamado no onCreate da Activity
//     */
//    private void fixVirtualPinpadOnCliSiTef(Context mContext) {
//        try {
//            SitefIntegration.getSitefInstance(mContext).setActivity(transactionMessenger.getActivity());
//        } catch (Exception e) { }
//    }
//
//    /**
//     * Inicializa a biblioteca de impressão para que funcione com o firmware antigo da GPOS700
//     */
//    private void initOldPrinter(Context mContext) {
//        new Thread(){
//            @Override
//            public void run() {
//            OldGertecPrinter.com.odhen.stoneintegration.printer = new wangpos.sdk4.libbasebinder.Printer(mContext);
//            }
//        }.start();
//    }
//
//    /**
//     * Devolve a instância do SitefIntegration
//     * @return
//     */
//    public static SitefIntegration getInstance() {
//        return instance != null ? instance : new SitefIntegration();
//    }
//
//    /**
//     * Devolve a instância corrente do CliSiTef
//     * Caso ela ainda não exista, ela será instanciada
//     * @param applicationContext
//     * @return
//     */
//    public static CliSiTef getSitefInstance(Context applicationContext) {
//        if (CliSiTef.getInstance() == null) {
//            return new CliSiTef(applicationContext);
//        } else {
//            return CliSiTef.getInstance();
//        }
//    }
//
//    /**
//     * Devolve a instância corrente do CliSiTef
//     * Caso ela ainda não exista, ela será instanciada
//     * @return
//     */
//    public static CliSiTef getSitefInstance(WebViewImpl webview) {
//        if (CliSiTef.getInstance() == null) {
//            return new CliSiTef(context);
//        } else {
//            return CliSiTef.getInstance();
//        }
//    }
//
//    /**
//     * Configura o CliSiTef e chama o início da transação
//     * @throws JSONException
//     * @throws InterruptedException
//     */
//    private void payment(JSONArray args) throws JSONException, InterruptedException {
//        JSONObject data      = new JSONObject(args.getString(0));
//        paymentData          = new PaymentData(data);
//        int sts = sitef.configure(paymentData.getIp(), paymentData.getStore(),
//                paymentData.getTerminal(), paymentData.getParams());
//
//        if(sts == 0) {
//            startTransaction();
//        } else {
//            callbackContext.error("Erro ao configurar: " + sts);
//        }
//    }
//
//    /**
//     * Inicia a transação financeira. Os métodos de callback onData e onTransactionResult serão chamados
//     * @throws InterruptedException
//     * @throws JSONException
//     */
//    private void startTransaction() throws InterruptedException, JSONException {
//        String restricoes = "";
//        int sts = sitef.startTransaction(
//                this, paymentData.getType(), paymentData.getValue(),
//                paymentData.getInvoice(), paymentData.getDate(),
//                paymentData.getHour(), paymentData.getOperator(),
//                restricoes
//        );
//
//        if (sts != 10000) {
//            callbackContext.error("Erro ao iniciar: " + getErrorMessageFromStatus(sts) + " (" + sts + ")");
//        }
//    }
//
//    /**
//     * Método chamado sempre que um dado novo é devolvido pelo CliSiTef
//     * @param currentStage
//     * @param command
//     * @param fieldId
//     * @param minLength
//     * @param maxLength
//     * @param input
//     */
//    @Override
//    public void onData(int currentStage, int command, int fieldId, int minLength, int maxLength, byte [] input) {
//        try {
//            if (instance.identifyCommand(command, minLength, maxLength)) {
//                sitef.continueTransaction(sitef.getBuffer());
//            } else {
//                //sitef.abortTransaction(0);
//            }
//        } catch (JSONException e) {
//            callbackContext.error(e.getMessage());
//        }
//    }
//
//    /**
//     * Chamado após o usuário escolher uma opção do menu para continuar com a transação
//     * @param args
//     * @throws JSONException
//     * @throws InterruptedException
//     */
//    private void continueTransaction(JSONArray args) throws JSONException, InterruptedException {
//        try {
//            JSONObject data = new JSONObject(args.getString(0));
//            String buffer = data.get("buffer").toString();
//
//            if (buffer.equals("null")) {
//                abortTransaction();
//            } else {
//                sitef.setBuffer(buffer);
//                sitef.continueTransaction(sitef.getBuffer());
//            }
//        } catch (Exception e) {
//            callbackContext.error(e.getMessage());
//        }
//    }
//
//    /**
//     * Cancela a transação sitef corrente
//     */
//    private void abortTransaction() {
//        sitef.abortTransaction(1);
//    }
//
//    /**
//     * Devolve uma instância do com.odhen.stoneintegration.printer
//     * Caso ele ainda não tenha sido inicializado, uma nova instância será criada
//     */
//    private Printer getPrinterInstance() {
//        if (this.com.odhen.stoneintegration.printer == null)
//            this.com.odhen.stoneintegration.printer = new OldGertecPrinter(WebViewImpl.getInstance());
//        return this.com.odhen.stoneintegration.printer;
//    }
//
//    /**
//     * Imprime um código QR com a semente recebida
//     * Recebe no JSON o atributo seed
//     * @param args
//     * @throws JSONException
//     */
//    private void printQrCode(JSONArray args) throws JSONException {
//        try {
//            Printer com.odhen.stoneintegration.printer = this.getPrinterInstance();
//            JSONObject data = new JSONObject(args.getString(0));
//            String seed     = data.get("seed").toString();
//
//            com.odhen.stoneintegration.printer.printQrCode(seed);
//            Thread.sleep(1000);
//            transactionMessenger.customLoadUrl("javascript:onReturnValue('', 0)");
//        } catch (Exception e) {
//            callbackContext.error(e.getMessage());
//        }
//    }
//
//    /**
//     * Imprime um código de barras com a semente recebida
//     * Recebe no JSON os atributos seed e isLast(caso true, imprimirá um espaço ao final da impressão
//     * @param args
//     * @throws JSONException
//     */
//    private void printBarCode(JSONArray args) throws JSONException {
//        try {
//            Printer com.odhen.stoneintegration.printer = this.getPrinterInstance();
//            JSONObject data       = new JSONObject(args.getString(0));
//            String seed           = data.get("seed").toString();
//            String isLast         = data.get("isLast").toString();
//
//            com.odhen.stoneintegration.printer.printText("", 36, "CENTER", "NORMAL");
//            com.odhen.stoneintegration.printer.printBarCode(seed);
//            Thread.sleep(800);
//            if (isLast.equals("true")) {
//                com.odhen.stoneintegration.printer.printText("\n", 72);
//            }
//            transactionMessenger.onReturnValue("", 0);
//        } catch (Exception e) {
//            callbackContext.error(e.getMessage());
//        }
//    }
//
//    /**
//     * Imprime o texto enviado por parâmetro
//     * Recebe no JSON os atributos text, fontSize, textAlign, fontWeight
//     * @param args
//     * @throws JSONException
//     */
//    private void printText(JSONArray args) throws JSONException {
//        try {
//            Printer com.odhen.stoneintegration.printer = this.getPrinterInstance();
//            JSONObject data       = new JSONObject(args.getString(0));
//            String text           = data.get("text").toString();
//            int fontSize          = Integer.parseInt(data.get("fontSize").toString());
//            String fontWeight     = data.get("fontWeight").toString();
//            String textAlign      = data.get("textAlign").toString();
//
//            for (String s : text.split("\n", -1))
//                com.odhen.stoneintegration.printer.printText(s, fontSize, textAlign, fontWeight);
//
//            transactionMessenger.customLoadUrl("javascript:onReturnValue('', 0)");
//        } catch (Exception e) {
//            transactionMessenger.customLoadUrl("javascript:onReturnValue(" + e.getMessage() + ", " + e.getMessage() + ")");
//        }
//    }
//
//    /**
//     * Imprime o cupom fiscal da transação recém-realizada
//     */
//    private void printCoupon() {
//        try {
//            Printer com.odhen.stoneintegration.printer = this.getPrinterInstance();
//
//            for (String s : this.couponText.split("\n", -1))
//                com.odhen.stoneintegration.printer.printText(s, 16, "CENTER", "NORMAL");
//
//            com.odhen.stoneintegration.printer.printText("\n", 72);
//
//            transactionMessenger.customLoadUrl("javascript:onReturnValue('', 0)");
//        } catch (Exception e) {
//            callbackContext.error(e.getMessage());
//        }
//    }
//
//    /**
//     * Chamado ao final da transação
//     * @param currentStage
//     * @param resultCode
//     */
//    public void onTransactionResult(int currentStage, int resultCode) {
//        try {
//            if (!this.isFinishing && resultCode == 0) {
//                int sts = sitef.finishTransaction(1, paymentData.getInvoice(), paymentData.getDate(),
//                        paymentData.getHour(), paymentData.getParams());
//                this.isFinishing = true;
//                return ;
//            }
//
//            transactionMessenger.customLoadUrl("javascript:onTransactionMessage('')");
//
//            if (resultCode == 0) {
//                transactionMessenger.customLoadUrl("javascript:onReturnValue('Transação efetuada com sucesso!', 0, '" + documentId + "', '" + cardNumber + "', '" + cardBrand + "', '" + authorizationCode + "')");
//            }
//            else {
//                transactionMessenger.customLoadUrl("javascript:onReturnValue('" + getErrorMessageFromStatus(resultCode) + "', " + resultCode + ")");
//            }
//
//            givenCommands = "";
//        } catch (JSONException e) { }
//        catch (Exception e) {
//            callbackContext.error("Erro ao completar transação: " + e.getMessage());
//        }
//    }
//
//    /**
//     * Identifica o comando devolvido pelo CliSiTef e determina o que fazer com ele
//     * Devolve true se a transação deve ser continuada imediatamente ou false se ela deve esperar
//     * o usuário continuar manualmente
//     * @param command
//     * @throws JSONException
//     * @return
//     */
//    private boolean identifyCommand(int command, int minLength, int maxLength) throws JSONException {
//        String buffer = sitef.getBuffer();
//        int tipoCampo = sitef.getFieldId();
//
//        instance.givenCommands += command + ", ";
//
//        switch (command) {
//            /* Exibir Título do Menu */
//            case CliSiTefI.CMD_TITULO_MENU:
//                transactionMessenger.customLoadUrl("javascript:setMenuTitle('" + buffer + "')");
//                return true;
//            /* Remover Título do Menu */
//            case CliSiTefI.CMD_REMOVE_TITULO_MENU:
//            case CliSiTefI.CMD_REMOVE_CABECALHO:
//                transactionMessenger.customLoadUrl("javascript:setMenuTitle('')");
//                return true;
//            /* Exibir Mensagem */
//            case CliSiTefI.CMD_MENSAGEM_OPERADOR:
//            case CliSiTefI.CMD_MENSAGEM_CLIENTE:
//            case CliSiTefI.CMD_MENSAGEM:
//                transactionMessenger.customLoadUrl("javascript:onTransactionMessage('" + buffer + "')");
//                return true;
//            /* Remover Mensagem*/
//            case CliSiTefI.CMD_REMOVE_MENSAGEM_OPERADOR:
//            case CliSiTefI.CMD_REMOVE_MENSAGEM_CLIENTE:
//            case CliSiTefI.CMD_REMOVE_MENSAGEM:
//                transactionMessenger.customLoadUrl("javascript:onTransactionMessage('')");
//                return true;
//            /* Obter Cupom Fiscal*/
//            case CliSiTefI.CMD_RETORNO_VALOR:
//                if (tipoCampo == 121 /* || tipoCampo == 122 */)
//                    couponText = buffer;
//                if (tipoCampo == 134)
//                    documentId = buffer;
//                if (tipoCampo == 136)
//                    cardNumber = buffer;
//                if (tipoCampo == 1190)
//                    cardNumber = cardNumber + "******" + buffer;
//                if (tipoCampo == 135)
//                    authorizationCode = buffer;
//                if (tipoCampo == 1003)
//                    cardHolderName = buffer;
//                if (tipoCampo == 2053)
//                    cardBrand = buffer;
//                return true;
//            /* Prosseguir automaticamente em alguns casos*/
//            case CliSiTefI.CMD_OBTEM_QUALQUER_TECLA:
//            case CliSiTefI.CMD_PERGUNTA_SE_INTERROMPE:
//                sitef.setBuffer("0");
//                return true;
//            /* Confirmação Antes de Cancelar Transação */
//            case CliSiTefI.CMD_CONFIRMA_CANCELA:
//                transactionMessenger.customLoadUrl("javascript:promptBoolean('" + buffer + "')");
//                return false;
//            /* Pedir Entrada do Usuário */
//            case CliSiTefI.CMD_OBTEM_CAMPO:
//            case CliSiTefI.CMD_OBTEM_VALOR:
//            case CliSiTefI.CMD_SELECIONA_MENU:
//                transactionMessenger.customLoadUrl("javascript:promptCommand('" + buffer + "', " + minLength + ", " + maxLength + ", " + tipoCampo + ")");
//                return false;
//        }
//
//        return true;
//    }
//
//    /**
//     * Devolve uma string com um separador de linhas específico para a forma que será exibido
//     * @param buffer
//     * @param lineBreakString
//     * @return
//     */
//    protected String handleLineBreaks(String buffer, String lineBreakString) {
//        String newString = "";
//        if (buffer.contains(System.getProperty("line.separator"))) {
//            String[] splittedString = buffer.split(System.getProperty("line.separator"));
//            for (String s : splittedString) {
//                newString += s + lineBreakString;
//            }
//        } else {
//            newString = buffer;
//        }
//
//        return newString;
//    }
//
//    /**
//     * Devolve a mensagem de erro referente ao código informado por parâmetro
//     * @param status
//     * @return
//     */
//    protected String getErrorMessageFromStatus(int status) {
//        String message;
//
//        if(status > 0) {
//            message = "Transação negada pelo autorizador.";
//        } else {
//            switch(status) {
//                case -1:
//                    message = "Módulo não inicializado. O PDV tentou chamar alguma rotina sem antes executar a função configura.";
//                    break;
//                case -2:
//                    message = "Operação cancelada pelo operador.";
//                    break;
//                case -3:
//                    message = "O parãmetro / função / modalidade é inexistente/inválido.";
//                    break;
//                case -4:
//                    message = "Falta de memória no PDV.";
//                    break;
//                case -5:
//                    message = "Sem conexão SiTef.";
//                    break;
//                case -6:
//                    message = "Operação cancelada pelo usuário.";
//                    break;
//                case -7:
//                case -42:
//                    message = "Reservado (" + status + ").";
//                    break;
//                case -8:
//                    message = "A CliSiTef não possui a implementação da função necessária, provavelmente está desatualizada (a CliSiTefI é mais recente).";
//                    break;
//                case -9:
//                    message = "A automação chamou a rotina ContinuaFuncaoSiTefInterativo sem antes iniciar uma função iterativa.";
//                    break;
//                case -10:
//                    message = "Algum parÃ¢metro obrigatório não foi passado pela automação comercial.";
//                    break;
//                case -12:
//                    message = "Erro na execução da rotina iterativa. Provavelmente o processo iterativo anterior não foi executado até o final (enquanto o retorno for igual a 10000).";
//                    break;
//                case -13:
//                    message = "Documento fiscal não encontrado nos registros da CliSiTef. Retornado em funções de consulta tais como ObtemQuantidadetransaçõesPendentes.";
//                    break;
//                case -15:
//                    message = "Operação cancelada pela automação comercial.";
//                    break;
//                case -20:
//                    message = "Parâmetro inválido passado para a função.";
//                    break;
//                case -21:
//                    message = "Utilizada uma palavra proibida, por exemplo SENHA, para coletar dados em aberto no pinpad. Por exemplo na função ObtemDadoPinpadDiretoEx.";
//                    break;
//                case -25:
//                    message = "Erro no Correspondente Bancário: Deve realizar sangria.";
//                    break;
//                case -30:
//                    message = "Erro de acesso ao arquivo. Certifique-se que o usuário que roda a aplicação tem direitos de leitura/escrita.";
//                    break;
//                case -40:
//                    message = "transação negada pelo SiTef";
//                    break;
//                case -41:
//                    message = "Dados inválidos.";
//                    break;
//                case -43:
//                    message = "Problema na execução de alguma das rotinas no pinpad.";
//                    break;
//                case -50:
//                    message = "transação não segura.";
//                    break;
//                case -100:
//                    message = "Erro interno do módulo.";
//                    break;
//                case -500:
//                    message = "Erro na execução da Thread.";
//                    break;
//                default:
//                    message = "Transação negada.";
//                    break;
//            }
//        }
//
//        return message;
//    }

}