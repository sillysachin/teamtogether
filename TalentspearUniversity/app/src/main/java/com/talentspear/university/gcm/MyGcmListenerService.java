/**
 * Copyright 2015 Google Inc. All Rights Reserved.
 * <p/>
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * <p/>
 * http://www.apache.org/licenses/LICENSE-2.0
 * <p/>
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

package com.talentspear.university.gcm;

import android.app.Notification;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.Context;
import android.content.Intent;
import android.database.Cursor;
import android.graphics.Typeface;
import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;
import android.support.v4.app.NotificationCompat;
import android.util.Log;
import android.view.View;
import android.widget.Toast;

import com.android.volley.AuthFailureError;
import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.github.florent37.materialviewpager.adapter.RecyclerViewMaterialAdapter;
import com.google.android.gms.gcm.GcmListenerService;
import com.rey.material.app.Dialog;
import com.rey.material.app.DialogFragment;
import com.rey.material.app.SimpleDialog;

import com.talentspear.university.NavActivity;
import com.talentspear.university.R;
import com.talentspear.university.adapter.ContactsAdapter;
import com.talentspear.university.adapter.RecyclerViewAdapter;
import com.talentspear.university.dbhandlers.ContactsHandler;
import com.talentspear.university.dbhandlers.PostHandler;
import com.talentspear.university.dbhandlers.StoreNotif;
import com.talentspear.university.ds.ContactsHolder;
import com.talentspear.university.ds.PostHolder;
import com.talentspear.university.fragments.DepartmentContacts;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.TimeZone;

public class MyGcmListenerService extends GcmListenerService {


    private static final String TAG = "MyGcmListenerService";
    private static int PostId = -1;
    int cid,tid;
    private Handler handler;

    /**
     * Called when message is received.
     *
     * @param from SenderID of the sender.
     * @param data Data bundle containing message data as key/value pairs.
     *             For Set of keys use data.keySet().
     */
    // [START receive_message]
    @Override
    public void onMessageReceived(String from, Bundle data) {
        StoreNotif storeNotif = new StoreNotif(getApplicationContext());
        handler = new Handler(Looper.getMainLooper());
        String image_link;
        final String message = data.getString("price");
        final String title = "TALENTSPEAR";

        if (data.containsKey("PostType")) {
            if (data.get("PostType").equals("1")) {
                if (data.containsKey("PostId"))
                    PostId = Integer.parseInt(data.getString("PostId"));
                Log.i("Notif Data Parsed", "New post...post ID  " + PostId);
                if (NavActivity.isInForeground()) {
                    handler.post(new Runnable() {
                        public void run() {
                            Toast toast = Toast.makeText(MyGcmListenerService.this, "New Post:" + title + ":" + message, Toast.LENGTH_SHORT);
                            toast.show();
                        }
                    });
                    //  Toast.makeText(this,"New Post:"+title,Toast.LENGTH_SHORT).show();

                } else {
                    storeNotif.insertMsg(title, message);
                }
                sendNotification(title, message);
                addNewPost();
            } else if (data.get("PostType").equals("2")) {
                if (data.containsKey("PostId"))
                    PostId = Integer.parseInt(data.getString("PostId"));
                sendNotification(title, message);
                PostAddUpdate(0);

                Log.i("Notif Data Parsed", "Edited post...Post ID  " + PostId);
            } else if (data.get("PostType").equals("3")) {
                if (data.containsKey("PostId"))
                    PostId = Integer.parseInt(data.getString("PostId"));
                PostHandler db = new PostHandler(getApplicationContext());
                db.deleteSinglePost(PostId);
            }else if(data.get("PostType").equals("4")){
                if (data.containsKey("PostId")) {
                    PostId = Integer.parseInt(data.getString("PostId"));
                    getNewContact(1);
                }
            }else if(data.get("PostType").equals("5")){
                if (data.containsKey("PostId")) {
                    PostId = Integer.parseInt(data.getString("PostId"));
                    getNewContact(0);
                }
            }else if(data.get("PostType").equals("6")){
                if (data.containsKey("PostId")) {
                    PostId = Integer.parseInt(data.getString("PostId"));
                    deleteContact(PostId);
                }
            }else if(data.get("PostType").equals("7")){
                storeNotif.insertMsg(title, message);
                sendNotification(title, message);
            }
        }
        Log.d(TAG, "From: " + from);
        Log.d(TAG, "Message: " + message);

        // for (String key : data.keySet()) {
        //     Log.d("GCM Downstream", key + " = \"" + data.get(key) + "\"");
        //  }
        /**
         * Production applications would usually process the message here.
         * Eg: - Syncing with server.
         *     - Store message in local database.
         *     - Update UI.
         */

        /**
         * In some cases it may be useful to show a notification indicating to the user
         * that a message was received.
         */


    }

    private void deleteContact(int postId) {
        ContactsHandler contactsHandler = new ContactsHandler(this);
        contactsHandler.deleteContact(postId);
    }

    private void editContact(int postId) {
        RequestQueue queue = Volley.newRequestQueue(this);
        String url = "http://ts.icias2015.org/university/app_files/getSingleContact.php";

        // Request a string response from the provided URL.
        StringRequest str = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String response) {
                ContactsHandler db = new ContactsHandler(getApplicationContext());
                JSONObject jsonObject = new JSONObject();
                try {
                    jsonObject = new JSONObject(response);
                } catch (JSONException e) {
                    e.printStackTrace();
                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError volleyError) {

            }
        }) {
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<>();
                params.put("id", Integer.toString(PostId));
                return params;
            }

            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                Map<String, String> params = new HashMap<>();
                params.put("Content-Type", "application/x-www-form-urlencoded");
                return params;
            }
        };
