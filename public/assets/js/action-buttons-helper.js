/**
 * Action Buttons Helper
 * Helper untuk render button Edit dan Hapus yang konsisten
 * 
 * @version 1.0.0
 * @author Manajemen Kelas Team
 */

/**
 * Render button aksi (Edit & Hapus) untuk digunakan dalam JavaScript
 * 
 * @param {number|string} id - ID item yang akan di-edit/hapus
 * @param {string} type - Tipe entity (student, teacher, class, device, dll)
 * @param {object} options - Opsi tambahan
 * @param {string} options.editTooltip - Tooltip untuk button edit (default: 'Edit data')
 * @param {string} options.deleteTooltip - Tooltip untuk button delete (default: 'Hapus data')
 * @param {string} options.buttonSize - Ukuran button: sm, md, lg (default: 'sm')
 * @param {string} options.layout - Layout: horizontal, vertical (default: 'horizontal')
 * @param {string} options.editFunction - Custom function name untuk edit (override auto-generate)
 * @param {string} options.deleteFunction - Custom function name untuk delete (override auto-generate)
 * 
 * @returns {string} HTML string untuk action buttons
 * 
 * @example
 * // Basic usage
 * renderActionButtons(123, 'student')
 * 
 * @example
 * // With custom options
 * renderActionButtons(456, 'teacher', {
 *     editTooltip: 'Edit data guru',
 *     deleteTooltip: 'Hapus guru',
 *     buttonSize: 'md'
 * })
 * 
 * @example
 * // With custom function names
 * renderActionButtons(789, 'custom', {
 *     editFunction: 'openEditModal(789)',
 *     deleteFunction: 'confirmDelete(789)'
 * })
 */
function renderActionButtons(id, type, options = {}) {
    const defaults = {
        editTooltip: 'Edit data',
        deleteTooltip: 'Hapus data',
        buttonSize: 'sm',
        layout: 'horizontal',
        editFunction: null,
        deleteFunction: null
    };
    
    const opts = { ...defaults, ...options };
    
    // Size classes mapping
    const sizeClasses = {
        sm: 'px-3 py-2 text-sm',
        md: 'px-4 py-2.5 text-base',
        lg: 'px-5 py-3 text-lg'
    };
    const sizeClass = sizeClasses[opts.buttonSize] || sizeClasses.sm;
    
    // Container layout
    const containerClass = opts.layout === 'vertical' 
        ? 'flex flex-col gap-1' 
        : 'flex items-center justify-center gap-1';
    
    // Function names
    const editFunc = opts.editFunction || `edit${capitalize(type)}(${id})`;
    const deleteFunc = opts.deleteFunction || `delete${capitalize(type)}(${id})`;
    
    return `
        <div class="${containerClass}">
            <button 
                onclick="${editFunc}" 
                title="${escapeHtml(opts.editTooltip)}"
                class="${sizeClass} bg-primary-600 text-white hover:bg-primary-700 rounded-lg transition-colors font-medium whitespace-nowrap">
                Edit
            </button>
            <button 
                onclick="${deleteFunc}" 
                title="${escapeHtml(opts.deleteTooltip)}"
                class="${sizeClass} bg-red-600 text-white hover:bg-red-700 rounded-lg transition-colors font-medium whitespace-nowrap">
                Hapus
            </button>
        </div>
    `.trim();
}

/**
 * Capitalize first letter of string
 * @param {string} str - String to capitalize
 * @returns {string} Capitalized string
 */
function capitalize(str) {
    if (!str || typeof str !== 'string') return '';
    return str.charAt(0).toUpperCase() + str.slice(1);
}

/**
 * Escape HTML to prevent XSS
 * @param {string} text - Text to escape
 * @returns {string} Escaped text
 */
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, m => map[m]);
}

/**
 * Render button aksi dengan icon (untuk mobile atau card view)
 * 
 * @param {number|string} id - ID item
 * @param {string} type - Tipe entity
 * @param {object} options - Opsi (sama seperti renderActionButtons)
 * @returns {string} HTML string dengan icon
 */
function renderActionButtonsWithIcon(id, type, options = {}) {
    const defaults = {
        editTooltip: 'Edit data',
        deleteTooltip: 'Hapus data',
        buttonSize: 'sm',
        layout: 'horizontal',
        editFunction: null,
        deleteFunction: null
    };
    
    const opts = { ...defaults, ...options };
    
    const sizeClasses = {
        sm: 'px-3 py-2 text-sm',
        md: 'px-4 py-2.5 text-base',
        lg: 'px-5 py-3 text-lg'
    };
    const sizeClass = sizeClasses[opts.buttonSize] || sizeClasses.sm;
    
    const containerClass = opts.layout === 'vertical' 
        ? 'flex flex-col gap-2' 
        : 'flex items-center justify-center gap-2';
    
    const editFunc = opts.editFunction || `edit${capitalize(type)}(${id})`;
    const deleteFunc = opts.deleteFunction || `delete${capitalize(type)}(${id})`;
    
    return `
        <div class="${containerClass}">
            <button 
                onclick="${editFunc}" 
                title="${escapeHtml(opts.editTooltip)}"
                class="${sizeClass} bg-primary-600 text-white hover:bg-primary-700 rounded-lg transition-colors font-medium whitespace-nowrap flex items-center justify-center space-x-1">
                <span class="material-symbols-outlined text-base">edit</span>
                <span>Edit</span>
            </button>
            <button 
                onclick="${deleteFunc}" 
                title="${escapeHtml(opts.deleteTooltip)}"
                class="${sizeClass} bg-red-600 text-white hover:bg-red-700 rounded-lg transition-colors font-medium whitespace-nowrap flex items-center justify-center space-x-1">
                <span class="material-symbols-outlined text-base">delete</span>
                <span>Hapus</span>
            </button>
        </div>
    `.trim();
}

// Export untuk module system (jika digunakan)
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        renderActionButtons,
        renderActionButtonsWithIcon,
        capitalize,
        escapeHtml
    };
}
