package com.talentspear.university.dbhandlers;

import android.content.ContentValues;
import android.content.Context;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;
import android.util.Log;

import com.talentspear.university.ds.PostHolder;

import java.util.ArrayList;
import java.util.List;

public class PostHandler extends SQLiteOpenHelper {

    // All Static variables
    // Database Version
    private static final int DATABASE_VERSION = 1;

    // Database Name
    private static final String DATABASE_NAME = "talentspear";

    // Contacts table name
    private static final String TABLE_POSTS = "posts";

    // Contacts Table Columns names
    private static final String KEY_ID = "id";
    private static final String KEY_CID = "cid";
    private static final String KEY_TID = "tid";
    private static final String KEY_SUB = "subject";
    private static final String KEY_MES = "message";
    private static final String KEY_FILE_SIZE = "file_size";
    private static final String KEY_FILE_NAME = "file_name";
    private static final String KEY_DATE = "date";
    private static final String KEY_YEAR = "year";
    private static final String KEY_TIME = "time";
    private static final String KEY_ATTACHMENT = "attachment";
    private static final String KEY_FEATURED = "isFeatured";
    private static final String KEY_TIMESTAMP = "timestamp";
    private static final String KEY_IS_EDITED = "isEdited";

    public PostHandler(Context context) {
        super(context, DATABASE_NAME, null, DATABASE_VERSION);
    }

    // Creating Tables
    @Override
    public void onCreate(SQLiteDatabase db) {
        String CREATE_POSTS_TABLE = "CREATE TABLE IF NOT EXISTS " + TABLE_POSTS + "("
                + KEY_ID + " INTEGER PRIMARY KEY,"+ KEY_CID + " INTEGER,"+KEY_TID + " INTEGER,"+KEY_SUB+" TEXT," + KEY_MES + " TEXT,"+ KEY_ATTACHMENT + " TEXT,"
                + KEY_FILE_NAME + " TEXT," + KEY_FILE_SIZE + " TEXT,"+ KEY_DATE + " TEXT,"+ KEY_YEAR + " TEXT,"
                + KEY_TIME  + " TEXT,"+ KEY_FEATURED  + " INTEGER,"+KEY_TIMESTAMP  + " TEXT,"+KEY_IS_EDITED + " INTEGER"+")";
        db.execSQL(CREATE_POSTS_TABLE);
        //db.close();
    }

    // Upgrading database
    @Override
    public void onUpgrade(SQLiteDatabase db, int oldVersion, int newVersion) {
        // Drop older table if existed
        db.execSQL("DROP TABLE IF EXISTS " + TABLE_POSTS);

        // Create tables again
        onCreate(db);
    }

    /**
     * All CRUD(Create, Read, Update, Delete) Operations
     */

    // Adding new Post
    public void addPost(PostHolder post) {
        SQLiteDatabase db = this.getWritableDatabase();

        ContentValues values = new ContentValues();
        values.put(KEY_ID, post.getID());
        values.put(KEY_CID, post.getCID());
        values.put(KEY_TID, post.getTID());
        values.put(KEY_YEAR, post.getYEAR());
        values.put(KEY_TIME, post.getTime());
        values.put(KEY_DATE, post.getDate());
        values.put(KEY_ATTACHMENT, post.getAttachment());
        values.put(KEY_FILE_NAME, post.getFilename());
        values.put(KEY_FILE_SIZE, post.getFilesize());
        values.put(KEY_MES, post.getMessage());
        values.put(KEY_SUB, post.getSubject());
        values.put(KEY_FEATURED, post.getIsFeatured());
        values.put(KEY_TIMESTAMP, post.getTimestamp());
        values.put(KEY_IS_EDITED, post.isEdited());

        // Inserting Row
        db.insert(TABLE_POSTS, null, values);
    }

