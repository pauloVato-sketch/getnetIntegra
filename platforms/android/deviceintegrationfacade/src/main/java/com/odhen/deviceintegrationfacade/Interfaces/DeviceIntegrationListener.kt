package com.odhen.deviceintegrationfacade.Interfaces

import android.content.Intent

interface DeviceIntegrationListener {
    fun onIntegrationResult(requestCode: Int, resultCode: Int, data: Intent?)
}