package com.talentspear.university.dbhandlers;

import android.content.ContentValues;
import android.content.Context;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;

import com.talentspear.university.ds.Notifications;

/**
 * Created by SHESHA on 22-Jul-15.
 */
public class StoreNotif extends SQLiteOpenHelper {

    private static final int DATABASE_VERSION = 1;
    private static final String DATABASE_NAME = "talentspear";
    private static final String TABLE_NAME = "GCM";

    public StoreNotif(Context context) {
        super(context, DATABASE_NAME, null, DATABASE_VERSION);
    }

    @Override
    public void onCreate(SQLiteDatabase db) {
        db.execSQL("CREATE TABLE " + TABLE_NAME
                + "(ID INTEGER PRIMARY KEY AUTOINCREMENT, MESSAGE TEXT, TITLE varchar(50))");
    }

    @Override
    public void onUpgrade(SQLiteDatabase db, int i, int i1) {
        db.execSQL("DROP TABLE IF EXISTS " + TABLE_NAME);
        onCreate(db);
    }

    public Cursor getStoredNotification() {
        Notifications noti = new Notifications();
        SQLiteDatabase db = getWritableDatabase();
        db.execSQL("CREATE TABLE IF NOT EXISTS " + TABLE_NAME
                + "(ID INTEGER PRIMARY KEY AUTOINCREMENT, MESSAGE TEXT, TITLE varchar(50))");
        Cursor cursor = db.query(TABLE_NAME, null, null, null, null, null, null);
        return cursor;
    }

    public void insertMsg(String title, String msg) {
        SQLiteDatabase db = getWritableDatabase();
        db.execSQL("CREATE TABLE IF NOT EXISTS " + TABLE_NAME
                + "(ID INTEGER PRIMARY KEY AUTOINCREMENT, MESSAGE TEXT, TITLE varchar(50))");
        ContentValues cv = new ContentValues();
        cv.put("MESSAGE", msg);
        cv.put("TITLE", title);
        db.insert(TABLE_NAME, null, cv);
        db.close();
    }

    public void dropTuples() {
        SQLiteDatabase db = getWritableDatabase();
        try{
        db.execSQL("delete from " + TABLE_NAME);
        db.close();
        }catch(Exception e){

        }
    }
}
