package com.teknisa.tef

import android.app.Dialog
import android.content.Context
import android.os.Bundle
import android.view.View
import android.view.ViewGroup
import android.view.Window
import android.view.WindowManager
import android.widget.Button
import android.widget.TextView
import android.widget.Toast
import com.odhen.POS.R


class CustomDialog(context: Context?): Dialog(context!!) {

    private var alterText: TextView? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        requestWindowFeature(Window.FEATURE_NO_TITLE)
        setContentView(R.layout.custom_dialog)
        alterText = findViewById<View>(R.id.textview_message) as TextView
        val buttonContato = findViewById<View>(R.id.btn_cancel) as Button
        buttonContato.setOnClickListener {
            //Toast.makeText(context, alterText?.text.toString(), Toast.LENGTH_SHORT)
            //       .show()
            this.dismiss()
        }
    }

    override fun onStart() {
        super.onStart()
        window?.setLayout(ViewGroup.LayoutParams.MATCH_PARENT, ViewGroup.LayoutParams.WRAP_CONTENT)
        window?.setFlags(WindowManager.LayoutParams.FLAG_NOT_TOUCH_MODAL,
                WindowManager.LayoutParams.FLAG_NOT_TOUCH_MODAL);
        window?.clearFlags(WindowManager.LayoutParams.FLAG_DIM_BEHIND);
    }
    fun setMessage(message : String){
        alterText?.text = message
    }

}