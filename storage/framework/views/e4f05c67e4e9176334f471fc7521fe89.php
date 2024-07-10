<?php
    $plan = Utility::getChatGPTSettings();
?>

<?php echo e(Form::model($assetacquisition, ['route' => ['assetacquisition.update', $assetacquisition->id], 'method' => 'PUT'])); ?>

<div class="modal-body">

    <?php if($plan->enable_chatgpt == 'on'): ?>
        <div class="card-footer text-end">
            <a href="#" class="btn btn-sm btn-primary" data-size="medium" data-ajax-popup-over="true"
                data-url="<?php echo e(route('generate', ['assetacquisition'])); ?>" data-bs-toggle="tooltip" data-bs-placement="top"
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

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <?php echo e(Form::label('asset_acquisition_type_id', __('Asset Acquisition Type*'), ['class' => 'col-form-label'])); ?>

                
                <select name="asset_acquisition_type_id" id="asset_acquisition_type_id" class="form-control select">
                    
                    <?php $__currentLoopData = $assetacquisitiontypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assetacquisition): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($assetacquisition->id); ?>"><?php echo e($assetacquisition->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <?php echo e(Form::label('device_number', __('Device Number'), ['class' => 'col-form-label'])); ?>

                <?php echo e(Form::text('device_number', null, ['class' => 'form-control', 'placeholder' => __('Enter device number')])); ?>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <?php echo e(Form::label('name', __('Device Name'), ['class' => 'col-form-label'])); ?>

                <?php echo e(Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter device name')])); ?>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <?php echo e(Form::label('return_on', __('Return Date'), ['class' => 'col-form-label'])); ?>

                <?php echo e(Form::text('return_on', null, ['class' => 'form-control d_week', 'autocomplete' => 'off'])); ?>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <?php echo e(Form::label('reason', __('Asset Acquisition Reason'), ['class' => 'col-form-label'])); ?>

                <?php echo e(Form::textarea('reason', null, ['class' => 'form-control', 'placeholder' => __('Asset Acquisition Reason'), 'rows' => '3'])); ?>

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
                        <option value="Pending" <?php if($assetacquisition->status == 'Pending'): ?> selected="" <?php endif; ?>><?php echo e(__('Pending')); ?>

                        </option>
                        <option value="Approved" <?php if($assetacquisition->status == 'Approved'): ?> selected="" <?php endif; ?>><?php echo e(__('Approved')); ?>

                        </option>
                        <option value="Reject" <?php if($assetacquisition->status == 'Reject'): ?> selected="" <?php endif; ?>><?php echo e(__('Reject')); ?>

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
<?php /**PATH /Applications/MAMP/htdocs/hrm/resources/views/assetacquisition/edit.blade.php ENDPATH**/ ?>