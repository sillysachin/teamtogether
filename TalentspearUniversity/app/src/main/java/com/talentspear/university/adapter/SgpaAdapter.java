package com.talentspear.university.adapter;

import android.content.Context;
import android.graphics.Typeface;
import android.support.v4.app.FragmentActivity;
import android.support.v4.app.FragmentManager;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.inputmethod.InputMethodManager;
import android.widget.ArrayAdapter;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.daimajia.androidanimations.library.Techniques;
import com.daimajia.androidanimations.library.YoYo;
import com.rengwuxian.materialedittext.MaterialEditText;
import com.rey.material.app.Dialog;
import com.rey.material.app.DialogFragment;
import com.rey.material.app.SimpleDialog;
import com.rey.material.widget.Spinner;

import com.talentspear.university.R;
import com.talentspear.university.dbhandlers.SgpaHandler;
import com.talentspear.university.ds.SgpaHolder;
import com.talentspear.university.fragments.Sgpa;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;

/**
 * Created by darsh on 8/12/2015.
 */
public class SgpaAdapter extends RecyclerView.Adapter<SgpaAdapter.ViewHolder> {

    static List<SgpaHolder> sgpaHolderList;
    SgpaHolder holder;
    static HashMap<String, String> lookup = new HashMap<String, String>();
    static HashMap<String, String> gradeLookup = new HashMap<String, String>();
    final FragmentManager manager;
    FragmentActivity fragmentActivity;
    SgpaHandler sgpaHandler;

    public class ViewHolder extends RecyclerView.ViewHolder {
        // each data item is just a string in this case
        public TextView name, grade, see, cie, edit, delete, showMore, lock;
        public RelativeLayout optionsLayout, optionsShadow;
        Typeface mTf, fontAwesome;
        ViewGroup viewer;

        public ViewHolder(View v) {
            super(v);
            viewer = (ViewGroup) v;
            sgpaHandler = new SgpaHandler(v.getContext());
            mTf = Typeface.createFromAsset(v.getContext().getAssets(), "titillium_light.ttf");
            fontAwesome = Typeface.createFromAsset(v.getContext().getAssets(), "fontawesome.ttf");

            name = (TextView) v.findViewById(R.id.listSubName);
            grade = (TextView) v.findViewById(R.id.ListGrade);
            TextView cap_grade = (TextView) v.findViewById(R.id.captionGrade);
            see = (TextView) v.findViewById(R.id.ListSeeMarks);
            TextView cap_see = (TextView) v.findViewById(R.id.captionSee);
            cie = (TextView) v.findViewById(R.id.ListCieMarks);
            TextView cap_cie = (TextView) v.findViewById(R.id.captionCie);
            edit = (TextView) v.findViewById(R.id.edit_marks);
            delete = (TextView) v.findViewById(R.id.delete_marks);
            showMore = (TextView) v.findViewById(R.id.showMoreOption);
            optionsLayout = (RelativeLayout) v.findViewById(R.id.optionsLayout);
            optionsShadow = (RelativeLayout) v.findViewById(R.id.optionsDrawer);
            lock = (TextView) v.findViewById(R.id.lock_marks);

            edit.setTypeface(fontAwesome);
            delete.setTypeface(fontAwesome);
            showMore.setTypeface(fontAwesome);
            lock.setTypeface(fontAwesome);

            name.setTypeface(mTf);
            grade.setTypeface(mTf);
            cap_grade.setTypeface(mTf);
            cap_cie.setTypeface(mTf);
            cap_see.setTypeface(mTf);
            see.setTypeface(mTf);
            cie.setTypeface(mTf);

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

        }
    }

    public SgpaAdapter(List<SgpaHolder> list, FragmentManager fragmentManager, FragmentActivity activity) {
        this.sgpaHolderList = list;
        this.manager = fragmentManager;
        this.fragmentActivity = activity;
    }

    @Override
    public int getItemViewType(int position) {
        return position;
    }


