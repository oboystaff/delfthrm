<?php echo e(Form::open(['url' => 'leave/changeaction', 'method' => 'post'])); ?>

<div class="modal-body">
    <div class="row">
        <div class="col-12">
            <table class="table modal-table" id="pc-dt-simple">

                <tr role="row">
                    <th><?php echo e(__('Employee')); ?></th>
                    <td><?php echo e(!empty($employee->name) ? $employee->name : ''); ?></td>
                </tr>
                <tr>
                    <th><?php echo e(__('Leave Type ')); ?></th>
                    <td><?php echo e(!empty($leavetype->title) ? $leavetype->title : ''); ?></td>
                </tr>
                <tr>
                    <th><?php echo e(__('Releave Officer')); ?></th>
                    <td><?php echo e($leave->releave->name ?? ''); ?></td>
                </tr>
                <tr>
                    <th><?php echo e(__('Appplied On')); ?></th>
                    <td><?php echo e(\Auth::user()->dateFormat($leave->applied_on)); ?></td>
                </tr>
                <tr>
                    <th><?php echo e(__('Start Date')); ?></th>
                    <td><?php echo e(\Auth::user()->dateFormat($leave->start_date)); ?></td>
                </tr>
                <tr>
                    <th><?php echo e(__('End Date')); ?></th>
                    <td><?php echo e(\Auth::user()->dateFormat($leave->end_date)); ?></td>
                </tr>
                <tr>
                    <th><?php echo e(__('Leave Reason')); ?></th>
                    <td><?php echo e(!empty($leave->leave_reason) ? $leave->leave_reason : ''); ?></td>
                </tr>
                <tr>
                    <th><?php echo e(__('Status')); ?></th>
                    <td><?php echo e(!empty($leave->status) ? $leave->status : ''); ?></td>
                </tr>
                <input type="hidden" value="<?php echo e($leave->id); ?>" name="leave_id">
            </table>
        </div>
    </div>
</div>

<?php if(Auth::user()->type == 'company' || Auth::user()->type == 'hr'): ?>
    <?php if($leave->supervisor_status == 'Pending'): ?>
        <p style="margin-left:30px;color:green;"><span style="font-weight:bold">Note:</span> Waiting for approval from
            the employee's supervisor on the leave request.
        </p>
    <?php elseif($leave->supervisor_status == 'Reject'): ?>
        <p style="margin-left:30px;color:red;"><span style="font-weight:bold">Note:</span> Employee leave request has
            been rejected by their supervisor</p>
    <?php else: ?>
        <div class="modal-footer">
            <input type="submit" value="<?php echo e(__('Approve')); ?>" class="btn btn-success rounded" name="status">
            <input type="submit" value="<?php echo e(__('Reject')); ?>" class="btn btn-danger rounded" name="status">
        </div>
    <?php endif; ?>
<?php elseif(Auth::user()->type == 'md'): ?>
    <?php if($leave->supervisor_status == 'Pending'): ?>
        <p style="margin-left:30px;color:green;"><span style="font-weight:bold">Note:</span> Waiting for approval from
            the employee's supervisor on the leave request.
        </p>
    <?php elseif($leave->supervisor_status == 'Reject'): ?>
        <p style="margin-left:30px;color:red;"><span style="font-weight:bold">Note:</span> Employee leave request has
            been rejected by their supervisor</p>
    <?php elseif($leave->status == 'Pending'): ?>
        <p style="margin-left:30px;color:green;"><span style="font-weight:bold">Note:</span> Waiting for approval from
            the HR on the employee leave request.
        </p>
    <?php elseif($leave->status == 'Reject'): ?>
        <p style="margin-left:30px;color:red;"><span style="font-weight:bold">Note:</span> Employee leave request has
            been rejected by the HR</p>
    <?php else: ?>
        <div class="modal-footer">
            <input type="submit" value="<?php echo e(__('Approve')); ?>" class="btn btn-success rounded" name="status">
            <input type="submit" value="<?php echo e(__('Reject')); ?>" class="btn btn-danger rounded" name="status">
        </div>
    <?php endif; ?>
<?php elseif(Auth::user()->type == 'supervisor'): ?>
    <div class="modal-footer">
        <input type="submit" value="<?php echo e(__('Approve')); ?>" class="btn btn-success rounded" name="status">
        <input type="submit" value="<?php echo e(__('Reject')); ?>" class="btn btn-danger rounded" name="status">
    </div>
<?php endif; ?>

<?php echo e(Form::close()); ?>

<?php /**PATH /Applications/MAMP/htdocs/hrm/resources/views/leave/action.blade.php ENDPATH**/ ?>