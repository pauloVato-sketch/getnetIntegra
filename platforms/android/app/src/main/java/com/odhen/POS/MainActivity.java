/*
       Licensed to the Apache Software Foundation (ASF) under one
       or more contributor license agreements.  See the NOTICE file
       distributed with this work for additional information
       regarding copyright ownership.  The ASF licenses this file
       to you under the Apache License, Version 2.0 (the
       "License"); you may not use this file except in compliance
       with the License.  You may obtain a copy of the License at

         http://www.apache.org/licenses/LICENSE-2.0

       Unless required by applicable law or agreed to in writing,
       software distributed under the License is distributed on an
       "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
       KIND, either express or implied.  See the License for the
       specific language governing permissions and limitations
       under the License.
 */

package com.odhen.POS;

import android.content.Intent;
import android.os.Bundle;
import android.util.Log;

import com.getnet.posdigital.PosDigital;
import com.odhen.deviceintegrationfacade.Interfaces.DeviceIntegrationListener;

import org.apache.cordova.*;

public class MainActivity extends CordovaActivity
{

    private DeviceIntegrationListener deviceIntegrationListener = null;

    private PosDigital.BindCallback bindCallback = new PosDigital.BindCallback(){
        @Override
        public void onError(Exception e) { Log.d("TAGG","error"); }
        @Override
        public void onConnected() {
            Log.d("TAGG","blyat");
        }
        @Override
        public void onDisconnected() {
            Log.d("TAGG","disco");
        }
    };

    @Override
    public void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);

        // enable Cordova apps to be started in the background

        Bundle extras = getIntent().getExtras();
        if (extras != null && extras.getBoolean("cdvStartInBackground", false)) {
            moveTaskToBack(true);
        }
        connectPosDigitalService();

        // Set by <content src="index.html" /> in config.xml
        loadUrl(launchUrl);
    }

    private void connectPosDigitalService() {
        PosDigital.register(this, bindCallback);
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent intent) {
        super.onActivityResult(requestCode, resultCode, intent);
        deviceIntegrationListener.onIntegrationResult(requestCode, resultCode, intent);
    }

    public DeviceIntegrationListener getDeviceIntegrationListener() {
        return deviceIntegrationListener;
    }

    public void setDeviceIntegrationListener(DeviceIntegrationListener deviceIntegrationListener) {
        this.deviceIntegrationListener = deviceIntegrationListener;
    }

    @Override
    public void onDestroy() {
        super.onDestroy();
        try {
            Log.d("TAGG","destroy");
            if (PosDigital.getInstance().isInitiated()) {
                PosDigital.unregister(this);
            }
        } catch (Exception e) {
            Log.e("TAGG", "Erro de exception no Destroy da Activity");
        }
    }
}
