package com.teknisa.tef;

import org.json.JSONException;
import org.json.JSONObject;

public class PaymentData {

    private int paymentType;
    private int installmentsNumber;
    private String paymentValue;
    private String paymentInvoice;
    private String paymentIp;
    private String paymentStore;
    private String paymentTerminal;
    private String paymentDate;
    private String paymentHour;
    private String storeCnpj;
    private String automationCnpj;
    private String paymentOperator;
    private String tlsType;

    public PaymentData() {

    }

    public PaymentData(JSONObject data) throws JSONException {
        this.paymentType        = Integer.parseInt(data.get("paymentType").toString());
        this.installmentsNumber = Integer.parseInt(data.get("installmentsNumber").toString());
        this.paymentValue       = data.get("paymentValue").toString();
        this.paymentInvoice     = data.get("paymentInvoice").toString();
        this.paymentIp          = data.get("paymentIp").toString();
        this.paymentStore       = data.get("paymentStore").toString();
        this.paymentTerminal    = data.get("paymentTerminal").toString();
        this.paymentDate        = data.get("paymentDate").toString();
        this.paymentHour        = data.get("paymentHour").toString();
        this.storeCnpj          = data.get("storeCnpj").toString();
        this.automationCnpj     = data.get("automationCnpj").toString();
        this.paymentOperator    = data.get("paymentOperator").toString();
        this.tlsType            = data.get("tlsType").toString();
    }

    public int getType() {
        return paymentType;
    }

    public int getInstallmentsNumber() {
        return installmentsNumber;
    }

    public String getValue() {
        return paymentValue;
    }

    public String getInvoice() {
        return paymentInvoice;
    }

    public String getIp() {
        return paymentIp;
    }

    public String getStore() {
        return paymentStore;
    }

    public String getTerminal() {
        return paymentTerminal;
    }

    public String getDate() {
        return paymentDate;
    }

    public String getHour() {
        return paymentHour;
    }

    public String getStoreCnpj() {
        return storeCnpj;
    }

    public String getAutomationCnpj() {
        return automationCnpj;
    }

    public String getOperator() {
        return paymentOperator;
    }

    public String getParams() throws JSONException {
        switch (this.tlsType) {
            case "tls-softwareexpress":
                return "[ParmsClient=1="+storeCnpj+";2="+automationCnpj+";TipoPinPad=ANDROID_AUTO;[TipoComunicacaoExterna=SSL;CaminhoCertificadoCA=/sdcard/MYCA.PEM]]";
            case "tls-gsurf":
                return "[ParmsClient=1="+storeCnpj+";2="+automationCnpj+";TipoPinPad=ANDROID_AUTO]";
            default:
                return "[ParmsClient=1="+storeCnpj+";2="+automationCnpj+";TipoPinPad=ANDROID_AUTO]";
        }
    }

    public void setPaymentType(int paymentType) {
        this.paymentType = paymentType;
    }

    public void setInstallmentsNumber(int installmentsNumber) {
        this.installmentsNumber = installmentsNumber;
    }

    public void setPaymentValue(String paymentValue) {
        this.paymentValue = paymentValue;
    }

    public void setPaymentInvoice(String paymentInvoice) {
        this.paymentInvoice = paymentInvoice;
    }

    public void setPaymentIp(String paymentIp) {
        this.paymentIp = paymentIp;
    }

    public void setPaymentStore(String paymentStore) {
        this.paymentStore = paymentStore;
    }

    public void setPaymentTerminal(String paymentTerminal) {
        this.paymentTerminal = paymentTerminal;
    }

    public void setPaymentDate(String paymentDate) {
        this.paymentDate = paymentDate;
    }

    public void setPaymentHour(String paymentHour) {
        this.paymentHour = paymentHour;
    }

    public void setStoreCnpj(String storeCnpj) {
        this.storeCnpj = storeCnpj;
    }

    public void setAutomationCnpj(String automationCnpj) {
        this.automationCnpj = automationCnpj;
    }

    public void setPaymentOperator(String paymentOperator) {
        this.paymentOperator = paymentOperator;
    }

    public void setTlsType(String tlsType) {
        this.tlsType = tlsType;
    }
}