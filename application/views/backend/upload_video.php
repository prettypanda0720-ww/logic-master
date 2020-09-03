
<?php

use EddTurtle\DirectUpload\Signature;

// Require Composer's autoloader

$uploader = new Signature(
    "AKIA3KJKHLCDMAHBUUXB",
    "sulop9/cy2yiq6j+6oG4fjE2UhKFzpRA97j4F/rw",
    "clms-storage",
    "eu-west-1",
    [
            'acl' => 'public-read',
    ]

);

?>

<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Dynamic Learn</title>
        
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

        <style type="text/css">
            body {
    font-family: sans-serif;
}

.container {
    width: 600px;
    margin: 50px auto;
}

form {
    margin-bottom: 30px;
}
/* textarea to show files as json */
textarea#uploaded {
    width: 100%;
    min-height: 300px;
    font-size: 10px;
}


/* show upload progress with bars */
.progress-bar-area {
    margin-top: 20px;
}
.progress {
    display: none;
    position: relative;
    width: 100%; height: 15px;
    background: #C7DA9F;
    border-radius: 15px;
    overflow: hidden;
    margin-top: 10px;
}
.bar {
    position: absolute;
    top: 0; left: 0;
    width: 0; height: 15px;
    background: #85C220;
    text-align: center;
    color: white;
    font-weight: bold;
    font-size: .7em;
}
.bar.red { background: tomato; }
        </style>

    </head>
    <body>

        <div class="container">
            
            <center>
                <div class="alert alert-success">
                <h3>Upload Video | Dynamic Learn</h3>
            </div>
            <!-- Direct Upload to S3 Form -->
            <form action="<?php echo $uploader->getFormUrl(); ?>"
                  method="POST"
                  enctype="multipart/form-data"
                  class="direct-upload">

                <?php echo $uploader->getFormInputsAsHtml(); ?>
                
                <input type="file" name="file">

                <!-- Progress Bars to show upload completion percentage -->
                <div class="progress-bar-area"></div>

            </form>
            </center>
        </div>

        <!-- Start of the JavaScript -->
        <!-- Load jQuery & jQuery UI (Needed for the FileUpload Plugin) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

        <!-- Load the FileUpload Plugin (more info @ https://github.com/blueimp/jQuery-File-Upload) -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/10.2.0/js/jquery.fileupload.min.js"></script>

        <script>
            $(document).ready(function () {

                // Assigned to variable for later use.
                var form = $('.direct-upload');
                var filesUploaded = [];

                // Place any uploads within the descending folders
                // so ['test1', 'test2'] would become /test1/test2/filename
                var folders = ['<?= $directory ?>'];

                form.fileupload({
                    url: form.attr('action'),
                    type: form.attr('method'),
                    datatype: 'xml',
                    add: function (event, data) {

                        // Show warning message if your leaving the page during an upload.
                        window.onbeforeunload = function () {
                            return 'You have unsaved changes.';
                        };

                        // Give the file which is being uploaded it's current content-type (It doesn't retain it otherwise)
                        // and give it a unique name (so it won't overwrite anything already on s3).
                        var file = data.files[0];
                        var filename = Date.now() + '.' + file.name.split('.').pop();
                        form.find('input[name="Content-Type"]').val(file.type);
                        form.find('input[name="Content-Length"]').val(file.size);
                        form.find('input[name="key"]').val((folders.length ? folders.join('/') + '/' : '') + filename);

                        // Actually submit to form to S3.
                        data.submit();

                        // Show the progress bar
                        // Uses the file size as a unique identifier
                        var bar = $('<div class="progress" data-mod="'+file.size+'"><div class="bar"></div></div>');
                        $('.progress-bar-area').append(bar);
                        bar.slideDown('fast');
                    },
                    progress: function (e, data) {
                        // This is what makes everything really cool, thanks to that callback
                        // you can now update the progress bar based on the upload progress.
                        var percent = Math.round((data.loaded / data.total) * 100);
                        $('.progress[data-mod="'+data.files[0].size+'"] .bar').css('width', percent + '%').html(percent+'%');
                    },
                    fail: function (e, data) {
                        // Remove the 'unsaved changes' message.
                        window.onbeforeunload = null;
                        $('.progress[data-mod="'+data.files[0].size+'"] .bar').css('width', '100%').addClass('red').html('');
                    },
                    done: function (event, data) {
                        window.onbeforeunload = null;

                        // Upload Complete, show information about the upload in a textarea
                        // from here you can do what you want as the file is on S3
                        // e.g. save reference to your server using another ajax call or log it, etc.
                        var original = data.files[0];
                        var s3Result = data.result.documentElement.childNodes;
                        filesUploaded.push({
                            "original_name": original.name,
                            "s3_name": s3Result[2].textContent,
                            "size": original.size,
                            "url": s3Result[0].textContent.replace("%2F", "/")
                        });
                        /*window.opener.location.reload();*/
                        window.opener.customFUNC(original.name , s3Result[2].textContent , original.size,s3Result[0].textContent.replace("%2F", "/"), 1);
                        window.close();
                        $('#uploaded').html(JSON.stringify(filesUploaded, null, 2));
                    }
                });
            });
        </script>
    </body>
</html>