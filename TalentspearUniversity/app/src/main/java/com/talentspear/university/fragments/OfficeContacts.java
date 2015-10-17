package com.talentspear.university.fragments;

import android.app.Activity;
import android.graphics.Typeface;
import android.os.Bundle;
import android.os.Handler;
import android.support.annotation.Nullable;
import android.support.v4.app.Fragment;
import android.support.v7.widget.DefaultItemAnimator;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.android.volley.AuthFailureError;
import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.daimajia.androidanimations.library.Techniques;
import com.daimajia.androidanimations.library.YoYo;
import com.github.florent37.materialviewpager.MaterialViewPagerHelper;
import com.github.florent37.materialviewpager.adapter.RecyclerViewMaterialAdapter;
import com.rey.material.app.Dialog;
import com.rey.material.app.DialogFragment;
import com.rey.material.app.SimpleDialog;
import com.rey.material.widget.ProgressView;

import com.talentspear.university.R;
import com.talentspear.university.adapter.ContactsAdapter;
import com.talentspear.university.dbhandlers.ContactsHandler;
import com.talentspear.university.ds.ContactsHolder;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

public class OfficeContacts extends Fragment {

    private RecyclerView mRecyclerView;
    private RecyclerView.Adapter mAdapter;
    boolean isNetworkErrorShown=false;
    Activity activity;
    RelativeLayout no_contact_layout;
    TextView no_contact_text;
    DefaultItemAnimator animator;
    ImageView no_contact_image;
    private RelativeLayout loading_post;
    ProgressView pv_load_post;
    Handler handler;
    LinearLayoutManager mLayoutManager;
    private List<ContactsHolder> contactsholder = new ArrayList<>();


    @Override
    public View onCreateView(LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
        View view= inflater.inflate(R.layout.contacts_recycler, container, false);
        no_contact_layout= (RelativeLayout) view.findViewById(R.id.no_contacts_layout);
        no_contact_text= (TextView) view.findViewById(R.id.no_contact_text);
        no_contact_image= (ImageView) view.findViewById(R.id.no_contact_image);
        loading_post= (RelativeLayout) view.findViewById(R.id.loading_pager_layout);
        pv_load_post= (ProgressView) view.findViewById(R.id.load_contact_progress);
        loading_post.setVisibility(View.INVISIBLE);
        TextView loading_text= (TextView) view.findViewById(R.id.load_progress_text);
        loading_text.setTypeface(Typeface.createFromAsset(getResources().getAssets(), "fontawesome.ttf"));

        return view;
    }

