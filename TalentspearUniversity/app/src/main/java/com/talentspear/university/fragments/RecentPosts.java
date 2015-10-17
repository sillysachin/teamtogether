package com.talentspear.university.fragments;

import android.os.Bundle;
import android.support.annotation.Nullable;
import android.support.v4.app.Fragment;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.RelativeLayout;

import com.github.florent37.materialviewpager.MaterialViewPagerHelper;
import com.github.florent37.materialviewpager.adapter.RecyclerViewMaterialAdapter;

import com.talentspear.university.R;
import com.talentspear.university.adapter.RecyclerViewAdapter;
import com.talentspear.university.dbhandlers.PostHandler;
import com.talentspear.university.ds.PostHolder;
import java.util.List;

public class RecentPosts extends Fragment {


    @Override
    public View onCreateView(LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
        return inflater.inflate(R.layout.recent_posts, container, false);

    }

    public void onViewCreated(View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);
        RecyclerView mRecyclerView= (RecyclerView) view.findViewById(R.id.recyclerView_recentposts);
        RelativeLayout layout= (RelativeLayout) view.findViewById(R.id.no_post_layout);
        LinearLayoutManager mLayoutManager = new LinearLayoutManager(getActivity());
        mRecyclerView.setLayoutManager(mLayoutManager);
        List<PostHolder> postholder;
        PostHandler db=new PostHandler(getActivity());
        postholder=db.getRecentPosts();
        RecyclerView.Adapter mAdapter = new RecyclerViewMaterialAdapter(new RecyclerViewAdapter(postholder,getChildFragmentManager(),true, mRecyclerView));
        mRecyclerView.setAdapter(mAdapter);
        MaterialViewPagerHelper.registerRecyclerView(getActivity(), mRecyclerView, null);
        if(db.getAllPostsCount()>0)
        {
            layout.setVisibility(View.INVISIBLE);
        }
        else
        {
            layout.setVisibility(View.VISIBLE);

        }
    db.closeDB();

    }


}