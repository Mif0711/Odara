(() => {
    'use strict';

    /* =====================================================
       Sidebar
       ===================================================== */
    const sidebar = document.getElementById('sidebar');
    const overlay = document.querySelector('[data-sidebar-close]');

    const openSidebar = () => {
        if (!sidebar) return;
        sidebar.classList.add('is-open');
        overlay?.classList.add('is-visible');
        document.body.style.overflow = 'hidden';
    };

    const closeSidebar = () => {
        if (!sidebar) return;
        sidebar.classList.remove('is-open');
        overlay?.classList.remove('is-visible');
        document.body.style.overflow = '';
    };

    document.querySelectorAll('[data-sidebar-toggle]').forEach((button) => {
        button.addEventListener('click', openSidebar);
    });

    overlay?.addEventListener('click', closeSidebar);

    window.addEventListener('resize', () => {
        if (window.innerWidth > 900) closeSidebar();
    });

    /* =====================================================
       Flash alerts
       ===================================================== */
    document.querySelectorAll('.toast').forEach((alert) => {
        let timer;

        const dismiss = () => {
            window.clearTimeout(timer);
            alert.classList.add('is-hiding');
            window.setTimeout(() => alert.remove(), 260);
        };

        alert.querySelector('[data-dismiss-alert]')?.addEventListener('click', dismiss);
        timer = window.setTimeout(dismiss, 6000);
    });

    /* =====================================================
       Confirmation dialogs
       ===================================================== */
    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const message = form.getAttribute('data-confirm');

            if (message && !window.confirm(message)) {
                event.preventDefault();
            }
        });
    });

    /* =====================================================
       Submit / loading state
       ===================================================== */
    document.querySelectorAll('form[method="POST"]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (event.defaultPrevented) return;

            const button = form.querySelector('button[type="submit"]');

            if (!button || button.disabled) return;

            window.setTimeout(() => {
                button.disabled = true;
                button.classList.add('is-loading');
                button.innerHTML = '<span class="spinner" aria-hidden="true"></span> Memproses...';
            }, 0);
        });
    });

    /* =====================================================
       Total calculation on transaction forms
       ===================================================== */
    document.querySelectorAll('[data-calc-form]').forEach((form) => {
        const priceInput = form.querySelector('[data-price]');
        const serviceInput = form.querySelector('[data-service]');
        const qtyInput = form.querySelector('[data-qty]');
        const output = form.querySelector('[data-total-output]');

        if (!output) return;

        const parseRupiah = (value) => {
            const digits = String(value ?? '').replace(/\D/g, '');
            return digits === '' ? 0 : Number.parseInt(digits, 10);
        };

        const formatRupiah = (value) => new Intl.NumberFormat('id-ID').format(value);

        const updateTotal = () => {
            const price = parseRupiah(priceInput?.value);
            const service = parseRupiah(serviceInput?.value);
            const qty = qtyInput ? Math.max(1, Number.parseInt(qtyInput.value || '1', 10) || 1) : 1;
            const total = (price + service) * qty;

            output.textContent = 'Rp ' + formatRupiah(total);
        };

        const bindCurrencyInput = (input) => {
            if (!input) return;

            input.addEventListener('input', () => {
                const digits = input.value.replace(/\D/g, '');
                input.value = digits === '' ? '' : formatRupiah(Number.parseInt(digits, 10));
                updateTotal();
            });
        };

        bindCurrencyInput(priceInput);
        bindCurrencyInput(serviceInput);
        qtyInput?.addEventListener('input', updateTotal);

        updateTotal();
    });

    /* =====================================================
       Modern date picker
       ===================================================== */
    const MONTHS = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
    ];
    const MONTHS_SHORT = [
        'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
        'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des',
    ];

    const pad = (value) => String(value).padStart(2, '0');

    const toISO = (date) => {
        return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
    };

    const fromISO = (iso) => {
        if (!iso || !/^\d{4}-\d{2}-\d{2}$/.test(iso)) return null;
        const [year, month, day] = iso.split('-').map(Number);
        return new Date(year, month - 1, day);
    };

    const formatLong = (iso) => {
        const date = fromISO(iso);
        if (!date) return '';
        return `${date.getDate()} ${MONTHS[date.getMonth()]} ${date.getFullYear()}`;
    };

    const formatShort = (iso) => {
        const date = fromISO(iso);
        if (!date) return '';
        return `${date.getDate()} ${MONTHS_SHORT[date.getMonth()]} ${date.getFullYear()}`;
    };

    class OdaraDatePicker {
        constructor(root) {
            this.root = root;
            this.form = root.closest('form');
            this.trigger = root.querySelector('[data-dp-trigger]');
            this.popup = root.querySelector('[data-dp-popup]');
            this.monthLabel = root.querySelector('[data-dp-month]');
            this.days = root.querySelector('[data-dp-days]');
            this.selection = root.querySelector('[data-dp-selection]');
            this.dateInput = root.querySelector('[data-dp-date]');
            this.startInput = root.querySelector('[data-dp-start]');
            this.endInput = root.querySelector('[data-dp-end]');
            this.modeButtons = root.querySelectorAll('[data-dp-mode]');

            this.pending = { date: null, start: null, end: null };
            this.mode = 'single';
            this.viewDate = new Date();
            this.viewDate.setDate(1);

            this.bind();
            this.updateTriggerLabel();
        }

        syncFromInputs() {
            this.pending = {
                date: this.dateInput?.value || null,
                start: this.startInput?.value || null,
                end: this.endInput?.value || null,
            };

            if (this.pending.start || this.pending.end) {
                this.mode = 'range';
            } else if (this.pending.date) {
                this.mode = 'single';
            }

            const base = this.pending.date || this.pending.start || this.pending.end;
            const baseDate = base ? fromISO(base) : new Date();

            this.viewDate = new Date(baseDate.getFullYear(), baseDate.getMonth(), 1);
        }

        bind() {
            this.trigger?.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();

                if (this.popup?.hidden === false) {
                    this.close();
                } else {
                    this.open();
                }
            });

            this.root.querySelector('[data-dp-prev]')?.addEventListener('click', () => this.changeMonth(-1));
            this.root.querySelector('[data-dp-next]')?.addEventListener('click', () => this.changeMonth(1));

            this.days?.addEventListener('click', (event) => {
                const button = event.target.closest('[data-date]');
                if (!button) return;
                this.pickDate(button.dataset.date);
            });

            this.modeButtons.forEach((button) => {
                button.addEventListener('click', () => this.switchMode(button.dataset.dpMode));
            });

            this.root.querySelector('[data-dp-today]')?.addEventListener('click', () => this.pickToday());
            this.root.querySelector('[data-dp-apply]')?.addEventListener('click', () => this.apply());
            this.root.querySelector('[data-dp-clear]')?.addEventListener('click', () => this.clearAndApply());

            document.addEventListener('click', (event) => {
                if (this.popup?.hidden === false && !this.root.contains(event.target)) {
                    this.close();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && this.popup?.hidden === false) {
                    this.close();
                    this.trigger?.focus();
                }
            });
        }

        open() {
            this.syncFromInputs();
            this.popup.hidden = false;
            this.trigger?.setAttribute('aria-expanded', 'true');

            document.querySelectorAll('[data-datepicker]').forEach((other) => {
                if (other !== this.root) {
                    const popup = other.querySelector('[data-dp-popup]');
                    if (popup) popup.hidden = true;
                }
            });

            this.render();
        }

        close() {
            this.popup.hidden = true;
            this.trigger?.setAttribute('aria-expanded', 'false');
        }

        changeMonth(offset) {
            this.viewDate = new Date(this.viewDate.getFullYear(), this.viewDate.getMonth() + offset, 1);
            this.render();
        }

        pickDate(iso) {
            if (this.mode === 'single') {
                this.pending.date = iso;
                this.pending.start = null;
                this.pending.end = null;
            } else {
                if (!this.pending.start || (this.pending.start && this.pending.end)) {
                    this.pending.start = iso;
                    this.pending.end = null;
                } else if (iso < this.pending.start) {
                    this.pending.end = this.pending.start;
                    this.pending.start = iso;
                } else {
                    this.pending.end = iso;
                }

                this.pending.date = null;
            }

            this.render();
        }

        pickToday() {
            const today = new Date();
            const iso = toISO(today);
            this.viewDate = new Date(today.getFullYear(), today.getMonth(), 1);

            if (this.mode === 'single') {
                this.pending = { date: iso, start: null, end: null };
            } else {
                this.pending = { date: null, start: iso, end: null };
            }

            this.render();
        }

        switchMode(mode) {
            if (mode === this.mode) return;

            if (mode === 'single') {
                this.pending.date = this.pending.date || this.pending.start || this.pending.end;
                this.pending.start = null;
                this.pending.end = null;
            } else {
                this.pending.start = this.pending.start || this.pending.date;
                this.pending.end = null;
                this.pending.date = null;
            }

            this.mode = mode;
            this.render();
        }

        updateModeButtons() {
            this.modeButtons.forEach((button) => {
                const active = button.dataset.dpMode === this.mode;
                button.classList.toggle('is-active', active);
                button.setAttribute('aria-pressed', active ? 'true' : 'false');
            });
        }

        updateTriggerLabel() {
            const label = this.trigger?.querySelector('[data-dp-label]');
            if (!label) return;

            const date = this.dateInput?.value;
            const start = this.startInput?.value;
            const end = this.endInput?.value;

            if (date) {
                label.textContent = formatLong(date);
                this.trigger.classList.add('has-value');
            } else if (start && end) {
                label.textContent = start === end
                    ? formatLong(start)
                    : `${formatShort(start)} – ${formatShort(end)}`;
                this.trigger.classList.add('has-value');
            } else if (start) {
                label.textContent = `Sejak ${formatShort(start)}`;
                this.trigger.classList.add('has-value');
            } else if (end) {
                label.textContent = `Sampai ${formatShort(end)}`;
                this.trigger.classList.add('has-value');
            } else {
                label.textContent = 'Semua tanggal';
                this.trigger.classList.remove('has-value');
            }
        }

        updateSelectionText() {
            if (!this.selection) return;

            if (this.mode === 'single') {
                this.selection.textContent = this.pending.date
                    ? `Tanggal: ${formatLong(this.pending.date)}`
                    : 'Pilih tanggal pada kalender';
                return;
            }

            if (this.pending.start && this.pending.end) {
                this.selection.textContent = `${formatShort(this.pending.start)} – ${formatShort(this.pending.end)}`;
            } else if (this.pending.start) {
                this.selection.textContent = `Mulai ${formatShort(this.pending.start)} · pilih tanggal akhir`;
            } else {
                this.selection.textContent = 'Pilih tanggal awal dan tanggal akhir';
            }
        }

        render() {
            if (!this.days || !this.monthLabel) return;

            const year = this.viewDate.getFullYear();
            const month = this.viewDate.getMonth();
            const firstDay = new Date(year, month, 1);
            const offset = (firstDay.getDay() + 6) % 7;
            const totalDays = new Date(year, month + 1, 0).getDate();
            const todayISO = toISO(new Date());

            this.monthLabel.textContent = `${MONTHS[month]} ${year}`;

            let html = '';

            for (let i = 0; i < offset; i += 1) {
                html += '<span class="dp-blank" aria-hidden="true"></span>';
            }

            for (let day = 1; day <= totalDays; day += 1) {
                const iso = `${year}-${pad(month + 1)}-${pad(day)}`;
                const classes = ['dp-day'];

                if (iso === todayISO) classes.push('is-today');

                if (this.mode === 'single') {
                    if (this.pending.date === iso) classes.push('is-selected');
                } else {
                    const { start, end } = this.pending;

                    if (start && end) {
                        if (iso === start && iso === end) {
                            classes.push('is-range-start', 'is-range-end');
                        } else if (iso === start) {
                            classes.push('is-range-start');
                        } else if (iso === end) {
                            classes.push('is-range-end');
                        } else if (iso > start && iso < end) {
                            classes.push('is-in-range');
                        }
                    } else if (start && iso === start) {
                        classes.push('is-range-start');
                    }
                }

                html += `<button type="button" class="${classes.join(' ')}" data-date="${iso}" aria-label="${formatLong(iso)}">${day}</button>`;
            }

            this.days.innerHTML = html;
            this.updateModeButtons();
            this.updateSelectionText();
        }

        writeInputs() {
            if (this.mode === 'single') {
                if (this.dateInput) this.dateInput.value = this.pending.date || '';
                if (this.startInput) this.startInput.value = '';
                if (this.endInput) this.endInput.value = '';
            } else {
                if (this.dateInput) this.dateInput.value = '';
                if (this.startInput) this.startInput.value = this.pending.start || '';
                if (this.endInput) this.endInput.value = this.pending.end || '';
            }
        }

        apply() {
            this.writeInputs();
            this.updateTriggerLabel();
            this.close();

            if (this.form) {
                if (this.form.requestSubmit) {
                    this.form.requestSubmit();
                } else {
                    this.form.submit();
                }
            }
        }

        clearAndApply() {
            this.pending = { date: null, start: null, end: null };
            this.mode = 'single';
            this.writeInputs();
            this.updateTriggerLabel();
            this.close();

            if (this.form) {
                if (this.form.requestSubmit) {
                    this.form.requestSubmit();
                } else {
                    this.form.submit();
                }
            }
        }
    }

    document.querySelectorAll('[data-datepicker]').forEach((root) => {
        const trigger = root.querySelector('[data-dp-trigger]');
        trigger?.setAttribute('aria-haspopup', 'dialog');
        trigger?.setAttribute('aria-expanded', 'false');

        new OdaraDatePicker(root);
    });

    /* =====================================================
       Report month & year picker
       ===================================================== */
    document.querySelectorAll('[data-mp]').forEach((form) => {
        const trigger = form.querySelector('[data-mp-trigger]');
        const popup = form.querySelector('[data-mp-popup]');
        const monthInput = form.querySelector('[data-mp-month]');
        const yearInput = form.querySelector('[data-mp-year]');
        const label = form.querySelector('[data-mp-label]');
        const yearLabel = form.querySelector('[data-mp-year-label]');
        const options = Array.from(form.querySelectorAll('[data-mp-month-option]'));

        if (!trigger || !popup || !monthInput || !yearInput || !label) return;

        const now = new Date();
        let month = Number.parseInt(monthInput.value, 10) || now.getMonth() + 1;
        let year = Number.parseInt(yearInput.value, 10) || now.getFullYear();

        const render = () => {
            label.textContent = `${MONTHS[month - 1]} ${year}`;

            if (yearLabel) {
                yearLabel.textContent = String(year);
            }

            monthInput.value = String(month);
            yearInput.value = String(year);

            options.forEach((option) => {
                const optionMonth = Number.parseInt(option.dataset.mpMonthOption, 10);
                option.classList.toggle('is-selected', optionMonth === month);
            });
        };

        const close = () => {
            popup.hidden = true;
            trigger.setAttribute('aria-expanded', 'false');
        };

        const submit = () => {
            render();

            if (form.requestSubmit) {
                form.requestSubmit();
            } else {
                form.submit();
            }
        };

        trigger.setAttribute('aria-haspopup', 'dialog');
        trigger.setAttribute('aria-expanded', 'false');

        trigger.addEventListener('click', () => {
            if (popup.hidden) {
                popup.hidden = false;
                trigger.setAttribute('aria-expanded', 'true');
            } else {
                close();
            }
        });

        form.querySelector('[data-mp-year-prev]')?.addEventListener('click', () => {
            year -= 1;
            render();
        });

        form.querySelector('[data-mp-year-next]')?.addEventListener('click', () => {
            year += 1;
            render();
        });

        options.forEach((option) => {
            option.addEventListener('click', () => {
                month = Number.parseInt(option.dataset.mpMonthOption, 10) || month;
                submit();
            });
        });

        form.querySelector('[data-mp-current]')?.addEventListener('click', () => {
            const current = new Date();
            month = current.getMonth() + 1;
            year = current.getFullYear();
            submit();
        });

        document.addEventListener('click', (event) => {
            if (!popup.hidden && !form.contains(event.target)) {
                close();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !popup.hidden) {
                close();
            }
        });

        render();
    });
})();
