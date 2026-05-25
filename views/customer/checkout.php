<?php require_once __DIR__ . '/../../includes/header.php'; ?>
<?php
// Calculate totals from session
$cart = $_SESSION['cart'] ?? ['items' => [], 'total_items' => 0, 'total_amount' => 0];
if ($cart['total_items'] === 0) {
    echo "<div class='p-8 text-center text-gray-500'>Your cart is empty. <br><a href='/pararbazar/home' class='text-green-600 font-bold mt-4 inline-block'>Go back to shopping</a></div>";
    require_once __DIR__ . '/../../includes/footer.php';
    exit;
}

$subtotal = $cart['total_amount'];
$delivery_fee = 30; // Mock delivery fee
$grand_total = $subtotal + $delivery_fee;

$pdo = getDB();
$slotStmt = $pdo->query("SELECT * FROM delivery_slots WHERE status = 1 ORDER BY start_time ASC");
$slots = $slotStmt->fetchAll();
?>

<div class="px-4 py-4 bg-white border-b sticky top-[56px] z-30">
    <div class="flex items-center gap-3">
        <a href="/pararbazar/home" class="text-gray-600"><i class="fa-solid fa-arrow-left"></i></a>
        <h2 class="text-lg font-bold text-gray-800">Checkout</h2>
    </div>
</div>

<form id="checkoutForm" class="px-4 py-6 space-y-6">
    <!-- Delivery Slot Selection -->
    <div>
        <h3 class="font-semibold text-gray-800 mb-3">Select Delivery Slot</h3>
        <div class="flex overflow-x-auto no-scrollbar gap-3 pb-2" id="slotContainer">
            <?php foreach($slots as $index => $slot): ?>
            <label class="relative flex flex-col p-3 border rounded-xl cursor-pointer bg-white shadow-sm min-w-[120px]">
                <input type="radio" name="slot_id" value="<?= $slot['id'] ?>" class="absolute right-3 top-3 text-green-600 focus:ring-green-500" <?= $index === 0 ? 'required' : '' ?>>
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Today</span>
                <span class="text-sm font-bold text-gray-800"><?= date('h:i A', strtotime($slot['start_time'])) ?></span>
                <span class="text-xs text-gray-500">to <?= date('h:i A', strtotime($slot['end_time'])) ?></span>
            </label>
            <?php endforeach; ?>
            
            <?php if(empty($slots)): ?>
                <div class="text-sm text-gray-500 py-2">No delivery slots available.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Delivery Details -->
    <div>
        <h3 class="font-semibold text-gray-800 mb-3">Delivery Details</h3>
        <div class="space-y-4">
            <div>
                <label class="block text-sm text-gray-600 mb-1">Full Name</label>
                <input type="text" name="name" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none" required>
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Phone Number (Used for Login)</label>
                <input type="tel" name="phone" placeholder="01XXXXXXXXX" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none" required>
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Detailed Address</label>
                <textarea name="address" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none" required></textarea>
            </div>
        </div>
    </div>

    <!-- Order Summary -->
    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
        <h3 class="font-semibold text-gray-800 mb-3">Order Summary</h3>
        <div class="space-y-2 text-sm text-gray-600">
            <div class="flex justify-between">
                <span>Subtotal (<?= $cart['total_items'] ?> items)</span>
                <span>৳<?= number_format($subtotal, 2) ?></span>
            </div>
            <div class="flex justify-between">
                <span>Delivery Charge</span>
                <span>৳<?= number_format($delivery_fee, 2) ?></span>
            </div>
            <hr class="my-2 border-gray-200">
            <div class="flex justify-between items-center text-lg">
                <span class="font-bold text-gray-800">Grand Total</span>
                <span class="font-bold text-green-600">৳<?= number_format($grand_total, 2) ?></span>
            </div>
        </div>
    </div>

    <button type="submit" class="w-full bg-green-600 text-white font-bold text-lg py-3 rounded-xl shadow-lg hover:bg-green-700 transition-colors" id="placeOrderBtn">
        Place Order & Register
    </button>
</form>

<!-- Include script after jQuery is loaded by footer -->
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

<script>
$(document).ready(function() {
    // Hide floating cart on checkout page
    $('#floatingCart').hide();

    // Style radio buttons selection
    $('input[name="slot_id"]').change(function() {
        $('input[name="slot_id"]').parent().removeClass('border-green-500 bg-green-50').addClass('border-gray-200 bg-white');
        if($(this).is(':checked')) {
            $(this).parent().addClass('border-green-500 bg-green-50').removeClass('border-gray-200 bg-white');
        }
    });

    $('#checkoutForm').submit(function(e) {
        e.addTask = true; // prevent default behavior
        e.preventDefault();
        
        let btn = $('#placeOrderBtn');
        btn.prop('disabled', true).text('Processing...');

        $.ajax({
            url: '/pararbazar/api/checkout',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if(response.status === 'success') {
                    alert('Order placed successfully! You are now logged in.');
                    window.location.href = '/pararbazar/orders';
                } else {
                    alert('Error: ' + response.message);
                    btn.prop('disabled', false).text('Place Order & Register');
                }
            },
            error: function() {
                alert('An error occurred during checkout.');
                btn.prop('disabled', false).text('Place Order & Register');
            }
        });
    });
});
</script>
