package com.talentspear.university.fragments;

import android.content.Context;
import android.graphics.Typeface;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v7.app.ActionBar;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.support.v7.widget.Toolbar;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.inputmethod.InputMethodManager;
import android.widget.ArrayAdapter;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.melnykov.fab.FloatingActionButton;
import com.rengwuxian.materialedittext.MaterialEditText;
import com.rey.material.app.Dialog;
import com.rey.material.app.DialogFragment;
import com.rey.material.app.SimpleDialog;
import com.rey.material.widget.Spinner;

import com.talentspear.university.NavActivity;
import com.talentspear.university.R;
import com.talentspear.university.adapter.SgpaAdapter;
import com.talentspear.university.dbhandlers.SgpaHandler;
import com.talentspear.university.ds.SgpaHolder;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;

/**
 * Created by darsh on 8/12/2015.
 */
public class Sgpa extends Fragment {
    private static Toolbar toolbar;
    static FloatingActionButton add;
    DialogFragment addSubject;
    Dialog.Builder builder;
    static View rootView;
    static LinearLayout layout1;
    static RelativeLayout layout2;
    static RelativeLayout layout3;
    ArrayAdapter<String> grade_adapter;
    ArrayAdapter<String> credits_adapter;
    String courseName, courseCredits, courseGrade, courseCie, courseSee;
    MaterialEditText subName;
    TextView target_mode,estimate_mode;
    static int mode=0;
    MaterialEditText cie;
    Spinner gradeSpinner;
    RecyclerView mRecyclerView;
    Spinner creditsSpinner;
    SgpaHandler handler;
    static Context context;
    static List<SgpaHolder> sgpaHolderList;
    static TextView sgpaDisplay, sgpaEdit,targetSgpaEdit;
    static RecyclerView.Adapter mAdapter;
    static HashMap<String, String> lookup = new HashMap<String, String>();
    static HashMap<String, String> gradeLookup = new HashMap<String, String>();
    Typeface mTf, fontAwesome;

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        setRetainInstance(true);
        rootView = inflater.inflate(R.layout.activity_sgpa_calculator, container, false);
        mTf = Typeface.createFromAsset(getActivity().getAssets(), "titillium_light.ttf");
        fontAwesome = Typeface.createFromAsset(getActivity().getAssets(), "fontawesome.ttf");
        context=getActivity();
        mRecyclerView = (RecyclerView) rootView.findViewById(R.id.SgpaRecyclerView);

        layout1 = (LinearLayout) rootView.findViewById(R.id.sgpaDisplayLayout);
        layout2 = (RelativeLayout) rootView.findViewById(R.id.layout_no_sub);
        layout3 = (RelativeLayout) rootView.findViewById(R.id.noSgpaEntered);

        sgpaEdit = (TextView) rootView.findViewById(R.id.targetSgpaEdit);
        target_mode = (TextView) rootView.findViewById(R.id.target_mode);
        estimate_mode = (TextView) rootView.findViewById(R.id.estimate_mode);
        targetSgpaEdit = (TextView) rootView.findViewById(R.id.target_tvSgpa);
        sgpaEdit.setTypeface(fontAwesome);
        target_mode.setTypeface(mTf);
        estimate_mode.setTypeface(mTf);


        LinearLayoutManager mLayoutManager = new LinearLayoutManager(getActivity());
        mRecyclerView.setLayoutManager(mLayoutManager);
        sgpaHolderList = new ArrayList<>();
        handler = new SgpaHandler(getActivity());
        sgpaHolderList = handler.getAllStoredMarks();
        mAdapter = new SgpaAdapter(sgpaHolderList, getFragmentManager(), getActivity());
        mRecyclerView.setAdapter(mAdapter);

        add = (FloatingActionButton) rootView.findViewById(R.id.fab_add_subj);
        final SgpaHandler sgpaHandler = new SgpaHandler(getActivity());
        lookup.put("S", "90");
        lookup.put("A", "75");
        lookup.put("B", "60");
        lookup.put("C", "50");
        lookup.put("D", "45");
        lookup.put("E", "40");

        gradeLookup.put("S", "10");
        gradeLookup.put("A", "9");
        gradeLookup.put("B", "8");
        gradeLookup.put("C", "7");
        gradeLookup.put("D", "5");
        gradeLookup.put("E", "4");


