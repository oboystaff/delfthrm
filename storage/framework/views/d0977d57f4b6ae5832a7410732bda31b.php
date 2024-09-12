<?php
    $plan = Utility::getChatGPTSettings();
?>

<?php echo e(Form::model($officeProperty, ['route' => ['office-property.update', $officeProperty->id], 'method' => 'PUT'])); ?>

<div class="modal-body">

    <?php if($plan->enable_chatgpt == 'on'): ?>
        <div class="card-footer text-end">
            <a href="#" class="btn btn-sm btn-primary" data-size="medium" data-ajax-popup-over="true"
                data-url="<?php echo e(route('generate', ['office-property'])); ?>" data-bs-toggle="tooltip" data-bs-placement="top"
                title="<?php echo e(__('Generate')); ?>" data-title="<?php echo e(__('Generate Content With AI')); ?>">
                <i class="fas fa-robot"></i><?php echo e(__(' Generate With AI')); ?>

            </a>
        </div>
    <?php endif; ?>

    <?php if(\Auth::user()->type != 'employee'): ?>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <?php echo e(Form::label('employee_id', __('Employee'), ['class' => 'col-form-label'])); ?>

                    <?php echo e(Form::select('employee_id', $employees, null, ['class' => 'form-control select2', 'placeholder' => __('Select Employee')])); ?>

                </div>
            </div>
        </div>
    <?php else: ?>
        <?php echo Form::hidden('employee_id', !empty($employees) ? $employees->id : 0, ['id' => 'employee_id']); ?>

    <?php endif; ?>

    <input type="hidden" name="request_type" id="request_type" value="<?php echo e(request()->type); ?>">

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <?php echo e(Form::label('purpose', __('Purpose'), ['class' => 'col-form-label'])); ?>

                <?php echo e(Form::textarea('purpose', null, ['class' => 'form-control', 'placeholder' => __('Purpose of use'), 'rows' => '3'])); ?>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <?php echo e(Form::label('start_date', __('Start Date*'), ['class' => 'col-form-label'])); ?>

                    <?php echo e(Form::text('start_date', null, ['class' => 'form-control d_week current_date', 'autocomplete' => 'off'])); ?>

                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Start Time*</label>
                    <input type="time" name="start_time" class="form-control" style="height:43px;margin-top:10px"
                        value="<?php echo e($officeProperty->start_time); ?>">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <?php echo e(Form::label('end_date', __('End Date*'), ['class' => 'col-form-label'])); ?>

                    <?php echo e(Form::text('end_date', null, ['class' => 'form-control d_week current_date', 'autocomplete' => 'off'])); ?>

                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">End Time*</label>
                    <input type="time" name="end_time" class="form-control" style="height:43px;margin-top:10px"
                        value="<?php echo e($officeProperty->end_time); ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <?php echo e(Form::label('accompany_by', __('Accompanied By (You can seperate the names with comma)*'), ['class' => 'col-form-label'])); ?>

                <?php echo e(Form::textarea('accompany_by', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('People who are accompanying you'), 'rows' => '3'])); ?>

            </div>
        </div>
    </div>

    <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', 'Company')): ?>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <?php echo e(Form::label('status', __('Status'), ['class' => 'col-form-label'])); ?>

                    <select name="status" id="" class="form-control select2">
                        <option value=""><?php echo e(__('Select Status')); ?></option>
                        <option value="Pending" <?php if($officeProperty->status == 'Pending'): ?> selected="" <?php endif; ?>><?php echo e(__('Pending')); ?>

                        </option>
                        <option value="Approved" <?php if($officeProperty->status == 'Approved'): ?> selected="" <?php endif; ?>><?php echo e(__('Approved')); ?>

                        </option>
                        <option value="Reject" <?php if($officeProperty->status == 'Reject'): ?> selected="" <?php endif; ?>><?php echo e(__('Reject')); ?>

                        </option>
                    </select>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal"><?php echo e(__('Close')); ?></button>
    <input type="submit" value="<?php echo e(__('Update')); ?>" class="btn  btn-primary">

</div>
<?php echo e(Form::close()); ?>


<script>
    $(document).ready(function() {
        setTimeout(() => {
            var employee_id = $('#employee_id').val();
            if (employee_id) {
                $('#employee_id').trigger('change');
            }
        }, 100);
    });
</script>
<?php /**PATH /Applications/MAMP/htdocs/hrm/resources/views/office_property/edit.blade.php ENDPATH**/ ?>