    public void upDatePost(PostHolder post) {
        SQLiteDatabase db = this.getWritableDatabase();
        String updateQuery = "UPDATE "+TABLE_POSTS+" SET "+
                                KEY_SUB+"='"+post.getSubject()+"',"+
                                KEY_MES+"='"+post.getMessage()+"',"+
                                KEY_TIME+"='"+post.getTime()+"',"+
                                KEY_YEAR+"='"+post.getYEAR()+"',"+
                                KEY_DATE+"='"+post.getDate()+"',"+
                                KEY_FILE_NAME+"='"+post.getFilename()+"',"+
                                KEY_FILE_SIZE+"='"+post.getFilesize()+"',"+
                                KEY_ATTACHMENT+"='"+post.getAttachment()+"'"+
                                " WHERE "+KEY_ID+"='"+post.getID()+"' "+"AND "+
                                KEY_CID+"='"+post.getCID()+"' "+"AND "+
                                KEY_IS_EDITED+"='"+post.isEdited()+"' "+"AND "+
                                KEY_TID+"='"+post.getTID()+"'";
        db.execSQL(updateQuery);
        db.close();
    }

    //Check whether post exist
    public boolean rowExist(int id, int cid, int tid)
    {
        String countQuery = "SELECT  * FROM " + TABLE_POSTS+" WHERE "+KEY_ID+"='"+id+"' "+"AND "+
                KEY_CID+"='"+cid+"' "+"AND "+
                KEY_TID+"='"+tid+"'";
        SQLiteDatabase db = this.getReadableDatabase();
        Cursor cursor = db.rawQuery(countQuery, null);
        int count=cursor.getCount();
        cursor.close();
        // return count

        return count > 0;
    }
    public void deleteAllPosts(int cid,int tid)
    {
        SQLiteDatabase db = this.getWritableDatabase();
        db.execSQL("DELETE FROM "+TABLE_POSTS+" WHERE "+
                KEY_CID+"='"+cid+"' "+"AND "+
                KEY_TID+"='"+tid+"'");
        String CREATE_POSTS_TABLE = "CREATE TABLE IF NOT EXISTS " + TABLE_POSTS + "("
                + KEY_ID + " INTEGER PRIMARY KEY,"+ KEY_CID + " INTEGER,"+KEY_TID + " INTEGER,"+KEY_SUB+" TEXT," + KEY_MES + " TEXT,"+ KEY_ATTACHMENT + " TEXT,"
                + KEY_FILE_NAME + " TEXT," + KEY_FILE_SIZE + " TEXT,"+ KEY_DATE + " TEXT,"+ KEY_YEAR + " TEXT,"
                + KEY_TIME  + " TEXT,"+ KEY_FEATURED  + " INTEGER,"+KEY_TIMESTAMP  + " TEXT,"+KEY_IS_EDITED + " INTEGER"+")";
        db.execSQL(CREATE_POSTS_TABLE);
    }

    // Getting post
    public PostHolder getPost(int id) {
        SQLiteDatabase db = this.getReadableDatabase();

        Cursor cursor = db.query(TABLE_POSTS, new String[] {
                        KEY_YEAR, KEY_SUB,  KEY_MES, KEY_ATTACHMENT, KEY_FILE_NAME,
                        KEY_TIME, KEY_DATE,KEY_FILE_SIZE,KEY_TIMESTAMP,KEY_FEATURED,KEY_IS_EDITED}, KEY_ID + "=?",
                new String[] { String.valueOf(id) }, null, null, null, null);
        if (cursor != null)
            cursor.moveToFirst();

        PostHolder post=new PostHolder();
        post.setYEAR(cursor.getInt(0));
        post.setSubject(cursor.getString(1));
        post.setMessage(cursor.getString(2));
        post.setAttachment(cursor.getString(3));
        post.setFilename(cursor.getString(4));
        post.setTime(cursor.getString(5));
        post.setDate(cursor.getString(6));
        post.setFilesize(cursor.getString(7));
        post.setTimestamp(cursor.getLong(8));
        post.setIsFeatured(cursor.getInt(9));
        post.setIsEdited(cursor.getInt(10));
        cursor.close();
        db.close();
        post.setID(id);
        // return post
        return post;
    }