// Add the request to the RequestQueue.
        queue.add(str);
    }

    private void getNewContact(final int aoe) {

        RequestQueue queue = Volley.newRequestQueue(getApplicationContext());
        // final ProgressView pDialog= (ProgressView) view.findViewById(R.id.load_contact_pd);
        String url = "http://ts.icias2015.org/university/app_files/getNewContact.php";
        // pDialog.start();
        // showLoadPost();

// Request a string response from the provided URL.
        StringRequest str=new StringRequest(Request.Method.POST, url,new Response.Listener<String>() {
            @Override
            public void onResponse(String response) {
                ContactsHandler db = new ContactsHandler(getApplicationContext());
                JSONObject jsonObject=new JSONObject();
                try {
                    jsonObject= new JSONObject(response);
                } catch (JSONException e) {
                    e.printStackTrace();
                }
                JSONArray contacts_array=null;
                try {
                    contacts_array=jsonObject.getJSONArray("contacts");
                } catch (JSONException e) {
                    e.printStackTrace();
                }
                if(contacts_array.length()>0) {
                    for (int i = 0; i < contacts_array.length(); i++) {
                        ContactsHolder adder = new ContactsHolder();
                        int timestamp=0;
                        JSONObject contacts = null;
                        try {
                            contacts = contacts_array.getJSONObject(i);
                        } catch (JSONException e) {
                            e.printStackTrace();
                        }
                        //Get Appliance Name
                        try {
                            adder.setID(contacts.getInt("ID"));
                        } catch (JSONException e) {
                            e.printStackTrace();
                        }
                        //Get Appliance Usage Time
                        try {
                            adder.setEMAIL(contacts.getString("email"));
                        } catch (JSONException e) {
                            e.printStackTrace();
                        }
                        //Get Appliance Age
                        try {
                            adder.setPHONE(contacts.getString("number"));
                        } catch (JSONException e) {

                            e.printStackTrace();
                        }
                        //Get Appliance Rating
                        try {
                            adder.setNAME(contacts.getString("name"));
                        } catch (JSONException e) {
                            e.printStackTrace();
                        }
                        //Get Appliance NType
                        try {
                            adder.setDEPARTMENT(contacts.getString("department"));
                        } catch (JSONException e) {
                            e.printStackTrace();
                        }

                        try {
                            adder.setDESIGNATION(contacts.getString("desig"));
                        } catch (JSONException e) {
                            e.printStackTrace();
                        }

                        try {
                            adder.setQUALIFICATION(contacts.getString("qualification"));
                        } catch (JSONException e) {
                            e.printStackTrace();
                        }

                        try
                        {
                            if(aoe==1)
                            db.addContact(adder);
                            else
                                db.updateContact(adder);
                        }
                        catch (NullPointerException e)
                        {
                            //Toast.makeText(activity,"Please don't change tabs while loading",Toast.LENGTH_SHORT).show();
                        }
                    }
                    db.closeDB();
                }

            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError volleyError) {
            }

        }) {
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<>();
                params.put("id", Integer.toString(PostId));
                return params;
            }

            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                Map<String, String> params = new HashMap<>();
                params.put("Content-Type", "application/x-www-form-urlencoded");
                return params;
            }
        };
