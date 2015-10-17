package com.talentspear.university.fragments;

import android.app.Activity;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.support.annotation.Nullable;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.github.florent37.materialviewpager.MaterialViewPagerHelper;
import com.github.florent37.materialviewpager.adapter.RecyclerViewMaterialAdapter;

import com.talentspear.university.R;
import com.talentspear.university.adapter.FavouriteAdapter;
import com.talentspear.university.adapter.RecyclerViewAdapter;
import com.talentspear.university.dbhandlers.PostHandler;
import com.talentspear.university.ds.PostHolder;
import java.util.List;

public class FeaturedPosts extends Fragment {
    static RecyclerView mRecyclerView;
    static RecyclerView.Adapter mAdapter;
    static PostHandler db;
    static List<PostHolder> postholder;
    static FragmentManager fm;
    Activity activity;
    boolean hasLoadedOnce=false;
    @Override
    public View onCreateView(LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
        return inflater.inflate(R.layout.recent_posts, container, false);

    }

    public void onViewCreated(View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);
        mRecyclerView= (RecyclerView) view.findViewById(R.id.recyclerView_recentposts);
        RelativeLayout layout= (RelativeLayout) view.findViewById(R.id.no_post_layout);
        TextView no_post_text= (TextView) view.findViewById(R.id.no_post_text);
        LinearLayoutManager mLayoutManager = new LinearLayoutManager(getActivity());
        mRecyclerView.setLayoutManager(mLayoutManager);
        db=new PostHandler(getActivity());
        fm=getChildFragmentManager();
        activity=getActivity();
        postholder=db.getFavouritePosts();
        mAdapter = new RecyclerViewMaterialAdapter(new FavouriteAdapter(postholder,getChildFragmentManager(),true, mRecyclerView));
        mRecyclerView.setAdapter(mAdapter);
        MaterialViewPagerHelper.registerRecyclerView(getActivity(), mRecyclerView, null);
        if(postholder.size()>0)
        {
            layout.setVisibility(View.INVISIBLE);
        }
        else
        {
            layout.setVisibility(View.VISIBLE);
            no_post_text.setText("No Favourite Posts");

        }
        db.closeDB();


    }


}