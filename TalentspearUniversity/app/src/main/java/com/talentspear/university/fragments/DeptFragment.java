package com.talentspear.university.fragments;

import android.content.SharedPreferences;
import android.graphics.Typeface;
import android.os.Bundle;
import android.preference.PreferenceManager;
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
import android.widget.Toast;

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


public class DeptFragment extends Fragment {

    private static MaterialViewPager mViewPager;
    View rootView;
    private static Toolbar toolbar;
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        setRetainInstance(true);
        rootView = inflater.inflate(R.layout.activity_page, container, false);
        mViewPager = (MaterialViewPager) rootView.findViewById(R.id.materialViewPager);
        toolbar = mViewPager.getToolbar();
        toolbar.setTitle("Talentspear");
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
                int[] ids;
                ids=getDeptIds();
                switch (position % 2) {
                    case 0:
                        return RecyclerViewFragment.newInstance(ids[0],ids[1],ids[2]);
                    case 1:
                        return RecyclerViewFragment.newInstance(ids[3],ids[4],ids[5]);
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
                String[] tabnames;
                tabnames=getDeptLabels();
                switch (position % 2) {
                    case 0:
                        return tabnames[0];
                    case 1:
                        return tabnames[1];
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
                                "http://ts.icias2015.org/university/images/college_6.jpg");
                    case 1:
                        return HeaderDesign.fromColorAndUrl(
                                getResources().getColor(R.color.primaryColorMedium),
                                "http://ts.icias2015.org/university/images/college_5.jpg");
                }

                //execute others actions if needed (ex : modify your header logo)

                return null;
            }
        });

        mViewPager.getViewPager().setOffscreenPageLimit(mViewPager.getViewPager().getAdapter().getCount());
        mViewPager.getPagerTitleStrip().setViewPager(mViewPager.getViewPager());
        mViewPager.getViewPager().setCurrentItem(0);
        return rootView;
    }


    public int[] getDeptIds() {
        int[] ids=new int[6];
        SharedPreferences preferences= PreferenceManager.getDefaultSharedPreferences(getActivity().getApplicationContext());
        String usn=preferences.getString("USN", "4MC13EC011");
        String branch=usn.substring(5,7);
        switch(branch)
        {
            case "EC":{
                ids[0]=5;
                ids[1]=15;
                ids[2]=22;
                ids[3]=5;
                ids[4]=25;
                ids[5]=32;break;
            }
            case "CS":{
                ids[0]=6;
                ids[1]=16;
                ids[2]=23;
                ids[3]=6;
                ids[4]=26;
                ids[5]=33;break;
            }
            case "CV":{
                ids[0]=12;
                ids[1]=17;
                ids[2]=24;
                ids[3]=12;
                ids[4]=27;
                ids[5]=34;break;
            }
            case "ME":{
                ids[0]=7;
                ids[1]=19;
                ids[2]=26;
                ids[3]=7;
                ids[4]=29;
                ids[5]=36;break;
            }
            case "IS":{
                ids[0]=8;
                ids[1]=20;
                ids[2]=27;
                ids[3]=8;
                ids[4]=30;
                ids[5]=37;break;
            }
            case "EE":{
                ids[0]=10;
                ids[1]=22;
                ids[2]=29;
                ids[3]=10;
                ids[4]=31;
                ids[5]=38;break;
            }
            case "IP":{
                ids[0]=11;
                ids[1]=18;
                ids[2]=25;
                ids[3]=11;
                ids[4]=28;
                ids[5]=35;break;
            }
            case "AU":{
                ids[0]=9;
                ids[1]=21;
                ids[2]=28;
                ids[3]=9;
                ids[4]=32;
                ids[5]=39;break;
            }
            case "IT":{
                ids[0]=13;
                ids[1]=23;
                ids[2]=30;
                ids[3]=13;
                ids[4]=33;
                ids[5]=40;break;
            }
            case "EI":{
                ids[0]=13;
                ids[1]=23;
                ids[2]=30;
                ids[3]=13;
                ids[4]=33;
                ids[5]=40;break;
            }
            default:
            {
                ids[0]=0;
                ids[1]=0;
                ids[2]=0;
                ids[3]=0;
                ids[4]=0;
                ids[5]=0;break;
            }


        }
        return ids;
    }

    public String[] getDeptLabels() {
        String[] tabnames=new String[2];
        SharedPreferences preferences= PreferenceManager.getDefaultSharedPreferences(getActivity().getApplicationContext());
        String usn=preferences.getString("USN","EC");
        String branch=usn.substring(5, 7);
        switch(branch)
        {
            case "EC":{
                tabnames[0]="EC";
                tabnames[1]="EC Repository";break;
            }
            case "CS":{
                tabnames[0]="CS";
                tabnames[1]="CS Repository";break;
            }
            case "CV":{
                tabnames[0]="Civil";
                tabnames[1]="Civil Repository";break;
            }
            case "ME":{
                tabnames[0]="Mechanical";
                tabnames[1]="Mechanical Repository";break;
            }
            case "IS":{
                tabnames[0]="IS";
                tabnames[1]="IS Repository";break;
            }
            case "EE":{
                tabnames[0]="E&E";
                tabnames[1]="E&E Repository";break;
            }
            case "IP":{
                tabnames[0]="IP";
                tabnames[1]="IP Repository";break;
            }
            case "AU":{
                tabnames[0]="Automobile";
                tabnames[1]="Automobile Repository";break;
            }
            case "IT":{
                tabnames[0]="IT";
                tabnames[1]="IT Repository";break;
            }
            case "EI":{
                tabnames[0]="E&I";
                tabnames[1]="E&I Repository";break;
            }
            default:
            {
                tabnames[0]="Department";
                tabnames[1]="Department Repository";
                Toast.makeText(getActivity(),"This section is not available for freshers",Toast.LENGTH_SHORT).show();break;
            }


        }
        return tabnames;
    }




}