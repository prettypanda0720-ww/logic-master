<style>
.preload { 
    z-index: 99999;
    width:100px;
    height: 100px;
    position: fixed;
    top: 30%;
    left: 45%;
    opacity: 1!important;
}
.blure-model{
    opacity: 0;
}
</style>
<?php
use EddTurtle\DirectUpload\Signature;
$uploader = new Signature(
    "AKIA3KJKHLCDMAHBUUXB",
    "sulop9/cy2yiq6j+6oG4fjE2UhKFzpRA97j4F/rw",
    "clms-storage",
    "eu-west-1"
);
// $param2 = course id
$course_details = $this->crud_model->get_course_by_id($param2)->row_array();
$sections = $this->crud_model->get_section('course', $param2)->result_array();
$institute = $this->crud_model->get_institute($course_details['institute_id'])->row_array();
$ins_name = str_replace(' ','_',$institute['first_name']).'_'.str_replace(' ','_',$institute['last_name']);
$course = str_replace(' ','_',$course_details['title']);
?>
<div class="preload">
<img src="http://i.imgur.com/KUJoe.gif">
</div>
<form action="<?php echo site_url('admin/lessons/'.$param2.'/add'); ?>" method="post" enctype="multipart/form-data">

    <div class="form-group">
        <label><?php echo get_phrase('title'); ?></label>
        <input type="text" id="title" name = "title" class="form-control" required>
    </div>

    <input type="hidden" name="course_id" value="<?php echo $param2; ?>">

    <div class="form-group">
        <label for="section_id"><?php echo get_phrase('section'); ?></label>
        <select class="form-control select2" data-toggle="select2" name="section_id" id="section_id" required>
            <?php foreach ($sections as $section): ?>
                <option value="<?php echo $section['id']; ?>"><?php echo $section['title']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="section_id"><?php echo get_phrase('lesson_type'); ?></label>
        <select class="form-control select2" data-toggle="select2" name="lesson_type" id="lesson_type" required onchange="show_lesson_type_form(this.value)">
            <option value="video-url"><?php echo get_phrase('video'); ?></option>
            <option value="other-txt"><?php echo get_phrase('text_file'); ?></option>
            <option value="other-pdf"><?php echo get_phrase('pdf_file'); ?></option>
            <option value="other-doc"><?php echo get_phrase('document_file'); ?></option>
            <option value="other-img"><?php echo get_phrase('image_file'); ?></option>
        </select>
    </div>

    <div class="" id="video" style="display: none;">

        <div class="form-group">
            <label for="lesson_provider"><?php echo get_phrase('lesson_provider'); ?>( <?php echo get_phrase('for_web_application'); ?> )</label>
            <select class="form-control select2 lesson_provider_s3" data-toggle="select2" name="lesson_provider" id="lesson_provider" onchange="check_video_provider(this.value)">
                <option value=""><?php echo get_phrase('select_lesson_provider'); ?></option>
                <option value="s3"><?php echo get_phrase('upload_video'); ?></option>
                <option value="youtube"><?php echo get_phrase('youtube'); ?></option>
                <option value="vimeo"><?php echo get_phrase('vimeo'); ?></option>
                <option value="html5">HTML5</option>
            </select>
        </div>



        <div class="" id = "youtube_vimeo" style="display: none;">
            <div class="form-group">
                <label><?php echo get_phrase('video_url'); ?>( <?php echo get_phrase('for_web_application'); ?> )</label>
                <input type="text" id = "video_url" name = "video_url" class="form-control" onblur="ajax_get_video_details(this.value)" placeholder="<?php echo get_phrase('this_video_will_be_shown_on_web_application'); ?>">
                <label class="form-label" id = "perloader" style ="margin-top: 4px; display: none;"><i class="mdi mdi-spin mdi-loading">&nbsp;</i><?php echo get_phrase('analyzing_the_url'); ?></label>
                <label class="form-label" id = "invalid_url" style ="margin-top: 4px; color: red; display: none;"><?php echo get_phrase('invalid_url').'. '.get_phrase('your_video_source_has_to_be_either_youtube_or_vimeo'); ?></label>
            </div>

            <div class="form-group">
                <label><?php echo get_phrase('duration'); ?>( <?php echo get_phrase('for_web_application'); ?> )</label>
                <input type="text" name = "duration" id = "duration" class="form-control">
            </div>
        </div>

        <div class="" id = "html5" style="display: none;">
            <div class="form-group">
                <label><?php echo get_phrase('video_url'); ?>( <?php echo get_phrase('for_web_application'); ?> )</label>
                <input type="text" id = "html5_video_url" name = "html5_video_url" class="form-control" placeholder="<?php echo get_phrase('this_video_will_be_shown_on_web_application'); ?>">
            </div>

            <div class="form-group">
                <label><?php echo get_phrase('duration'); ?>( <?php echo get_phrase('for_web_application'); ?> )</label>
                <input type="text" class="form-control" data-toggle='timepicker' data-minute-step="5" name="html5_duration" id = "html5_duration" data-show-meridian="false" value="00:00:00">
            </div>

            <div class="form-group">
                <label><?php echo get_phrase('thumbnail'); ?> <small>(<?php echo get_phrase('the_image_size_should_be'); ?>: 979 x 551)</small> </label>
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="thumbnail" name="thumbnail" onchange="changeTitleOfImageUploader(this)">
                        <label class="custom-file-label" for="thumbnail"><?php echo get_phrase('thumbnail'); ?></label>
                    </div>
                </div>
            </div>
        </div>

        <!-- This portion is for mobile application video lesson -->
        <div id="mobile-view">
        <div class="form-group">
            <label for="lesson_provider"><?php echo get_phrase('lesson_provider'); ?>( <?php echo get_phrase('for_mobile_application'); ?> )</label>
            <select class="form-control select2" data-toggle="select2" name="lesson_provider_for_mobile_application" id="lesson_provider_for_mobile_application">
                <option value="html5">HTML5</option>
            </select>
        </div>
        <div class="form-group">
            <label><?php echo get_phrase('video_url'); ?>( <?php echo get_phrase('for_mobile_application'); ?> )</label>
            <input type="text" id = "html5_video_url_for_mobile_application" name = "html5_video_url_for_mobile_application" class="form-control" placeholder="<?php echo get_phrase('only'); ?> HTML5 <?php echo get_phrase('type_video_is_acceptable_for_mobile_application'); ?>">
        </div>

        <div class="form-group">
            <label><?php echo get_phrase('duration'); ?>( <?php echo get_phrase('for_mobile_application'); ?> )</label>
            <input type="text" class="form-control" data-toggle='timepicker' data-minute-step="5" name="html5_duration_for_mobile_application" id = "html5_duration_for_mobile_application" data-show-meridian="false" value="00:00:00">
        </div>
        </div>
    </div>

    <div class="" id = "other" style="display: none;">
        <div class="form-group">
            <label> <?php echo get_phrase('attachment'); ?></label>
            <div class="input-group">
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="attachment" name="attachment" onchange="changeTitleOfImageUploader(this)">
                    <label class="custom-file-label" for="attachment"><?php echo get_phrase('attachment'); ?></label>
                </div>
            </div>
        </div>
    </div>

     <div class="" id = "amazon-s3" style="display: none;">
        <div class="form-group">
            <label> <?php echo get_phrase('upload_video_to').' Amazon S3'; ?>( <?php echo get_phrase('for_web_and_mobile_application'); ?> )</label>
            <div class="input-group">

                <input type="hidden" id="video_file_for_amazon_s3" name="video_file_for_amazon_s3">
                <input type="hidden" id="video_file_for_amazon_s3_size" name="video_file_for_amazon_s3_size">
                <!-- <div class="custom-file">
                <input type="file" class="custom-file-input" id="video_file_for_amazon_s3" name="video_file_for_amazon_s3" onchange="changeTitleOfImageUploader(this)" accept="video/mp4,video/flv,video/wmv,video/avi,video/mov">
                <label class="custom-file-label" for="video_file_for_amazon_s3"><?php /*echo get_phrase('select_video_file'); */?></label>
                </div> -->
                <input type="text" class="form-control fileName" style="display: none" disabled>
                <a href="javascript:void();" onclick="window.open('<?= site_url("modal/upload_video/".$ins_name."/".$course); ?>', 'NewButtonWindowName','width=700,height=300,scrollbars=yes')" data-id="<?= $lesson['id'] ?>" class="btn btn-outline-primary btn-rounded btn-sm ml-1 uploadVideo" style="float: right"><i class="mdi mdi-sort-variant"></i> Upload Video</a>
            </div>
        </div>
        <div class="form-group">
            <label><?php echo get_phrase('duration'); ?>( <?php echo get_phrase('for_web_and_mobile_application'); ?> )</label>
            <input type="text" class="form-control" data-toggle='timepicker' data-minute-step="5" name="amazon_s3_duration" id = "amazon_s3_duration" data-show-meridian="false" value="00:00:00">
        </div>
     </div>

    <div class="form-group">
        <label><?php echo get_phrase('summary'); ?></label>
        <textarea name="summary" class="form-control"></textarea>
        </div>

        <div class="text-center">
            <button class = "btn btn-success" id="add_lesson" type="submit" name="button"><?php echo get_phrase('add_lesson'); ?></button>
        </div>
    </form>
    <script type="text/javascript">
    $(document).ready(function() {
        $('#other').hide();
        $('#video').show();
        $('#amazon-s3').hide();
        initSelect2(['#section_id','#lesson_type', '#lesson_provider', '#lesson_provider_for_mobile_application']);
        initTimepicker();
        $('.preload').hide();
        $('#add_lesson').click(function(){
        if(typeof($('#video_file_for_amazon_s3').val()) != "undefined"){
            if($('#title').val() != ''){
            $(".preload").fadeIn(1000, function() {});
            }
        }
    });
    });
    function ajax_get_video_details(video_url) {
        $('#perloader').show();
        if(checkURLValidity(video_url)){
            $.ajax({
                url: '<?php echo site_url('admin/ajax_get_video_details');?>',
                type : 'POST',
                data : {video_url : video_url},
                success: function(response)
                {
                    jQuery('#duration').val(response);
                    $('#perloader').hide();
                    $('#invalid_url').hide();
                }
            });
        }else {
            $('#invalid_url').show();
            $('#perloader').hide();
            jQuery('#duration').val('');

        }
    }

    function checkURLValidity(video_url) {
        var youtubePregMatch = /^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
        var vimeoPregMatch = /^(http\:\/\/|https\:\/\/)?(www\.)?(vimeo\.com\/)([0-9]+)$/;
        if (video_url.match(youtubePregMatch)) {
            return true;
        }
        else if (vimeoPregMatch.test(video_url)) {
            return true;
        }
        else {
            return false;
        }
    }

    function show_lesson_type_form(param) {
        var checker = param.split('-');
        var lesson_type = checker[0];
        if (lesson_type === "video") {
            $('#other').hide();
            $('#video').show();
            $('#amazon-s3').hide();
        }else if (lesson_type === "other") {
            $('#video').hide();
            $('#other').show();
            $('#amazon-s3').hide();
        }
        else if (lesson_type === "s3") {
            $('#video').hide();
            $('#other').hide();
            $('#amazon-s3').show();
        }else {
            $('#video').hide();
            $('#other').hide();
            $('#amazon-s3').hide();
        }
    }

    function check_video_provider(provider) {
        if (provider === 'youtube' || provider === 'vimeo') {
            $('#html5').hide();
            $('#amazon-s3').hide();
            $('#mobile-view').hide();
            $('#youtube_vimeo').show();
        }else if(provider === 'html5'){
            $('#youtube_vimeo').hide();
            $('#amazon-s3').hide();
            $('#mobile-view').hide();
            $('#html5').show();
        }else if(provider === 's3'){
            $('#youtube_vimeo').hide();
            $('#html5').hide();
            $('#mobile-view').hide();
            $('#amazon-s3').show();
        }else {
            $('#youtube_vimeo').hide();
            $('#html5').hide();
            $('#mobile-view').hide();
            $('#amazon-s3').hide();
        }
    }
</script>
<script>
    $(document).ready(function () {
        less_id = 0;
        $(".uploadVideo").on('click' , function(){
            less_id = $(this).attr('data-id');
        });
    });

    function customFUNC(val1 , val2, val3 ,val4)
    {
        $("#video_file_for_amazon_s3").val(val2);
        $("#video_file_for_amazon_s3_size").val(val3);
        $('.uploadVideo').css('display','none');
        $('.fileName').val(val1);
        $('.fileName').css('display','block');
    }
</script>