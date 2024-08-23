<?php
    $plan = Utility::getChatGPTSettings();
?>

<?php echo e(Form::model($complaint, ['route' => ['complaint.update', $complaint->id], 'method' => 'PUT'])); ?>

<div class="modal-body">

    <?php if($plan->enable_chatgpt == 'on'): ?>
        <div class="card-footer text-end">
            <a href="#" class="btn btn-sm btn-primary" data-size="medium" data-ajax-popup-over="true"
                data-url="<?php echo e(route('generate', ['complaint'])); ?>" data-bs-toggle="tooltip" data-bs-placement="top"
                title="<?php echo e(__('Generate')); ?>" data-title="<?php echo e(__('Generate Content With AI')); ?>">
                <i class="fas fa-robot"></i><?php echo e(__(' Generate With AI')); ?>

            </a>
        </div>
    <?php endif; ?>

    <div class="row">
        <?php if(\Auth::user()->type != 'employee' && \Auth::user()->type != 'supervisor'): ?>
            <?php if($complaint->is_anonymous != 'Yes'): ?>
                <div class="form-group col-md-6 col-lg-6">
                    <?php echo e(Form::label('complaint_from', __('Complaint From'), ['class' => 'col-form-label'])); ?>

                    <?php echo e(Form::select('complaint_from', $employees, null, ['class' => 'form-control  select2', 'required' => 'required'])); ?>

                </div>
            <?php else: ?>
                <div class="form-group col-md-6 col-lg-6">
                    <?php echo e(Form::label('complaint_from', __('Complaint From'), ['class' => 'col-form-label'])); ?>

                    <input type="text" class="form-control" name="anonymous" value="Anonymous" readonly>
                </div>

                <input type="hidden" name="complaint_from" value="<?php echo e($complaint->complaint_from); ?>">
            <?php endif; ?>
        <?php endif; ?>
        <div class="form-group col-md-6 col-lg-6">
            <?php echo e(Form::label('complaint_against', __('Complaint Against'), ['class' => 'col-form-label'])); ?>

            <?php echo e(Form::select('complaint_against', $employees, null, ['class' => 'form-control select2', 'required' => 'required'])); ?>

        </div>
        <div class="form-group col-md-6 col-lg-6">
            <?php echo e(Form::label('title', __('Title'), ['class' => 'col-form-label'])); ?>

            <?php echo e(Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Enter Complaint Title', 'required' => 'required'])); ?>

        </div>
        <div class="form-group col-md-6 col-lg-6">
            <?php echo e(Form::label('complaint_date', __('Complaint Date'), ['class' => 'col-form-label'])); ?>

            <?php echo e(Form::text('complaint_date', null, ['class' => 'form-control d_week', 'autocomplete' => 'off', 'required' => 'required'])); ?>

        </div>
        <div class="form-group col-md-12">
            <?php echo e(Form::label('description', __('Description'), ['class' => 'col-form-label'])); ?>

            <?php echo e(Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => __('Enter Description'), 'rows' => '3', 'required' => 'required'])); ?>

        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="Cancel" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="<?php echo e(__('Update')); ?>" class="btn btn-primary">
</div>

<?php echo e(Form::close()); ?>

<?php /**PATH /Applications/MAMP/htdocs/hrm/resources/views/complaint/edit.blade.php ENDPATH**/ ?>