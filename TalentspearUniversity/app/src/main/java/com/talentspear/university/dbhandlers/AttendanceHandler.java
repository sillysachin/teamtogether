package com.talentspear.university.dbhandlers;

import android.content.ContentValues;
import android.content.Context;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;

import com.talentspear.university.ds.AttendanceHolder;

import java.util.ArrayList;
import java.util.List;

/**
 * Created by Darshan Muralidhar on 24-Jul-15.
 */
public class AttendanceHandler extends SQLiteOpenHelper {
    private static final int DATABASE_VERSION = 1;

    // Database Name
    private static final String DATABASE_NAME = "talentspear";

    // Contacts table name
    private static final String TABLE_NAME = "attendance";

    public AttendanceHandler(Context context) {
        super(context, DATABASE_NAME, null, DATABASE_VERSION);
    }

    @Override
    public void onCreate(SQLiteDatabase db) {
        db.execSQL("CREATE TABLE IF NOT EXISTS " + TABLE_NAME
                + "(ID INTEGER PRIMARY KEY AUTOINCREMENT, NAME VARCHAR(50)" +
                ", CREDITS INTEGER)");
    }

    @Override
    public void onUpgrade(SQLiteDatabase db, int i, int i1) {
        db.execSQL("DROP TABLE IF EXISTS " + TABLE_NAME);
        onCreate(db);
    }

    public int addSubject(AttendanceHolder attendanceHolder) {
        SQLiteDatabase db = getWritableDatabase();
        onCreate(db);
        ContentValues cv = new ContentValues();
        cv.put("NAME", attendanceHolder.getSubName());
        cv.put("CREDITS", attendanceHolder.getSubCredits());

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



    public List<AttendanceHolder> getAllSubjects() {

        List<AttendanceHolder> attendanceHolderList = new ArrayList<AttendanceHolder>();
        String query = "Select * from " + TABLE_NAME;
        SQLiteDatabase db = getWritableDatabase();
        onCreate(db);
        Cursor cursor = db.rawQuery(query, null);


        if (cursor.moveToFirst()) {
            do {
                AttendanceHolder attendanceHolder = new AttendanceHolder();
                attendanceHolder.setSubjectId(cursor.getInt(0));
                attendanceHolder.setSubName(cursor.getString(1));
                attendanceHolder.setSubCredits(cursor.getFloat(2));
                attendanceHolderList.add(attendanceHolder);
            } while (cursor.moveToNext());
        }

        cursor.close();
        db.close();

        return attendanceHolderList;
    }

   public void deleteSubject(int id)
   {
       SQLiteDatabase db = this.getWritableDatabase();
       db.execSQL("DELETE FROM " + TABLE_NAME + " WHERE ID" + "='" + id + "'");
   }
    public void truncate()
    {
        SQLiteDatabase db = this.getWritableDatabase();
        db.execSQL("delete from "+ TABLE_NAME);
        db.close();
    }


    public void closedb() {
        SQLiteDatabase db = this.getReadableDatabase();
        if (db.isOpen())
            db.close();
    }
}
