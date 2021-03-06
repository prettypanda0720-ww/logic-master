<?php
$this->db->where('user_id', $this->session->userdata('user_id'));
$purchase_history = $this->db->get('payment',$per_page, $this->uri->segment(3));
?>
<section class="page-header-area my-course-area">
    <div class="container">
        <div class="row">
            <div class="col">
                <h1 class="page-title"><?php echo get_phrase('purchase_history'); ?></h1>
                <ul>
                    <li><a href="<?php echo site_url('home/my_courses'); ?>"><?php echo get_phrase('all_courses'); ?></a></li>
                    <li><a href="<?php echo site_url('home/my_wishlist'); ?>"><?php echo get_phrase('wishlists'); ?></a></li>
                    <li><a href="<?php echo site_url('home/my_messages'); ?>"><?php echo get_phrase('my_messages'); ?></a></li>
                    <li class="active"><a href="<?php echo site_url('home/purchase_history'); ?>"><?php echo get_phrase('purchase_history'); ?></a></li>
                    <!-- <li><a href="<?php echo site_url('home/amazons3_setting_form/add_form'); ?>"><?php echo get_phrase('s3_settngs'); ?></a></li> -->
                    <li><a href="<?php echo site_url('home/profile/user_profile'); ?>"><?php echo get_phrase('user_profile'); ?></a></li>
                    <li><a href="<?php echo site_url('home/live_sessions'); ?>"><?php echo get_phrase('live_sessions'); ?></a></li>
                </ul>
            </div>
        </div>
    </div>
</section>


<section class="purchase-history-list-area">
    <div class="container">
        <div class="row">
            <div class="col">
                <ul class="purchase-history-list">
                    <li class="purchase-history-list-header">
                        <div class="row">
                            <div class="col-sm-6"><h4 class="purchase-history-list-title"> <?php echo get_phrase('purchase_history'); ?> </h4></div>
                            <div class="col-sm-6 hidden-xxs hidden-xs">
                                <div class="row">
                                    <div class="col-sm-3"> <?php echo get_phrase('date'); ?> </div>
                                    <div class="col-sm-3"> <?php echo get_phrase('total_price'); ?> </div>
                                    <div class="col-sm-4"> <?php echo get_phrase('payment_type'); ?> </div>
                                    <div class="col-sm-2"> <?php echo get_phrase('actions'); ?> </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <?php if ($purchase_history->num_rows() > 0):
                        foreach($purchase_history->result_array() as $each_purchase):
                            $course_details = $this->crud_model->get_course_by_id($each_purchase['course_id'])->row_array();?>
                            <li class="purchase-history-items mb-2">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="purchase-history-course-img">
                                            <img src="<?php echo $this->crud_model->get_course_thumbnail_url($each_purchase['course_id']);?>" class="img-fluid">
                                        </div>
                                        <a class="purchase-history-course-title" href="<?php echo site_url('home/course/'.slugify($course_details['title']).'/'.$course_details['id']); ?>" >
                                            <?php
                                            echo $course_details['title'];
                                            ?>
                                        </a>
                                    </div>
                                    <div class="col-sm-6 purchase-history-detail">
                                        <div class="row">
                                            <div class="col-sm-3 date">
                                                <?php echo date('D, d-M-Y', $each_purchase['date_added']); ?>
                                            </div>
                                            <div class="col-sm-3 price"><b>
                                                <?php echo currency($each_purchase['amount']); ?>
                                            </b></div>
                                            <div class="col-sm-4 payment-type">
                                                <?php echo ucfirst($each_purchase['payment_type']); ?>
                                            </div>
                                            <div class="col-sm-2">
                                                <a href="<?php echo site_url('home/invoice/'.$each_purchase['id']); ?>" target="_blank" class="btn btn-receipt"><?php echo get_phrase('invoice'); ?></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>
                            <div class="row" style="text-align: center;">
                                <?php echo get_phrase('no_records_found'); ?>
                            </div>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</section>
<?php
  if(addon_status('offline_payment') == 1):
    include "pending_purchase_course_history.php";
  endif;
?>
<nav>
    <?php echo $this->pagination->create_links(); ?>
</nav>