// Add the request to the RequestQueue.
        queue.add(str);


    }


    private void PostAddUpdate(final int aoe) {
        RequestQueue queue = Volley.newRequestQueue(this);
        String url = "http://ts.icias2015.org/university/app_files/getSingle.php";
        PostHandler handler=new PostHandler(getApplicationContext());
        final PostHolder holder=handler.getPost(PostId);
        // Request a string response from the provided URL.
        StringRequest str = new StringRequest(Request.Method.POST, url, new Response.Listener<String>() {
            @Override
            public void onResponse(String response) {
                PostHandler db = new PostHandler(getApplicationContext());
                JSONObject jsonObject = null;
                try {
                    jsonObject = new JSONObject(response);
                } catch (JSONException e) {
                    e.printStackTrace();
                }
                JSONArray posts_array=null;
                try {
                    posts_array=jsonObject.getJSONArray("posts");
                } catch (JSONException e) {
                    e.printStackTrace();
                }

                try {
                    jsonObject = new JSONObject(response);
                } catch (JSONException e) {
                    e.printStackTrace();
                }
                //PostHolder adder = new PostHolder();
                /*adder.setMessage("Some Message Added");
                adder.setAttachment(null);
                adder.setDate("today");
                adder.setFilename(null);
                adder.setFilesize(null);
                adder.setID(153);
                adder.setIsDownloaded(false);
                adder.setIsEdited();
                adder.setSubject("Some Subject");
                adder.setTime("Now");
                adder.setYEAR(2015);*/
                        HashMap<String, Integer> map_time = new HashMap<>();
                        PostHolder adder = new PostHolder();
                JSONObject posts=null;
                int timestamp = 0;
                try {
                    posts = posts_array.getJSONObject(0);
                } catch (JSONException e) {
                    e.printStackTrace();
                }
                //Get Appliance Name
                try {
                    adder.setSubject(posts.getString("sub"));
                } catch (JSONException e) {
                    e.printStackTrace();
                }
                //Get Appliance Usage Time
                try {
                    adder.setMessage(posts.getString("message"));
                } catch (JSONException e) {
                    e.printStackTrace();
                }
                //Get Appliance Age
                try {
                    adder.setAttachment(posts.getString("link"));
                    adder.setFilesize(posts.getString("size"));
                    adder.setFilename(posts.getString("link").split("/")[8]);
                } catch (JSONException e) {

                    adder.setAttachment("null");
                }
                //Get Appliance Rating
                try {
                    adder.setID(posts.getInt("id"));
                } catch (JSONException e) {
                    e.printStackTrace();
                }
                //Get Appliance NType
                try {
                    timestamp=posts.getInt("time");
                } catch (JSONException e) {
                    try {
                        timestamp=posts.getInt("mtime");
                        adder.setIsEdited(1);
                    } catch (JSONException e1) {
                        e1.printStackTrace();
                    }
                }
                adder.setTimestamp((long)timestamp);

                map_time.put("comparator", timestamp);
                Date date = new Date(timestamp*1000L); // *1000 is to convert seconds to milliseconds
                SimpleDateFormat sdf = new SimpleDateFormat("dd MMM yyyy hh:mm a"); // the format of your date
                sdf.setTimeZone(TimeZone.getTimeZone("GMT+5:30")); // give a timezone reference for formating (see comment at the bottom
                String formattedDate = sdf.format(date);
                String dt[]=formattedDate.split(" ");
                adder.setYEAR(Integer.parseInt(dt[2]));
                adder.setTime(dt[3] + " " + dt[4]);
                adder.setDate(dt[1] + " " + dt[0]);
                adder.setIsFeatured(0);
                try {
                    adder.setCID(Integer.parseInt(posts.getString("cid")));
                } catch (JSONException e) {
                    e.printStackTrace();
                }
                try {
                    adder.setTID(Integer.parseInt(posts.getString("tid")));
                } catch (JSONException e) {
                    e.printStackTrace();
                }
                Log.i("PostHolder data::::", adder.getMessage() + "..." + adder.getSubject() + "...." + adder.getTimestamp());
                if(aoe==1)
                    db.addPost(adder);
                else
                {
                    db.deleteSinglePost(PostId);
                    db.addPost(adder);
                }
                db.closeDB();

            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError volleyError) {
                //pDialog.stop();
                //showNetDisconnect();
                //Add a tuple to updates table in local db
            }
        }) {
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<>();
                params.put("mid", Integer.toString(PostId));
                return params;
            }

            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                Map<String, String> params = new HashMap<>();
                params.put("Content-Type", "application/x-www-form-urlencoded");
                return params;
            }
        };
