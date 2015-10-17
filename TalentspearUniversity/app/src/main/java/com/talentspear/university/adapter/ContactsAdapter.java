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
import com.talentspear.university.dbhandlers.PostHandler;
import com.talentspear.university.ds.ContactsHolder;
import com.talentspear.university.ds.PostHolder;
import com.talentspear.university.fragments.FeaturedPosts;

import java.io.File;
import java.util.List;


public class ContactsAdapter extends RecyclerView.Adapter<RecyclerView.ViewHolder> {

    List<ContactsHolder> contactsHolders;
    final int TYPE_HEADER = 0;
    //static final int TYPE_CELL = 1;
    int pos;
    MediaPlayer mPlayer;
    Typeface mTf;
    View view;

    public ContactsAdapter(List<ContactsHolder> contactsHolder) {
        this.contactsHolders = contactsHolder;
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
            return contactsHolders.size();
    }

    @Override
    public RecyclerView.ViewHolder onCreateViewHolder(final ViewGroup parent, int viewType) {

        view = null;
        pos = viewType;
            view = LayoutInflater.from(parent.getContext())
                    .inflate(R.layout.contact_layout, parent, false);

            mTf = Typeface.createFromAsset(parent.getContext().getAssets(), "fontawesome.ttf");
            final Typeface mTf_gothic = Typeface.createFromAsset(parent.getContext().getAssets(), "gothic.ttf");
            final Typeface mTf_tt_light = Typeface.createFromAsset(parent.getContext().getAssets(), "titillium_light.ttf");
            final Typeface mTf_tt_bold = Typeface.createFromAsset(parent.getContext().getAssets(), "titillium_bold.ttf");
            TextView contact_name= (TextView) view.findViewById(R.id.contact_name);
            TextView contact_desig= (TextView) view.findViewById(R.id.contact_designation);
            TextView contact_qualification= (TextView) view.findViewById(R.id.contact_qualification);
            final TextView contact_number= (TextView) view.findViewById(R.id.contact_phone);
            TextView contact_email= (TextView) view.findViewById(R.id.contact_email);
            final TextView contact_add= (TextView) view.findViewById(R.id.contact_add);
        contact_name.setTypeface(mTf);
        contact_desig.setTypeface(mTf);
        contact_qualification.setTypeface(mTf);
        contact_number.setTypeface(mTf);
        contact_email.setTypeface(mTf);
        contact_add.setTypeface(mTf);
        String name_text=!contactsHolders.get(pos).getNAME().equals("")?contactsHolders.get(pos).getNAME():"Not Available";
        String design_text=!contactsHolders.get(pos).getDESIGNATION().equals("")?contactsHolders.get(pos).getDESIGNATION():"Not Available";
        String qualification_text=!contactsHolders.get(pos).getQUALIFICATION().equals("")?contactsHolders.get(pos).getQUALIFICATION():"Not Available";
        String email_text=!contactsHolders.get(pos).getEMAIL().equals("")?contactsHolders.get(pos).getEMAIL():"Not Available";
        String number_text=!contactsHolders.get(pos).getPHONE().equals("")?contactsHolders.get(pos).getPHONE():"Not Available";
        contact_name.setText(contact_name.getText() +" "+ name_text);
        contact_desig.setText(contact_desig.getText() +" "+ design_text);
        contact_qualification.setText(contact_qualification.getText() +" "+ qualification_text);
        contact_number.setText(contact_number.getText() +" "+ number_text);
        contact_email.setText(contact_email.getText() + " " +  email_text);
        contact_add.setTag(pos);
        contact_number.setTag(pos);
        fixTextCutting(contact_email);
        contact_number.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                mPlayer = MediaPlayer.create(parent.getContext(), R.raw.tap);
                mPlayer.setAudioStreamType(AudioManager.STREAM_MUSIC);
                mPlayer.start();
                YoYo.with(Techniques.Pulse)
                        .duration(200)
                        .playOn(contact_number);
                Integer taggedpos= (Integer) v.getTag();
                Intent intent = new Intent(Intent.ACTION_DIAL);
                intent.setData(Uri.parse("tel:" + contactsHolders.get(taggedpos).getPHONE()));
                parent.getContext().startActivity(intent);
            }
        });
        contact_add.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                mPlayer = MediaPlayer.create(parent.getContext(), R.raw.tap);
                mPlayer.setAudioStreamType(AudioManager.STREAM_MUSIC);
                mPlayer.start();
                YoYo.with(Techniques.Pulse)
                        .duration(200)
                        .playOn(contact_add);
                Integer taggedpos= (Integer) v.getTag();
                Intent intent = new Intent(
                        ContactsContract.Intents.SHOW_OR_CREATE_CONTACT,
                        Uri.parse("tel:" + contactsHolders.get(taggedpos).getPHONE()));
                intent.putExtra(ContactsContract.Intents.Insert.NAME,contactsHolders.get(taggedpos).getNAME());
                intent.putExtra(ContactsContract.Intents.Insert.EMAIL,contactsHolders.get(taggedpos).getEMAIL());
                intent.putExtra(ContactsContract.Intents.Insert.JOB_TITLE,contactsHolders.get(taggedpos).getDESIGNATION());
                intent.putExtra(ContactsContract.Intents.EXTRA_FORCE_CREATE, true);
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