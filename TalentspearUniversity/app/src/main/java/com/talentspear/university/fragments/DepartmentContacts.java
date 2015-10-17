package com.talentspear.university.fragments;

import android.app.Activity;
import android.content.SharedPreferences;
import android.graphics.Typeface;
import android.os.Bundle;
import android.preference.PreferenceManager;
import android.support.annotation.Nullable;
import android.support.v4.app.Fragment;
import android.support.v7.widget.DefaultItemAnimator;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.github.florent37.materialviewpager.MaterialViewPagerHelper;
import com.github.florent37.materialviewpager.adapter.RecyclerViewMaterialAdapter;
import com.melnykov.fab.FloatingActionButton;
import com.rey.material.app.Dialog;
import com.rey.material.app.DialogFragment;
import com.rey.material.app.SimpleDialog;
import com.rey.material.widget.ProgressView;

import com.talentspear.university.R;
import com.talentspear.university.adapter.ContactsAdapter;
import com.talentspear.university.dbhandlers.ContactsHandler;
import com.talentspear.university.ds.ContactsHolder;

import java.util.ArrayList;
import java.util.List;

public class DepartmentContacts extends Fragment {

    private static RecyclerView mRecyclerView;
    private static RecyclerView.Adapter mAdapter;
    static Activity activity;
    static RelativeLayout no_contact_layout;
    TextView no_contact_text;
    DefaultItemAnimator animator;
    ImageView no_contact_image;
    private RelativeLayout loading_post;
    static String usn;
    ProgressView pv_load_post;
    LinearLayoutManager mLayoutManager;
    static FloatingActionButton fab;
    private List<ContactsHolder> contactsholder = new ArrayList<>();


    @Override
    public View onCreateView(LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
        View view= inflater.inflate(R.layout.contacts_recycler, container, false);
        no_contact_layout= (RelativeLayout) view.findViewById(R.id.no_contacts_layout);
        no_contact_text= (TextView) view.findViewById(R.id.no_contact_text);
        no_contact_image= (ImageView) view.findViewById(R.id.no_contact_image);
        loading_post= (RelativeLayout) view.findViewById(R.id.loading_pager_layout);
        pv_load_post= (ProgressView) view.findViewById(R.id.load_contact_progress);
        fab= (FloatingActionButton) view.findViewById(R.id.fab_dept_contact);
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
        final ContactsHandler contactsHandler=new ContactsHandler(activity);
        SharedPreferences preferences= PreferenceManager.getDefaultSharedPreferences(getActivity().getApplicationContext());
        usn=preferences.getString("USN", "4MC13EC011");
        if(contactsHandler.getAllPostsCount()>150)
        {
            List<ContactsHolder> holder=contactsHandler.getAllContacts(getDepartmentName(usn.substring(5, 7)));
            mAdapter = new RecyclerViewMaterialAdapter(new ContactsAdapter(holder));
            mRecyclerView.setAdapter(mAdapter);
            fab.setVisibility(View.VISIBLE);
        }
        else
        {
            showNoPost(true);
            fab.setVisibility(View.INVISIBLE);
        }

        fab.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Dialog.Builder builder = new SimpleDialog.Builder(R.style.SimpleDialogLight) {
                    @Override
                    public void onPositiveActionClicked(DialogFragment fragment) {
                        List<ContactsHolder> holder = contactsHandler.getAllContacts(getDepartmentNameForBuilder(getSelectedValue().toString()));
                        mAdapter = new RecyclerViewMaterialAdapter(new ContactsAdapter(holder));
                        mRecyclerView.setAdapter(mAdapter);
                        super.onPositiveActionClicked(fragment);
                    }

                    @Override
                    public void onNegativeActionClicked(DialogFragment fragment) {
                        super.onNegativeActionClicked(fragment);
                    }
                };

                ((SimpleDialog.Builder) builder).items(new String[]{"Electronics", "Computer Science", "Civil", "Mechanical", "Information Science", "Electrical", "Industrial Production", "Automobile", "E&I"}, 0)
                        .title("Department")
                        .positiveAction("OK")
                        .negativeAction("CANCEL");
                DialogFragment fragment = DialogFragment.newInstance(builder);
                fragment.setCancelable(true);
                fragment.show(getChildFragmentManager(), null);
            }
        });
        contactsHandler.closeDB();
    }

    public void showNoPost(boolean noInternet)
    {
        no_contact_layout.setVisibility(View.VISIBLE);
        if(!noInternet)
            no_contact_text.setText("No contacts to display.");
        else
            no_contact_text.setText("Lost connectivity.");
    }
    public static void hideNoPost()
    {
        no_contact_layout.setVisibility(View.INVISIBLE);
        fab.setVisibility(View.VISIBLE);
    }

    public static String getDepartmentName(String usn)
    {
        String deptname;
        switch(usn)
        {
            case "EC":{
                deptname="Electronics";break;
            }
            case "CS":{
                deptname="Computer Science";break;
            }
            case "CV":{
                deptname="Civil ";
                break;
            }
            case "ME":{
                deptname="Mechanical";
                break;
            }
            case "IS":{
                deptname="Information Science";
                break;
            }
            case "EE":{
                deptname="Electrical";
                break;
            }
            case "IP":{
                deptname="Industrial Production";
                break;
            }
            case "AU":{
                deptname="Automobile";
                break;
            }
            case "IT":{
                deptname="Electronics & Instrumentation";
                break;
            }
            case "EI":{
                deptname="Electronics & Instrumentation";
                break;
            }
            default:
            {
                deptname="None";
                break;
            }


        }
        return deptname;
    }

    public String getDepartmentNameForBuilder(String usn)
    {
        String deptname;
        switch(usn)
        {
            case "Electronics":{
                deptname="Electronics";break;
            }
            case "Computer Science":{
                deptname="Computer Science";break;
            }
            case "Civil":{
                deptname="Civil ";
                break;
            }
            case "Mechanical":{
                deptname="Mechanical";
                break;
            }
            case "Information Science":{
                deptname="Information Science";
                break;
            }
            case "Electrical":{
                deptname="Electrical";
                break;
            }
            case "Industrial Production":{
                deptname="Industrial Production";
                break;
            }
            case "Automobile":{
                deptname="Automobile";
                break;
            }
            case "E&I":{
                deptname="Electronics & Instrumentation";
                break;
            }
            default:
            {
                deptname="None";
                break;
            }


        }
        return deptname;
    }
public static void recall()
{
    ContactsHandler contactsHandler=new ContactsHandler(activity);
    List<ContactsHolder> holder = contactsHandler.getAllContacts(getDepartmentName(usn.substring(5,7)));
    mAdapter = new RecyclerViewMaterialAdapter(new ContactsAdapter(holder));
    mRecyclerView.setAdapter(mAdapter);
    hideNoPost();

}


}