package com.talentspear.university;

import android.app.NotificationManager;
import android.content.Context;
import android.content.SharedPreferences;
import android.graphics.Color;
import android.os.Bundle;
import android.preference.PreferenceManager;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentTransaction;
import android.support.v4.content.res.ResourcesCompat;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.Toolbar;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.AdapterView;
import android.widget.Toast;

import com.mikepenz.iconics.typeface.FontAwesome;
import com.mikepenz.materialdrawer.Drawer;
import com.mikepenz.materialdrawer.DrawerBuilder;
import com.mikepenz.materialdrawer.accountswitcher.AccountHeader;
import com.mikepenz.materialdrawer.accountswitcher.AccountHeaderBuilder;
import com.mikepenz.materialdrawer.model.PrimaryDrawerItem;
import com.mikepenz.materialdrawer.model.ProfileDrawerItem;
import com.mikepenz.materialdrawer.model.interfaces.IDrawerItem;
import com.mikepenz.materialdrawer.model.interfaces.IProfile;

import com.talentspear.university.dbhandlers.PostHandler;
import com.talentspear.university.dbhandlers.StoreNotif;
import com.talentspear.university.fragments.AttendanceMan;
import com.talentspear.university.fragments.DeptFragment;
import com.talentspear.university.fragments.GeneralExtras;
import com.talentspear.university.fragments.QuickContacts;
import com.talentspear.university.fragments.RecentFeatured;
import com.talentspear.university.fragments.Sgpa;

public class NavActivity extends AppCompatActivity {
     static Toolbar toolbar;
    Drawer result;
    private int temp=0;
    Fragment frag;
    private int isReturned=0;
    private boolean isNotSelected=true;
    ProfileDrawerItem myprofile;
    private boolean shouldIExit=false;
    private static boolean isInForeground=false;
    SharedPreferences preferences;
    static String usn;

    public static boolean isInForeground() {
        return isInForeground;
    }

    int pos=0;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        //drop tuples from the db once the app is started
        StoreNotif storeNotif = new StoreNotif(this);
        storeNotif.dropTuples();
        NotificationManager notificationManager = (NotificationManager)getSystemService(Context.NOTIFICATION_SERVICE);
        notificationManager.cancel(0);
        setContentView(R.layout.activity_nav);
        preferences= PreferenceManager.getDefaultSharedPreferences(getApplicationContext());
        usn=preferences.getString("USN", "4MC13CS063");
        toolbar = (Toolbar) findViewById(R.id.activity_nav_toolbar);
        setSupportActionBar(toolbar);
        toolbar.setTitle("TALENTSPEAR");
        myprofile=new ProfileDrawerItem().withName(usn).withEmail(getDepartmentName(usn.substring(5,7))).withIcon(ResourcesCompat.getDrawable(getResources(), R.drawable.user, null));
        AccountHeader headerResult = new AccountHeaderBuilder()
                .withActivity(this)
                .withHeaderBackground(R.drawable.profile_back)
                .addProfiles(
                        myprofile
                )
                .withOnAccountHeaderListener(new AccountHeader.OnAccountHeaderListener() {
                    @Override
                    public boolean onProfileChanged(View view, IProfile profile, boolean currentProfile) {
                        return false;
                    }
                })
                .withOnAccountHeaderListener(new AccountHeader.OnAccountHeaderListener() {
                    @Override
                    public boolean onProfileChanged(View view, IProfile iProfile, boolean b) {
                        //startActivity(new Intent(NavDrawerr.this, UserProfile.class));
                        //isReturned++;
                        return false;
                    }
                })
                .build();
        DrawerBuilder builder=new DrawerBuilder()
                .withActivity(this)
                .withToolbar(toolbar)
                .withAccountHeader(headerResult)
                .withTranslucentStatusBar(true)
                .addDrawerItems(
                        new PrimaryDrawerItem().withName("Home").withIcon(FontAwesome.Icon.faw_home).withIdentifier(0).withCheckable(false),
                        new PrimaryDrawerItem().withName("General").withIcon(FontAwesome.Icon.faw_bullhorn).withIdentifier(1).withCheckable(false),
                        new PrimaryDrawerItem().withName("Department").withIcon(FontAwesome.Icon.faw_book).withIdentifier(2).withCheckable(false),
                        new PrimaryDrawerItem().withName("Attendance Manager").withIcon(FontAwesome.Icon.faw_bar_chart).withIdentifier(3).withCheckable(false),
                        new PrimaryDrawerItem().withName("SGPA Calculator").withIcon(FontAwesome.Icon.faw_calculator).withIdentifier(4).withCheckable(false)
                )
                .addStickyDrawerItems(
                        new PrimaryDrawerItem().withName("Quick Contacts").withIcon(FontAwesome.Icon.faw_group).withIdentifier(5).withCheckable(false)
                )
                .withActionBarDrawerToggleAnimated(true)
                .withActionBarDrawerToggle(true);
        result=builder.build();
        result.setStatusBarColor(Color.TRANSPARENT);
        PostHandler db=new PostHandler(this);

