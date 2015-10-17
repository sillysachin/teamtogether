package com.talentspear.university.adapter;

import android.app.Activity;
import android.app.DownloadManager;
import android.content.ActivityNotFoundException;
import android.content.Intent;
import android.content.pm.PackageInfo;
import android.content.pm.PackageManager;
import android.database.Cursor;
import android.database.CursorIndexOutOfBoundsException;
import android.graphics.Typeface;
import android.media.AudioManager;
import android.media.MediaPlayer;
import android.net.Uri;
import android.os.Environment;
import android.provider.ContactsContract;
import android.support.v4.app.FragmentManager;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.ViewTreeObserver;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.daimajia.androidanimations.library.Techniques;
import com.daimajia.androidanimations.library.YoYo;
import com.rey.material.app.Dialog;
import com.rey.material.app.DialogFragment;
import com.rey.material.app.SimpleDialog;
import com.rey.material.widget.ProgressView;

import com.talentspear.university.FullScreenPost;
import com.talentspear.university.R;
import com.talentspear.university.SubjectAttendance;
import com.talentspear.university.dbhandlers.BunkHandler;
import com.talentspear.university.dbhandlers.PostHandler;
import com.talentspear.university.ds.AttendanceHolder;
import com.talentspear.university.ds.BunkHolder;
import com.talentspear.university.ds.PostHolder;
import com.talentspear.university.fragments.FeaturedPosts;

import java.io.File;
import java.util.List;


public class BunkSubjectAdapter extends RecyclerView.Adapter<RecyclerView.ViewHolder> {

    List<AttendanceHolder> attendanceHolders;
    final int TYPE_HEADER = 0;
    //static final int TYPE_CELL = 1;
    int pos;
    MediaPlayer mPlayer;
    Typeface mTf;
    View view;
    BunkHandler handler;
    public BunkSubjectAdapter(List<AttendanceHolder> attendanceHolder) {
        this.attendanceHolders = attendanceHolder;
    }
    @Override
    public int getItemViewType(int position) {
        /*switch (position) {
            case 0:
             */
        //return TYPE_HEADER;
            /*default:
                return TYPE_CELL;
        }*/
        return position;
    }

    @Override
    public int getItemCount() {
        return attendanceHolders.size();
    }

    @Override
    public RecyclerView.ViewHolder onCreateViewHolder(final ViewGroup parent, int viewType) {

        view = null;
        pos = viewType;
        view = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.bunk_item_layout, parent, false);

        mTf = Typeface.createFromAsset(parent.getContext().getAssets(), "fontawesome.ttf");
        final Typeface mTf_gothic = Typeface.createFromAsset(parent.getContext().getAssets(), "gothic.ttf");
        final Typeface mTf_tt_light = Typeface.createFromAsset(parent.getContext().getAssets(), "titillium_light.ttf");
        final Typeface mTf_tt_bold = Typeface.createFromAsset(parent.getContext().getAssets(), "titillium_bold.ttf");
        TextView course_name= (TextView) view.findViewById(R.id.bunk_course_title);
        TextView credits= (TextView) view.findViewById(R.id.credit_bunk_text);
        final TextView total_bunks= (TextView) view.findViewById(R.id.total_bunks);
        final TextView add_bunk= (TextView) view.findViewById(R.id.add_bunk);
        final TextView view_bunk= (TextView) view.findViewById(R.id.view_bunk);
        add_bunk.setTypeface(mTf);
        view_bunk.setTypeface(mTf);
        course_name.setTypeface(mTf_tt_bold);
        credits.setTypeface(mTf_tt_light);
        total_bunks.setTypeface(mTf_tt_light);
        course_name.setText(attendanceHolders.get(pos).getSubName());
        credits.setText("Credits: " + String.valueOf(attendanceHolders.get(pos).getSubCredits()));
        handler=new BunkHandler(parent.getContext());
        total_bunks.setText("Total Bunks: "+handler.getAllBunksCount(attendanceHolders.get(pos).getSubjectId()));
        add_bunk.setText(parent.getResources().getString(R.string.plus_icon)+" Add Bunk");
        view_bunk.setText(parent.getResources().getString(R.string.eye_icon)+" View Bunks");
        add_bunk.setTag(pos);
        view_bunk.setTag(pos);
        add_bunk.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                YoYo.with(Techniques.Pulse)
                        .duration(250).playOn(add_bunk);
                int taggedpos = (int) v.getTag();
                BunkHolder holder = new BunkHolder();
                Long tsLong = System.currentTimeMillis() / 1000;
                holder.setSubject_id(attendanceHolders.get(taggedpos).getSubjectId());
                holder.setTimestamp(tsLong);
                handler.addBunk(holder);
                Toast.makeText(parent.getContext(), "+1 classes bunked today", Toast.LENGTH_SHORT).show();
                total_bunks.setText("Total Bunks: " + handler.getAllBunksCount(attendanceHolders.get(taggedpos).getSubjectId()));
            }
        });

        view_bunk.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                YoYo.with(Techniques.Pulse)
                        .duration(250).playOn(view_bunk);
                int taggedpos = (int) v.getTag();
                Intent intent=new Intent(parent.getContext(),SubjectAttendance.class);
                intent.putExtra("sid",attendanceHolders.get(taggedpos).getSubjectId());
                intent.putExtra("name",attendanceHolders.get(taggedpos).getSubName());
                intent.putExtra("credits",attendanceHolders.get(taggedpos).getSubCredits());
                parent.getContext().startActivity(intent);
            }
        });

        return new RecyclerView.ViewHolder(view) {
        };
    }
            /*case TYPE_CELL: {
                view = LayoutInflater.from(parent.getContext())
                        .in
                        flate(R.layout.list_item_card_small, parent, false);
                return new RecyclerView.ViewHolder(view) {
                };
            }
        }
        return null;
    }*/


    @Override
    public void onBindViewHolder(RecyclerView.ViewHolder holder, int position) {
        switch (getItemViewType(position)) {
            case TYPE_HEADER:
                break;
            /*case TYPE_CELL:
                break;*/
        }
    }

    private void fixTextCutting(final TextView text) {
        ViewTreeObserver vto = text.getViewTreeObserver();
        vto.addOnGlobalLayoutListener(new ViewTreeObserver.OnGlobalLayoutListener() {
            private int maxLines = -1;

            @Override
            public void onGlobalLayout() {
                if (maxLines < 0 && text.getHeight() > 0 && text.getLineHeight() > 0) {
                    int height = text.getHeight();
                    int lineHeight = text.getLineHeight();
                    maxLines = height / lineHeight;
                    text.setMaxLines(maxLines);
                }
            }
        });

    }


}