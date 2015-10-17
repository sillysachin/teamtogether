package com.talentspear.university;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.SharedPreferences;
import android.graphics.Typeface;
import android.os.Bundle;
import android.preference.PreferenceManager;
import android.support.v4.app.FragmentManager;
import android.support.v4.content.LocalBroadcastManager;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.Toolbar;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.inputmethod.InputMethodManager;
import android.widget.EditText;
import android.widget.Toast;

import com.daimajia.androidanimations.library.Techniques;
import com.daimajia.androidanimations.library.YoYo;
import com.github.florent37.materialviewpager.adapter.RecyclerViewMaterialAdapter;
import com.github.jorgecastilloprz.FABProgressCircle;
import com.google.android.gms.common.ConnectionResult;
import com.google.android.gms.common.GooglePlayServicesUtil;
import com.melnykov.fab.FloatingActionButton;
import com.nineoldandroids.animation.Animator;
import com.rey.material.app.Dialog;
import com.rey.material.app.DialogFragment;
import com.rey.material.app.SimpleDialog;

import com.talentspear.university.adapter.ContactsAdapter;
import com.talentspear.university.dbhandlers.StoreNotif;
import com.talentspear.university.ds.ContactsHolder;
import com.talentspear.university.gcm.QuickstartPreferences;
import com.talentspear.university.gcm.RegistrationIntentService;

import java.util.List;


public class RegisterActivity extends AppCompatActivity {

    private String s_usn = "";
    private String s_name = "";
    private String s_section = "";
    static FragmentManager fm;
    private int makeAnim = 1;
    static Context context;
    private static Runnable mytask;
    private int REQUEST_REGISTER = 2005;
    private boolean mIsSent = false;
    private static final int PLAY_SERVICES_RESOLUTION_REQUEST = 9000;
    private static final String TAG = "WelcomeActivity";
    static FABProgressCircle progress;
    private BroadcastReceiver mRegistrationBroadcastReceiver;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        // startService(new Intent(this, SilentService.class));
        SharedPreferences sharedPreferences =
                PreferenceManager.getDefaultSharedPreferences(getApplicationContext());
        if (sharedPreferences.getBoolean(QuickstartPreferences.SENT_TOKEN_TO_SERVER, false)) {
            StoreNotif storeNotif = new StoreNotif(getApplicationContext());
            startActivity(new Intent(RegisterActivity.this, NavActivity.class));
            finish();
        }

        setContentView(R.layout.activity_welcome);


        //call to alarmmgr method


        //UI setup
        Typeface mTf = Typeface.createFromAsset(this.getAssets(), "titillium_bold.ttf");
        final FloatingActionButton btn = (FloatingActionButton) findViewById(R.id.fab_login);
        final EditText name = (EditText) findViewById(R.id.name);
        final EditText usn = (EditText) findViewById(R.id.usn);
        progress= (FABProgressCircle) findViewById(R.id.fabProgressCircle_login);
        name.setTypeface(mTf);
        usn.setTypeface(mTf);
        name.setVisibility(View.INVISIBLE);
        usn.setVisibility(View.INVISIBLE);
        btn.setVisibility(View.INVISIBLE);
        context=this;
        Toolbar toolbar = (Toolbar) findViewById(R.id.login_toolbar);
        toolbar.setTitleTextColor(getResources().getColor(R.color.white));
        fm=getSupportFragmentManager();
        toolbar.setTitle("TALENTSPEAR UNIVERSITY");

