<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Manage Asset Acquisition')); ?>

<?php $__env->stopSection(); ?>


<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Home')); ?></a></li>
    <li class="breadcrumb-item"><?php echo e(__('Asset Acquisition ')); ?></li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('action-button'); ?>
    <a href="<?php echo e(route('assetacquisition.export')); ?>" class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
        data-bs-original-title="<?php echo e(__('Export')); ?>">
        <i class="ti ti-file-export"></i>
    </a>

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Create Leave')): ?>
        <a href="#" data-url="<?php echo e(route('assetacquisition.create')); ?>" data-ajax-popup="true"
            data-title="<?php echo e(__('Create New Asset Acquisition')); ?>" data-size="lg" data-bs-toggle="tooltip" title=""
            class="btn btn-sm btn-primary" data-bs-original-title="<?php echo e(__('Create')); ?>">
            <i class="ti ti-plus"></i>
        </a>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">

        <div class="col-xl-12">
            <div class="card">
                <div class="card-header card-body table-border-style">
                    
                    <div class="table-responsive">
                        <table class="table" id="pc-dt-simple">
                            <thead>
                                <tr>
                                    <?php if(\Auth::user()->type != 'employee'): ?>
                                        <th><?php echo e(__('Employee')); ?></th>
                                    <?php endif; ?>
                                    <th><?php echo e(__('Asset Acquisition Type')); ?></th>
                                    <th><?php echo e(__('Device No.')); ?></th>
                                    <th><?php echo e(__('Name')); ?></th>
                                    <th><?php echo e(__('Applied On')); ?></th>
                                    <th><?php echo e(__('Return On')); ?></th>
                                    <th><?php echo e(__('Reason')); ?></th>
                                    <th><?php echo e(__('Status')); ?></th>
                                    <th width="200px"><?php echo e(__('Action')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $assetacquisitions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assetacquisition): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <?php if(\Auth::user()->type != 'employee'): ?>
                                            <td><?php echo e($assetacquisition->employee->name ?? ''); ?>

                                            </td>
                                        <?php endif; ?>
                                        <td><?php echo e($assetacquisition->assetAcquisitionType->name ?? ''); ?>

                                        </td>
                                        <td><?php echo e($assetacquisition->device_number); ?></td>
                                        <td><?php echo e($assetacquisition->name); ?></td>
                                        <td><?php echo e(\Auth::user()->dateFormat($assetacquisition->applied_on)); ?></td>
                                        <td><?php echo e(\Auth::user()->dateFormat($assetacquisition->return_on)); ?></td>

                                        <td><?php echo e($assetacquisition->reason); ?></td>
                                        <td>
                                            <?php if($assetacquisition->status == 'Pending'): ?>
                                                <div class="badge bg-warning p-2 px-3 rounded status-badge5">
                                                    <?php echo e($assetacquisition->status); ?></div>
                                            <?php elseif($assetacquisition->status == 'Approved'): ?>
                                                <div class="badge bg-success p-2 px-3 rounded status-badge5">
                                                    <?php echo e($assetacquisition->status); ?></div>
                                            <?php elseif($assetacquisition->status == 'Reject'): ?>
                                                <div class="badge bg-danger p-2 px-3 rounded status-badge5">
                                                    <?php echo e($assetacquisition->status); ?></div>
                                            <?php endif; ?>
                                        </td>

                                        <td class="Action">

                                            <span>
                                                <?php if(\Auth::user()->type != 'employee'): ?>
                                                    <div class="action-btn bg-success ms-2">
                                                        <a href="#" class="mx-3 btn btn-sm  align-items-center"
                                                            data-size="lg"
                                                            data-url="<?php echo e(URL::to('assetacquisition/' . $assetacquisition->id . '/action')); ?>"
                                                            data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                                                            title=""
                                                            data-title="<?php echo e(__('Asset acquisition Action')); ?>"
                                                            data-bs-original-title="<?php echo e(__('Manage Asset Acquisition')); ?>">
                                                            <i class="ti ti-caret-right text-white"></i>
                                                        </a>
                                                    </div>
                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Edit Leave')): ?>
                                                        <div class="action-btn bg-info ms-2">
                                                            <a href="#" class="mx-3 btn btn-sm  align-items-center"
                                                                data-size="lg"
                                                                data-url="<?php echo e(URL::to('assetacquisition/' . $assetacquisition->id . '/edit')); ?>"
                                                                data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                                                                title="" data-title="<?php echo e(__('Edit Asset Acquisition')); ?>"
                                                                data-bs-original-title="<?php echo e(__('Edit')); ?>">
                                                                <i class="ti ti-pencil text-white"></i>
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Delete Leave')): ?>
                                                        <?php if(\Auth::user()->type != 'employee'): ?>
                                                            <div class="action-btn bg-danger ms-2">
                                                                <?php echo Form::open([
                                                                    'method' => 'DELETE',
                                                                    'route' => ['assetacquisition.destroy', $assetacquisition->id],
                                                                    'id' => 'delete-form-' . $assetacquisition->id,
                                                                ]); ?>

                                                                <a href="#"
                                                                    class="mx-3 btn btn-sm  align-items-center bs-pass-para"
                                                                    data-bs-toggle="tooltip" title=""
                                                                    data-bs-original-title="Delete" aria-label="Delete"><i
                                                                        class="ti ti-trash text-white text-white"></i></a>
                                                                </form>
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <div class="action-btn bg-success ms-2">
                                                        <a href="#" class="mx-3 btn btn-sm  align-items-center"
                                                            data-size="lg"
                                                            data-url="<?php echo e(URL::to('assetacquisition/' . $assetacquisition->id . '/action')); ?>"
                                                            data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                                                            title=""
                                                            data-title="<?php echo e(__('Asset Acquisition Action')); ?>"
                                                            data-bs-original-title="<?php echo e(__('Manage Asset Acquisition')); ?>">
                                                            <i class="ti ti-caret-right text-white"></i>
                                                        </a>
                                                    </div>
                                                <?php endif; ?>

                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('script-page'); ?>
    <script>
        $(document).on('change', '#employee_id', function() {
            var employee_id = $(this).val();

            $.ajax({
                url: '<?php echo e(route('leave.jsoncount')); ?>',
                type: 'POST',
                data: {
                    "employee_id": employee_id,
                    "_token": "<?php echo e(csrf_token()); ?>",
                },
                success: function(data) {
                    var oldval = $('#leave_type_id').val();
                    $('#leave_type_id').empty();
                    $('#leave_type_id').append(
                        '<option value=""><?php echo e(__('Select Leave Type')); ?></option>');

                    $.each(data, function(key, value) {

                        if (value.total_leave == value.days) {
                            $('#leave_type_id').append('<option value="' + value.id +
                                '" disabled>' + value.title + '&nbsp(' + value.total_leave +
                                '/' + value.days + ')</option>');
                        } else {
                            $('#leave_type_id').append('<option value="' + value.id + '">' +
                                value.title + '&nbsp(' + value.total_leave + '/' + value
                                .days + ')</option>');
                        }
                        if (oldval) {
                            if (oldval == value.id) {
                                $("#leave_type_id option[value=" + oldval + "]").attr(
                                    "selected", "selected");
                            }
                        }
                    });
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/hrm/resources/views/assetacquisition/index.blade.php ENDPATH**/ ?>