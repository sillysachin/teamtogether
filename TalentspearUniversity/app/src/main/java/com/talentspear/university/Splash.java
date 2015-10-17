package com.talentspear.university;

/**
 * Created by SHESHA on 01-08-2015.
 */

import android.app.Activity;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Typeface;
import android.media.Image;
import android.os.Bundle;
import android.os.Handler;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
import android.widget.TextView;
import android.graphics.Matrix;
import android.widget.ImageView;
import com.daimajia.androidanimations.library.Techniques;
import com.daimajia.androidanimations.library.YoYo;

public class Splash extends Activity {

    private static int SPLASH_TIMEOUT=3000;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.splash_layout);
        TextView tvsplash= (TextView) findViewById(R.id.tvsplash);
        ImageView logo= (ImageView) findViewById(R.id.splashImg);
        tvsplash.setTypeface(Typeface.createFromAsset(getAssets(), "titillium_light.ttf"));
        YoYo.with(Techniques.FadeInDown)
                .duration(2800)
                .playOn(tvsplash);
        Bitmap myImg = BitmapFactory.decodeResource(getResources(), R.drawable.university_logo);

        Matrix matrix = new Matrix();
        matrix.postRotate(90);

        Bitmap rotated = Bitmap.createBitmap(myImg, 0, 0, myImg.getWidth(), myImg.getHeight(),
                matrix, true);

        logo.setImageBitmap(rotated);
        Animation rotation = AnimationUtils.loadAnimation(this, R.anim.rotate_logo);
        logo.startAnimation(rotation);
        new Handler().postDelayed(new Runnable() {

            /*
             * Showing splash screen with a timer. This will be useful when you
             * want to show case your app logo / company
             */

            @Override
            public void run() {
                // This method will be executed once the timer is over
                // Start your app main activity
                Intent i = new Intent(Splash.this, RegisterActivity.class);
                startActivity(i);
                // close this activity
                finish();
            }
        }, SPLASH_TIMEOUT);
    }
}

