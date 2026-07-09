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

    // Searchable select: progressively enhances any <select data-searchable>
    // into a type-to-filter combobox, while keeping the original <select>
    // (hidden) as the real form field — no backend/validation changes needed.
    document.querySelectorAll('select[data-searchable]').forEach(function (select) {
        var options = Array.prototype.map.call(select.options, function (opt) {
            return { value: opt.value, label: opt.textContent.trim(), disabled: opt.disabled };
        });
        var placeholder = select.getAttribute('data-placeholder') || 'Search…';

        var wrap = document.createElement('div');
        wrap.className = 'relative';

        var input = document.createElement('input');
        input.type = 'text';
        input.autocomplete = 'off';
        input.setAttribute('role', 'combobox');
        input.setAttribute('aria-expanded', 'false');
        input.setAttribute('aria-autocomplete', 'list');
        input.className = select.className + ' pr-9';
        input.placeholder = placeholder;

        var icon = document.createElement('i');
        icon.className = 'ti ti-search text-slate-400 text-sm absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none';

        var list = document.createElement('ul');
        list.setAttribute('role', 'listbox');
        list.className = 'hidden absolute z-10 mt-1 w-full max-h-56 overflow-y-auto bg-white border border-slate-200 rounded-lg shadow-lg py-1 text-sm';

        var noResults = document.createElement('li');
        noResults.className = 'hidden px-3 py-2 text-slate-400';
        noResults.textContent = 'No matches found.';

        function buildItems(filter) {
            list.innerHTML = '';
            var term = (filter || '').toLowerCase();
            var shown = 0;
            options.forEach(function (opt) {
                if (opt.value === '' || opt.disabled) return;
                if (term && opt.label.toLowerCase().indexOf(term) === -1) return;
                var li = document.createElement('li');
                li.setAttribute('role', 'option');
                li.dataset.value = opt.value;
                li.className = 'px-3 py-2 cursor-pointer hover:bg-indigo-50 text-slate-700' +
                    (opt.value === select.value ? ' bg-indigo-50 font-medium' : '');
                li.textContent = opt.label;
                li.addEventListener('mousedown', function (e) {
                    e.preventDefault();
                    selectOption(opt);
                });
                list.appendChild(li);
                shown++;
            });
            list.appendChild(noResults);
            noResults.classList.toggle('hidden', shown > 0);
        }

        function selectOption(opt) {
            select.value = opt.value;
            input.value = opt.value === '' ? '' : opt.label;
            closeList();
            select.dispatchEvent(new Event('change', { bubbles: true }));
        }

        function openList() {
            buildItems(input.value === currentLabel() ? '' : input.value);
            list.classList.remove('hidden');
            input.setAttribute('aria-expanded', 'true');
        }

        function closeList() {
            list.classList.add('hidden');
            input.setAttribute('aria-expanded', 'false');
        }

        function currentLabel() {
            if (select.value === '') return '';
            var opt = options.find(function (o) { return o.value === select.value; });
            return opt ? opt.label : '';
        }

        input.addEventListener('focus', openList);
        input.addEventListener('input', function () { buildItems(input.value); list.classList.remove('hidden'); input.setAttribute('aria-expanded', 'true'); });
        input.addEventListener('blur', function () {
            // Revert to the last confirmed selection if the user tabs/clicks away mid-search
            setTimeout(function () {
                input.value = currentLabel();
                closeList();
            }, 150);
        });
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') { input.value = currentLabel(); closeList(); input.blur(); }
            if (e.key === 'Enter') {
                e.preventDefault();
                var first = list.querySelector('li[role="option"]');
                if (first) selectOption({ value: first.dataset.value, label: first.textContent });
            }
        });

        input.value = currentLabel();

        select.classList.add('sr-only');
        select.setAttribute('tabindex', '-1');
        select.setAttribute('aria-hidden', 'true');
        select.parentNode.insertBefore(wrap, select);
        wrap.appendChild(select);
        wrap.appendChild(input);
        wrap.appendChild(icon);
        wrap.appendChild(list);
    });

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
