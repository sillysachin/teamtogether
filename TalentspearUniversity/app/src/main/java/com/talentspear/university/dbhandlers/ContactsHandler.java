package com.talentspear.university.dbhandlers;

import android.content.ContentValues;
import android.content.Context;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;

import com.talentspear.university.ds.ContactsHolder;
import com.talentspear.university.ds.PostHolder;

import java.util.ArrayList;
import java.util.List;

/**
 * Created by Darshan Muralidhar on 31-Jul-15.
 */
public class ContactsHandler extends SQLiteOpenHelper {

    private static final String TABLE_CONTACTS = "contacts";
    private static final String DATABASE_NAME = "talentspear";
    private static final int DATABASE_VERSION = 1;

    private static final String KEY_ID = "id";
    private static final String KEY_NAME = "name";
    private static final String KEY_DESIG = "desig";
    private static final String KEY_QUALIFICATION = "qualification";
    private static final String KEY_DEPARTMENT = "department";
    private static final String KEY_NUMBER = "number";
    private static final String KEY_EMAIL = "email";

    public ContactsHandler(Context context) {
        super(context, DATABASE_NAME, null, DATABASE_VERSION);
    }

    @Override
    public void onCreate(SQLiteDatabase sqLiteDatabase) {

        String create_query = "CREATE TABLE IF NOT EXISTS " + TABLE_CONTACTS + "(" + KEY_ID + " INTEGER PRIMARY KEY," + KEY_NAME + " TEXT," + KEY_DESIG + " TEXT," + KEY_QUALIFICATION + " TEXT," + KEY_DEPARTMENT + " TEXT," + KEY_NUMBER + " TEXT," + KEY_EMAIL + " TEXT)";
        sqLiteDatabase.execSQL(create_query);
    }

    @Override
    public void onUpgrade(SQLiteDatabase db, int i, int i1) {
        db.execSQL("DROP TABLE IF EXISTS " + TABLE_CONTACTS);
        // Create tables again
        onCreate(db);
    }

    public void addContact(ContactsHolder contactsHolder) {
        SQLiteDatabase db1 = this.getWritableDatabase();
        String CREATE_POSTS_TABLE = "CREATE TABLE IF NOT EXISTS " + TABLE_CONTACTS + "(" + KEY_ID + " INTEGER PRIMARY KEY," + KEY_NAME + " TEXT," + KEY_DESIG + " TEXT," + KEY_QUALIFICATION + " TEXT," + KEY_DEPARTMENT + " TEXT," + KEY_NUMBER + " TEXT," + KEY_EMAIL + " TEXT)";
        db1.execSQL(CREATE_POSTS_TABLE);
        db1.close();
        SQLiteDatabase db = this.getWritableDatabase();
        ContentValues values = new ContentValues();
        values.put(KEY_ID, contactsHolder.getID());
        values.put(KEY_NAME, contactsHolder.getNAME());
        values.put(KEY_DESIG, contactsHolder.getDESIGNATION());
        values.put(KEY_DEPARTMENT, contactsHolder.getDEPARTMENT());
        values.put(KEY_QUALIFICATION, contactsHolder.getQUALIFICATION());
        values.put(KEY_NUMBER, contactsHolder.getPHONE());
        values.put(KEY_EMAIL, contactsHolder.getEMAIL());
        db.insert(TABLE_CONTACTS, null, values);
        closedb();
    }

    public void updateContact(ContactsHolder contactsHolder) {
        SQLiteDatabase db = this.getWritableDatabase();
        String query = "UPDATE " + TABLE_CONTACTS + " SET " + KEY_NAME + "='" + contactsHolder.getNAME() + "'," + KEY_NUMBER + "='" + contactsHolder.getPHONE() + "'," + KEY_DEPARTMENT + "='" + contactsHolder.getDEPARTMENT() + "'," + KEY_QUALIFICATION + "='" + contactsHolder.getQUALIFICATION() + "'," + KEY_EMAIL + "='" + contactsHolder.getEMAIL() + "'," + KEY_DESIG + "='" + contactsHolder.getDESIGNATION()+ "' WHERE "+KEY_ID + "='" + contactsHolder.getID()+"'";
        db.execSQL(query);
        closedb();
    }

    public void deleteContact(int ID) {
        SQLiteDatabase db = this.getWritableDatabase();
        db.execSQL("DELETE FROM " + TABLE_CONTACTS + " WHERE " + KEY_ID + "='" + ID+"'");
        closedb();
    }

    public void closedb() {
        SQLiteDatabase db = this.getReadableDatabase();
        if (db.isOpen())
            db.close();
    }

    public int getAllPostsCount() {
        SQLiteDatabase db1 = this.getWritableDatabase();
        String CREATE_POSTS_TABLE = "CREATE TABLE IF NOT EXISTS " + TABLE_CONTACTS + "(" + KEY_ID + " INTEGER PRIMARY KEY," + KEY_NAME + " TEXT," + KEY_DESIG + " TEXT," + KEY_QUALIFICATION + " TEXT," + KEY_DEPARTMENT + " TEXT," + KEY_NUMBER + " TEXT," + KEY_EMAIL + " TEXT)";
        db1.execSQL(CREATE_POSTS_TABLE);
        db1.close();
        String countQuery = "SELECT  * FROM " + TABLE_CONTACTS;
        SQLiteDatabase db = this.getReadableDatabase();
        Cursor cursor = db.rawQuery(countQuery, null);
        int count=cursor.getCount();
        cursor.close();
        // return count
        return count;

    }

    public List<ContactsHolder> getAllContacts(String dept) {
        List<ContactsHolder> contacts = new ArrayList<>();
        // Select All Query
        SQLiteDatabase db1 = this.getWritableDatabase();
        String CREATE_POSTS_TABLE = "CREATE TABLE IF NOT EXISTS " + TABLE_CONTACTS + "(" + KEY_ID + " INTEGER PRIMARY KEY," + KEY_NAME + " TEXT," + KEY_DESIG + " TEXT," + KEY_QUALIFICATION + " TEXT," + KEY_DEPARTMENT + " TEXT," + KEY_NUMBER + " TEXT," + KEY_EMAIL + " TEXT)";
        db1.execSQL(CREATE_POSTS_TABLE);
        String selectQuery = "SELECT  * FROM " + TABLE_CONTACTS+ " WHERE "+
                KEY_DEPARTMENT+"='"+dept+"'";

        SQLiteDatabase db = this.getReadableDatabase();
        Cursor cursor = db.rawQuery(selectQuery, null);

        // looping through all rows and adding to list
        if (cursor.moveToFirst()) {
            do {
                ContactsHolder contactsHolder=new ContactsHolder();
                contactsHolder.setID(cursor.getInt(0));
                contactsHolder.setNAME(cursor.getString(1));
                contactsHolder.setDESIGNATION(cursor.getString(2));
                contactsHolder.setQUALIFICATION(cursor.getString(3));
                contactsHolder.setPHONE(cursor.getString(5));
                contactsHolder.setEMAIL(cursor.getString(6));
                // Adding contact to list
                contacts.add(contactsHolder);
            } while (cursor.moveToNext());
        }
        cursor.close();
        // return contact list
        return contacts;
    }

    public void closeDB() {
        SQLiteDatabase db = this.getReadableDatabase();
        if (db != null && db.isOpen())
            db.close();
    }
}
