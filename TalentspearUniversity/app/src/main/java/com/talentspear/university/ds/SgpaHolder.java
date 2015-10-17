package com.talentspear.university.ds;

/**
 * Created by Darshan Muralidhar on 24-Jul-15.
 */
public class SgpaHolder {
    private int subjectId;
    private String subName;
    private int cieTotal;
    private int seeMarks;
    private float subCredits;
    private String grade;

    public boolean isLocked() {
        return isLocked;
    }

    public void setIsLocked(boolean isLocked) {
        this.isLocked = isLocked;
    }

    private boolean isLocked;

    public int getCieTotal() {
        return cieTotal;
    }

    public void setCieTotal(int cieTotal) {
        this.cieTotal = cieTotal;
    }

    public String getGrade() {
        return grade;
    }

    public void setGrade(String grade) {
        this.grade = grade;
    }

    public int getSeeMarks() {
        return seeMarks;
    }

    public void setSeeMarks(int seeMarks) {
        this.seeMarks = seeMarks;
    }

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