        if(db.getAllPostsCount()>0)
        {
            final RecentFeatured bill = new RecentFeatured();
            FragmentManager fragmentManager = getSupportFragmentManager();
            FragmentTransaction fragmentTransaction1 = fragmentManager.beginTransaction();
            frag = bill;
            fragmentTransaction1.add(R.id.fragment_container, bill, "BILL").commit();
        }
        else
        {
            FragmentManager fragmentManager = getSupportFragmentManager();
            FragmentTransaction fragmentTransaction1 = fragmentManager.beginTransaction();
            Fragment frag = new GeneralExtras();
            fragmentTransaction1.add(R.id.fragment_container, frag, "General").commit();
            result.setSelectionByIdentifier(1, true);
        }
        result.setOnDrawerItemClickListener(new Drawer.OnDrawerItemClickListener() {
            @Override
            public boolean onItemClick(AdapterView<?> adapterView, View view, int i, long l, IDrawerItem iDrawerItem) {

                Fragment fragment = null;
                int x=iDrawerItem.getIdentifier();
                if (pos != x || isNotSelected) {
                    isNotSelected=false;
                    switch (x) {
                        case 0:
                            toolbar.setBackgroundColor(Color.TRANSPARENT);
                            fragment = new RecentFeatured();
                            result.setSelectionByIdentifier(x, false);
                            pos = x;
                            shouldIExit=false;
                            break;
                        case 1:
                            toolbar.setBackgroundColor(Color.TRANSPARENT);
                            fragment = new GeneralExtras();
                            result.setSelectionByIdentifier(x,false);
                            shouldIExit=false;
                            pos = x;
                            break;
                        case 2:
                            toolbar.setBackgroundColor(Color.TRANSPARENT);
                            fragment = new DeptFragment();
                            result.setSelectionByIdentifier(x,false);
                            shouldIExit=false;
                            pos = x;
                            break;

                        case 3:
                        {
                            toolbar.setBackgroundColor(getResources().getColor(R.color.primaryColorMedium));
                            fragment = new AttendanceMan();
                            result.setSelectionByIdentifier(x,false);
                            shouldIExit=false;
                            pos = x;
                            break;
                        }
                        case 4:
                        {
                            toolbar.setBackgroundColor(getResources().getColor(R.color.primaryColorMedium));
                            fragment = new Sgpa();
                            result.setSelectionByIdentifier(x,false);
                            shouldIExit=false;
                            pos = x;
                            break;
                        }
                        case 5: {
                            toolbar.setBackgroundColor(Color.TRANSPARENT);
                            fragment = new QuickContacts();
                            result.setSelectionByIdentifier(x, false);
                            pos = x;
                            break;
                        }
                        default:
                            break;
                    }
                    if (fragment != null) {
                        FragmentManager fragmentManager = getSupportFragmentManager();
                        fragmentManager.beginTransaction()
                                .replace(R.id.fragment_container, fragment).commit();

                        // update selected item and title, then close the drawer
                    }
                }
                return false;
            }
        });
        result.keyboardSupportEnabled(this, true);

    }
    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        //getMenuInflater().inflate(R.menu.menu_nav, menu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        // Handle action bar item clicks here. The action bar will
        // automatically handle clicks on the Home/Up button, so long
        // as you specify a parent activity in AndroidManifest.xml.
        int id = item.getItemId();

        //noinspection SimplifiableIfStatement
        if (id == R.id.action_settings) {
            return true;
        }

        return super.onOptionsItemSelected(item);
    }

    @Override
    public void onBackPressed() {
        //handle the back press :D close the drawer first and if the drawer is closed close the activity
        if (result != null && result.isDrawerOpen()) {
            result.closeDrawer();
        } else if(shouldIExit) {
            super.onBackPressed();
        }
        else
        {
            Toast.makeText(this,"Press back again to exit",Toast.LENGTH_SHORT).show();
            shouldIExit=true;
        }
    }

    @Override
    protected void onResume() {
        super.onResume();
        isInForeground = true;
        if (isReturned != temp) {
            finish();
            startActivity(getIntent());
            temp++;
        }
    }

    @Override
    protected void onPause() {
        super.onPause();
        isInForeground=false;
    }

    public static String getDepartmentName(String usn)
    {
        String deptname;
        switch(usn)
        {
            case "EC":{
                deptname="Electronics";break;
            }
            case "CS":{
                deptname="Computer Science";break;
            }
            case "CV":{
                deptname="Civil ";
                break;
            }
            case "ME":{
                deptname="Mechanical";
                break;
            }
            case "IS":{
                deptname="Information Science";
                break;
            }
            case "EE":{
                deptname="Electrical";
                break;
            }
            case "IP":{
                deptname="Industrial Production";
                break;
            }
            case "AU":{
                deptname="Automobile";
                break;
            }
            case "IT":{
                deptname="Electronics & Instrumentation";
                break;
            }
            case "EI":{
                deptname="Electronics & Instrumentation";
                break;
            }
            default:
            {
                deptname="None";
                break;
            }


        }
        return deptname;
    }

    public static String getUsn()
    {
        return usn;
    }
    public static float getToolbarHeight()
    {
        return toolbar.getHeight();
    }
}
