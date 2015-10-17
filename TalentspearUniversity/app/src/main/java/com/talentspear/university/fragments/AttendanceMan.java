package com.talentspear.university.fragments;

import android.content.Intent;
import android.graphics.Color;
import android.graphics.Typeface;
import android.os.Bundle;
import android.support.annotation.Nullable;
import android.support.v4.app.Fragment;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.daimajia.androidanimations.library.Techniques;
import com.daimajia.androidanimations.library.YoYo;
import com.github.mikephil.charting.animation.Easing;
import com.github.mikephil.charting.charts.BarChart;
import com.github.mikephil.charting.charts.Chart;
import com.github.mikephil.charting.components.Legend;
import com.github.mikephil.charting.components.LimitLine;
import com.github.mikephil.charting.components.XAxis;
import com.github.mikephil.charting.components.YAxis;
import com.github.mikephil.charting.data.BarData;
import com.github.mikephil.charting.data.BarDataSet;
import com.github.mikephil.charting.data.BarEntry;
import com.github.mikephil.charting.data.Entry;
import com.github.mikephil.charting.listener.OnChartValueSelectedListener;
import com.github.mikephil.charting.utils.Highlight;
import com.github.mikephil.charting.utils.ValueFormatter;

import com.talentspear.university.ManageBunks;
import com.talentspear.university.NavActivity;
import com.talentspear.university.R;
import com.talentspear.university.SubjectAttendance;
import com.talentspear.university.dbhandlers.AttendanceHandler;
import com.talentspear.university.dbhandlers.BunkHandler;
import com.talentspear.university.ds.AttendanceHolder;
import com.talentspear.university.ds.BunkHolder;

import java.text.DecimalFormat;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

