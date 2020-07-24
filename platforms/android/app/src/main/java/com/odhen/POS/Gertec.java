//package com.odhen.POS;
//
//import org.apache.cordova.CordovaPlugin;
//import org.apache.cordova.CallbackContext;
//
//import org.json.JSONArray;
//import org.json.JSONException;
//import org.json.JSONObject;
//
///**
// * This class echoes a string called from JavaScript.
// */
//public class Gertec extends CordovaPlugin {
//
//    private static final String PRINT_STRING = "printString";
//    private static final String PRINT_QR_CODE = "printQrCode";
//    private static final String PRINT_BAR_CODE = "printBarCode";
//
//    @Override
//    public boolean execute(String action, JSONArray args, CallbackContext callbackContext) throws JSONException {
//        GertecPrinter com.odhen.stoneintegration.printer = new GertecPrinter(this.cordova.getActivity().getApplicationContext());
//
//        if(action.equals(PRINT_STRING)) {
//            if (args != null) {
//                String text = args.getString(0);
//                com.odhen.stoneintegration.printer.printText(text, callbackContext);
//            } else {
//                callbackContext.success(getParamNotFoundMessage());
//                return true;
//            }
//        } else if(action.equals(PRINT_QR_CODE)) {
//            if(args != null) {
//                String text = args.getString(0);
//                com.odhen.stoneintegration.printer.printQrCode(text, callbackContext);
//            } else {
//                callbackContext.success(getParamNotFoundMessage());
//                return true;
//            }
//        } else if (action.equals(PRINT_BAR_CODE)) {
//            if(args != null) {
//                String text = args.getString(0);
//                com.odhen.stoneintegration.printer.printBarCode(text, callbackContext);
//            } else {
//                callbackContext.success(getParamNotFoundMessage());
//                return true;
//            }
//        }
//
//        return false;
//    }
//
//    private JSONObject getParamNotFoundMessage() throws JSONException {
//        JSONObject returnObj = new JSONObject();
//        returnObj.put("error", true);
//        returnObj.put("message", "Parâmetros não encontrados.");
//        return returnObj;
//    }
//}