// Add the request to the RequestQueue.
        queue.add(str);
    }

    private void addNewPost()
    {

        RequestQueue queue = Volley.newRequestQueue(getApplicationContext());
        // final ProgressView pDialog= (ProgressView) view.findViewById(R.id.load_post_pd);
        String url = "http://ts.icias2015.org/university/app_files/getNewPosts.php";
        // pDialog.start();
        // showLoadPost();
        final PostHandler db = new PostHandler(getApplicationContext());
// Request a string response from the provided URL.
        StringRequest str=new StringRequest(Request.Method.POST, url,new Response.Listener<String>() {
            @Override
            public void onResponse(String response) {

                JSONObject jsonObject=new JSONObject();
                try {
                    jsonObject= new JSONObject(response);
                } catch (JSONException e) {
                    e.printStackTrace();
                }
                JSONArray posts_array=new JSONArray();
                try {
                    posts_array=jsonObject.getJSONArray("posts");
                } catch (JSONException e) {
                    e.printStackTrace();
                }

                if(posts_array.length()>0) {
                    for (int i = 0; i < posts_array.length(); i++) {
                        HashMap<String,Integer> map_time=new HashMap<>();
                        PostHolder adder = new PostHolder();
                        int timestamp=0;
                        JSONObject posts = null;
                        try {
                            posts = posts_array.getJSONObject(i);
                        } catch (JSONException e) {
                            e.printStackTrace();
                        }
                        //Get Appliance Name
                        try {
                            adder.setSubject(posts.getString("sub"));
                        } catch (JSONException e) {
                            e.printStackTrace();
                        }
                        //Get Appliance Usage Time
                        try {
                            adder.setMessage(posts.getString("message"));
                        } catch (JSONException e) {
                            e.printStackTrace();
                        }
                        //Get Appliance Age
                        try {
                            adder.setAttachment(posts.getString("link"));
                            adder.setFilesize(posts.getString("size"));
                            adder.setFilename(posts.getString("link").split("/")[8]);
                        } catch (JSONException e) {

                            adder.setAttachment("null");
                        }
                        //Get Appliance Rating
                        try {
                            adder.setID(posts.getInt("id"));
                        } catch (JSONException e) {
                            e.printStackTrace();
                        }
                        //Get Appliance NType
                        try {
                            timestamp=posts.getInt("time");
                        } catch (JSONException e) {
                            try {
                                timestamp=posts.getInt("mtime");
                                adder.setIsEdited(1);
                            } catch (JSONException e1) {
                                e1.printStackTrace();
                            }
                        }
                        adder.setTimestamp((long)timestamp);
                        map_time.put("comparator", timestamp);
                        Date date = new Date(timestamp*1000L); // *1000 is to convert seconds to milliseconds
                        SimpleDateFormat sdf = new SimpleDateFormat("dd MMM yyyy hh:mm a"); // the format of your date
                        sdf.setTimeZone(TimeZone.getTimeZone("GMT+5:30")); // give a timezone reference for formating (see comment at the bottom
                        String formattedDate = sdf.format(date);
                        String dt[]=formattedDate.split(" ");
                        adder.setYEAR(Integer.parseInt(dt[2]));
                        adder.setTime(dt[3] + " " + dt[4]);
                        adder.setDate(dt[1] + " " + dt[0]);
                        adder.setIsFeatured(0);
                        try {
                            adder.setCID(posts.getInt("cid"));
                            cid=posts.getInt("cid");
                            tid=posts.getInt("cid");
                        } catch (JSONException e) {
                            e.printStackTrace();
                        }
                        try {
                            adder.setTID(posts.getInt("tid"));
                        } catch (JSONException e) {
                            e.printStackTrace();
                        }
                        //Get Appliance Count
                        try
                        {
                            db.addPost(adder);
                        }
                        catch (NullPointerException e)
                        {
                            //Toast.makeText(activity,"Please don't change tabs while loading",Toast.LENGTH_SHORT).show();
                        }
                    }
                   /* if(postholder.size()>1)
                    Toast.makeText(activity, "Loaded "+postholder.size()+" new posts", Toast.LENGTH_SHORT).show();
                    else
                        Toast.makeText(activity, "Loaded "+postholder.size()+" new post", Toast.LENGTH_SHORT).show();
                    //pDialog.stop();
                    //showPostList();*/
                    db.closeDB();
 }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError volleyError) {


               /* if(pDialog.isShown())
                { pDialog.stop();btn.setVisibility(View.VISIBLE);}*/
            }

        }) {
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<>();
                params.put("cid", String.valueOf(cid));
                params.put("tid", String.valueOf(tid));
                params.put("mid", String.valueOf(db.getMaxId(cid,tid)));
                return params;
            }

            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                Map<String, String> params = new HashMap<>();
                params.put("Content-Type", "application/x-www-form-urlencoded");
                return params;
            }
        };
