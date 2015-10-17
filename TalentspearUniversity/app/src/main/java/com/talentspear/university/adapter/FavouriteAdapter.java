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
import android.support.v4.app.FragmentManager;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
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
import com.talentspear.university.ds.PostHolder;
import com.talentspear.university.fragments.FeaturedPosts;

import java.io.File;
import java.util.List;


public class FavouriteAdapter extends RecyclerView.Adapter<RecyclerView.ViewHolder> {

    List<PostHolder> postholder;
    final int TYPE_HEADER = 0;
    //static final int TYPE_CELL = 1;
    private boolean showList;
    FragmentManager manager;
    private ProgressView pv_linear_indeterminate;
    TextView download_progress_text;
    Typeface mTf;
    DownloadManager downloadManager;
    Double filesize;
    DialogFragment downloadfragment;
    Dialog.Builder builder;
    TextView file_name;
    String filesize_rounded;
    String file_ext;
    int pos;
    MediaPlayer mPlayer;
    private long downloadReference;
    boolean isFileShown=false;
    RelativeLayout file_info_layout;
    boolean downloading;
    RelativeLayout file_download_layout;
    View view;
    Cursor cursor;
    PostHandler db;
    String down_icon;
    RecyclerView rView;

    public FavouriteAdapter(List<PostHolder> postHolder, FragmentManager manager, boolean showlist, RecyclerView rView) {
        this.postholder = postHolder;
        this.showList=showlist;
        this.manager=manager;
        this.rView=rView;
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
        if(showList)
            return postholder.size();
        else
            return 1;
    }