    @Override
    public void onViewCreated(View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);
        mRecyclerView = (RecyclerView) view.findViewById(R.id.recyclerView);
        activity=getActivity();
        hideNoPost();
        RecyclerView.LayoutManager layoutManager = new LinearLayoutManager(activity);
        mRecyclerView.setLayoutManager(layoutManager);
        mRecyclerView.setHasFixedSize(true);
        animator=new DefaultItemAnimator();
        animator.setAddDuration(1000);
        /*View v = mRecyclerView.getChildAt(0);
        load_post= (RelativeLayout) v.findViewById(R.id.load_contact_layout);
        no_internet= (RelativeLayout) v.findViewById(R.id.connect_internet_layout);
        post_list= (RelativeLayout) v.findViewById(R.id.list_contact_items);*/
        //Call to load posts from internet
        MaterialViewPagerHelper.registerRecyclerView(activity, mRecyclerView, null);
        mLayoutManager = new LinearLayoutManager(activity);
        mRecyclerView.setLayoutManager(mLayoutManager);
        ContactsHandler contactsHandler=new ContactsHandler(activity);
        if(contactsHandler.getAllPostsCount()>150)
        {
            List<ContactsHolder> holder=contactsHandler.getAllContacts("Members of Faculty and Staff");
            mAdapter = new RecyclerViewMaterialAdapter(new ContactsAdapter(holder));
            mRecyclerView.setAdapter(mAdapter);
        }
        else
            LoadPost();
        contactsHandler.closeDB();
    }

    private void LoadPost() {

        RequestQueue queue = Volley.newRequestQueue(activity);
        // final ProgressView pDialog= (ProgressView) view.findViewById(R.id.load_contact_pd);
        String url = "http://ts.icias2015.org/university/app_files/contacts.php?pass=talentspear";
        Typeface mTf=Typeface.createFromAsset(activity.getAssets(), "gothic.ttf");
        // pDialog.start();
        // showLoadPost();
        if(getLoadingVisibility()!=View.VISIBLE)
            startPostLoading();

// Request a string response from the provided URL.
        StringRequest str=new StringRequest(Request.Method.POST, url,new Response.Listener<String>() {
            @Override
            public void onResponse(String response) {
                ContactsHandler db = new ContactsHandler(activity);
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

                        contactsholder.add(adder);
                        try
                        {
                            db.addContact(adder);
                        }
                        catch (NullPointerException e)
                        {
                            Toast.makeText(activity,"Please don't change tabs while loading",Toast.LENGTH_SHORT).show();
                        }
                    }
                    if(db.getAllPostsCount()>150)
                    {
                        List<ContactsHolder> holder=db.getAllContacts("Members of Faculty and Staff");
                        hideNoPost();
                        mAdapter = new RecyclerViewMaterialAdapter(new ContactsAdapter(holder));
                        mRecyclerView.setAdapter(mAdapter);
                    }
                    else
                        LoadPost();
                    DepartmentContacts.recall();
                    Toast.makeText(activity,"Loaded Contacts",Toast.LENGTH_SHORT).show();
                    db.closeDB();
                }

                else
                {
                    Toast.makeText(activity, "Invalid Configuration", Toast.LENGTH_SHORT).show();
                    // pDialog.stop();
                    //showPostList();
                }
                stopPostLoading();
                isNetworkErrorShown=false;
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError volleyError) {
                showNoPost(true);
                //pDialog.stop();
                //showNetDisconnect();
                stopPostLoading();
                if(!isNetworkErrorShown)
                {
                    Toast.makeText(activity,"Please check your network connection",Toast.LENGTH_SHORT).show();
                    isNetworkErrorShown=true;
                }

                Dialog.Builder builder = new SimpleDialog.Builder(R.style.SimpleDialogLight){
                    @Override
                    public void onPositiveActionClicked(DialogFragment fragment) {
                        super.onPositiveActionClicked(fragment);
                        LoadPost();
                    }

                    @Override
                    public void onNegativeActionClicked(DialogFragment fragment) {
                        super.onNegativeActionClicked(fragment);
                        stopPostLoading();
                    }
                };

                ((SimpleDialog.Builder)builder).message("Connection Error. Please get a stable connection.")
                        .positiveAction("RETRY")
                        .negativeAction("CANCEL");

                DialogFragment fragment = DialogFragment.newInstance(builder);
                fragment.setCancelable(false);
                fragment.show(getChildFragmentManager(),null);

               /* if(pDialog.isShown())
                { pDialog.stop();btn.setVisibility(View.VISIBLE);}*/
            }

        }) {
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<>();
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

    public void showNoPost(boolean noInternet)
    {
        no_contact_layout.setVisibility(View.VISIBLE);
        if(!noInternet)
            no_contact_text.setText("No contacts to display.");
        else
            no_contact_text.setText("Lost connectivity.");
    }
    public void hideNoPost()
    {
        no_contact_layout.setVisibility(View.INVISIBLE);
    }

    public void startPostLoading()
    {
        YoYo.with(Techniques.FadeInUp)
                .duration(200).playOn(loading_post);
        loading_post.setVisibility(View.VISIBLE);
        pv_load_post.start();
    }

    public void stopPostLoading()
    {

        YoYo.with(Techniques.FadeOutDown)
                .duration(200).playOn(loading_post);
        if(pv_load_post.isShown())
            pv_load_post.stop();
        loading_post.setVisibility(View.INVISIBLE);

    }

    public int getLoadingVisibility()
    {
        return loading_post.getVisibility();
    }

    public class LoadThread extends Thread {

        public void run()
        {

        }
    }



}