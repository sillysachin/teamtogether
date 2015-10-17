package com.talentspear.university.dbhandlers;

import android.content.ContentValues;
import android.content.Context;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;

import com.talentspear.university.ds.SgpaHolder;

import java.util.ArrayList;
import java.util.List;

/**
 * Created by Darshan Muralidhar on 24-Jul-15.
 */
public class SgpaHandler extends SQLiteOpenHelper {
    private static final int DATABASE_VERSION = 1;

    // Database Name
    private static final String DATABASE_NAME = "talentspear";

    // Contacts table name
    private static final String TABLE_NAME = "marks";

    public SgpaHandler(Context context) {
        super(context, DATABASE_NAME, null, DATABASE_VERSION);
    }

    @Override
    public void onCreate(SQLiteDatabase db) {
        db.execSQL("CREATE TABLE IF NOT EXISTS " + TABLE_NAME
                + "(ID INTEGER PRIMARY KEY AUTOINCREMENT, NAME VARCHAR(50), CIE INTEGER , SEE INTEGER , GRADE VARCHAR(5)" +
                ", CREDITS VARCHAR(5))");
    }

    @Override
    public void onUpgrade(SQLiteDatabase db, int i, int i1) {
        db.execSQL("DROP TABLE IF EXISTS " + TABLE_NAME);
        onCreate(db);
    }

    public void updateMarksTuple(SgpaHolder sgpaHolder) {
        SQLiteDatabase db = getWritableDatabase();
        db.execSQL("CREATE TABLE IF NOT EXISTS " + TABLE_NAME
                + "(ID INTEGER PRIMARY KEY AUTOINCREMENT, NAME VARCHAR(50), CIE INTEGER , SEE INTEGER , GRADE VARCHAR(5)" +
                ", CREDITS VARCHAR(5))");

        ContentValues cv = new ContentValues();
        cv.put("NAME", sgpaHolder.getSubName());
        cv.put("CIE", sgpaHolder.getCieTotal());
        cv.put("SEE", sgpaHolder.getSeeMarks());
        cv.put("GRADE", sgpaHolder.getGrade());
        cv.put("CREDITS", sgpaHolder.getSubCredits());
        String condition = "ID=" + sgpaHolder.getSubjectId();
        db.update(TABLE_NAME, cv, condition, null);
        db.close();
    }

    public void storeMarksInTable(SgpaHolder sgpaHolder) {
        SQLiteDatabase db = getWritableDatabase();
        db.execSQL("CREATE TABLE IF NOT EXISTS " + TABLE_NAME
                + "(ID INTEGER PRIMARY KEY AUTOINCREMENT, NAME VARCHAR(50), CIE INTEGER , SEE INTEGER , GRADE VARCHAR(5)" +
                ", CREDITS VARCHAR(5))");
        ContentValues cv = new ContentValues();
        cv.put("NAME", sgpaHolder.getSubName());
        cv.put("CIE", sgpaHolder.getCieTotal());
        cv.put("SEE", sgpaHolder.getSeeMarks());
        cv.put("GRADE", sgpaHolder.getGrade());
        cv.put("CREDITS", sgpaHolder.getSubCredits());

        // Inserting Row
        db.insert(TABLE_NAME, null, cv);
        db.close(); // Closing database connection
    }

    public void deleteEntry(int ID) {
        SQLiteDatabase db = getWritableDatabase();
        db.delete(TABLE_NAME, "ID = " + ID, null);
        db.close();
    }

    public List<SgpaHolder> getAllStoredMarks() {
        SQLiteDatabase db = getWritableDatabase();
        db.execSQL("CREATE TABLE IF NOT EXISTS " + TABLE_NAME
                + "(ID INTEGER PRIMARY KEY AUTOINCREMENT, NAME VARCHAR(50), CIE INTEGER , SEE INTEGER , GRADE VARCHAR(5)" +
                ", CREDITS VARCHAR(5))");
        List<SgpaHolder> sgpaHolderList = new ArrayList<SgpaHolder>();
        String query = "Select * from " + TABLE_NAME;
        Cursor cursor = db.rawQuery(query, null);


        if (cursor.moveToFirst()) {
            do {
                SgpaHolder sgpaHolder = new SgpaHolder();
                sgpaHolder.setSubjectId(cursor.getInt(0));
                sgpaHolder.setSubName(cursor.getString(1));
                sgpaHolder.setCieTotal(cursor.getInt(2));
                sgpaHolder.setSeeMarks(cursor.getInt(3));
                sgpaHolder.setGrade(cursor.getString(4));
                sgpaHolder.setSubCredits(cursor.getFloat(5));
                sgpaHolderList.add(sgpaHolder);
            } while (cursor.moveToNext());
        }

        cursor.close();
        db.close();

        return sgpaHolderList;
    }

    public int getMarksTupleCount() {
        SQLiteDatabase db1 = getWritableDatabase();
        String createQuery = "CREATE TABLE IF NOT EXISTS " + TABLE_NAME + " " +
                "(ID INTEGER PRIMARY KEY AUTOINCREMENT, NAME VARCHAR(50), CIE INTEGER , SEE INTEGER , GRADE VARCHAR(5)" +
                ", CREDITS VARCHAR(5))";
        db1.execSQL(createQuery);
        db1.close();
        String query = "select count(*) from" + TABLE_NAME;
        SQLiteDatabase db2 = getReadableDatabase();
        Cursor cursor = db2.rawQuery(query, null);
        int count = cursor.getCount();
        cursor.close();
        db2.close();
        return count;
    }

}