    @Override
    public RecyclerView.ViewHolder onCreateViewHolder(final ViewGroup parent, int viewType) {

        view = null;
        pos = viewType;
        if(showList) {
            view = LayoutInflater.from(parent.getContext())
                    .inflate(R.layout.list_item_card_big, parent, false);
            view.setTag(pos);
            view.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    Integer taggedpos= (Integer) v.getTag();
                    Intent intent=new Intent(parent.getContext(), FullScreenPost.class);
                    intent.putExtra("id",postholder.get(taggedpos).getID());
                    parent.getContext().startActivity(intent);
                }
            });
            final TextView content = (TextView) view.findViewById(R.id.content_post);
               /* ViewTreeObserver observer = content.getViewTreeObserver();
                observer.addOnGlobalLayoutListener(new ViewTreeObserver.OnGlobalLayoutListener() {
                    @Override
                    public void onGlobalLayout() {
                        int maxLines = (int) content.getHeight()
                                / content.getLineHeight();
                        content.setMaxLines(maxLines);
                        if (Build.VERSION.SDK_INT < 16) {
                            content.getViewTreeObserver().removeGlobalOnLayoutListener(this);
                        } else {
                            content.getViewTreeObserver().removeOnGlobalLayoutListener(this);
                        }
                    }
                });*/
            db = new PostHandler(parent.getContext());
            mTf = Typeface.createFromAsset(parent.getContext().getAssets(), "fontawesome.ttf");
            final Typeface mTf_gothic = Typeface.createFromAsset(parent.getContext().getAssets(), "gothic.ttf");
            final Typeface mTf_tt_light = Typeface.createFromAsset(parent.getContext().getAssets(), "titillium_light.ttf");
            final Typeface mTf_tt_bold = Typeface.createFromAsset(parent.getContext().getAssets(), "titillium_bold.ttf");
            TextView content_date = (TextView) view.findViewById(R.id.content_post_date);
            TextView content_title = (TextView) view.findViewById(R.id.title_post);
            TextView content_time = (TextView) view.findViewById(R.id.content_post_time);
            final TextView whatsapp_share = (TextView) view.findViewById(R.id.content_post_whatsapp);
            final TextView star_fav = (TextView) view.findViewById(R.id.star_fav);
            final TextView content_attach = (TextView) view.findViewById(R.id.content_post_attach);
            final TextView content_share = (TextView) view.findViewById(R.id.content_post_share);

            content_date.setTypeface(mTf);
            content_time.setTypeface(mTf);
            content_title.setTypeface(mTf_tt_bold);
            content_attach.setTypeface(mTf);
            content_attach.setTag(pos);
            content_share.setTypeface(mTf);
            whatsapp_share.setTypeface(mTf);
            //Share icons action
            String share_content= postholder.get(pos).getSubject()+
                    "\n---------------------\n"+
                    postholder.get(pos).getMessage();
            if (!postholder.get(pos).getAttachment().equals("null"))
                share_content+="\n~Download Attachment in TALENTSPEAR.";
            final String text =    share_content+"\n---------------------\n"+
                    "Shared Via TALENTSPEAR."+
                    "\n" +
                    "Download at http://bit.ly/SoMeLiNk";
            whatsapp_share.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    YoYo.with(Techniques.Swing)
                            .duration(700)
                            .playOn(whatsapp_share);
                    PackageManager pm = parent.getContext().getPackageManager();
                    try {

                        Intent waIntent = new Intent(Intent.ACTION_SEND);
                        waIntent.setType("text/plain");
                        PackageInfo info = pm.getPackageInfo("com.whatsapp", PackageManager.GET_META_DATA);
                        //Check if package exists or not. If not then code
                        //in catch block will be called
                        waIntent.setPackage("com.whatsapp");

                        waIntent.putExtra(Intent.EXTRA_TEXT, text);
                        parent.getContext().startActivity(Intent.createChooser(waIntent, "Share with"));

                    } catch (PackageManager.NameNotFoundException e) {
                        Toast.makeText(parent.getContext(), "WhatsApp not Installed", Toast.LENGTH_SHORT)
                                .show();
                    }
                }
            });

            content_share.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    YoYo.with(Techniques.Swing)
                            .duration(700)
                            .playOn(content_share);
                    Intent shareIntent = new Intent(Intent.ACTION_SEND);
                    shareIntent.setType("text/plain");
                    shareIntent.putExtra(Intent.EXTRA_TEXT, text);
                    parent.getContext().startActivity(Intent.createChooser(shareIntent, "Share using"));
                }
            });

            //Share icons action end
            star_fav.setTag(pos);
            star_fav.setTypeface(mTf);
            if(db.getisFeatured(postholder.get(pos).getID())==1)
            {
                star_fav.setText(parent.getResources().getString(R.string.star_icon_filled));
                star_fav.setTextColor(parent.getResources().getColor(R.color.golden_star));
            }
            else
            {
                star_fav.setText(parent.getResources().getString(R.string.star_icon_empty));
                star_fav.setTextColor(parent.getResources().getColor(R.color.primaryColorDark));
            }
            star_fav.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    final Integer taggedpos = (Integer) v.getTag();
                    YoYo.with(Techniques.BounceInDown)
                            .duration(700)
                            .playOn(star_fav);
                    if (db.getisFeatured(postholder.get(taggedpos).getID()) == 0) {
                        star_fav.setText(parent.getResources().getString(R.string.star_icon_filled));
                        star_fav.setTextColor(parent.getResources().getColor(R.color.golden_star));
                        db.setisFeatured(1, postholder.get(taggedpos).getID());
                        Toast.makeText(parent.getContext(), "Post added to favourites", Toast.LENGTH_SHORT).show();
                    } else {
                        star_fav.setText(parent.getResources().getString(R.string.star_icon_empty));
                        star_fav.setTextColor(parent.getResources().getColor(R.color.primaryColorDark));
                        db.setisFeatured(0, postholder.get(taggedpos).getID());
                        Toast.makeText(parent.getContext(), "Post removed from favourites", Toast.LENGTH_SHORT).show();
                    }

                }
            });
            content.setTypeface(mTf_tt_light);
            content_title.setText(postholder.get(pos).getSubject().replace('\n', ' '));
            content.setText(postholder.get(pos).getMessage().replace('\n', ' '));
            content_time.setText(content_time.getText().toString() + " " + postholder.get(pos).getTime());
            if(db.getisEdited(postholder.get(pos).getID())==1)
                content_date.setText(content_date.getText().toString() + " " + postholder.get(pos).getDate()+" "+parent.getResources().getString(R.string.edited_icon_pencil));
            else
                content_date.setText(content_date.getText().toString() + " " + postholder.get(pos).getDate());
            if (!postholder.get(pos).getAttachment().equals("null")) {
                setFileDimensions(pos);
                checkAttachmentStatus(pos,content_attach,parent);
            } else
                content_attach.setVisibility(View.GONE);
            //Set Attach Click Listener
            //content_attach.setClickable(true);
            content_attach.setOnClickListener(new View.OnClickListener() {

                @Override
                public void onClick(View v) {
                    mPlayer = MediaPlayer.create(parent.getContext(), R.raw.tap);
                    mPlayer.setAudioStreamType(AudioManager.STREAM_MUSIC);
                    mPlayer.start();
                    YoYo.with(Techniques.Pulse)
                            .duration(200)
                            .playOn(content_attach);
                    final Activity activity = (Activity) parent.getContext();
                    final Integer taggedpos = (Integer) v.getTag();
                    if (postholder.get(taggedpos).isDownloaded)
                        read(activity, postholder.get(taggedpos).getFilename());
                    else {
                        isFileShown = false;
                        builder = new SimpleDialog.Builder(R.style.SimpleDialogLight) {
                            @Override
                            protected void onBuildDone(Dialog dialog) {
                                dialog.layoutParams(ViewGroup.LayoutParams.MATCH_PARENT, ViewGroup.LayoutParams.WRAP_CONTENT);
                                file_name = (TextView) dialog.findViewById(R.id.file_name_download);
                                TextView file_type = (TextView) dialog.findViewById(R.id.file_type_download);
                                TextView file_size = (TextView) dialog.findViewById(R.id.file_size_download);
                                file_size.setTypeface(mTf_gothic);
                                file_type.setTypeface(mTf_gothic);
                                setFileDimensions(taggedpos);
                                file_name.setText("Name: " + postholder.get(taggedpos).getFilename().split("\\.")[0]);
                                file_type.setText("Type: " + postholder.get(taggedpos).getFilename().split("\\.")[1].toUpperCase());
                                file_size.setText("Size: " + filesize_rounded + "" + file_ext);
                            }

                            @Override
                            public void onPositiveActionClicked(final DialogFragment fragment) {
                                fragment.dismiss();
                                //////////////////////////////////////Start of child builder
                                Dialog.Builder builder = new SimpleDialog.Builder(R.style.SimpleDialogLight) {
                                    @Override
                                    protected void onBuildDone(Dialog dialog) {
                                        dialog.layoutParams(ViewGroup.LayoutParams.MATCH_PARENT, ViewGroup.LayoutParams.WRAP_CONTENT);

                                        download_progress_text = (TextView) dialog.findViewById(R.id.progress_download_text);
                                        file_info_layout = (RelativeLayout) dialog.findViewById(R.id.file_info_layout);
                                        pv_linear_indeterminate = (ProgressView) dialog.findViewById(R.id.download_progress);
                                        file_download_layout = (RelativeLayout) dialog.findViewById(R.id.download_progress_layout);
                                        file_name.setTypeface(mTf_gothic);

                                        downloadManager = (DownloadManager) activity.getSystemService(parent.getContext().DOWNLOAD_SERVICE);
                                        Uri Download_Uri = Uri.parse(postholder.get(taggedpos).getAttachment());
                                        DownloadManager.Request request = new DownloadManager.Request(Download_Uri);

                                        //Restrict the types of networks over which this download may proceed.
                                        request.setAllowedNetworkTypes(DownloadManager.Request.NETWORK_WIFI | DownloadManager.Request.NETWORK_MOBILE);
                                        //Set whether this download may proceed over a roaming connection.
                                        request.setAllowedOverRoaming(false);
                                        //Set the title of this download, to be displayed in notifications (if enabled).
                                        request.setTitle(postholder.get(taggedpos).getFilename());
                                        //Set a description of this download, to be displayed in notifications (if enabled)
                                        request.setDescription("TALENTSPEAR");
                                        String downloadPath=Environment.getExternalStorageDirectory()+"/TALENTSPEAR";
                                        File fileDir=new File(downloadPath);
                                        if (!fileDir.isDirectory())
                                            fileDir.mkdirs();

                                        //Set the local destination for the downloaded file to a path within the application's external files directory
                                        request.setDestinationInExternalPublicDir("/TALENTSPEAR", postholder.get(taggedpos).getFilename());

                                        //Enqueue a new download and same the referenceId
                                        downloadReference = downloadManager.enqueue(request);
                                        pv_linear_indeterminate.setProgress(0f);
                                        pv_linear_indeterminate.start();
                                        file_download_layout.setVisibility(View.VISIBLE);
                                        file_info_layout.setVisibility(View.INVISIBLE);
                                        down_icon = download_progress_text.getText().toString();
                                        download_progress_text.setTypeface(mTf);
                                        new Thread(new Runnable() {

                                            @Override
                                            public void run() {

                                                downloading = true;

                                                while (downloading) {

                                                    DownloadManager.Query q = new DownloadManager.Query();
                                                    q.setFilterById(downloadReference);
                                                    try {
                                                        cursor = downloadManager.query(q);
                                                        cursor.moveToFirst();
                                                        int bytes_downloaded = cursor.getInt(cursor
                                                                .getColumnIndex(DownloadManager.COLUMN_BYTES_DOWNLOADED_SO_FAR));
                                                        int bytes_total = cursor.getInt(cursor.getColumnIndex(DownloadManager.COLUMN_TOTAL_SIZE_BYTES));

                                                        if (cursor.getInt(cursor.getColumnIndex(DownloadManager.COLUMN_STATUS)) == DownloadManager.STATUS_SUCCESSFUL) {
                                                            downloading = false;
                                                        }

                                                        final int dl_progress = (int) ((bytes_downloaded * 100l) / bytes_total);

                                                        activity.runOnUiThread(new Runnable() {

                                                            @Override
                                                            public void run() {
                                                                download_progress_text.setText(down_icon + " " + String.valueOf(dl_progress) + "%");
                                                                if (dl_progress == 100 && !isFileShown) {
                                                                    download_progress_text.setText("Download Complete.");
                                                                    Toast.makeText(parent.getContext(), "Download Successful", Toast.LENGTH_SHORT).show();
                                                                    checkAttachmentStatus(taggedpos, content_attach, parent);
                                                                    downloadfragment.dismiss();
                                                                    read(activity, postholder.get(taggedpos).getFilename());
                                                                    isFileShown = true;
                                                                }
                                                                pv_linear_indeterminate.stop();

                                                            }
                                                        });

                                                        cursor.close();
                                                    }
                                                    catch(CursorIndexOutOfBoundsException e)
                                                    {

                                                    }
                                                }


                                            }
                                        }).start();
                                    }

                                    @Override
                                    public void onNegativeActionClicked(DialogFragment fragment) {
                                        super.onNegativeActionClicked(fragment);
                                        try {
                                            if (downloading)
                                                downloading = false;
                                            cursor.close();
                                        } catch (Exception e) {

                                        }

                                        try {
                                            downloadManager.remove(downloadReference);
                                        } catch (Exception e) {

                                        }
                                    }
                                };
                                builder.title("Downloading...")
                                        .negativeAction("CANCEL")
                                        .contentView(R.layout.download_dialog);

                                downloadfragment = DialogFragment.newInstance(builder);
                                downloadfragment.setCancelable(false);
                                downloadfragment.show(manager, null);
                                //////////////////////////////////////End of child builder

                                //super.onPositiveActionClicked(fragment);


                            }

                            @Override
                            public void onNegativeActionClicked(DialogFragment fragment) {
                                super.onNegativeActionClicked(fragment);

                            }
                        };


                        builder.positiveAction("DOWNLOAD")
                                .contentView(R.layout.download_dialog)
                                .negativeAction("CANCEL");

                        DialogFragment fragment = DialogFragment.newInstance(builder);
                        fragment.setCancelable(true);
                        fragment.show(manager, null);
                    }
                }
            });
        }
        else
        {
            view = LayoutInflater.from(parent.getContext())
                    .inflate(R.layout.list_item_card_big, parent, false);
            return new RecyclerView.ViewHolder(view) {
            };
        }

        return new RecyclerView.ViewHolder(view) {
        };
    }
            /*case TYPE_CELL: {
                view = LayoutInflater.from(parent.getContext())
                        .inflate(R.layout.list_item_card_small, parent, false);
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


    public void setFileDimensions(int fileDimensions) {
        filesize = Double.parseDouble(postholder.get(fileDimensions).getFilesize());
        if (filesize > 1048576D) {
            filesize_rounded = String.format("%.2f", filesize / (1024 * 1024));
            file_ext = "MB";
        } else {
            filesize_rounded = String.format("%.2f", filesize / (1024));
            file_ext = "KB";
        }
        if (filesize_rounded.equals("0.00")) {
            filesize_rounded = "0.01";
        }
    }

    public void read(Activity context,  String fileName){
        File appFolder = new File( Environment.getExternalStorageDirectory()+"/TALENTSPEAR");
        File file = new File(appFolder, fileName);

        Uri path = Uri.fromFile(file);

        Intent intent = new Intent();
        intent.setAction(Intent.ACTION_VIEW);
        intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
        intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
        if(fileName.split("\\.")[1].equals("pdf")||fileName.split("\\.")[1].equals("PDF"))
        {
            intent.setDataAndType(path, "application/pdf");
            try {
                context.startActivity(intent);
            }
            catch (ActivityNotFoundException e) {
                Toast.makeText(context, "Please Download PDF reader to view this file!",
                        Toast.LENGTH_LONG).show();
                try {
                    context.startActivity(new Intent(Intent.ACTION_VIEW, Uri.parse("market://details?id=" + "com.google.android.apps.pdfviewer")));
                } catch (android.content.ActivityNotFoundException anfe) {
                    context.startActivity(new Intent(Intent.ACTION_VIEW, Uri.parse("https://play.google.com/store/apps/details?id=" + "com.google.android.apps.pdfviewer")));
                }
            }
        }
        else if(fileName.split("\\.")[1].equals("jpg") ||
                fileName.split("\\.")[1].equals("jpeg") ||
                fileName.split("\\.")[1].equals("png") ||
                fileName.split("\\.")[1].equals("bmp") ||
                fileName.split("\\.")[1].equals("JPG") ||
                fileName.split("\\.")[1].equals("JPEG") ||
                fileName.split("\\.")[1].equals("PNG") ||
                fileName.split("\\.")[1].equals("BMP"))
        {

            intent.setDataAndType(path, "image/*");
            try {
                context.startActivity(intent);
            }
            catch (ActivityNotFoundException e) {
                Toast.makeText(context, "Install an image viewer app",
                        Toast.LENGTH_LONG).show();
            }
        }
        else if(fileName.split("\\.")[1].equals("zip")||fileName.split("\\.")[1].equals("rar"))
        {
            intent.setDataAndType(path, "application/zip");
            try {
                context.startActivity(intent);
            }
            catch (ActivityNotFoundException e) {
                Toast.makeText(context, "Please Download Archive extractor to view this file!",
                        Toast.LENGTH_LONG).show();
                Toast.makeText(context, "File saved in TALENTSPEAR DIRECTORY",
                        Toast.LENGTH_LONG).show();
            }
        }
        else if(fileName.split("\\.")[1].equals("doc")||fileName.split("\\.")[1].equals("docx"))
        {
            intent.setDataAndType(path, "application/msword");
            try {
                context.startActivity(intent);
            }
            catch (ActivityNotFoundException e) {
                Toast.makeText(context, "Please Download Word viewer to view this file!",
                        Toast.LENGTH_LONG).show();
                Toast.makeText(context, "File saved in TALENTSPEAR DIRECTORY",
                        Toast.LENGTH_LONG).show();
            }
        }
        else
        {

            Toast.makeText(context, fileName+ " is saved in TALENTSPEAR DIRECTORY",
                    Toast.LENGTH_LONG).show();
        }


    }
    public void checkAttachmentStatus(int pos,TextView content_attach,ViewGroup parent)
    {
        File file = new File(Environment.getExternalStorageDirectory()+"/TALENTSPEAR/"+postholder.get(pos).getFilename());
        if (file.exists()) {
            if(postholder.get(pos).getFilename().split("\\.")[1].equals("pdf"))
                content_attach.setText(parent.getContext().getResources().getString(R.string.pdf_icon) + " View attachment");
            else if(postholder.get(pos).getFilename().split("\\.")[1].equals("jpg") ||
                    postholder.get(pos).getFilename().split("\\.")[1].equals("jpeg") ||
                    postholder.get(pos).getFilename().split("\\.")[1].equals("png") ||
                    postholder.get(pos).getFilename().split("\\.")[1].equals("bmp") ||
                    postholder.get(pos).getFilename().split("\\.")[1].equals("JPG") ||
                    postholder.get(pos).getFilename().split("\\.")[1].equals("JPEG") ||
                    postholder.get(pos).getFilename().split("\\.")[1].equals("PNG") ||
                    postholder.get(pos).getFilename().split("\\.")[1].equals("BMP"))
                content_attach.setText(parent.getContext().getResources().getString(R.string.image_icon) + " View attachment");
            else
                content_attach.setText(parent.getContext().getResources().getString(R.string.doc_icon) + " View attachment");
            postholder.get(pos).setIsDownloaded(true);

        }
        else
            content_attach.setText(content_attach.getText().toString() + " " + postholder.get(pos).getFilename().split("\\.")[1].toUpperCase()+ " ("+filesize_rounded+file_ext+")");
    }
}