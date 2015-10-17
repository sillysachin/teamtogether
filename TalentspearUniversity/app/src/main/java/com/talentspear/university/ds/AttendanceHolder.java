package com.talentspear.university.ds;

/**
 * Created by Darshan Muralidhar on 24-Jul-15.
 */
public class AttendanceHolder {
    private int subjectId;
    private String subName;
    private float subCredits;



    public float getSubCredits() {
        return subCredits;
    }

    public void setSubCredits(float subCredits) {
        this.subCredits = subCredits;
    }

    public int getSubjectId() {
        return subjectId;
    }

    public void setSubjectId(int subjectId) {
        this.subjectId = subjectId;
    }

    public String getSubName() {
        return subName;
    }

    public void setSubName(String subName) {
        this.subName = subName;
    }
}