public class AttendanceMan extends Fragment {
    ImageView no_chart_image;
    TextView no_chart_text;
    BarChart chart;
    int REQUEST_CODE=5001;
    TextView manage_attendance;
    HashMap<Float,Integer> maxAttendance;
    List<AttendanceHolder> attendanceHolders;
    int max=0;
    @Override
    public View onCreateView(LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
        setHasOptionsMenu(true);
        View view= inflater.inflate(R.layout.attendance_man, container, false);
        no_chart_image= (ImageView) view.findViewById(R.id.no_attendance_image);
        no_chart_text= (TextView) view.findViewById(R.id.no_attendance_text);
        maxAttendance=new HashMap<>();
        maxAttendance.put(4f,50);
        maxAttendance.put(5f,50);
        maxAttendance.put(3f,37);
        maxAttendance.put(2f, 25);
        maxAttendance.put(1.5f,19);
        maxAttendance.put(1f,12);
        chart = (BarChart) view.findViewById(R.id.attendance_chart);
         manage_attendance= (TextView) view.findViewById(R.id.manage_subjects);
        YoYo.with(Techniques.SlideInUp)
                .duration(700).playOn(manage_attendance);
        manage_attendance.setTypeface(Typeface.createFromAsset(getResources().getAssets(), "titillium_light.ttf"));
        RelativeLayout layout= (RelativeLayout) view.findViewById(R.id.layout_container_chart);
        RelativeLayout.LayoutParams params = new RelativeLayout.LayoutParams(
                RelativeLayout.LayoutParams.WRAP_CONTENT,
                RelativeLayout.LayoutParams.WRAP_CONTENT
        );
        params.setMargins(0, Math.round(NavActivity.getToolbarHeight()), 0, 0);
        layout.setLayoutParams(params);
        chart= (BarChart) view.findViewById(R.id.attendance_chart);
        getMeChart();

        AttendanceHandler attendanceHandler=new AttendanceHandler(getActivity());
        List<AttendanceHolder> attendanceHolders=attendanceHandler.getAllSubjects();
        if(attendanceHolders.size()>0)
            showChart();
        else
            showNoChart();
        attendanceHandler.closedb();
        manage_attendance.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                YoYo.with(Techniques.SlideOutDown)
                        .duration(700).playOn(manage_attendance);
                startActivityForResult(new Intent(getActivity(), ManageBunks.class), REQUEST_CODE);
            }
        });
        return view;
    }

    public void showChart()
    {
        no_chart_image.setVisibility(View.INVISIBLE);
        no_chart_text.setVisibility(View.INVISIBLE);
        chart.setVisibility(View.VISIBLE);
    }

    public void showNoChart()
    {
        no_chart_image.setVisibility(View.VISIBLE);
        no_chart_text.setVisibility(View.VISIBLE);
        chart.setVisibility(View.INVISIBLE);
    }

    public void getMeChart()
    {
        ArrayList<BarEntry> adder = new ArrayList<>();
        AttendanceHandler attendanceHandler=new AttendanceHandler(getActivity());
        attendanceHolders=new ArrayList<>();
        List<BarDataSet> dataSet = new ArrayList<>();
        attendanceHolders=attendanceHandler.getAllSubjects();
        BunkHandler bunkHandler=new BunkHandler(getActivity());
        YAxis leftAxis = chart.getAxisLeft();
        for(int i=0;i<attendanceHolders.size();i++) {
            int temp = bunkHandler.getAllBunksCount(attendanceHolders.get(i).getSubjectId());
            if (max < temp)
                max = temp;
        }
        if(max<8)
            leftAxis.setAxisMaxValue(8f);
        else if(max<10)
            leftAxis.setAxisMaxValue(10f);
        else if(max<15)
            leftAxis.setAxisMaxValue(15f);
        else
            leftAxis.setAxisMaxValue(30f);
        ArrayList<String> xVals = new ArrayList<>();
        HashMap<Integer,String> limits=new HashMap<>();
        leftAxis.removeAllLimitLines();
        int[] colors=new int[attendanceHolders.size()];
        boolean isLimitedReached=false;
        for(int i=0;i<attendanceHolders.size();i++) {
            int temp = bunkHandler.getAllBunksCount(attendanceHolders.get(i).getSubjectId());
            adder.add(new BarEntry(temp,i));
            String subname = attendanceHolders.get(i).getSubName().length() > 5 ? attendanceHolders.get(i).getSubName().substring(0, 5) + ".." : attendanceHolders.get(i).getSubName();
            xVals.add(subname);
            if(getPercentage(i,temp)<75)
                colors[i]=Color.parseColor("#F44336");
            else
                colors[i]=getResources().getColor(R.color.primaryColorMedium);
            if (getPercentage(i, temp) < 85 && getPercentage(i, temp) > 75) {
                if (getPercentage(i, temp + 1) < 75) {
                    if (limits.containsKey(temp)) {
                        String subjects = limits.get(temp);
                        limits.remove(temp);
                        limits.put(temp, subjects + ", " + subname + "( 75% Limit reached!)");
                    } else {
                        limits.remove(temp);
                        isLimitedReached=true;
                        limits.put(temp, subname + " ( 75% Limit reached!)");
                    }
                } else if (getPercentage(i, temp + 2) < 75) {
                    if(!isLimitedReached || !limits.containsKey(temp+1) )
                    {
                        if (limits.containsKey(temp+1)) {
                            String subjects = limits.get(temp+1);
                            limits.remove(temp+1);
                            limits.put(temp + 1, subjects + ", " + subname + "( Only 1 bunks left for 75% )");
                        } else {
                            limits.put(temp + 1, subname + " ( Only 1 bunk left for 75% )");
                        }
                    }

                }
            }
        }
        for(Map.Entry<Integer, String> entry : limits.entrySet()) {
            Integer key = entry.getKey();
            String value = entry.getValue();
            LimitLine ll = new LimitLine(key, value);
            ll.setLineColor(Color.RED);
            ll.setLineWidth(1f);
            ll.setTextColor(Color.parseColor("#01579B"));
            ll.setTextSize(7f);
            leftAxis.addLimitLine(ll);
        }

        BarDataSet setData = new BarDataSet(adder, "Subjects");
        setData.setColors(colors);
        setData.setValueFormatter(new MyValueFormatter());
        setData.setValueTextColor(Color.WHITE);
        setData.setValueTextSize(10f);
        setData.setAxisDependency(YAxis.AxisDependency.LEFT);
        if (adder.size() <=8){ // barEntries is my Entry Array
            int factor = 10; // increase this to decrease the bar width. Decrease to increase he bar width
            int percent = (factor - adder.size())*10;
            setData.setBarSpacePercent(percent);
        }
        dataSet.add(setData);
        BarData finaldata = new BarData(xVals,dataSet);
        chart.setData(finaldata);
        chart.setOnChartValueSelectedListener(new OnChartValueSelectedListener() {
            @Override
            public void onValueSelected(Entry e, int dataSetIndex, Highlight h) {
                Intent intent = new Intent(getActivity(), SubjectAttendance.class);
                intent.putExtra("sid", attendanceHolders.get(e.getXIndex()).getSubjectId());
                intent.putExtra("name", attendanceHolders.get(e.getXIndex()).getSubName());
                intent.putExtra("credits", attendanceHolders.get(e.getXIndex()).getSubCredits());
                startActivityForResult(intent, REQUEST_CODE);
            }

            @Override
            public void onNothingSelected() {

            }
        });
        //Config Chart
        chart.setDoubleTapToZoomEnabled(false);
        chart.setPinchZoom(false);
        chart.setDrawValueAboveBar(false);
        XAxis xAxis = chart.getXAxis();
        xAxis.setDrawGridLines(false);
        YAxis right = chart.getAxisRight();
        leftAxis.setDrawGridLines(false);
        right.setDrawGridLines(false);
        chart.setBackgroundColor(Color.parseColor("#FFFFFF"));
        leftAxis.setTextColor(Color.parseColor("#3F51B5"));
        xAxis.setTextColor(Color.parseColor("#3F51B5"));
        xAxis.setTextSize(10.20f);
        leftAxis.setValueFormatter(new MyValueFormatter());
        leftAxis.setTextSize(13.20f);
        right.setEnabled(false);
        chart.setDescription("");
        chart.setBorderColor(Color.parseColor("#3F51B5"));
        leftAxis.setPosition(YAxis.YAxisLabelPosition.OUTSIDE_CHART);
        xAxis.setPosition(XAxis.XAxisPosition.BOTTOM);
        chart.animateY(1000, Easing.EasingOption.EaseInSine);
        xAxis.setTypeface(Typeface.createFromAsset(getResources().getAssets(), "titillium_light.ttf"));
        chart.setDescription("");
        Legend legend = chart.getLegend();
        legend.setEnabled(false);
        legend.setPosition(Legend.LegendPosition.BELOW_CHART_CENTER);
        legend.setTextColor(Color.parseColor("#3F51B5"));
        chart.setHighlightEnabled(false);
        String size= String.valueOf(leftAxis.getTextSize());
        String xsize= String.valueOf(xAxis.getTextSize());
        legend.setTypeface(Typeface.createFromAsset(getResources().getAssets(), "titillium_light.ttf"));
        chart.fitScreen();
        chart.setGridBackgroundColor(Color.parseColor("#ffffff"));
        xAxis.setAxisLineColor(Color.parseColor("#3F51B5"));
        xAxis.setLabelsToSkip(0);
        xAxis.setAvoidFirstLastClipping(true);
        leftAxis.setAxisLineColor(Color.parseColor("#3F51B5"));
        //Start Chart
        chart.invalidate();
        chart.notifyDataSetChanged();
    }
    public class MyValueFormatter implements ValueFormatter {

        private DecimalFormat mFormat;

        public MyValueFormatter() {
            mFormat = new DecimalFormat("###,###,##"); // use one decimal
        }

        @Override
        public String getFormattedValue(float value) {
            return mFormat.format(value); // append a dollar-sign
        }
    }

    @Override
    public void onResume() {
        chart.invalidate();
        super.onResume();
    }

    @Override
    public void onActivityResult(int requestCode, int resultCode, Intent data) {
        YoYo.with(Techniques.SlideInUp)
                .duration(700).playOn(manage_attendance);
        getMeChart();
        super.onActivityResult(requestCode, resultCode, data);
    }

    public int getPercentage(int i,int bunks)
    {
        Float per=((float)(maxAttendance.get(attendanceHolders.get(i).getSubCredits())-bunks)/maxAttendance.get(attendanceHolders.get(i).getSubCredits()))*100;
        return Math.round(per);
    }
}