        if (makeAnim == 1) {
            YoYo.with(Techniques.SlideInUp)
                    .duration(700).withListener(new Animator.AnimatorListener() {

                @Override
                public void onAnimationStart(Animator animation) {
                }

                @Override
                public void onAnimationEnd(Animator animation) {
                    name.setVisibility(View.VISIBLE);
                    YoYo.with(Techniques.FlipInX)
                            .duration(700).playOn(findViewById(R.id.name));
                    usn.setVisibility(View.VISIBLE);
                    YoYo.with(Techniques.FlipInX)
                            .duration(700).withListener(new Animator.AnimatorListener() {
                        @Override
                        public void onAnimationStart(Animator animation) {

                        }

                        @Override
                        public void onAnimationEnd(Animator animation) {
                            btn.setVisibility(View.VISIBLE);
                            YoYo.with(Techniques.BounceInUp)
                                    .duration(700).playOn(btn);
                        }

                        @Override
                        public void onAnimationCancel(Animator animation) {

                        }

                        @Override
                        public void onAnimationRepeat(Animator animation) {

                        }
                    })
                            .playOn(findViewById(R.id.usn));
                }

                @Override
                public void onAnimationCancel(Animator animation) {

                }

                @Override
                public void onAnimationRepeat(Animator animation) {

                }


            })
                    .playOn(findViewById(R.id.logImg));
        }

        btn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                InputMethodManager imm = (InputMethodManager) getSystemService(
                        Context.INPUT_METHOD_SERVICE);
                imm.hideSoftInputFromWindow(btn.getWindowToken(), 0);

