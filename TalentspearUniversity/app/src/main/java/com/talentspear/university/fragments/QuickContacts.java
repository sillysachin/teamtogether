package com.talentspear.university.fragments;

import android.graphics.Typeface;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentStatePagerAdapter;
import android.support.v4.content.res.ResourcesCompat;
import android.support.v7.app.ActionBar;
import android.support.v7.widget.Toolbar;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.daimajia.androidanimations.library.Techniques;
import com.daimajia.androidanimations.library.YoYo;
import com.github.florent37.materialviewpager.MaterialViewPager;
import com.github.florent37.materialviewpager.header.HeaderDesign;
import com.rey.material.widget.ProgressView;

import com.talentspear.university.NavActivity;
import com.talentspear.university.R;
import com.talentspear.university.fragments.RecyclerViewFragment;
import com.talentspear.university.fragments.ScrollFragment;

import org.w3c.dom.Text;

import java.util.ArrayList;
import java.util.List;


public class QuickContacts extends Fragment {

    private static MaterialViewPager mViewPager;
    View rootView;
    private static Toolbar toolbar;
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        setRetainInstance(true);
        rootView = inflater.inflate(R.layout.activity_page, container, false);
        mViewPager = (MaterialViewPager) rootView.findViewById(R.id.materialViewPager);
        toolbar = mViewPager.getToolbar();
        toolbar.setTitle("TALENTSPEAR");
        if (toolbar != null) {
            ((NavActivity)getActivity()).setSupportActionBar(toolbar);

            final ActionBar actionBar = ((NavActivity)getActivity()).getSupportActionBar();
            if (actionBar != null) {
                actionBar.setDisplayHomeAsUpEnabled(true);
                actionBar.setDisplayShowHomeEnabled(true);
                actionBar.setDisplayShowTitleEnabled(true);
                actionBar.setDisplayUseLogoEnabled(false);
                actionBar.setHomeButtonEnabled(true);
            }
        }
        toolbar.setVisibility(View.INVISIBLE);
        mViewPager.getViewPager().setAdapter(new FragmentStatePagerAdapter(getChildFragmentManager()) {

            @Override
            public Fragment getItem(int position) {
                switch (position % 2) {
                    case 0:
                        return new OfficeContacts();
                    case 1:
                        return new DepartmentContacts();
                    default:
                        return RecyclerViewFragment.newInstance(2,35,47);
                }
            }

            @Override
            public int getCount() {
                return 2;
            }

            @Override
            public CharSequence getPageTitle(int position) {
                switch (position % 2) {
                    case 0:
                        return "Office";
                    case 1:
                        return "Department";
                }
                return "";
            }
        });

        mViewPager.setMaterialViewPagerListener(new MaterialViewPager.Listener() {
            @Override
            public HeaderDesign getHeaderDesign(int page) {
                switch (page) {
                    case 0:
                        return HeaderDesign.fromColorAndUrl(
                                getResources().getColor(R.color.primaryColor),
                                "http://ts.icias2015.org/university/images/college_7.jpg");
                    case 1:
                        return HeaderDesign.fromColorAndUrl(
                                getResources().getColor(R.color.primaryColorMedium),
                                "http://ts.icias2015.org/university/images/college_8.jpg");
                }

                //execute others actions if needed (ex : modify your header logo)

                return null;
            }
        });

        mViewPager.getViewPager().setOffscreenPageLimit(mViewPager.getViewPager().getAdapter().getCount());
        mViewPager.getPagerTitleStrip().setViewPager(mViewPager.getViewPager());
        mViewPager.getViewPager().setCurrentItem(1);
        mViewPager.getViewPager().setCurrentItem(0);
        return rootView;
    }




}