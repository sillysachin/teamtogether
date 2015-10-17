package com.talentspear.university;

import android.content.Intent;
import android.graphics.Typeface;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.support.v7.widget.Toolbar;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import com.daimajia.androidanimations.library.Techniques;
import com.daimajia.androidanimations.library.YoYo;
import com.github.florent37.materialviewpager.adapter.RecyclerViewMaterialAdapter;
import com.rengwuxian.materialedittext.MaterialEditText;
import com.rey.material.app.Dialog;
import com.rey.material.app.DialogFragment;
import com.rey.material.app.SimpleDialog;
import com.rey.material.widget.Spinner;

import com.talentspear.university.adapter.BunkSubjectAdapter;
import com.talentspear.university.adapter.RecyclerViewAdapter;
import com.talentspear.university.adapter.singleSubjectAdapter;
import com.talentspear.university.dbhandlers.AttendanceHandler;
import com.talentspear.university.ds.AttendanceHolder;
import com.talentspear.university.fragments.AttendanceMan;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;

public class ManageBunks extends AppCompatActivity {
    static RecyclerView mRecyclerView;
    TextView add_sub;
    TextView no_sub_text;
    ImageView no_sub_image;
    DialogFragment fragment;
    List<AttendanceHolder> attendanceHolders;
    private static RecyclerView.Adapter mAdapter;
    ArrayAdapter<String> credit_adapter;
    List<String> credits_list;
    Spinner credits;
    public static int REQUEST_RESULT=5003;
    static AttendanceHandler attendanceHandler;
    boolean noSubjects=false;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_manage_bunks);
        mRecyclerView= (RecyclerView) findViewById(R.id.recyclerView_attendance_sub);
        add_sub= (TextView) findViewById(R.id.add_subject);
        no_sub_text= (TextView) findViewById(R.id.no_subject_text);
        no_sub_image= (ImageView) findViewById(R.id.no_subject_image);
        Toolbar toolbar = (Toolbar) findViewById(R.id.activity_bunks_toolbar);
        setSupportActionBar(toolbar);
        getSupportActionBar().setDisplayHomeAsUpEnabled(true);
        toolbar.setTitle("TALENTSPEAR");
        toolbar.setBackgroundColor(getResources().getColor(R.color.primaryColorMedium));
        attendanceHandler=new AttendanceHandler(this);
        attendanceHolders=attendanceHandler.getAllSubjects();
        LinearLayoutManager mLayoutManager = new LinearLayoutManager(this);
        mRecyclerView.setLayoutManager(mLayoutManager);
        mAdapter = new RecyclerViewMaterialAdapter(new BunkSubjectAdapter(attendanceHolders));
        mRecyclerView.setAdapter(mAdapter);
        if(attendanceHolders.size()>0)
            showSubjects();
        else
        {
            showNoSubjects();
            noSubjects=true;
        }
        attendanceHandler.closedb();
        add_sub.setTypeface(Typeface.createFromAsset(getResources().getAssets(), "titillium_light.ttf"));
        add_sub.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                YoYo.with(Techniques.Pulse)
                        .duration(700).playOn(add_sub);
                Dialog.Builder builder = new SimpleDialog.Builder(R.style.SimpleDialogLight) {
                    @Override
                    protected void onBuildDone(Dialog dialog) {
                        dialog.layoutParams(ViewGroup.LayoutParams.MATCH_PARENT, ViewGroup.LayoutParams.WRAP_CONTENT);
                        MaterialEditText course = (MaterialEditText)dialog.findViewById(R.id.add_attendance_course);
                        course.setTypeface(Typeface.createFromAsset(getAssets(), "titillium_light.ttf"));
                        credits_list = Arrays.asList(getResources().getStringArray(R.array.credits));
                        credit_adapter = new ArrayAdapter<>(ManageBunks.this, R.layout.row_spn_light, credits_list);
                        credits = (Spinner) dialog.findViewById(R.id.add_attendance_credits);
                        credit_adapter.setDropDownViewResource(R.layout.row_spn_dropdown_light);
                        credits.setAdapter(credit_adapter);
                        credits.setSelection(1);
                    }

                    @Override
                    public void onPositiveActionClicked(DialogFragment fragment) {
                        MaterialEditText course = (MaterialEditText)getDialog().findViewById(R.id.add_attendance_course);
                        Spinner selected_credits= (Spinner) getDialog().findViewById(R.id.add_attendance_credits);
                        if(course.getText().toString().equals("")||course.getText().toString().equals(" "))
                        {
                            Toast.makeText(ManageBunks.this,"Invalid Course Name",Toast.LENGTH_SHORT).show();
                            fragment.dismiss();
                        }
                        else
                        {
                            float credits=Float.parseFloat(selected_credits.getSelectedItem().toString());
                            AttendanceHolder holder=new AttendanceHolder();
                            holder.setSubName(course.getText().toString().trim().toUpperCase());
                            holder.setSubCredits(credits);
                            AttendanceHandler handler=new AttendanceHandler(ManageBunks.this);
                            int id=handler.addSubject(holder);
                            holder.setSubjectId(id);
                            attendanceHolders.add(holder);
                            mAdapter.notifyDataSetChanged();
                            if(noSubjects)
                            {
                                showSubjects();
                                noSubjects=false;
                            }

                        }
                        super.onPositiveActionClicked(fragment);
                    }

                    @Override
                    public void onNegativeActionClicked(DialogFragment fragment) {
                        Toast.makeText(ManageBunks.this, "Cancelled", Toast.LENGTH_SHORT).show();
                        super.onNegativeActionClicked(fragment);
                    }
                };
                builder.title("ADD SUBJECT")
                        .positiveAction("ADD")
                        .negativeAction("CANCEL")
                        .contentView(R.layout.add_bunk_dialog);
                fragment = DialogFragment.newInstance(builder);
                fragment.setCancelable(true);
                fragment.show(getSupportFragmentManager(), null);
            }

        });

    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.menu_manage_bunks, menu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        // Handle action bar item clicks here. The action bar will
        // automatically handle clicks on the Home/Up button, so long
        // as you specify a parent activity in AndroidManifest.xml.
        int id = item.getItemId();

        //noinspection SimplifiableIfStatement
        if (id == R.id.action_delete) {
            Dialog.Builder builder = new SimpleDialog.Builder(R.style.SimpleDialogLight) {
                @Override
                public void onPositiveActionClicked(DialogFragment fragment) {
                    attendanceHandler.truncate();
                    ManageBunks.refreshAdapter();
                    super.onPositiveActionClicked(fragment);
                }

                @Override
                public void onNegativeActionClicked(DialogFragment fragment) {
                    Toast.makeText(ManageBunks.this, "Cancelled", Toast.LENGTH_SHORT).show();
                    super.onNegativeActionClicked(fragment);
                }
            };
            builder.title("CLEAR ALL DATA ?")
                    .positiveAction("CLEAR")
                    .negativeAction("CANCEL");
            fragment = DialogFragment.newInstance(builder);
            fragment.setCancelable(true);
            fragment.show(getSupportFragmentManager(), null);
            return true;
        }
        if (id == android.R.id.home) {
            finish();
            return true;
        }

        return super.onOptionsItemSelected(item);
    }

    public void showSubjects()
    {
        mRecyclerView.setVisibility(View.VISIBLE);
        no_sub_image.setVisibility(View.INVISIBLE);
        no_sub_text.setVisibility(View.INVISIBLE);
    }
    public void showNoSubjects()
    {
        mRecyclerView.setVisibility(View.INVISIBLE);
        no_sub_image.setVisibility(View.VISIBLE);
        no_sub_text.setVisibility(View.VISIBLE);
    }

    public static void refreshAdapter()
    {
        try {
            mAdapter = new RecyclerViewMaterialAdapter(new BunkSubjectAdapter(attendanceHandler.getAllSubjects()));
            mRecyclerView.setAdapter(mAdapter);
        }
        catch(Exception e)
        {
            System.out.println("From Graph");
        }
    }

}