    // Getting All Posts
    public List<PostHolder> getAllPosts(int cid,int tid) {
        List<PostHolder> posts = new ArrayList<PostHolder>();
        // Select All Query
        SQLiteDatabase db1 = this.getWritableDatabase();
        String CREATE_POSTS_TABLE = "CREATE TABLE IF NOT EXISTS " + TABLE_POSTS + "("
                + KEY_ID + " INTEGER PRIMARY KEY,"+ KEY_CID + " INTEGER,"+KEY_TID + " INTEGER,"+KEY_SUB+" TEXT," + KEY_MES + " TEXT,"+ KEY_ATTACHMENT + " TEXT,"
                + KEY_FILE_NAME + " TEXT," + KEY_FILE_SIZE + " TEXT,"+ KEY_DATE + " TEXT,"+ KEY_YEAR + " TEXT,"
                + KEY_TIME  + " TEXT,"+ KEY_FEATURED  + " INTEGER,"+KEY_TIMESTAMP  + " TEXT,"+KEY_IS_EDITED + " INTEGER"+")";
            db1.execSQL(CREATE_POSTS_TABLE);
        String selectQuery = "SELECT  * FROM " + TABLE_POSTS+ " WHERE "+
                KEY_CID+"='"+cid+"' "+"AND "+
                KEY_TID+"='"+tid+"'"+" ORDER BY "+KEY_TIMESTAMP+" DESC";

        SQLiteDatabase db = this.getReadableDatabase();
        Cursor cursor = db.rawQuery(selectQuery, null);

        // looping through all rows and adding to list
        if (cursor.moveToFirst()) {
            do {
                PostHolder post=new PostHolder();
                post.setID(cursor.getInt(0));
                post.setSubject(cursor.getString(3));
                post.setMessage(cursor.getString(4));
                post.setAttachment(cursor.getString(5));
                post.setFilename(cursor.getString(6));
                post.setFilesize(cursor.getString(7));
                post.setDate(cursor.getString(8));
                post.setYEAR(cursor.getInt(9));
                post.setTime(cursor.getString(10));
                post.setTimestamp(cursor.getLong(11));
                post.setIsFeatured(cursor.getInt(12));
                post.setIsEdited(cursor.getInt(13));
                // Adding contact to list
                posts.add(post);
            } while (cursor.moveToNext());
        }
        cursor.close();
        // return contact list
        return posts;
    }


    // Updating single Post
  /*  public int updateBill(String value,String month, String Year) {
        SQLiteDatabase db = this.getWritableDatabase();

        ContentValues values = new ContentValues();
        values.put(month, value);


        // updating row
        return db.update(TABLE_POSTS, values, KEY_YEAR + " = ?",
                new String[] { String.valueOf(Year) });
    }*/

    public int getMinId(int cid,int tid)
    {
        String maxQuery = "SELECT  MIN(id) FROM " + TABLE_POSTS+ " WHERE "+
                KEY_CID+"='"+cid+"' "+"AND "+
                KEY_TID+"='"+tid+"'";
        SQLiteDatabase db = this.getReadableDatabase();
        Cursor cursor = db.rawQuery(maxQuery, null);
        int minid;
        if (cursor != null)
            cursor.moveToFirst();
        minid=cursor.getInt(0);
        return minid;
    }

    public int getMaxId(int cid,int tid)
    {
        String maxQuery = "SELECT  MAX(id) FROM " + TABLE_POSTS+ " WHERE "+
                KEY_CID+"='"+cid+"' "+"AND "+
                KEY_TID+"='"+tid+"'";
        SQLiteDatabase db = this.getReadableDatabase();
        Cursor cursor = db.rawQuery(maxQuery, null);
        int maxid;
        if (cursor != null)
            cursor.moveToFirst();
        maxid=cursor.getInt(0);
        return maxid;

    }

    public int getisFeatured(int postid)
    {
        String FeatureQuery = "SELECT "+KEY_FEATURED+" FROM "+TABLE_POSTS+" WHERE "+KEY_ID+"='"+postid+"'";
        SQLiteDatabase db = this.getReadableDatabase();
        Cursor cursor = db.rawQuery(FeatureQuery, null);
        int isfeatured;
        if (cursor != null)
            cursor.moveToFirst();
        isfeatured=cursor.getInt(0);
        return isfeatured;
    }
    public void setisFeatured(int isFeatured, int postid)
    {
        String FeatureQuery = "UPDATE "+TABLE_POSTS+" SET "+KEY_FEATURED+"='"+isFeatured+"' WHERE "+KEY_ID+"='"+postid+"'";
        SQLiteDatabase db = this.getWritableDatabase();
        db.execSQL(FeatureQuery);
    }

