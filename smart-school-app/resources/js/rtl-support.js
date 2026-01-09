/**
 * RTL Support JavaScript
 * 
 * Prompt 494: Fix RTL Charts and Tables
 * 
 * Provides JavaScript support for RTL layout including:
 * - Chart.js RTL configuration
 * - DataTables RTL adjustments
 * - Dynamic RTL detection and application
 */

(function() {
    'use strict';

    /**
     * RTL Support Manager
     */
    const RTLSupport = {
        /**
         * RTL languages list
         */
        rtlLanguages: ['ar', 'he', 'fa', 'ur', 'ps', 'sd', 'yi'],

        /**
         * Check if current locale is RTL
         */
        isRTL: function() {
            const htmlDir = document.documentElement.getAttribute('dir');
            if (htmlDir) {
                return htmlDir === 'rtl';
            }
            const htmlLang = document.documentElement.getAttribute('lang');
            if (htmlLang) {
                const langCode = htmlLang.split('-')[0].toLowerCase();
                return this.rtlLanguages.includes(langCode);
            }
            return false;
        },

        /**
         * Initialize RTL support
         */
        init: function() {
            if (this.isRTL()) {
                this.applyRTL();
            }
            this.setupChartDefaults();
            this.setupTableDefaults();
            this.setupFormDefaults();
        },

        /**
         * Apply RTL to document
         */
        applyRTL: function() {
            document.documentElement.setAttribute('dir', 'rtl');
            document.body.classList.add('rtl');
            
            // Load RTL Bootstrap CSS if not already loaded
            this.loadRTLBootstrap();
        },

        /**
         * Load RTL Bootstrap CSS
         */
        loadRTLBootstrap: function() {
            const existingRTLCSS = document.querySelector('link[href*="bootstrap.rtl"]');
            if (!existingRTLCSS) {
                const rtlLink = document.createElement('link');
                rtlLink.rel = 'stylesheet';
                rtlLink.href = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css';
                rtlLink.id = 'bootstrap-rtl-css';
                
                // Insert after regular Bootstrap CSS
                const bootstrapCSS = document.querySelector('link[href*="bootstrap"]');
                if (bootstrapCSS) {
                    bootstrapCSS.parentNode.insertBefore(rtlLink, bootstrapCSS.nextSibling);
                } else {
                    document.head.appendChild(rtlLink);
                }
            }
        },

        /**
         * Setup Chart.js defaults for RTL
         */
        setupChartDefaults: function() {
            if (typeof Chart === 'undefined') {
                return;
            }

            const isRTL = this.isRTL();

            // Set global Chart.js defaults for RTL
            Chart.defaults.font.family = isRTL 
                ? "'Segoe UI', 'Tahoma', 'Arial', sans-serif"
                : "'Figtree', sans-serif";

            // RTL-specific defaults
            if (isRTL) {
                // Reverse x-axis for bar charts
                Chart.defaults.scales.x = Chart.defaults.scales.x || {};
                Chart.defaults.scales.x.reverse = true;

                // Adjust legend position
                Chart.defaults.plugins.legend = Chart.defaults.plugins.legend || {};
                Chart.defaults.plugins.legend.rtl = true;
                Chart.defaults.plugins.legend.textDirection = 'rtl';

                // Adjust tooltip
                Chart.defaults.plugins.tooltip = Chart.defaults.plugins.tooltip || {};
                Chart.defaults.plugins.tooltip.rtl = true;
                Chart.defaults.plugins.tooltip.textDirection = 'rtl';
            }
        },

        /**
         * Configure a chart instance for RTL
         */
        configureChartForRTL: function(chartConfig) {
            if (!this.isRTL()) {
                return chartConfig;
            }

            // Ensure options exist
            chartConfig.options = chartConfig.options || {};
            chartConfig.options.plugins = chartConfig.options.plugins || {};
            chartConfig.options.scales = chartConfig.options.scales || {};

            // Configure legend for RTL
            chartConfig.options.plugins.legend = chartConfig.options.plugins.legend || {};
            chartConfig.options.plugins.legend.rtl = true;
            chartConfig.options.plugins.legend.textDirection = 'rtl';

            // Configure tooltip for RTL
            chartConfig.options.plugins.tooltip = chartConfig.options.plugins.tooltip || {};
            chartConfig.options.plugins.tooltip.rtl = true;
            chartConfig.options.plugins.tooltip.textDirection = 'rtl';

            // Configure x-axis for RTL (reverse for horizontal charts)
            if (chartConfig.type === 'bar' || chartConfig.type === 'line') {
                chartConfig.options.scales.x = chartConfig.options.scales.x || {};
                chartConfig.options.scales.x.reverse = true;
                chartConfig.options.scales.x.ticks = chartConfig.options.scales.x.ticks || {};
                chartConfig.options.scales.x.ticks.align = 'end';
            }

            // Configure y-axis for RTL
            chartConfig.options.scales.y = chartConfig.options.scales.y || {};
            chartConfig.options.scales.y.position = 'right';

            return chartConfig;
        },

        /**
         * Setup DataTables defaults for RTL
         */
        setupTableDefaults: function() {
            if (typeof $.fn === 'undefined' || typeof $.fn.dataTable === 'undefined') {
                return;
            }

            if (this.isRTL()) {
                $.extend(true, $.fn.dataTable.defaults, {
                    language: {
                        paginate: {
                            first: '&laquo;',
                            last: '&raquo;',
                            next: '&lsaquo;',
                            previous: '&rsaquo;'
                        }
                    },
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                         '<"row"<"col-sm-12"tr>>' +
                         '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
                });
            }
        },

        /**
         * Configure a DataTable instance for RTL
         */
        configureTableForRTL: function(tableConfig) {
            if (!this.isRTL()) {
                return tableConfig;
            }

            tableConfig = tableConfig || {};
            tableConfig.language = tableConfig.language || {};
            tableConfig.language.paginate = tableConfig.language.paginate || {};

            // Swap pagination arrows for RTL
            tableConfig.language.paginate.first = '&raquo;';
            tableConfig.language.paginate.last = '&laquo;';
            tableConfig.language.paginate.next = '&rsaquo;';
            tableConfig.language.paginate.previous = '&lsaquo;';

            return tableConfig;
        },

        /**
         * Setup form defaults for RTL
         */
        setupFormDefaults: function() {
            if (!this.isRTL()) {
                return;
            }

            // Adjust Select2 for RTL
            if (typeof $.fn !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
                $.fn.select2.defaults.set('dir', 'rtl');
            }

            // Adjust date pickers for RTL
            document.querySelectorAll('input[type="date"], input[type="datetime-local"]').forEach(function(input) {
                input.style.textAlign = 'right';
            });
        },

        /**
         * Mirror an element's position for RTL
         */
        mirrorPosition: function(element) {
            if (!this.isRTL() || !element) {
                return;
            }

            const computedStyle = window.getComputedStyle(element);
            const left = computedStyle.left;
            const right = computedStyle.right;

            if (left !== 'auto' && right === 'auto') {
                element.style.right = left;
                element.style.left = 'auto';
            } else if (right !== 'auto' && left === 'auto') {
                element.style.left = right;
                element.style.right = 'auto';
            }
        },

        /**
         * Get text direction
         */
        getDirection: function() {
            return this.isRTL() ? 'rtl' : 'ltr';
        },

        /**
         * Get alignment class
         */
        getAlignmentClass: function(alignment) {
            if (!this.isRTL()) {
                return alignment;
            }
            
            const alignmentMap = {
                'text-start': 'text-end',
                'text-end': 'text-start',
                'text-left': 'text-right',
                'text-right': 'text-left',
                'float-start': 'float-end',
                'float-end': 'float-start',
                'ms-auto': 'me-auto',
                'me-auto': 'ms-auto'
            };

            return alignmentMap[alignment] || alignment;
        },

        /**
         * Apply RTL to dynamically loaded content
         */
        applyToContent: function(container) {
            if (!this.isRTL() || !container) {
                return;
            }

            // Apply RTL to tables
            container.querySelectorAll('table').forEach(function(table) {
                table.setAttribute('dir', 'rtl');
            });

            // Apply RTL to forms
            container.querySelectorAll('form').forEach(function(form) {
                form.setAttribute('dir', 'rtl');
            });

            // Reinitialize any charts in the container
            container.querySelectorAll('canvas').forEach(function(canvas) {
                const chartInstance = Chart.getChart(canvas);
                if (chartInstance) {
                    RTLSupport.configureChartForRTL(chartInstance.config);
                    chartInstance.update();
                }
            });
        }
    };

    /**
     * Localization Helper
     */
    const LocalizationHelper = {
        /**
         * Format number according to locale
         */
        formatNumber: function(number, locale, options) {
            locale = locale || document.documentElement.lang || 'en';
            options = options || {};
            
            try {
                return new Intl.NumberFormat(locale, options).format(number);
            } catch (e) {
                return number.toString();
            }
        },

        /**
         * Format currency according to locale
         */
        formatCurrency: function(amount, currency, locale) {
            locale = locale || document.documentElement.lang || 'en';
            currency = currency || 'USD';
            
            try {
                return new Intl.NumberFormat(locale, {
                    style: 'currency',
                    currency: currency
                }).format(amount);
            } catch (e) {
                return currency + ' ' + amount.toFixed(2);
            }
        },

        /**
         * Format date according to locale
         */
        formatDate: function(date, locale, options) {
            locale = locale || document.documentElement.lang || 'en';
            options = options || { dateStyle: 'medium' };
            
            if (typeof date === 'string') {
                date = new Date(date);
            }
            
            try {
                return new Intl.DateTimeFormat(locale, options).format(date);
            } catch (e) {
                return date.toLocaleDateString();
            }
        },

        /**
         * Format time according to locale
         */
        formatTime: function(date, locale, options) {
            locale = locale || document.documentElement.lang || 'en';
            options = options || { timeStyle: 'short' };
            
            if (typeof date === 'string') {
                date = new Date(date);
            }
            
            try {
                return new Intl.DateTimeFormat(locale, options).format(date);
            } catch (e) {
                return date.toLocaleTimeString();
            }
        },

        /**
         * Format date and time according to locale
         */
        formatDateTime: function(date, locale, options) {
            locale = locale || document.documentElement.lang || 'en';
            options = options || { dateStyle: 'medium', timeStyle: 'short' };
            
            if (typeof date === 'string') {
                date = new Date(date);
            }
            
            try {
                return new Intl.DateTimeFormat(locale, options).format(date);
            } catch (e) {
                return date.toLocaleString();
            }
        },

        /**
         * Format relative time (e.g., "2 hours ago")
         */
        formatRelativeTime: function(date, locale) {
            locale = locale || document.documentElement.lang || 'en';
            
            if (typeof date === 'string') {
                date = new Date(date);
            }
            
            const now = new Date();
            const diffInSeconds = Math.floor((now - date) / 1000);
            
            const units = [
                { unit: 'year', seconds: 31536000 },
                { unit: 'month', seconds: 2592000 },
                { unit: 'week', seconds: 604800 },
                { unit: 'day', seconds: 86400 },
                { unit: 'hour', seconds: 3600 },
                { unit: 'minute', seconds: 60 },
                { unit: 'second', seconds: 1 }
            ];
            
            try {
                const rtf = new Intl.RelativeTimeFormat(locale, { numeric: 'auto' });
                
                for (const { unit, seconds } of units) {
                    if (Math.abs(diffInSeconds) >= seconds) {
                        const value = Math.floor(diffInSeconds / seconds);
                        return rtf.format(-value, unit);
                    }
                }
                
                return rtf.format(0, 'second');
            } catch (e) {
                return date.toLocaleString();
            }
        },

        /**
         * Get day names for locale
         */
        getDayNames: function(locale, format) {
            locale = locale || document.documentElement.lang || 'en';
            format = format || 'long';
            
            const days = [];
            const baseDate = new Date(2024, 0, 7); // A Sunday
            
            for (let i = 0; i < 7; i++) {
                const date = new Date(baseDate);
                date.setDate(baseDate.getDate() + i);
                days.push(date.toLocaleDateString(locale, { weekday: format }));
            }
            
            return days;
        },

        /**
         * Get month names for locale
         */
        getMonthNames: function(locale, format) {
            locale = locale || document.documentElement.lang || 'en';
            format = format || 'long';
            
            const months = [];
            
            for (let i = 0; i < 12; i++) {
                const date = new Date(2024, i, 1);
                months.push(date.toLocaleDateString(locale, { month: format }));
            }
            
            return months;
        }
    };

    /**
     * Chart RTL Plugin for Chart.js
     */
    const ChartRTLPlugin = {
        id: 'rtlSupport',
        beforeInit: function(chart) {
            if (RTLSupport.isRTL()) {
                RTLSupport.configureChartForRTL(chart.config);
            }
        }
    };

    // Register Chart.js plugin if Chart.js is available
    if (typeof Chart !== 'undefined') {
        Chart.register(ChartRTLPlugin);
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            RTLSupport.init();
        });
    } else {
        RTLSupport.init();
    }

    // Expose to global scope
    window.RTLSupport = RTLSupport;
    window.LocalizationHelper = LocalizationHelper;

})();
