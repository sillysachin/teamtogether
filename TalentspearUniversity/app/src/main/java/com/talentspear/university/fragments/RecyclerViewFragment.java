package com.talentspear.university.fragments;

import android.app.Activity;
import android.graphics.Color;
import android.graphics.Typeface;
import android.os.Bundle;
import android.support.annotation.Nullable;
import android.support.v4.app.Fragment;
import android.support.v4.widget.SwipeRefreshLayout;
import android.support.v7.widget.DefaultItemAnimator;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.util.Log;
import android.view.LayoutInflater;
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
import com.talentspear.university.adapter.RecyclerViewAdapter;
import com.talentspear.university.dbhandlers.PostHandler;
import com.talentspear.university.ds.PostHolder;

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


public class RecyclerViewFragment extends Fragment {

    private RecyclerView mRecyclerView;
    private RecyclerView.Adapter mAdapter;
    int MIN_ID;
    boolean isNetworkErrorShown=false;
    private int jid=0;
    int CID;
    int TID;
    Activity activity;
    RelativeLayout no_post_layout;
    TextView no_post_text;
    DefaultItemAnimator animator;
    ImageView no_post_image;
    private RelativeLayout loading_post;
     ProgressView pv_load_post;
    private boolean loading = true;
    int pastVisiblesItems, visibleItemCount, totalItemCount;
    LinearLayoutManager mLayoutManager;
    SwipeRefreshLayout postRefresher;
    private List<PostHolder> postholder = new ArrayList<>();

    public static RecyclerViewFragment newInstance(int cid,int tid,int minid) {
        Bundle bundle = new Bundle();
        bundle.putInt("cid", cid);
        bundle.putInt("tid", tid);
        bundle.putInt("minid", minid);
        Fragment frag=new RecyclerViewFragment();
        frag.setArguments(bundle);
        return (RecyclerViewFragment) frag;
    }

    @Override
    public View onCreateView(LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
        View view= inflater.inflate(R.layout.fragment_recyclerview, container, false);
        Bundle bundle=getArguments();
        activity=getActivity();
        CID=bundle.getInt("cid");
        TID=bundle.getInt("tid");
        MIN_ID=bundle.getInt("minid");
        no_post_layout= (RelativeLayout) view.findViewById(R.id.no_post_layout);
        no_post_text= (TextView) view.findViewById(R.id.no_post_text);
        no_post_image= (ImageView) view.findViewById(R.id.no_post_image);
        loading_post= (RelativeLayout) view.findViewById(R.id.loading_pager_layout);
        pv_load_post= (ProgressView) view.findViewById(R.id.load_post_progress);
        loading_post.setVisibility(View.INVISIBLE);
        TextView loading_text= (TextView) view.findViewById(R.id.load_progress_text);
        loading_text.setTypeface(Typeface.createFromAsset(getResources().getAssets(), "fontawesome.ttf"));

        return view;
    }

    @Override
    public void onViewCreated(View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);
        mRecyclerView = (RecyclerView) view.findViewById(R.id.recyclerView);
        hideNoPost();
        postRefresher= (SwipeRefreshLayout) view.findViewById(R.id.swipePostRefreshLayout);
        postRefresher.setColorSchemeColors(Color.parseColor("#FF3F8AF8"), Color.parseColor("#FFD8433C"), Color.parseColor("#FFF2AF3A"), Color.parseColor("#FF279B5E"));
        RecyclerView.LayoutManager layoutManager = new LinearLayoutManager(activity);
        mRecyclerView.setLayoutManager(layoutManager);
        mRecyclerView.setHasFixedSize(true);
        animator=new DefaultItemAnimator();
        animator.setAddDuration(1000);