    public int getisEdited(int postid)
    {
        String FeatureQuery = "SELECT "+KEY_IS_EDITED+" FROM "+TABLE_POSTS+" WHERE "+KEY_ID+"='"+postid+"'";
        SQLiteDatabase db = this.getReadableDatabase();
        Cursor cursor = db.rawQuery(FeatureQuery, null);
        int isEdited;
        if (cursor != null)
            cursor.moveToFirst();
        isEdited=cursor.getInt(0);
        return isEdited;
    }
    public void setisEdited(int isEdited, int postid)
    {
        String FeatureQuery = "UPDATE "+TABLE_POSTS+" SET "+KEY_IS_EDITED+"='"+isEdited+"' WHERE "+KEY_ID+"='"+postid+"'";
        SQLiteDatabase db = this.getWritableDatabase();
        db.execSQL(FeatureQuery);
    }


    // Getting Posts Count
    public int getPostsCount(int cid,int tid) {
        SQLiteDatabase db1 = this.getWritableDatabase();
        String CREATE_POSTS_TABLE = "CREATE TABLE IF NOT EXISTS " + TABLE_POSTS + "("
                + KEY_ID + " INTEGER PRIMARY KEY,"+ KEY_CID + " INTEGER,"+KEY_TID + " INTEGER,"+KEY_SUB+" TEXT," + KEY_MES + " TEXT,"+ KEY_ATTACHMENT + " TEXT,"
                + KEY_FILE_NAME + " TEXT," + KEY_FILE_SIZE + " TEXT,"+ KEY_DATE + " TEXT,"+ KEY_YEAR + " TEXT,"
                + KEY_TIME  + " TEXT,"+ KEY_FEATURED  + " INTEGER,"+KEY_TIMESTAMP  + " TEXT,"+KEY_IS_EDITED + " INTEGER"+")";
        db1.execSQL(CREATE_POSTS_TABLE);
        db1.close();
        String countQuery = "SELECT  * FROM " + TABLE_POSTS+ " WHERE "+
                KEY_CID+"='"+cid+"' "+"AND "+
                KEY_TID+"='"+tid+"'";
        SQLiteDatabase db = this.getReadableDatabase();
        Cursor cursor = db.rawQuery(countQuery, null);
        int count=cursor.getCount();
        cursor.close();
        // return count
        return count;

    }

    public int getAllPostsCount() {
        SQLiteDatabase db1 = this.getWritableDatabase();
        String CREATE_POSTS_TABLE = "CREATE TABLE IF NOT EXISTS " + TABLE_POSTS + "("
                + KEY_ID + " INTEGER PRIMARY KEY,"+ KEY_CID + " INTEGER,"+KEY_TID + " INTEGER,"+KEY_SUB+" TEXT," + KEY_MES + " TEXT,"+ KEY_ATTACHMENT + " TEXT,"
                + KEY_FILE_NAME + " TEXT," + KEY_FILE_SIZE + " TEXT,"+ KEY_DATE + " TEXT,"+ KEY_YEAR + " TEXT,"
                + KEY_TIME  + " TEXT,"+ KEY_FEATURED  + " INTEGER,"+KEY_TIMESTAMP  + " TEXT,"+KEY_IS_EDITED + " INTEGER"+")";
        db1.execSQL(CREATE_POSTS_TABLE);
        db1.close();
        String countQuery = "SELECT  * FROM " + TABLE_POSTS+" LIMIT 1";
        SQLiteDatabase db = this.getReadableDatabase();
        Cursor cursor = db.rawQuery(countQuery, null);
        int count=cursor.getCount();
        cursor.close();
        // return count
        return count;

    }

    public void closeDB() {
        SQLiteDatabase db = this.getReadableDatabase();
        if (db != null && db.isOpen())
            db.close();
    }

    public void deleteSinglePost(int ID){
        SQLiteDatabase db = this.getWritableDatabase();
        String query="DELETE FROM "+TABLE_POSTS+" WHERE  `"+KEY_ID+"`='"+ID+"';";
        db.execSQL(query);
        db.close();
    }

