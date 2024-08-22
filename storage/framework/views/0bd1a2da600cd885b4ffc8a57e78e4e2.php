<?php echo e(Form::open(['url' => 'assetacquisition/fireaction', 'method' => 'post'])); ?>

<div class="modal-body">
    <div class="row">
        <div class="col-12">
            <table class="table modal-table" id="pc-dt-simple">

                <tr role="row">
                    <th><?php echo e(__('Employee')); ?></th>
                    <td><?php echo e(!empty($employee->name) ? $employee->name : ''); ?></td>
                </tr>
                <tr>
                    <th><?php echo e(__('Asset Acquisition Type')); ?></th>
                    <td><?php echo e(!empty($assetacquisitiontype->name) ? $assetacquisitiontype->name : ''); ?></td>
                </tr>
                <tr>
                    <th><?php echo e(__('Device Number')); ?></th>
                    <td><?php echo e($assetacquisition->device_number); ?></td>
                </tr>
                <tr>
                    <th><?php echo e(__('Device Name')); ?></th>
                    <td><?php echo e($assetacquisition->name); ?></td>
                </tr>
                <tr>
                    <th><?php echo e(__('Applied Date')); ?></th>
                    <td><?php echo e(\Auth::user()->dateFormat($assetacquisition->applied_on)); ?></td>
                </tr>
                <tr>
                    <th><?php echo e(__('Return Date')); ?></th>
                    <td><?php echo e(\Auth::user()->dateFormat($assetacquisition->return_on)); ?></td>
                </tr>
                <tr>
                    <th><?php echo e(__('Asset Acquisition Reason')); ?></th>
                    <td><?php echo e(!empty($assetacquisition->reason) ? $assetacquisition->reason : ''); ?></td>
                </tr>
                <tr>
                    <th><?php echo e(__('Status')); ?></th>
                    <td><?php echo e(!empty($assetacquisition->status) ? $assetacquisition->status : ''); ?></td>
                </tr>
                <input type="hidden" value="<?php echo e($assetacquisition->id); ?>" name="assetacquisition_id">
            </table>
        </div>
    </div>
</div>

<?php if(Auth::user()->type == 'company' || Auth::user()->type == 'hr'): ?>
    <div class="modal-footer">
        <input type="submit" value="<?php echo e(__('Approved')); ?>" class="btn btn-success rounded" name="status">
        <input type="submit" value="<?php echo e(__('Reject')); ?>" class="btn btn-danger rounded" name="status">
    </div>
<?php endif; ?>

<?php echo e(Form::close()); ?>

<?php /**PATH /Applications/MAMP/htdocs/hrm/resources/views/assetacquisition/action.blade.php ENDPATH**/ ?>