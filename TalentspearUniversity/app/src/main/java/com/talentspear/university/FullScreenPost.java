package com.talentspear.university;

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
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.view.animation.AccelerateDecelerateInterpolator;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.daimajia.androidanimations.library.Techniques;
import com.daimajia.androidanimations.library.YoYo;
import com.rey.material.app.Dialog;
import com.rey.material.app.DialogFragment;
import com.rey.material.app.SimpleDialog;
import com.rey.material.widget.ProgressView;

import com.talentspear.university.dbhandlers.PostHandler;
import com.talentspear.university.ds.PostHolder;

import java.io.File;


public class FullScreenPost extends AppCompatActivity {
    PostHolder postHolder;
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
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.full_post_activity);
        final TextView content = (TextView) findViewById(R.id.full_post_content);
        RelativeLayout post_layout= (RelativeLayout) findViewById(R.id.full_post_container);
        /*int cx = (post_layout.getLeft() + post_layout.getRight()) / 2;
        int cy = (post_layout.getTop() + post_layout.getBottom()) / 2;

        // get the final radius for the clipping circle
        //int finalRadius = Math.max(post_layout.getWidth(), post_layout.getHeight());

        SupportAnimator animator =
                ViewAnimationUtils.createCircularReveal(post_layout, cx, cy, 0, finalRadius);
        animator.setInterpolator(new AccelerateDecelerateInterpolator());
        animator.setDuration(1500);
        animator.start();

        YoYo.with(Techniques.BounceInUp)
                .duration(700)
                .playOn(post_layout);*/
        Intent intent=getIntent();
        int id=intent.getIntExtra("id", 0);
        db=new PostHandler(this);
        postHolder=new PostHolder();
        postHolder=db.getPost(id);
        mTf = Typeface.createFromAsset(getAssets(), "fontawesome.ttf");
        final Typeface mTf_gothic = Typeface.createFromAsset(getAssets(), "gothic.ttf");
        final Typeface mTf_tt_light = Typeface.createFromAsset(getAssets(), "titillium_light.ttf");
        final Typeface mTf_tt_bold = Typeface.createFromAsset(getAssets(), "titillium_bold.ttf");
        TextView content_date = (TextView) findViewById(R.id.full_content_post_date);
        TextView content_title = (TextView) findViewById(R.id.full_post_title);
        TextView content_time = (TextView) findViewById(R.id.full_content_post_time);
        final TextView whatsapp_share = (TextView) findViewById(R.id.full_content_post_whatsapp);
        final TextView content_attach = (TextView) findViewById(R.id.full_content_post_attach);
        final TextView content_share = (TextView) findViewById(R.id.full_content_post_share);
        content_date.setTypeface(mTf);
        content_time.setTypeface(mTf);
        content_title.setTypeface(mTf_tt_bold);
        content_attach.setTypeface(mTf);
        content_share.setTypeface(mTf);
        whatsapp_share.setTypeface(mTf);

        String share_content= postHolder.getSubject()+
                "\n---------------------\n"+
                postHolder.getMessage();
        if (!postHolder.getAttachment().equals("null"))
            share_content+="\n~Download Attachment in TALENTSPEAR University.";
        final String text =    share_content+"\n---------------------\n"+
                "Shared Via TALENTSPEAR University."+
                "\n" +
                "Download at http://bit.ly/SoMeLiNk";
        whatsapp_share.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                YoYo.with(Techniques.Swing)
                        .duration(700)
                        .playOn(whatsapp_share);
                PackageManager pm = getPackageManager();
                try {

                    Intent waIntent = new Intent(Intent.ACTION_SEND);
                    waIntent.setType("text/plain");
                    PackageInfo info = pm.getPackageInfo("com.whatsapp", PackageManager.GET_META_DATA);
                    //Check if package exists or not. If not then code
                    //in catch block will be called
                    waIntent.setPackage("com.whatsapp");

                    waIntent.putExtra(Intent.EXTRA_TEXT, text);
                    startActivity(Intent.createChooser(waIntent, "Share with"));

                } catch (PackageManager.NameNotFoundException e) {
                    Toast.makeText(FullScreenPost.this, "WhatsApp not Installed", Toast.LENGTH_SHORT)
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
                startActivity(Intent.createChooser(shareIntent, "Share using"));
            }
        });

        //Share icons action end
        content.setTypeface(mTf_tt_light);
        content_title.setText(postHolder.getSubject());
        content.setText(postHolder.getMessage());
        content_time.setText(content_time.getText().toString() + " " + postHolder.getTime());
        if(db.getisEdited(postHolder.getID())==1)
            content_date.setText(content_date.getText().toString() + " " + postHolder.getDate()+" "+getResources().getString(R.string.edited_icon_pencil));
        else
            content_date.setText(content_date.getText().toString() + " " + postHolder.getDate());
        if (!postHolder.getAttachment().equals("null")) {
            setFileDimensions();
            checkAttachmentStatus(content_attach);
        } else
            content_attach.setVisibility(View.GONE);
        //Set Attach Click Listener
        //content_attach.setClickable(true);
        content_attach.setOnClickListener(new View.OnClickListener() {

            @Override
            public void onClick(View v) {
                mPlayer = MediaPlayer.create(FullScreenPost.this, R.raw.tap);
                mPlayer.setAudioStreamType(AudioManager.STREAM_MUSIC);
                mPlayer.start();
                YoYo.with(Techniques.Pulse)
                        .duration(200)
                        .playOn(content_attach);
                final Activity activity = (Activity) FullScreenPost.this;
                if (postHolder.isDownloaded)
                    read(activity, postHolder.getFilename());
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
                            setFileDimensions();
                            file_name.setText("Name: " + postHolder.getFilename().split("\\.")[0]);
                            file_type.setText("Type: " + postHolder.getFilename().split("\\.")[1].toUpperCase());
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

                                    downloadManager = (DownloadManager) activity.getSystemService(DOWNLOAD_SERVICE);
                                    Uri Download_Uri = Uri.parse(postHolder.getAttachment());
                                    DownloadManager.Request request = new DownloadManager.Request(Download_Uri);

                                    //Restrict the types of networks over which this download may proceed.
                                    request.setAllowedNetworkTypes(DownloadManager.Request.NETWORK_WIFI | DownloadManager.Request.NETWORK_MOBILE);
                                    //Set whether this download may proceed over a roaming connection.
                                    request.setAllowedOverRoaming(false);
                                    //Set the title of this download, to be displayed in notifications (if enabled).
                                    request.setTitle(postHolder.getFilename());
                                    //Set a description of this download, to be displayed in notifications (if enabled)
                                    request.setDescription("TALENTSPEAR");
                                    String downloadPath= Environment.getExternalStorageDirectory()+"/TALENTSPEAR";
                                    File fileDir=new File(downloadPath);
                                    if (!fileDir.isDirectory())
                                        fileDir.mkdirs();

                                    //Set the local destination for the downloaded file to a path within the application's external files directory
                                    request.setDestinationInExternalPublicDir("/TALENTSPEAR", postHolder.getFilename());

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
                                                                Toast.makeText(FullScreenPost.this, "Download Successful", Toast.LENGTH_SHORT).show();
                                                                checkAttachmentStatus(content_attach);
                                                                downloadfragment.dismiss();
                                                                read(activity, postHolder.getFilename());
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
                            downloadfragment.show(getSupportFragmentManager(), null);
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
                    fragment.show(getSupportFragmentManager(), null);
                }
            }
        });
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.menu_full_screen_post, menu);
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

    public void setFileDimensions() {
        filesize = Double.parseDouble(postHolder.getFilesize());
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
    public void checkAttachmentStatus(TextView content_attach)
    {
        File file = new File(Environment.getExternalStorageDirectory()+"/TALENTSPEAR/"+postHolder.getFilename());
        if (file.exists()) {
            if(postHolder.getFilename().split("\\.")[1].equals("pdf"))
                content_attach.setText(getResources().getString(R.string.pdf_icon) + " View attachment");
            else if(postHolder.getFilename().split("\\.")[1].equals("jpg") ||
                    postHolder.getFilename().split("\\.")[1].equals("jpeg") ||
                    postHolder.getFilename().split("\\.")[1].equals("png") ||
                    postHolder.getFilename().split("\\.")[1].equals("bmp") ||
                    postHolder.getFilename().split("\\.")[1].equals("JPG") ||
                    postHolder.getFilename().split("\\.")[1].equals("JPEG") ||
                    postHolder.getFilename().split("\\.")[1].equals("PNG") ||
                    postHolder.getFilename().split("\\.")[1].equals("BMP"))
                content_attach.setText(getResources().getString(R.string.image_icon) + " View attachment");
            else
                content_attach.setText(getResources().getString(R.string.doc_icon) + " View attachment");
            postHolder.setIsDownloaded(true);

        }
        else
            content_attach.setText(content_attach.getText().toString() + " " + postHolder.getFilename().split("\\.")[1].toUpperCase()+ " ("+filesize_rounded+file_ext+")");
    }

    @Override
    public void onBackPressed() {

        super.onBackPressed();
    }
}
