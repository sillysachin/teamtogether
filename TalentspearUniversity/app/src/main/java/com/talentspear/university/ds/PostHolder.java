package com.talentspear.university.ds;

/**
 * Created by SHESHA on 21-07-2015.
 */
public class PostHolder {
    private String subject;
    private String message;
    private String filesize;
    private String filename;
    private String date;
    private int isFeatured=0;
    private long timestamp;
    private int YEAR;
    private int isEdited=0;
    private String time;

    public int getCID() {
        return CID;
    }

    public void setCID(int CID) {
        this.CID = CID;
    }

    public int getTID() {
        return TID;
    }

    public void setTID(int TID) {
        this.TID = TID;
    }

    public boolean isDownloaded=false;
    private int ID;
    private int CID;
    private int TID;
    private String attachment;

    public boolean isDownloaded() {
        return isDownloaded;
    }
    public String getFilesize() {
        return filesize;
    }
    public void setFilesize(String filesize) {
        this.filesize = filesize;
    }
    public String getFilename() {
        return filename;
    }
    public void setFilename(String filename) {
        this.filename = filename;
    }
    public void setIsDownloaded(boolean isDownloaded) {
        this.isDownloaded = isDownloaded;
    }
    public String getSubject() {
        return subject;
    }
    public String getMessage() {
        return message;
    }
    public int getID() {
        return ID;
    }
    public String getTime() {
        return time;
    }
    public String getDate() {
        return date;
    }
    public String getAttachment() {
        return attachment;
    }
    public void setSubject(String subject) {
        this.subject = subject;
    }
    public void setMessage(String message) {
        this.message = message;
    }
    public void setDate(String date) {
        this.date = date;
    }
    public void setID(int ID) {
        this.ID = ID;
    }
    public void setTime(String time) {
        this.time = time;
    }
    public void setAttachment(String attachment) {
        this.attachment = attachment;
    }
    public int getYEAR() {
        return YEAR;
    }
    public void setYEAR(int YEAR) {
        this.YEAR = YEAR;
    }
    public int isEdited() {
        return isEdited;
    }
    public void setIsEdited(int isEdited) {
        this.isEdited = isEdited;
    }
    public long getTimestamp() {
        return timestamp;
    }
    public void setTimestamp(long timestamp) {
        this.timestamp = timestamp;
    }
    public int getIsFeatured() {
        return isFeatured;
    }
    public void setIsFeatured(int isFeatured) {
        this.isFeatured = isFeatured;
    }


}
