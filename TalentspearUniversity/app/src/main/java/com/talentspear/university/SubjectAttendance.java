package com.talentspear.university;

import android.graphics.Typeface;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.support.v7.widget.Toolbar;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.TextView;
import android.widget.Toast;

import com.daimajia.androidanimations.library.Techniques;
import com.daimajia.androidanimations.library.YoYo;
import com.github.florent37.materialviewpager.adapter.RecyclerViewMaterialAdapter;

import com.talentspear.university.adapter.BunkSubjectAdapter;
import com.talentspear.university.adapter.singleSubjectAdapter;
import com.talentspear.university.dbhandlers.BunkHandler;
import com.talentspear.university.ds.BunkHolder;

import java.util.ArrayList;
import java.util.List;

public class SubjectAttendance extends AppCompatActivity {
    static RecyclerView mRecyclerView;
    private static RecyclerView.Adapter mAdapter;
    static BunkHandler handler;
    static TextView total_bunks;
    static int sid;
    static List<BunkHolder> holders;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_subject_attendance);
        Toolbar toolbar = (Toolbar) findViewById(R.id.activity_singlesub_toolbar);
        setSupportActionBar(toolbar);
        toolbar.setTitle("TALENTSPEAR");
        toolbar.setBackgroundColor(getResources().getColor(R.color.primaryColorMedium));
        TextView course= (TextView) findViewById(R.id.bunk_course_title);
        getSupportActionBar().setDisplayHomeAsUpEnabled(true);
        TextView course_credits= (TextView) findViewById(R.id.credit_bunk_text);
         total_bunks= (TextView) findViewById(R.id.total_bunks);
        final TextView add_bunk= (TextView) findViewById(R.id.add_bunk);
        mRecyclerView= (RecyclerView) findViewById(R.id.recyclerView_single_sub);
        final Bundle bundle=getIntent().getExtras();
        course.setText(bundle.getString("name"));
        sid=bundle.getInt("sid");
        course_credits.setText("Credits: "+bundle.getFloat("credits"));
        Typeface mTf = Typeface.createFromAsset(getAssets(), "fontawesome.ttf");
        final Typeface mTf_tt_light = Typeface.createFromAsset(getAssets(), "titillium_light.ttf");
        final Typeface mTf_tt_bold = Typeface.createFromAsset(getAssets(), "titillium_bold.ttf");
        course.setTypeface(mTf_tt_bold);
        course_credits.setTypeface(mTf_tt_light);
        total_bunks.setTypeface(mTf_tt_light);
        add_bunk.setTypeface(mTf);
        handler=new BunkHandler(this);
        holders=new ArrayList<>();
        holders=handler.getAllBunks(sid);
        LinearLayoutManager mLayoutManager = new LinearLayoutManager(this);
        mRecyclerView.setLayoutManager(mLayoutManager);
        mAdapter = new RecyclerViewMaterialAdapter(new singleSubjectAdapter(holders));
        mRecyclerView.setAdapter(mAdapter);
        add_bunk.setText(getString(R.string.plus_icon) + " Add Bunk");
        total_bunks.setText("Total bunks: "+handler.getAllBunksCount(sid));
        add_bunk.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                YoYo.with(Techniques.Pulse)
                        .duration(250).playOn(add_bunk);
                BunkHolder holder = new BunkHolder();
                Long tsLong = System.currentTimeMillis() / 1000;
                holder.setSubject_id(sid);
                holder.setTimestamp(tsLong);
                holder.setId(handler.addBunk(holder));
                Toast.makeText(SubjectAttendance.this, "+1 classes bunked today", Toast.LENGTH_SHORT).show();
                total_bunks.setText("Total Bunks: " + handler.getAllBunksCount(sid));
                holders.add(0, holder);
                mAdapter = new RecyclerViewMaterialAdapter(new singleSubjectAdapter(holders));
                mRecyclerView.setAdapter(mAdapter);
                ManageBunks.refreshAdapter();
            }
        });



    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.menu_subject_attendance, menu);
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
        if (id == android.R.id.home) {
            finish();
            return true;
        }

        return super.onOptionsItemSelected(item);
    }

    public void setTotalBunks()
    {
        total_bunks.setText("Total Bunks: " + handler.getAllBunksCount(sid));
    }

    public static void deleteFromAdapter(int pos)
    {
        holders.remove(pos);
        mAdapter = new RecyclerViewMaterialAdapter(new singleSubjectAdapter(holders));
        mRecyclerView.setAdapter(mAdapter);
        total_bunks.setText("Total Bunks: " + handler.getAllBunksCount(sid));
    }
}
