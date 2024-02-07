<?php if(!empty($customer)): ?>
    <div class="row">
        <div class="col-md-5">
            <h6><?php echo e(__('Bill to')); ?></h6>
            <div class="bill-to">
                <small>
                    <span><?php echo e($customer['billing_name']); ?></span><br>
                    <span><?php echo e($customer['billing_phone']); ?></span><br>
                    <span><?php echo e($customer['billing_address']); ?></span><br>
                    <span><?php echo e($customer['billing_city'] . ' , '.$customer['billing_state'].' , '.$customer['billing_country'].'.'); ?></span><br>
                    <span><?php echo e($customer['billing_zip']); ?></span>

                </small>
            </div>
        </div>
        <div class="col-md-5">
            <h6><?php echo e(__('Ship to')); ?></h6>
            <div class="bill-to">
                <small>
                    <span><?php echo e($customer['shipping_name']); ?></span><br>
                    <span><?php echo e($customer['shipping_phone']); ?></span><br>
                    <span><?php echo e($customer['shipping_address']); ?></span><br>
                    <span><?php echo e($customer['shipping_city'] . ' , '.$customer['shipping_state'].' , '.$customer['shipping_country'].'.'); ?></span><br>
                    <span><?php echo e($customer['shipping_zip']); ?></span>

                </small>
            </div>
        </div>
        <div class="col-md-2">
            <a href="#" id="remove" class="text-sm"><?php echo e(__(' Remove')); ?></a>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH E:\~Prgoramming\Projects\Laravel\Mini Projects\erpgo\resources\views/invoice/customer_detail.blade.php ENDPATH**/ ?>