<div class="fixed inset-0 flex flex-col justify-center bg-slate-900 text-white z-50 overflow-hidden touch-none select-none">
    <div class="text-center mb-8">
        <div class="w-20 h-20 mx-auto mb-5 relative">
            <div class="absolute inset-0 bg-blue-500 blur-xl opacity-30 rounded-full"></div>
            <img src="<?= BASE_URL ?>images/icon.png" alt="Parar Bazar Logo" class="w-full h-full object-contain relative z-10 rounded-2xl shadow-lg">
        </div>
        <h1 class="text-3xl font-bold tracking-tight">Rider Login</h1>
        <p class="text-slate-400 text-sm mt-2">Enter your assigned phone number</p>
    </div>

    <form id="dsrLoginForm" class="px-8 flex-1 flex flex-col justify-between max-h-[60vh]">
        <!-- Display Field -->
        <div class="mb-8">
            <input type="text" id="phoneDisplay" name="phone" readonly 
                   class="w-full bg-transparent border-b-2 border-slate-700 text-center text-4xl font-bold tracking-[0.2em] py-3 focus:outline-none focus:border-blue-500 transition-colors placeholder-slate-700 text-blue-400"
                   placeholder="01XXXXXXXXX">
        </div>

        <!-- Keypad -->
        <div class="grid grid-cols-3 gap-x-6 gap-y-4 max-w-xs mx-auto mb-6 pb-safe">
            <?php for($i=1; $i<=9; $i++): ?>
                <button type="button" class="keypad-btn w-20 h-20 rounded-full bg-slate-800 flex items-center justify-center text-3xl font-semibold mx-auto active:bg-blue-500 active:scale-90 transition-all duration-150 shadow-sm" data-val="<?= $i ?>"><?= $i ?></button>
            <?php endfor; ?>
            
            <button type="button" class="w-20 h-20 flex items-center justify-center text-2xl text-slate-500 active:text-red-400 active:scale-90 transition-all duration-150 mx-auto" id="keypadClear">
                <i class="fa-solid fa-delete-left"></i>
            </button>
            <button type="button" class="keypad-btn w-20 h-20 rounded-full bg-slate-800 flex items-center justify-center text-3xl font-semibold mx-auto active:bg-blue-500 active:scale-90 transition-all duration-150 shadow-sm" data-val="0">0</button>
            <button type="button" class="w-20 h-20 rounded-full bg-blue-600 flex items-center justify-center text-2xl shadow-lg shadow-blue-600/40 mx-auto active:bg-blue-700 active:scale-90 transition-all duration-150" id="keypadSubmit">
                <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
    </form>
</div>

<style>
/* Add safe area padding for iOS devices with notch */
.pb-safe {
    padding-bottom: env(safe-area-inset-bottom);
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    20%, 60% { transform: translateX(-10px); }
    40%, 80% { transform: translateX(10px); }
}
</style>

<script>
$(document).ready(function() {
    let display = $('#phoneDisplay');
    
    // Add vibration feedback if supported
    function vibrate() {
        if (navigator.vibrate) {
            navigator.vibrate(10);
        }
    }

    $('.keypad-btn').on('touchstart mousedown', function(e) {
        e.preventDefault(); // prevent default to avoid double click issues on mobile
        vibrate();
        if(display.val().length < 11) {
            display.val(display.val() + $(this).data('val'));
        }
    });

    $('#keypadClear').on('touchstart mousedown', function(e) {
        e.preventDefault();
        vibrate();
        let val = display.val();
        display.val(val.slice(0, -1)); // backspace functionality is better than clear all
    });

    // Handle long press to clear all
    let pressTimer;
    $('#keypadClear').on('touchstart mousedown', function(e) {
        pressTimer = window.setTimeout(function() {
            display.val('');
            if (navigator.vibrate) navigator.vibrate(50);
        }, 800);
    }).on('touchend mouseup', function(e) {
        clearTimeout(pressTimer);
    });

    $('#keypadSubmit').on('touchstart mousedown', function(e) {
        e.preventDefault();
        vibrate();
        let phone = display.val();
        if(phone.length < 11) {
            // Shake animation for error
            display.addClass('animate-[shake_0.5s_ease-in-out]');
            setTimeout(() => display.removeClass('animate-[shake_0.5s_ease-in-out]'), 500);
            return;
        }
        
        let btn = $(this);
        let originalContent = btn.html();
        btn.html('<i class="fa-solid fa-spinner fa-spin"></i>');

        $.ajax({
            url: '<?= BASE_URL ?>dsr/api',
            type: 'POST',
            data: { action: 'login', phone: phone },
            success: function(res) {
                if(res.status === 'success') {
                    display.addClass('text-green-400').removeClass('text-blue-400 border-b-2 border-slate-700').addClass('border-b-2 border-green-400');
                    setTimeout(() => {
                        window.location.href = '<?= BASE_URL ?>dsr/dashboard';
                    }, 300);
                } else {
                    display.addClass('text-red-400').removeClass('text-blue-400 border-b-2 border-slate-700').addClass('border-b-2 border-red-400 animate-[shake_0.5s_ease-in-out]');
                    setTimeout(() => {
                        display.removeClass('text-red-400 border-red-400 animate-[shake_0.5s_ease-in-out]').addClass('text-blue-400 border-slate-700');
                    }, 1000);
                    btn.html(originalContent);
                }
            },
            error: function() {
                alert('Connection error. Please try again.');
                btn.html(originalContent);
            }
        });
    });
});
</script>
