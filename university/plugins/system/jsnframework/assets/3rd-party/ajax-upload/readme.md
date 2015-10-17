This component isn't supported at the moment, please use a newer
[qq.FileUploader plugin](http://valums.com/ajax-upload/) instead, which
adds a couple of new options and features, and fixes some bugs.

### AJAX Upload ###

Browsers force us to use file inputs (&lt;input type="file" /&gt;) for uploads,
which are impossible to style. Moreover, form-based uploads look obsolete in modern
web applications. We can use flash to solve this problem, but JavaScript works nice too.

AJAX Upload allows you to easily upload multiple files without refreshing the page
and use any element to show file selection window. It works in all major browsers
and doesn't require any library to run. AJAX Upload doesn't pollute the global namespace,
and is tested with jQuery, Prototypejs.

### <a href="http://valums.com/files/2009/ajax-upload/demo-jquery.htm">Demo</a> ###

### Supported browsers ###
IE6 - IE8, FF2 - 4, Safari, Chrome, Opera

### How to use it? ###

#### Creating the uploader ####

First, you should create button. (You can use any element).

    <div id="upload_button">Upload</div>

Next, you should create ajax upload instance. In it’s simplest form, you can create it using the following code:

    // Do it after the DOM is ready for manipulations
    // Use $(document).ready - jquery
    // document.observe("dom:loaded" - prototype
    new AjaxUpload('upload_button_id', {action: 'upload.php'});
    
#### Configuring ajax upload ####

    new AjaxUpload('upload_button_id', {
      // Location of the server-side upload script
      // NOTE: You are not allowed to upload files to another domain
      action: 'upload.php',
      // File upload name
      name: 'userfile',
      // Additional data to send
      data: {
        example_key1 : 'example_value',
        example_key2 : 'example_value2'
      },
      // Submit file after selection
      autoSubmit: true,
      // The type of data that you're expecting back from the server.
      // HTML (text) and XML are detected automatically.
      // Useful when you are using JSON data as a response, set to "json" in that case.
      // Also set server response type to text/html, otherwise it will not work in IE6
      responseType: false,
      // Fired after the file is selected
      // Useful when autoSubmit is disabled
      // You can return false to cancel upload
      // @param file basename of uploaded file
      // @param extension of that file
      onChange: function(file, extension){},
      // Fired before the file is uploaded
      // You can return false to cancel upload
      // @param file basename of uploaded file
      // @param extension of that file
      onSubmit: function(file, extension) {},
      // Fired when file upload is completed
      // WARNING! DO NOT USE "FALSE" STRING AS A RESPONSE!
      // @param file basename of uploaded file
      // @param response server response
      onComplete: function(file, response) {}
    });

Note: Do not use the data parameter to attach dynamic data, like this "data: {txt: textfield.value}",
because it will assign data when the instance of the AJAX Upload is created and will not change later.
If you want to pass additional data from textfields use setData method in a onSubmit callback.

Instance methods

 * submit - Submits file to the server (useful when autoSubmit is disabled)
 * disable - Disables upload button
 * enable - Enables upload button
 * setData(data) - Overwrites data parameter

You can use these methods, to configure <em>AJAX Upload</em> later.

    var upload = new AjaxUpload('div_id',{action: 'upload.php'});
    //For example when user selects something, set some data
    upload.setData({'example_key': 'value'});
    
    //Or you can use these methods directly inside event function
    new AjaxUpload('div_id', {
      action: 'upload.php',
      onSubmit: function() {
        // allow only 1 upload
        this.disable();
      }
    });
    });

#### How do I access the uploaded files? ####
For the server-side code it looks like the file is uploaded with
the simple upload form, so it shouldn't be a problem.

You can access the uploaded file with:

* PHP: $_FILES['userfile']
* Rails: params[:userfile]

Note that "userfile" is the default value for the option 'name'.

You can access the additional data with:

* PHP: $_POST['yourkey']
* Rails: params[:yourkey]

#### Server-side script ####

If you are using php, here is a simplest example that I got straight from php manual

    $uploaddir = '/var/www/uploads/';
    $uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
    
    if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
      echo "success";
    } else {
      // WARNING! DO NOT USE "FALSE" STRING AS A RESPONSE!
      // Otherwise onSubmit event will not be fired
      echo "error"; 
    }
    
ColdFusion based file upload. (Default barebones solution)
<pre><code>
&lt;cffunction name=&quot;uploadFile&quot; access=&quot;remote&quot; output=&quot;false&quot;&gt;
  &lt;cfargument name=&quot;userFile&quot;&gt;
  &lt;cffile action=&quot;upload&quot; fileField=&quot;userFile&quot; destination=&quot;directory/path/on/server/&quot;&gt;
  &lt;cfreturn &quot;whatever&quot;&gt;
&lt;/cffunction&gt;
</code></pre>

And here is an ASPX handler, please modify it to suit your needs:
    using System;
    using System.Web;
    using System.IO;
    
    public class FileHandler : IHttpHandler
    {
    
        public void ProcessRequest(HttpContext context)
        {
            string strFileName = Path.GetFileName(context.Request.Files[0].FileName);
            string strExtension = Path.GetExtension(context.Request.Files[0].FileName).ToLower();
            string strSaveLocation = context.Server.MapPath("Upload") + "" + strFileName;
            context.Request.Files[0].SaveAs(strSaveLocation);
    
            context.Response.ContentType = "text/plain";
            context.Response.Write("success");
        }
    
        public bool IsReusable
        {
            get
            {
                return false;
            }
        }
    }
    
### How to allow only certain file types? ###

The best way is to check the file type of selected file in a onSubmit
callback and return false to cancel upload if invalid file is selected.
But don't forget to add a server-side check too.

    new AjaxUpload('button2', {
        action: 'upload.php',
        onSubmit : function(file , ext){
            if (! (ext &amp;&amp; /^(jpg|png|jpeg|gif)$/i.test(ext))){
                // extension is not allowed
                alert('Error: invalid file extension');
                // cancel upload
                return false;
            }
        }
    });

    
### How it works? ###

Plugin places transparent file input on top of the button you provide,
so when user clicks on your button the normal file selection window is shown.
And after user selects a file, plugin submits form that contains file input to an iframe.
It isn't true ajax upload, but brings the same user experience.

### License ###

 Copyright (c) 2010 Andrew Valums  

 Permission is hereby granted, free of charge, to any person obtaining a copy  
 of this software and associated documentation files (the "Software"), to deal  
 in the Software without restriction, including without limitation the rights  
 to use, copy, modify, merge, publish, distribute, sublicense, and/or sell  
 copies of the Software, and to permit persons to whom the Software is  
 furnished to do so, subject to the following conditions:  

 The above copyright notice and this permission notice shall be included in  
 all copies or substantial portions of the Software.  

 THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR  
 IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,  
 FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE  
 AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER  
 LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,  
 OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN  
 THE SOFTWARE.  