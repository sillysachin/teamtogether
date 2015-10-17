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
import com.talentspear.university.ManageBunks;
import com.talentspear.university.R;
import com.talentspear.university.SubjectAttendance;
import com.talentspear.university.dbhandlers.BunkHandler;
import com.talentspear.university.dbhandlers.PostHandler;
import com.talentspear.university.ds.BunkHolder;
import com.talentspear.university.ds.BunkHolder;
import com.talentspear.university.ds.PostHolder;
import com.talentspear.university.fragments.FeaturedPosts;

import java.io.File;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.HashMap;
import java.util.List;
import java.util.TimeZone;


public class singleSubjectAdapter extends RecyclerView.Adapter<RecyclerView.ViewHolder> {

    List<BunkHolder> bunkHolders;
    final int TYPE_HEADER = 0;
    //static final int TYPE_CELL = 1;
    int pos;
    MediaPlayer mPlayer;
    Typeface mTf;
    View view;
    BunkHandler handler;
    public singleSubjectAdapter(List<BunkHolder> bunkHolder) {
        this.bunkHolders = bunkHolder;
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
        return bunkHolders.size();
    }

    @Override
    public RecyclerView.ViewHolder onCreateViewHolder(final ViewGroup parent, int viewType) {

        view = null;
        pos = viewType;
        view = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.bunked_classes_items, parent, false);

        mTf = Typeface.createFromAsset(parent.getContext().getAssets(), "fontawesome.ttf");
        TextView date= (TextView) view.findViewById(R.id.bunk_date);
        TextView delete= (TextView) view.findViewById(R.id.delete_bunk);
        date.setTypeface(mTf);
        delete.setTypeface(mTf);
        delete.setTag(pos);
        handler=new BunkHandler(parent.getContext());
        HashMap<String,Integer> map_time=new HashMap<>();
        int timestamp=(int)bunkHolders.get(pos).getTimestamp();
        map_time.put("comparator", timestamp);
        Date date_formatted = new Date(timestamp*1000L); // *1000 is to convert seconds to milliseconds
        SimpleDateFormat sdf = new SimpleDateFormat("dd MMM yyyy hh:mm a"); // the format of your date
        sdf.setTimeZone(TimeZone.getTimeZone("GMT+5:30")); // give a timezone reference for formating (see comment at the bottom
        String formattedDate = sdf.format(date_formatted);
        String dt[]=formattedDate.split(" ");
        date.setText(parent.getResources().getString(R.string.cal_icon) + " " + dt[1] + " " + dt[0]);
        delete.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Integer taggedpos= (Integer) v.getTag();
                handler.deleteBunk(bunkHolders.get(taggedpos).getId());
                SubjectAttendance.deleteFromAdapter(taggedpos);
                ManageBunks.refreshAdapter();
            }
        });
        return new RecyclerView.ViewHolder(view) {
        };
    }
    public void notifyData()
    {
        this.notifyDataSetChanged();
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