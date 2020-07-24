package com.odhen.stoneintegration;

import android.content.Context;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.util.Base64;

import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

public class Util {

    public interface CallBack {
        void run();
    }

    public interface Promise {
        Promise then(CallBack cb);
    }

    public static String readFileFromRawDirectory(int resourceId, Context mContext){
        InputStream iStream = mContext.getResources().openRawResource(resourceId);
        ByteArrayOutputStream byteStream = null;
        try {
            byte[] buffer = new byte[iStream.available()];
            iStream.read(buffer);
            byteStream = new ByteArrayOutputStream();
            byteStream.write(buffer);
            byteStream.close();
            iStream.close();
        } catch (IOException e) {
            e.printStackTrace();
        }
        return byteStream != null ? byteStream.toString() : "";
    }

    public static int stringMoneyToCents(String string) {
        try {
            float value = Float.valueOf(string);
            return (int) (value * 100);
        } catch (NumberFormatException e) {
            return 0;
        }
    }

    public static String removeSpecialCharacters(String string) {
        String newString;
        newString = Pattern.compile("[ÁÀÃÂ]").matcher(string).replaceAll("A");
        newString = Pattern.compile("[ÉÈÊ]").matcher(newString).replaceAll("E");
        newString = Pattern.compile("[ÍÌÎ]").matcher(newString).replaceAll("I");
        newString = Pattern.compile("[ÓÒÕÔ]").matcher(newString).replaceAll("O");
        newString = Pattern.compile("[ÚÙÛ]").matcher(newString).replaceAll("U");

        return newString;
    }

    public static Bitmap base64ToBitmap(String base64) {
        byte[] decodedString = Base64.decode(base64, Base64.DEFAULT);
        return BitmapFactory.decodeByteArray(decodedString, 0, decodedString.length);
    }

}