    public List<PostHolder> getRecentPosts() {
        List<PostHolder> posts = new ArrayList<>();
        // Select All Query
        SQLiteDatabase db1 = this.getWritableDatabase();
        String CREATE_POSTS_TABLE = "CREATE TABLE IF NOT EXISTS " + TABLE_POSTS + "("
                + KEY_ID + " INTEGER PRIMARY KEY,"+ KEY_CID + " INTEGER,"+KEY_TID + " INTEGER,"+KEY_SUB+" TEXT," + KEY_MES + " TEXT,"+ KEY_ATTACHMENT + " TEXT,"
                + KEY_FILE_NAME + " TEXT," + KEY_FILE_SIZE + " TEXT,"+ KEY_DATE + " TEXT,"+ KEY_YEAR + " TEXT,"
                + KEY_TIME  + " TEXT,"+ KEY_FEATURED  + " INTEGER,"+KEY_TIMESTAMP  + " TEXT,"+KEY_IS_EDITED + " INTEGER"+")";
        db1.execSQL(CREATE_POSTS_TABLE);
        db1.close();
        String selectQuery = "SELECT  * FROM " + TABLE_POSTS+ " ORDER BY "+KEY_TIMESTAMP+" DESC "+ "LIMIT 10";

        SQLiteDatabase db = this.getReadableDatabase();
        Cursor cursor = db.rawQuery(selectQuery, null);

        // looping through all rows and adding to list
        if (cursor.moveToFirst()) {
            do {
                PostHolder post=new PostHolder();
                post.setID(cursor.getInt(0));
                post.setSubject(cursor.getString(3));
                post.setMessage(cursor.getString(4));
                post.setAttachment(cursor.getString(5));
                post.setFilename(cursor.getString(6));
                post.setFilesize(cursor.getString(7));
                post.setDate(cursor.getString(8));
                post.setYEAR(cursor.getInt(9));
                post.setTime(cursor.getString(10));
                post.setTimestamp(cursor.getLong(11));
                post.setIsFeatured(cursor.getInt(12));
                post.setIsEdited(cursor.getInt(13));
                // Adding contact to list
                posts.add(post);
            } while (cursor.moveToNext());
        }
        cursor.close();
        return posts;
    }

    public List<PostHolder> getFavouritePosts() {
        List<PostHolder> posts = new ArrayList<>();
        // Select All Query
        SQLiteDatabase db1 = this.getWritableDatabase();
        String CREATE_POSTS_TABLE = "CREATE TABLE IF NOT EXISTS " + TABLE_POSTS + "("
                + KEY_ID + " INTEGER PRIMARY KEY,"+ KEY_CID + " INTEGER,"+KEY_TID + " INTEGER,"+KEY_SUB+" TEXT," + KEY_MES + " TEXT,"+ KEY_ATTACHMENT + " TEXT,"
                + KEY_FILE_NAME + " TEXT," + KEY_FILE_SIZE + " TEXT,"+ KEY_DATE + " TEXT,"+ KEY_YEAR + " TEXT,"
                + KEY_TIME  + " TEXT,"+ KEY_FEATURED  + " INTEGER,"+KEY_TIMESTAMP  + " TEXT,"+KEY_IS_EDITED + " INTEGER"+")";
        db1.execSQL(CREATE_POSTS_TABLE);
        db1.close();
        String selectQuery = "SELECT  * FROM " + TABLE_POSTS+ " WHERE "+KEY_FEATURED+"='1' "+ "ORDER BY "+KEY_TIMESTAMP+" DESC ";

        SQLiteDatabase db = this.getReadableDatabase();
        Cursor cursor = db.rawQuery(selectQuery, null);

        // looping through all rows and adding to list
        if (cursor.moveToFirst()) {
            do {
                PostHolder post=new PostHolder();
                post.setID(cursor.getInt(0));
                post.setSubject(cursor.getString(3));
                post.setMessage(cursor.getString(4));
                post.setAttachment(cursor.getString(5));
                post.setFilename(cursor.getString(6));
                post.setFilesize(cursor.getString(7));
                post.setDate(cursor.getString(8));
                post.setYEAR(cursor.getInt(9));
                post.setTime(cursor.getString(10));
                post.setTimestamp(cursor.getLong(11));
                post.setIsFeatured(cursor.getInt(12));
                post.setIsEdited(cursor.getInt(13));
                // Adding contact to list
                posts.add(post);
            } while (cursor.moveToNext());
        }
        cursor.close();
        return posts;
    }
}