    @Override
    public ViewHolder onCreateViewHolder(final ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.subject_item_layout, parent, false);
        ViewHolder vh = new ViewHolder(view);
        return vh;
    }

    @Override
    public void onBindViewHolder(final ViewHolder viewHolder, final int position) {

        int see = sgpaHolderList.get(position).getSeeMarks();
        String grade = sgpaHolderList.get(position).getGrade();
        viewHolder.name.setText(sgpaHolderList.get(position).getSubName());

        viewHolder.grade.setText(sgpaHolderList.get(position).getGrade());
        if (grade.equals("-"))
            viewHolder.grade.setVisibility(View.GONE);
        else {
            viewHolder.see.setText(grade);
            viewHolder.grade.setVisibility(View.VISIBLE);
        }
        if (see == -1)
            viewHolder.see.setVisibility(View.GONE);
        else {
            viewHolder.see.setText(Integer.toString(see));
            viewHolder.see.setVisibility(View.VISIBLE);
        }

        viewHolder.cie.setText(Integer.toString(sgpaHolderList.get(position).getCieTotal()));

        viewHolder.delete.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                remove(position);
            }
        });

        viewHolder.setIsRecyclable(true);
        viewHolder.viewer.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if (viewHolder.optionsLayout.getVisibility() == View.GONE) {
                    viewHolder.optionsLayout.setVisibility(View.VISIBLE);
                    viewHolder.optionsShadow.setVisibility(View.VISIBLE);
                    YoYo.with(Techniques.BounceInDown)
                            .duration(700).playOn(viewHolder.optionsLayout);
                    viewHolder.showMore.setText(v.getContext().getResources().getString(R.string.show_less));
                } else {
                    viewHolder.optionsLayout.setVisibility(View.GONE);
                    viewHolder.optionsShadow.setVisibility(View.GONE);
                    viewHolder.showMore.setText(v.getContext().getResources().getString(R.string.show_more));
                }
            }
        });
        viewHolder.lock.setTag(position);

        viewHolder.lock.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Integer taggedpos = (Integer) v.getTag();
                if (viewHolder.lock.getText().equals(v.getContext().getResources().getString(R.string.lock_marks))) {
                    viewHolder.lock.setText(v.getContext().getResources().getString(R.string.unlock_marks));
                    sgpaHolderList.get(taggedpos).setIsLocked(true);

                } else {
                    viewHolder.lock.setText(v.getContext().getResources().getString(R.string.lock_marks));
                    sgpaHolderList.get(taggedpos).setIsLocked(false);
                }
            }
        });

        viewHolder.showMore.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Integer taggedpos = (Integer) v.getTag();
                if (viewHolder.optionsLayout.getVisibility() == View.GONE) {
                    viewHolder.optionsLayout.setVisibility(View.VISIBLE);
                    viewHolder.optionsShadow.setVisibility(View.VISIBLE);
                    YoYo.with(Techniques.BounceInDown)
                            .duration(700).playOn(viewHolder.optionsLayout);
                    viewHolder.showMore.setText(v.getContext().getResources().getString(R.string.show_less));
                } else {
                    viewHolder.optionsLayout.setVisibility(View.GONE);
                    viewHolder.optionsShadow.setVisibility(View.GONE);
                    viewHolder.showMore.setText(v.getContext().getResources().getString(R.string.show_more));
                }
            }
        });

        viewHolder.edit.setOnClickListener(new View.OnClickListener() {
            Spinner gradeSpinner;
            Spinner creditsSpinner;
            MaterialEditText subName;
            MaterialEditText cieMarks;
            List<String> grades = new ArrayList<>();
            List<String> credits = new ArrayList<>();

            @Override
            public void onClick(final View v) {
                if (Sgpa.getMode() == 0) {
                    holder = sgpaHolderList.get(position);
                    SimpleDialog.Builder builder = new SimpleDialog.Builder(R.style.SimpleDialogLight) {
                        protected void onBuildDone(Dialog dialog) {
                            dialog.layoutParams(ViewGroup.LayoutParams.MATCH_PARENT, ViewGroup.LayoutParams.WRAP_CONTENT);
                            subName = (MaterialEditText) dialog.findViewById(R.id.sub_name);
                            subName.setText(holder.getSubName());

                            cieMarks = (MaterialEditText) dialog.findViewById(R.id.cie_marks);
                            cieMarks.setText(Integer.toString(holder.getCieTotal()));

                            gradeSpinner = (Spinner) dialog.findViewById(R.id.target_grade);
                            creditsSpinner = (Spinner) dialog.findViewById(R.id.credits);

                            grades.clear();
                            grades.add("S");
                            grades.add("A");
                            grades.add("B");
                            grades.add("C");
                            grades.add("D");
                            grades.add("E");

                            credits.clear();
                            credits.add("9.0");
                            credits.add("5.0");
                            credits.add("4.0");
                            credits.add("3.0");
                            credits.add("2.0");
                            credits.add("1.5");

                            ArrayAdapter grade_adapter = new ArrayAdapter<>(v.getContext(), R.layout.row_spn_light, grades);
                            grade_adapter.setDropDownViewResource(R.layout.row_spn_dropdown_light);
                            gradeSpinner.setAdapter(grade_adapter);
                            gradeSpinner.setSelection(grades.indexOf(holder.getGrade()));

                            ArrayAdapter<String> credits_adapter = new ArrayAdapter<>(v.getContext(), R.layout.row_spn_light, credits);
                            credits_adapter.setDropDownViewResource(R.layout.row_spn_dropdown_light);
                            creditsSpinner.setAdapter(credits_adapter);
                            creditsSpinner.setSelection(credits.indexOf(Float.toString(holder.getSubCredits())));

                        }

                        @Override
                        public void onPositiveActionClicked(final DialogFragment fragment) {
                            int icie = 0;
                            String sgrade;
                            boolean valid = true;
                            try {
                                icie = Integer.parseInt(cieMarks.getText().toString().trim());
                            } catch (NumberFormatException e) {
                                valid = false;
                            }
                            sgrade = gradeSpinner.getSelectedItem().toString().trim();
                            if (valid) {
                                if (icie < 20) {
                                    cieMarks.setError("You cannot calculate SGPA without eligibility");
                                    valid = false;
                                } else if (icie > 50 && (!creditsSpinner.getSelectedItem().toString().equals("2.0".trim()))) {
                                    cieMarks.setError("CIE marks cannot exceed 50");
                                    valid = false;
                                } else if (((icie < 25 && sgrade.equals("A")) || (icie < 40 && sgrade.equals("S"))) && !creditsSpinner.getSelectedItem().toString().equals("2.0".trim())) {
                                    valid = false;
                                    cieMarks.setError("You cannot target " + sgrade + " grade with this CIE marks");
                                    gradeSpinner.performClick();
                                }
                            }
                            if (valid) {
                                holder.setSubName(subName.getText().toString().trim());
                                holder.setSubCredits(Float.parseFloat(creditsSpinner.getSelectedItem().toString()));
                                holder.setCieTotal(icie);
                                holder.setGrade(sgrade);
                                int see = Integer.parseInt(lookup.get(gradeSpinner.getSelectedItem().toString().trim())) - Integer.parseInt(cieMarks.getText().toString().trim());
                                if (see < 20)
                                    see = 20;
                                if (creditsSpinner.getSelectedItem().toString().equals("2.0"))
                                    see = 0;
                                holder.setSeeMarks(see);
                                SgpaHandler sgpaHandler = new SgpaHandler(v.getContext());
                                sgpaHandler.updateMarksTuple(holder);
                                sgpaHolderList.remove(position);
                                sgpaHolderList.add(position, holder);
                                notifyItemChanged(position);
                                Sgpa.notifyDataChanged(sgpaHolderList);
                                notifyItemChanged(position);
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
                            cieMarks.setText("");
                            gradeSpinner.setSelection(0);
                            subName.requestFocus();
                            InputMethodManager imm = (InputMethodManager) v.getContext().getSystemService(Context.INPUT_METHOD_SERVICE);
                            imm.showSoftInput(subName, InputMethodManager.SHOW_IMPLICIT);
                        }

                    };
                    builder.positiveAction("Update")
                            .contentView(R.layout.marks_entry_dialog)
                            .negativeAction("CANCEL")
                            .neutralAction("Clear");

                    DialogFragment fragment = DialogFragment.newInstance(builder);
                    fragment.setCancelable(true);
                    fragment.show(manager, null);
                } else
                    Toast.makeText(Sgpa.getcontext(), "Switch to estimation mode to update", Toast.LENGTH_SHORT).show();
            }
        });

    }

    public void remove(int position) {
        if (Sgpa.getMode() == 0) {
            holder = sgpaHolderList.get(position);

            sgpaHandler.deleteEntry(holder.getSubjectId());
            sgpaHolderList.remove(position);
            Sgpa.displaySgpa(sgpaHolderList);
            notifyItemRemoved(position);
        } else
            Toast.makeText(Sgpa.getcontext(), "Switch to estimation mode to delete", Toast.LENGTH_SHORT).show();
    }


    @Override
    public int getItemCount() {
        return sgpaHolderList.size();
    }

    public static List<SgpaHolder> getHolders() {
        return sgpaHolderList;
    }


}
