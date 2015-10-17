package com.talentspear.university.dbhandlers;

import android.content.ContentValues;
import android.content.Context;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;
import android.util.Log;

import com.talentspear.university.SubjectAttendance;
import com.talentspear.university.ds.BunkHolder;
import com.talentspear.university.ds.BunkHolder;

import java.util.ArrayList;
import java.util.List;

/**
 * Created by Darshan Muralidhar on 24-Jul-15.
 */
public class BunkHandler extends SQLiteOpenHelper {
    private static final int DATABASE_VERSION = 1;

    // Database Name
    private static final String DATABASE_NAME = "talentspear";

    // Contacts table name
    private static final String TABLE_NAME = "bunks";

    public BunkHandler(Context context) {
        super(context, DATABASE_NAME, null, DATABASE_VERSION);
    }

    @Override
    public void onCreate(SQLiteDatabase db) {
        db.execSQL("CREATE TABLE IF NOT EXISTS " + TABLE_NAME
                + "(ID INTEGER PRIMARY KEY AUTOINCREMENT, SID INTEGER" +
                ", TIMESTAMP VARCHAR(50))");
    }

    @Override
    public void onUpgrade(SQLiteDatabase db, int i, int i1) {
        db.execSQL("DROP TABLE IF EXISTS " + TABLE_NAME);
        onCreate(db);
    }

    public int addBunk(BunkHolder bunkHolder) {
        SQLiteDatabase db = getWritableDatabase();
        onCreate(db);
        ContentValues cv = new ContentValues();
        cv.put("SID", bunkHolder.getSubject_id());
        cv.put("TIMESTAMP", bunkHolder.getTimestamp());

        // Inserting Row
        db.insert(TABLE_NAME, null, cv);
        String idQuery = "SELECT  MAX(id) FROM " + TABLE_NAME;
        Cursor cursor = db.rawQuery(idQuery, null);
        int id=1;
        if (cursor.moveToFirst()) {
            id=cursor.getInt(0);
        }
        cursor.close();

        db.close(); // Closing database connection
        return id;
    }
    public int getAllBunksCount(int sid) {
        String countQuery = "SELECT  * FROM " + TABLE_NAME+" WHERE SID='"+sid+"'";
        SQLiteDatabase db = this.getWritableDatabase();
        onCreate(db);
        Cursor cursor = db.rawQuery(countQuery, null);
        int count=cursor.getCount();
        cursor.close();
        // return count
        return count;

    }


    public List<BunkHolder> getAllBunks(int sid) {

        List<BunkHolder> bunkHolderList = new ArrayList<BunkHolder>();
        String query = "Select * from " + TABLE_NAME+" WHERE SID='"+sid+"' ORDER BY TIMESTAMP DESC";
        SQLiteDatabase db = getWritableDatabase();
        onCreate(db);
        Cursor cursor = db.rawQuery(query, null);


        if (cursor.moveToFirst()) {
            do {
                BunkHolder bunkHolder = new BunkHolder();
                bunkHolder.setId(cursor.getInt(0));
                bunkHolder.setSubject_id(cursor.getInt(1));
                bunkHolder.setTimestamp(cursor.getLong(2));
                bunkHolderList.add(bunkHolder);
            } while (cursor.moveToNext());
        }

        cursor.close();
        db.close();

        return bunkHolderList;
    }

    public void deleteBunk(int id)
    {
        SQLiteDatabase db = this.getWritableDatabase();
        db.execSQL("DELETE FROM " + TABLE_NAME + " WHERE ID" + "='" + id + "'");
    }


    public void closedb() {
        SQLiteDatabase db = this.getReadableDatabase();
        if (db.isOpen())
            db.close();
    }
}
