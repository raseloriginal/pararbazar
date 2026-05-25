<div class="h-screen flex flex-col justify-center bg-slate-900 text-white relative">
    <div class="text-center mb-10">
        <div class="w-16 h-16 bg-blue-500 rounded-2xl mx-auto flex items-center justify-center mb-4 shadow-lg shadow-blue-500/30">
            <i class="fa-solid fa-motorcycle text-3xl"></i>
        </div>
        <h1 class="text-2xl font-bold tracking-tight">Rider Login</h1>
        <p class="text-slate-400 text-sm mt-1">Enter your phone number</p>
    </div>

    <form id="dsrLoginForm" class="px-8">
        <!-- Display Field -->
        <div class="mb-8">
            <input type="text" id="phoneDisplay" name="phone" readonly 
                   class="w-full bg-transparent border-b-2 border-slate-700 text-center text-3xl font-bold tracking-widest py-2 focus:outline-none focus:border-blue-500 transition-colors placeholder-slate-600"
                   placeholder="01XXXXXXXXX">
        </div>

        <!-- Keypad -->
        <div class="grid grid-cols-3 gap-4 max-w-xs mx-auto">
            <?php for($i=1; $i<=9; $i++): ?>
                <button type="button" class="keypad-btn w-16 h-16 rounded-full bg-slate-800 flex items-center justify-center text-2xl font-semibold mx-auto hover:bg-slate-700 active:bg-blue-500 transition-colors" data-val="<?= $i ?>"><?= $i ?></button>
            <?php endfor; ?>
            
            <button type="button" class="w-16 h-16 flex items-center justify-center text-xl text-red-400 mx-auto" id="keypadClear">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <button type="button" class="keypad-btn w-16 h-16 rounded-full bg-slate-800 flex items-center justify-center text-2xl font-semibold mx-auto hover:bg-slate-700 active:bg-blue-500 transition-colors" data-val="0">0</button>
            <button type="button" class="w-16 h-16 rounded-full bg-blue-600 flex items-center justify-center text-xl shadow-lg shadow-blue-600/30 mx-auto" id="keypadSubmit">
                <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    let display = $('#phoneDisplay');
    
    $('.keypad-btn').click(function() {
        if(display.val().length < 11) {
            display.val(display.val() + $(this).data('val'));
        }
    });

    $('#keypadClear').click(function() {
        display.val('');
    });

    $('#keypadSubmit').click(function() {
        let phone = display.val();
        if(phone.length < 11) {
            alert('Please enter a valid 11-digit phone number');
            return;
        }
        
        let btn = $(this);
        btn.html('<i class="fa-solid fa-spinner fa-spin"></i>');

        $.ajax({
            url: '<?= BASE_URL ?>dsr/api',
            type: 'POST',
            data: { action: 'login', phone: phone },
            success: function(res) {
                if(res.status === 'success') {
                    window.location.href = '<?= BASE_URL ?>dsr/dashboard';
                } else {
                    alert(res.message);
                    btn.html('<i class="fa-solid fa-arrow-right"></i>');
                }
            }
        });
    });
});
</script>