// Add the request to the RequestQueue.
        queue.add(str);



    }


    public void sendNotification(String title, String msg) {

        StoreNotif db = new StoreNotif(this);
        Cursor c = db.getStoredNotification();
        List<String> messages = new ArrayList<String>();
        //String msgs[] = new String[25];
        int i = 0;

        NotificationManager notificationManager = (NotificationManager)
                getApplicationContext().getSystemService(Context.NOTIFICATION_SERVICE);


        int count = c.getCount();
        if (count == 0) {
            notificationManager.cancel(0);
            return;
        }

        Intent intent = new Intent(getApplicationContext(), NavActivity.class);
        intent.addFlags(Intent.FLAG_ACTIVITY_SINGLE_TOP | Intent.FLAG_ACTIVITY_CLEAR_TOP);
        int requestID = (int) System.currentTimeMillis();
        PendingIntent contentIntent = PendingIntent.getActivity(getApplicationContext(), requestID,
                intent, PendingIntent.FLAG_UPDATE_CURRENT);
        String ticker = "New post available in Talentspear University";
        String contentTitle = "Talentspear University";
        NotificationCompat.Builder mBuilder =
                new NotificationCompat.Builder(getApplicationContext());
        mBuilder.setSmallIcon(R.mipmap.university_app)
                .setTicker(ticker)                      // the thicker is the message that appears on the status bar when the notification first appears
                .setDefaults(Notification.DEFAULT_ALL)  // use defaults for various notification settings
                .setContentIntent(contentIntent)        // intent used on click
                .setAutoCancel(true)                    // if you want the notification to be dismissed when clicked
                .setOnlyAlertOnce(true); // don't play any sound or flash light if since we're updating

        if (c.moveToFirst()) {
            do {
                msg = c.getString(1);
                title = c.getString(2);
                messages.add(msg);
                i++;
                // msgs[i++] = msg;
            } while (c.moveToNext());

            NotificationCompat.Style style;
            if (count >= 1) {
                NotificationCompat.InboxStyle inboxStyle = new NotificationCompat.InboxStyle();
                style = inboxStyle;

                mBuilder.setContentTitle(contentTitle);
                if (count == 1)
                    mBuilder.setContentText(messages.get(0));
                else
                    mBuilder.setContentText(count + " new posts");
                int coun = 0;
                for (int k = 0; k < messages.size(); k++) {
                    coun++;
                    inboxStyle.addLine(messages.get(k));
                    if (coun == 5) break;
                }
                if (count - coun > 0)
                    inboxStyle.setSummaryText("+"+ (count - coun) + " more posts");
                mBuilder.setStyle(style);
                notificationManager.notify(0, mBuilder.build());
            }

        }

    }
}
