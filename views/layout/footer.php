<footer class="md:ml-64 px-4 sm:px-8 py-4 text-center text-xs text-slate-400">
    &copy; <?= date('Y') ?> PTE Management System. All rights reserved.
</footer>

<script>
(function () {
    var viewport = document.getElementById('toast-viewport');
    if (!viewport) return;

    var ICONS = {
        success: 'ti-circle-check',
        error:   'ti-alert-circle',
    };
    var STYLES = {
        success: { bg: 'bg-green-50', border: 'border-green-200', text: 'text-green-700', icon: 'text-green-600' },
        error:   { bg: 'bg-red-50',   border: 'border-red-200',   text: 'text-red-700',   icon: 'text-red-600' },
    };

    window.showToast = function (type, message, duration) {
        type = STYLES[type] ? type : 'success';
        duration = typeof duration === 'number' ? duration : 4000;
        var style = STYLES[type];

        var toast = document.createElement('div');
        toast.className = 'toast ' + style.bg + ' ' + style.border + ' ' + style.text +
            ' border rounded-lg shadow-lg px-4 py-3 flex items-start gap-2.5 text-sm';
        toast.setAttribute('role', 'status');

        var icon = document.createElement('i');
        icon.className = 'ti ' + ICONS[type] + ' text-lg shrink-0 ' + style.icon;

        var text = document.createElement('span');
        text.className = 'flex-1';
        text.textContent = message;

        var closeBtn = document.createElement('button');
        closeBtn.type = 'button';
        closeBtn.className = 'shrink-0 -m-1 p-1 rounded hover:bg-black/5 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 focus-visible:ring-current';
        closeBtn.setAttribute('aria-label', 'Dismiss notification');
        closeBtn.innerHTML = '<i class="ti ti-x text-sm"></i>';

        toast.appendChild(icon);
        toast.appendChild(text);
        toast.appendChild(closeBtn);
        viewport.appendChild(toast);

        requestAnimationFrame(function () {
            toast.classList.add('toast-visible');
        });

        var dismissed = false;
        var timer = duration > 0 ? setTimeout(dismiss, duration) : null;

        function dismiss() {
            if (dismissed) return;
            dismissed = true;
            if (timer) clearTimeout(timer);
            toast.classList.remove('toast-visible');
            toast.classList.add('toast-leaving');
            toast.addEventListener('transitionend', function () {
                toast.remove();
            }, { once: true });
        }

        closeBtn.addEventListener('click', dismiss);
        toast.addEventListener('mouseenter', function () { if (timer) clearTimeout(timer); });
        toast.addEventListener('mouseleave', function () { if (duration > 0) timer = setTimeout(dismiss, duration); });

        return dismiss;
    };

    var flashEl = document.getElementById('flash-toasts');
    if (flashEl) {
        try {
            var toasts = JSON.parse(flashEl.textContent);
            toasts.forEach(function (t, i) {
                setTimeout(function () { window.showToast(t.type, t.message); }, i * 120);
            });
        } catch (e) { /* malformed payload, nothing to show */ }
    }

    // Malaysian IC number auto-format: 030101011234 -> 030101-01-1234
    function formatIc(digits) {
        digits = digits.slice(0, 12);
        var parts = [digits.slice(0, 6), digits.slice(6, 8), digits.slice(8, 12)];
        return parts.filter(function (p) { return p.length > 0; }).join('-');
    }

    document.querySelectorAll('input[data-format="ic"]').forEach(function (input) {
        input.addEventListener('input', function () {
            var before = input.value;
            var caret = input.selectionStart;
            var digitsBeforeCaret = before.slice(0, caret).replace(/[^0-9]/g, '').length;

            var digits = before.replace(/[^0-9]/g, '');
            var formatted = formatIc(digits);
            input.value = formatted;

            // Re-derive caret position from digit count so editing mid-string doesn't jump to the end
            var seen = 0, pos = 0;
            for (; pos < formatted.length; pos++) {
                if (seen === digitsBeforeCaret) break;
                if (/[0-9]/.test(formatted[pos])) seen++;
            }
            if (seen < digitsBeforeCaret) pos = formatted.length;
            input.setSelectionRange(pos, pos);
        });
    });
})();
</script>
</body>
</html>