        final PostHandler postHandler=new PostHandler(activity);
        postRefresher.setOnRefreshListener(new SwipeRefreshLayout.OnRefreshListener() {
            @Override
            public void onRefresh() {
                if (jid != 0)
                    getNewPosts();
                else
                    LoadPost();
            }
        });
        jid=postHandler.getPostsCount(CID,TID);
        if(jid>0)
        {
            postholder=postHandler.getAllPosts(CID,TID);
            mAdapter = new RecyclerViewMaterialAdapter(new RecyclerViewAdapter(postholder,getChildFragmentManager(),true, mRecyclerView));
            mRecyclerView.setAdapter(mAdapter);
            getNewPosts();
            postRefresher.setEnabled(true);
            mRecyclerView.setItemAnimator(animator);
        }
        else
        {
            LoadPost();
        }
        /*View v = mRecyclerView.getChildAt(0);
        load_post= (RelativeLayout) v.findViewById(R.id.load_post_layout);
        no_internet= (RelativeLayout) v.findViewById(R.id.connect_internet_layout);
        post_list= (RelativeLayout) v.findViewById(R.id.list_post_items);*/
        //Call to load posts from internet
        MaterialViewPagerHelper.registerRecyclerView(activity, mRecyclerView, null);
        mLayoutManager = new LinearLayoutManager(activity);
        mRecyclerView.setLayoutManager(mLayoutManager);
        mRecyclerView.addOnScrollListener(new RecyclerView.OnScrollListener() {
            @Override
            public void onScrolled(RecyclerView recyclerView, int dx, int dy) {

                visibleItemCount = mLayoutManager.getChildCount();
                totalItemCount = mLayoutManager.getItemCount();
                pastVisiblesItems = mLayoutManager.findFirstVisibleItemPosition();
                if (loading) {
                    if ((visibleItemCount + pastVisiblesItems) >= totalItemCount) {
                        loading = false;
                        if (postHandler.getMinId(CID, TID) > MIN_ID) {
                            LoadPost();
                        } else {
                            Toast.makeText(activity, "Loaded all posts", Toast.LENGTH_SHORT).show();
                        }
                    }
                }
            }
        });
    }

    private void LoadPost() {

        RequestQueue queue = Volley.newRequestQueue(activity);
       // final ProgressView pDialog= (ProgressView) view.findViewById(R.id.load_post_pd);
        String url = "http://ts.icias2015.org/university/app_files/getPosts.php";
        Typeface mTf=Typeface.createFromAsset(activity.getAssets(), "gothic.ttf");
       // pDialog.start();
       // showLoadPost();
        if(getLoadingVisibility()!=View.VISIBLE)
            startPostLoading();

// Request a string response from the provided URL.
        StringRequest str=new StringRequest(Request.Method.POST, url,new Response.Listener<String>() {
            @Override
            public void onResponse(String response) {
                PostHandler db = new PostHandler(activity);
                JSONObject jsonObject=new JSONObject();
                try {
                    jsonObject= new JSONObject(response);
                } catch (JSONException e) {
                    e.printStackTrace();
                }
                JSONArray posts_array=null;
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
                        adder.setCID(CID);
                        adder.setTID(TID);
                        postholder.add(adder);
                        try
                        {
                            db.addPost(adder);
                        }
                        catch (NullPointerException e)
                        {
                            Toast.makeText(activity,"Please don't change tabs while loading",Toast.LENGTH_SHORT).show();
                        }
                    }
                   /* if(postholder.size()>1)
                    Toast.makeText(activity, "Loaded "+postholder.size()+" new posts", Toast.LENGTH_SHORT).show();
                    else
                        Toast.makeText(activity, "Loaded "+postholder.size()+" new post", Toast.LENGTH_SHORT).show();
                    //pDialog.stop();
                    //showPostList();*/
                    if(jid==0)
                    {
                        mAdapter = new RecyclerViewMaterialAdapter(new RecyclerViewAdapter(postholder,getChildFragmentManager(), true, mRecyclerView));
                        mRecyclerView.setAdapter(mAdapter);
                        postRefresher.setEnabled(true);
                        mRecyclerView.setItemAnimator(animator);
                    }
                    jid=db.getPostsCount(CID,TID);
                    mAdapter.notifyDataSetChanged();
                    db.closeDB();
                    loading=true;
                }

                else
                {
                    Toast.makeText(activity, "Loaded all posts", Toast.LENGTH_SHORT).show();
                    // pDialog.stop();
                    //showPostList();
                }
                stopPostLoading();
                isNetworkErrorShown=false;
                postRefresher.setRefreshing(false);
                if(jid==0)
                    showNoPost(false);
                else
                    hideNoPost();
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError volleyError) {
                loading=true;
                postRefresher.setRefreshing(false);
                if(jid==0)
                    showNoPost(true);
                else
                    hideNoPost();
                //pDialog.stop();
                //showNetDisconnect();
                stopPostLoading();
                if(!isNetworkErrorShown)
                {
                    Toast.makeText(activity,"Please check your network connection",Toast.LENGTH_SHORT).show();
                    isNetworkErrorShown=true;
                }

                /*Dialog.Builder builder = new SimpleDialog.Builder(R.style.SimpleDialogLight){
                    @Override
                    public void onPositiveActionClicked(DialogFragment fragment) {
                        super.onPositiveActionClicked(fragment);
                        LoadPost();
                    }

                    @Override
                    public void onNegativeActionClicked(DialogFragment fragment) {
                        super.onNegativeActionClicked(fragment);
                        PageActivity.stopPostLoading();
                    }
                };

                ((SimpleDialog.Builder)builder).message("Connection Error. Please get a stable connection.")
                        .positiveAction("RETRY")
                        .negativeAction("CANCEL");

                DialogFragment fragment = DialogFragment.newInstance(builder);
                fragment.setCancelable(false);
                fragment.show(getChildFragmentManager(),null);*/

               /* if(pDialog.isShown())
                { pDialog.stop();btn.setVisibility(View.VISIBLE);}*/
            }

        }) {
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<>();
                params.put("cid", String.valueOf(CID));
                params.put("tid", String.valueOf(TID));
                params.put("jid", String.valueOf(jid));
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

    private void getNewPosts() {

        RequestQueue queue = Volley.newRequestQueue(activity);
        // final ProgressView pDialog= (ProgressView) view.findViewById(R.id.load_post_pd);
        String url = "http://ts.icias2015.org/university/app_files/getNewPosts.php";
        Typeface mTf=Typeface.createFromAsset(activity.getAssets(), "gothic.ttf");
        // pDialog.start();
        // showLoadPost();
        if(getLoadingVisibility()!=View.VISIBLE)
            startPostLoading();
        final PostHandler db = new PostHandler(activity);
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
                        try {
                            adder.setSubject(posts.getString("sub"));
                        } catch (JSONException e) {
                            e.printStackTrace();
                        }
                        try {
                            adder.setMessage(posts.getString("message"));
                        } catch (JSONException e) {
                            e.printStackTrace();
                        }
                        try {
                            adder.setAttachment(posts.getString("link"));
                            adder.setFilesize(posts.getString("size"));
                            adder.setFilename(posts.getString("link").split("/")[8]);
                        } catch (JSONException e) {

                            adder.setAttachment("null");
                        }
                        try {
                            adder.setID(posts.getInt("id"));
                        } catch (JSONException e) {
                            e.printStackTrace();
                        }
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
                        adder.setCID(CID);
                        adder.setTID(TID);
                        //Get Appliance Count
                        postholder.add(0, adder);
                        try
                        {
                            db.addPost(adder);
                        }
                        catch (NullPointerException e)
                        {
                            Toast.makeText(activity,"Please don't change tabs while loading",Toast.LENGTH_SHORT).show();
                        }
                    }
                    mAdapter = new RecyclerViewMaterialAdapter(new RecyclerViewAdapter(postholder,getChildFragmentManager(), true, mRecyclerView));
                    mRecyclerView.setAdapter(mAdapter);
                   /* if(postholder.size()>1)
                    Toast.makeText(activity, "Loaded "+postholder.size()+" new posts", Toast.LENGTH_SHORT).show();
                    else
                        Toast.makeText(activity, "Loaded "+postholder.size()+" new post", Toast.LENGTH_SHORT).show();
                    //pDialog.stop();
                    //showPostList();*/
                    jid=db.getPostsCount(CID,TID);
                    db.closeDB();

                    loading=true;

                    Toast.makeText(activity, (posts_array.length()>1)?(posts_array.length()+" new Posts"):(posts_array.length()+" new Post"), Toast.LENGTH_SHORT).show();
                }

                else
                {
                    Toast.makeText(activity, "No new Posts", Toast.LENGTH_SHORT).show();
                    // pDialog.stop();
                    //showPostList();
                }
                postRefresher.setRefreshing(false);
                stopPostLoading();
                if(jid==0)
                    showNoPost(false);
                else
                    hideNoPost();
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError volleyError) {
                postRefresher.setRefreshing(false);
                if(jid==0)
                 showNoPost(true);
                else
                    hideNoPost();
                //pDialog.stop();
                //showNetDisconnect();
                Dialog.Builder builder = new SimpleDialog.Builder(R.style.SimpleDialogLight){
                    @Override
                    public void onPositiveActionClicked(DialogFragment fragment) {
                        super.onPositiveActionClicked(fragment);
                        getNewPosts();
                        postRefresher.setRefreshing(true);
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
                params.put("cid", String.valueOf(CID));
                params.put("tid", String.valueOf(TID));
                params.put("mid", String.valueOf(db.getMaxId(CID,TID)));
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
        no_post_layout.setVisibility(View.VISIBLE);
        if(!noInternet)
            no_post_text.setText("No posts to display. Swipe down to reload.");
        else
            no_post_text.setText("Lost connectivity. Swipe down to reload.");
    }
    public void hideNoPost()
    {
        no_post_layout.setVisibility(View.INVISIBLE);
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

}