                s_name = name.getText().toString();
                s_usn = usn.getText().toString().trim().toUpperCase();
                if(s_name.length()==0)
                {
                    name.setError("Name cannot be blank");
                }
                if(!s_usn.trim().matches("[0-9a-zA-Z][a-zA-Z][a-zA-Z][0-9]{2}[a-zA-Z]{2}[0124][0-9]{2}([A-Za-z][0-9]{2})?"))
                {
                    usn.setError("Enter valid USN");
                }
                if (s_name.length() != 0 && s_usn.trim().matches("[0-9a-zA-Z][a-zA-Z][a-zA-Z][0-9]{2}[a-zA-Z]{2}[0124][0-9]{2}([A-Za-z][0-9]{2})?")) {

                    if((s_usn.substring(5,7).equals("ME")||s_usn.substring(5,7).equals("EC"))&&s_usn.length()==10)
                    {
                        Dialog.Builder builder = new SimpleDialog.Builder(R.style.SimpleDialogLight) {
                            @Override
                            public void onPositiveActionClicked(DialogFragment fragment) {
                                s_section=getSelectedValue().toString();
                                serviceStarter();
                                super.onPositiveActionClicked(fragment);
                            }

                            @Override
                            public void onNegativeActionClicked(DialogFragment fragment) {
                                super.onNegativeActionClicked(fragment);
                            }
                        };

                        ((SimpleDialog.Builder) builder).items(new String[]{"1", "2"}, 0)
                                .title("Which Section?")
                                .positiveAction("OK")
                                .negativeAction("CANCEL");
                        DialogFragment fragment = DialogFragment.newInstance(builder);
                        fragment.setCancelable(true);
                        fragment.show(getSupportFragmentManager(), null);
                    }

                    else if(s_usn.length()==13)
                    {
                        Dialog.Builder builder = new SimpleDialog.Builder(R.style.SimpleDialogLight) {
                            @Override
                            public void onPositiveActionClicked(DialogFragment fragment) {
                                s_section=getSelectedValue().toString();
                                serviceStarter();
                                super.onPositiveActionClicked(fragment);
                            }

                            @Override
                            public void onNegativeActionClicked(DialogFragment fragment) {
                                super.onNegativeActionClicked(fragment);
                            }
                        };

                        ((SimpleDialog.Builder) builder).items(new String[]{"A", "B","C","D","E","F","G","H","I"}, 0)
                                .title("Which Section?")
                                .positiveAction("OK")
                                .negativeAction("CANCEL");
                        DialogFragment fragment = DialogFragment.newInstance(builder);
                        fragment.setCancelable(true);
                        fragment.show(getSupportFragmentManager(), null);
                    }
                    else
                    {
                        s_section="1";
                        serviceStarter();
                    }

                } else {
                    Toast.makeText(getApplicationContext(), "Enter proper details and try again", Toast.LENGTH_LONG).show();
                }
            }
        });


        mytask = new Runnable() {
            @Override
            public void run() {

                runOnUiThread(new Runnable() {
                    public void run() {
                        Toast.makeText(RegisterActivity.this,"Network error. Try again",Toast.LENGTH_SHORT).show();
                        if(progress.isShown())
                            progress.hide();
                    }
                });
            }
        };

    }


    @Override
    protected void onResume() {
        super.onResume();
        LocalBroadcastManager.getInstance(this).registerReceiver(mRegistrationBroadcastReceiver,
                new IntentFilter(QuickstartPreferences.REGISTRATION_COMPLETE));
    }

    @Override
    protected void onPause() {
        LocalBroadcastManager.getInstance(this).unregisterReceiver(mRegistrationBroadcastReceiver);
        super.onPause();
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        //getMenuInflater().inflate(R.menu.menu_welcome, menu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        // Handle action bar item clicks here. The action bar will
        // automatically handle clicks on the Home/Up button, so long
        // as you specify a parent activity in AndroidManifest.xml.
        int id = item.getItemId();

        //noinspection SimplifiableIfStatement
        if (id == R.id.action_settings) {
            return true;
        }

        return super.onOptionsItemSelected(item);
    }

    private boolean checkPlayServices() {
        int resultCode = GooglePlayServicesUtil.isGooglePlayServicesAvailable(this);
        if (resultCode != ConnectionResult.SUCCESS) {
            if (GooglePlayServicesUtil.isUserRecoverableError(resultCode)) {
                GooglePlayServicesUtil.getErrorDialog(resultCode, this,
                        PLAY_SERVICES_RESOLUTION_REQUEST).show();
            } else {
                Log.i(TAG, "This device is not supported.");
                Toast.makeText(RegisterActivity.this, "Install Play store to use this app", Toast.LENGTH_LONG).show();
                finish();
            }
            return false;
        }
        return true;
    }

    public static Context getRegistrationContext()
    {
        return context;
    }

    public static FABProgressCircle getProgress()
    {
        return progress;
    }

    public static Runnable getTask()
    {
        return mytask;
    }


    public static void stopProgress()
    {
        if(progress.isShown())
        progress.hide();
    }
    public static FragmentManager getSupportFM()
    {
        return fm;
    }

    public void serviceStarter()
    {
        final boolean[] sentToken = new boolean[1];
        //GCM Set-up
        mRegistrationBroadcastReceiver = new BroadcastReceiver() {
            @Override
            public void onReceive(Context context, Intent intent) {
                Toast.makeText(RegisterActivity.this, "Service Started", Toast.LENGTH_SHORT).show();
                SharedPreferences sharedPreferences =
                        PreferenceManager.getDefaultSharedPreferences(context);
                sentToken[0] = sharedPreferences
                        .getBoolean(QuickstartPreferences.SENT_TOKEN_TO_SERVER, false);
            }
        };
        SharedPreferences sharedPreferences =
                PreferenceManager.getDefaultSharedPreferences(getApplicationContext());
        sharedPreferences.edit().putString("NAME", s_name).apply();
        sharedPreferences.edit().putString("USN", s_usn).apply();
        sharedPreferences.edit().putString("SECTION", s_section).apply();
        if (!sharedPreferences.getBoolean(QuickstartPreferences.SENT_TOKEN_TO_SERVER, false))
        {
            if (checkPlayServices()) {
                // Start IntentService to register this application with GCM.
                Intent i = new Intent(RegisterActivity.this, RegistrationIntentService.class);
                i.putExtra("NAME", s_name);
                i.putExtra("USN", s_usn);
                i.putExtra("SECTION", s_section);
                progress.show();
                startService(i);
                mIsSent = true;

            }
        }
    }


}
