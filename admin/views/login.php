<div class="w-full h-full flex items-center justify-center bg-gray-50">
    <div class="bg-white p-10 rounded-2xl shadow-xl w-full max-w-md border border-gray-100">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 text-green-600 mb-4 shadow-inner">
                <i class="fa-solid fa-lock text-2xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Admin Login</h2>
            <p class="text-gray-500 text-sm mt-1">Enter your credentials to access the dashboard</p>
        </div>

        <form id="adminLoginForm" class="space-y-5">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Phone / Username</label>
                <div class="relative">
                    <i class="fa-solid fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="phone" class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:bg-white transition" placeholder="admin" required>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                <div class="relative">
                    <i class="fa-solid fa-key absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="password" name="password" class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:bg-white transition" placeholder="••••••••" required>
                </div>
            </div>
            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all flex justify-center items-center gap-2">
                <span>Login</span> <i class="fa-solid fa-arrow-right"></i>
            </button>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#adminLoginForm').submit(function(e) {
        e.preventDefault();
        let btn = $(this).find('button[type="submit"]');
        let originalText = btn.html();
        btn.html('<i class="fa-solid fa-spinner fa-spin"></i> Authenticating...');
        
        $.ajax({
            url: '/pararbazar/admin/api',
            type: 'POST',
            data: $(this).serialize() + '&action=login',
            success: function(res) {
                if(res.status === 'success') {
                    window.location.href = '/pararbazar/admin/dashboard';
                } else {
                    alert(res.message);
                    btn.html(originalText);
                }
            },
            error: function() {
                alert('Connection error');
                btn.html(originalText);
            }
        });
    });
});
</script>
