package com.talentspear.university.ds;

/**
 * Created by Darshan Muralidhar on 22-Jul-15.
 */
public class Notifications {
    public String title;
    public String message;
    public int editPostId;
    public String imageLink;
    public int newPostId;

    public void setEditPostId(int editPostId) {
        this.editPostId = editPostId;
    }

    public void setImageLink(String imageLink) {
        this.imageLink = imageLink;
    }

    public void setMessage(String message) {
        this.message = message;
    }

    public void setNewPostId(int newPostId) {
        this.newPostId = newPostId;
    }

    public void setTitle(String title) {
        this.title = title;
    }

    public int getEditPostId() {
        return editPostId;
    }

    public String getImageLink() {
        return imageLink;
    }

    public String getMessage() {
        return message;
    }

    public int getNewPostId() {
        return newPostId;
    }

    public String getTitle() {
        return title;
    }


}