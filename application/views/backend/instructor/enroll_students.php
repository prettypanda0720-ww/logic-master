
<div class="row ">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h4 class="page-title"> <i class="mdi mdi-apple-keyboard-command title_icon"></i> <?php echo $page_title; ?>
                </h4>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h4 class="mb-3 header-title">Student list to enroll in class</h4>
                <!-- filter form here-->
                <div class="table-responsive-sm mt-4">
                    <table id="basic-datatable" class="table table-striped table-centered mb-0">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th><?php echo get_phrase('photo'); ?></th>
                            <th><?php echo get_phrase('name'); ?></th>
                            <th><?php echo get_phrase('email'); ?></th>
                            <th><?php echo get_phrase('actions'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($students as $key => $user): ?>
                            <tr>
                                <td><?php echo $key + 1; ?></td>
                                <td>
                                    <img src="<?php echo $this->user_model->get_user_image_url($user->id); ?>" alt="" height="50" width="50" class="img-fluid rounded-circle img-thumbnail">
                                </td>
                                <td><?php echo $user->first_name . ' ' . $user->last_name; ?>
                                </td>
                                <td><?php echo $user->email; ?></td>
                                <td>
                                    <div class="dropright dropright">
                                        <a href="<?= site_url('admin/enroll/'.$user->id.'/'.$course_id . '/' . $class_id) ?>" type="button" class="btn btn-sm btn-outline-primary btn-rounded btn-icon">
                                            Add
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach;?>
                        </tbody>
                    </table>
                </div>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