        displaySgpa(sgpaHolderList);
        if (toolbar != null) {
            ((NavActivity) getActivity()).setSupportActionBar(toolbar);

            final ActionBar actionBar = ((NavActivity) getActivity()).getSupportActionBar();
            if (actionBar != null) {
                actionBar.setDisplayHomeAsUpEnabled(true);
                actionBar.setDisplayShowHomeEnabled(true);
                actionBar.setDisplayShowTitleEnabled(true);
                actionBar.setDisplayUseLogoEnabled(false);
                actionBar.setHomeButtonEnabled(true);
            }
        }

        add.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(final View v) {
                builder = new SimpleDialog.Builder(R.style.SimpleDialogLight) {
                    SgpaHolder sgpaHolder = new SgpaHolder();

                    protected void onBuildDone(Dialog dialog) {
                        dialog.layoutParams(ViewGroup.LayoutParams.MATCH_PARENT, ViewGroup.LayoutParams.WRAP_CONTENT);
                        subName = (MaterialEditText) dialog.findViewById(R.id.sub_name);
                        cie = (MaterialEditText) dialog.findViewById(R.id.cie_marks);
                        gradeSpinner = (Spinner) dialog.findViewById(R.id.target_grade);
                        creditsSpinner = (Spinner) dialog.findViewById(R.id.credits);
                        subName.clearFocus();

                        List<String> credits = new ArrayList<>();
                        credits.add("9.0");
                        credits.add("5.0");
                        credits.add("4.0");
                        credits.add("3.0");
                        credits.add("2.0");
                        credits.add("1.5");
                        List<String> grades = new ArrayList<>();
                        grades.add("S");
                        grades.add("A");
                        grades.add("B");
                        grades.add("C");
                        grades.add("D");
                        grades.add("E");

                        credits_adapter = new ArrayAdapter<>(getActivity(), R.layout.row_spn_light, credits);
                        credits_adapter.setDropDownViewResource(R.layout.row_spn_dropdown_light);
                        creditsSpinner.setAdapter(credits_adapter);
                        creditsSpinner.setSelection(2);
                        grade_adapter = new ArrayAdapter<>(getActivity(), R.layout.row_spn_light, grades);
                        grade_adapter.setDropDownViewResource(R.layout.row_spn_dropdown_light);
                        gradeSpinner.setAdapter(grade_adapter);
                        gradeSpinner.setSelection(1);
                    }

                    @Override
                    public void onPositiveActionClicked(final DialogFragment fragment) {
                        int icie;
                        boolean valid = true;
                        icie = Integer.parseInt(cie.getText().toString().trim());
                        List<String> grades = new ArrayList<>();
                        grades.add("S");
                        grades.add("A");
                        grades.add("B");
                        grades.add("C");
                        grades.add("D");
                        grades.add("E");
                        String grade = gradeSpinner.getSelectedItem().toString();

                        if (icie < 20) {
                            cie.setError("You cannot calculate SGPA without eligibility");
                            valid = false;
                        } else if (icie > 50 && (!creditsSpinner.getSelectedItem().toString().equals("2.0".trim()))) {
                            cie.setError("CIE marks cannot exceed 50");
                            valid = false;
                        }
                        if (valid) {
                            sgpaHolder.setSubName(subName.getText().toString().trim());
                            sgpaHolder.setSubCredits(Float.parseFloat(creditsSpinner.getSelectedItem().toString()));
                            sgpaHolder.setCieTotal(icie);
                            sgpaHolder.setGrade(grade);
                            int see = Integer.parseInt(lookup.get(gradeSpinner.getSelectedItem().toString().trim())) - Integer.parseInt(String.valueOf(icie));
                            if (see < 20)
                            {
                                see = 20;
                                int total=icie+see;
                                if( total>=40 && total < 45 )
                                    sgpaHolder.setGrade("E");
                                else if( total>=45 && total < 50 )
                                    sgpaHolder.setGrade("D");
                                else if( total>=50 && total < 60 )
                                    sgpaHolder.setGrade("C");
                                else if( total>=60 && total < 75 )
                                    sgpaHolder.setGrade("B");
                                else if( total>=75 && total < 90 )
                                    sgpaHolder.setGrade("A");
                                else if( total>=90 && total <=100 )
                                    sgpaHolder.setGrade("S");

                            }
                            if (creditsSpinner.getSelectedItem().toString().equals("2.0"))
                                see = 0;
                            sgpaHolder.setSeeMarks(see);
                            sgpaHandler.storeMarksInTable(sgpaHolder);
                            sgpaHolderList.add(sgpaHolder);
                            mAdapter = new SgpaAdapter(sgpaHolderList, getFragmentManager(), getActivity());
                            mRecyclerView.setAdapter(mAdapter);
                            displaySgpa(sgpaHolderList);
                            fragment.dismiss();
                        }

                    }

                    @Override
                    public void onNegativeActionClicked(DialogFragment fragment) {
                        super.onNegativeActionClicked(fragment);
                    }

                    @Override
                    public void onNeutralActionClicked(DialogFragment fragment) {
                        subName.setText("");

                        creditsSpinner.setSelection(2);
                        cie.setText("");
                        subName.requestFocus();
                        InputMethodManager imm = (InputMethodManager) getActivity().getSystemService(Context.INPUT_METHOD_SERVICE);
                        imm.showSoftInput(subName, InputMethodManager.SHOW_IMPLICIT);
                    }
                };
                builder.positiveAction("Add")
                        .contentView(R.layout.marks_entry_dialog)
                        .negativeAction("CANCEL")
                        .neutralAction("Clear");

                DialogFragment fragment = DialogFragment.newInstance(builder);
                fragment.setCancelable(true);
                fragment.show(getFragmentManager(), null);
            }
        });
        sgpaEdit.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if(mode==0)
                {
                    Toast.makeText(getActivity(),"Switched to Target mode",Toast.LENGTH_SHORT).show();
                    mode=1;
                    changeMode(1);
                }
                SimpleDialog.Builder builder = new SimpleDialog.Builder(R.style.SimpleDialogLight) {

                    @Override
                    public void onPositiveActionClicked(final DialogFragment fragment) {
                        MaterialEditText target_sgpa= (MaterialEditText) getDialog().findViewById(R.id.get_target_sgpa);
                        boolean valid=true;
                        if(target_sgpa.getText().toString().equals(""))
                            valid=false;
                        if(valid)
                            if(!(Float.parseFloat(target_sgpa.getText().toString())<=10))
                                valid=false;
                        if(valid)
                        {
                            float target=Float.parseFloat(target_sgpa.getText().toString());
                            targetSgpaEdit.setText(target_sgpa.getText().toString());
                            List<SgpaHolder> list=setTarget(SgpaAdapter.getHolders(),target);
                                if(list!=null)
                                {
                                mAdapter = new SgpaAdapter(list, getFragmentManager(), getActivity());
                                mRecyclerView.setAdapter(mAdapter);
                                }
                        }
                        else
                        Toast.makeText(getActivity(),"Invalid input",Toast.LENGTH_SHORT).show();
                        super.onPositiveActionClicked(fragment);
                    }

                    @Override
                    public void onNegativeActionClicked(DialogFragment fragment) {
                        super.onNegativeActionClicked(fragment);
                    }

                };
                builder.positiveAction("Set Target")
                        .contentView(R.layout.enter_target_sgpa)
                        .negativeAction("CANCEL");

                DialogFragment fragment = DialogFragment.newInstance(builder);
                fragment.setCancelable(true);
                fragment.show(getChildFragmentManager(), null);
            }
        });
        target_mode.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if(mode==0)
                {
                    Toast.makeText(getActivity(),"Switched to Target mode",Toast.LENGTH_SHORT).show();
                    mode=1;
                    changeMode(1);
                }

            }
        });
        estimate_mode.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if(mode==1)
                {
                    Toast.makeText(getActivity(),"Switched to Estimation mode",Toast.LENGTH_SHORT).show();
                    mode=0;
                    changeMode(0);
                    mAdapter = new SgpaAdapter(handler.getAllStoredMarks(), getFragmentManager(), getActivity());
                    mRecyclerView.setAdapter(mAdapter);
                }

            }
        });
        return rootView;
    }

    public static void notifyDataChanged(List<SgpaHolder> holderList) {
        mAdapter.notifyDataSetChanged();
        displaySgpa(holderList);
    }

    public static void notifyDataDeleted(List<SgpaHolder> holderList, int pos) {
        mAdapter.notifyItemRemoved(pos);
        displaySgpa(holderList);
    }


    private boolean validate(int cie, String grade) {
        boolean valid = true;
        if (cie > 50 || cie < 20)
            valid = false;
        if (cie < 40 && grade.equals("S"))
            valid = false;
        if (cie < 25 && grade.equals("A"))
            valid = false;
        return valid;
    }


    private static float calculateSgpa(List<SgpaHolder> list) {
        float sgpa = 0;
        float credits = 0;
        float gpa = 0;
        int grade;
        String sgrade;
        for (int i = 0; i < list.size(); i++) {
            SgpaHolder holder = list.get(i);
            sgrade = holder.getGrade();
            grade = Integer.parseInt(gradeLookup.get(sgrade.trim()));
            gpa += (grade * holder.getSubCredits());
            credits += holder.getSubCredits();
        }
        sgpa = gpa / credits;
        return sgpa;
    }

    public List<SgpaHolder> setTarget(List<SgpaHolder> list, float target)
    {
        if(calculateSgpa(list)>=target)
        {
            Toast.makeText(getActivity(),"Target SGPA must be more than Estimated SGPA",Toast.LENGTH_SHORT).show();
            targetSgpaEdit.setText("-");
            return null;
        }
        for(int i=0;i<list.size();i++)
        {
            if(!list.get(i).isLocked())
            {
                list.get(i).setGrade("E");
                if(calculateSgpa(list)>=target)
                {
                    list.get(i).setSeeMarks(Integer.parseInt(lookup.get(list.get(i).getGrade())) - list.get(i).getCieTotal());
                    return list;
                }
                list.get(i).setGrade("D");
                if(calculateSgpa(list)>=target)
                {
                    list.get(i).setSeeMarks(Integer.parseInt(lookup.get(list.get(i).getGrade())) - list.get(i).getCieTotal());
                    return list;
                }
                list.get(i).setGrade("C");
                if(calculateSgpa(list)>=target)
                {
                    list.get(i).setSeeMarks(Integer.parseInt(lookup.get(list.get(i).getGrade())) - list.get(i).getCieTotal());
                    return list;
                }
                list.get(i).setGrade("B");
                if(calculateSgpa(list)>=target)
                {
                    list.get(i).setSeeMarks(Integer.parseInt(lookup.get(list.get(i).getGrade())) - list.get(i).getCieTotal());
                    return list;
                }
                list.get(i).setGrade("A");
                if(calculateSgpa(list)>=target)
                {
                    list.get(i).setSeeMarks(Integer.parseInt(lookup.get(list.get(i).getGrade())) - list.get(i).getCieTotal());
                    return list;
                }
                list.get(i).setGrade("S");
                if(calculateSgpa(list)>=target)
                {
                    list.get(i).setSeeMarks(Integer.parseInt(lookup.get(list.get(i).getGrade())) - list.get(i).getCieTotal());
                     return list;
                }
            }
        }
        Toast.makeText(getActivity(),"Unlock few subjects to attain this SGPA",Toast.LENGTH_SHORT).show();
        targetSgpaEdit.setText("-");
        return null;
    }

    public static void displaySgpa(List<SgpaHolder> list) {
        float sgpaFinal = -1;
        boolean doCalculate = true;
        for (int i = 0; i < list.size(); i++) {
            if (list.get(i).getGrade().equals("-")) {
                doCalculate = false;
                break;
            }
        }
        if (list.size() == 0)
            doCalculate = false;

        if (doCalculate) {
            sgpaFinal = calculateSgpa(list);
        }
        if (list.size() == 0) {
            layout1.setVisibility(View.GONE);
            layout2.setVisibility(View.VISIBLE);
            layout3.setVisibility(View.GONE);
        } else if (sgpaFinal == -1 && list.size() != 0) {
            layout1.setVisibility(View.GONE);
            layout2.setVisibility(View.GONE);
            layout3.setVisibility(View.VISIBLE);
        } else {
            layout1.setVisibility(View.VISIBLE);
            layout2.setVisibility(View.GONE);
            layout3.setVisibility(View.GONE);
            sgpaDisplay = (TextView) rootView.findViewById(R.id.tvSgpa);
            sgpaDisplay.setText(Float.toString(sgpaFinal));
        }

    }

    public void changeMode(int m)
    {
        if(m==0)
        {
            estimate_mode.setTextColor(getResources().getColor(R.color.white));
            estimate_mode.setBackgroundColor(getResources().getColor(R.color.primaryColorMedium));
            target_mode.setTextColor(getResources().getColor(R.color.primaryColorMedium));
            target_mode.setBackgroundColor(getResources().getColor(R.color.white));
            targetSgpaEdit.setText("-");
            mode=0;
        }
        if(m==1)
        {
            target_mode.setTextColor(getResources().getColor(R.color.white));
            target_mode.setBackgroundColor(getResources().getColor(R.color.primaryColorMedium));
            estimate_mode.setTextColor(getResources().getColor(R.color.primaryColorMedium));
            estimate_mode.setBackgroundColor(getResources().getColor(R.color.white));
            mode=1;
        }
    }

    public static int getMode()
    {
        return mode;
    }
    public static Context getcontext()
    {
        return context;
    }
}
