@props(['id', 'model', 'placeholder' => '', 'rows' => '6', 'label' => ''])

<div class="enhanced-textarea-wrapper">
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 mb-2">
            {{ $label }}
        </label>
    @endif
    
    <!-- Formatting Toolbar -->
    <div class="mb-2 p-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-t-md">
        <div class="flex flex-wrap items-center gap-2 text-sm">
            <span class="text-gray-600 dark:text-gray-400 font-medium">Format:</span>
            
            <!-- Bullet List Button -->
            <button type="button" 
                    onclick="insertBulletList('{{ $id }}')"
                    class="px-2 py-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"
                    title="Bullet List">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                </svg>
            </button>
            
            <!-- Numbered List Button -->
            <button type="button" 
                    onclick="insertNumberedList('{{ $id }}')"
                    class="px-2 py-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"
                    title="Numbered List">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                </svg>
            </button>
            
            <!-- Bold Button -->
            <button type="button" 
                    onclick="insertBold('{{ $id }}')"
                    class="px-2 py-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors font-bold"
                    title="Bold">
                B
            </button>
            
            <!-- Italic Button -->
            <button type="button" 
                    onclick="insertItalic('{{ $id }}')"
                    class="px-2 py-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors italic"
                    title="Italic">
                I
            </button>
            
            <!-- Clear Formatting -->
            <button type="button" 
                    onclick="clearFormatting('{{ $id }}')"
                    class="px-2 py-1 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors text-red-600 dark:text-red-400"
                    title="Clear Formatting">
                Clear
            </button>
        </div>
        
        <!-- Tips Section -->
        <div class="mt-2 p-2 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded">
            <p class="text-xs text-yellow-800 dark:text-yellow-200">
                <strong>💡 Tips:</strong> 
                • Gunakan bullet points untuk kegiatan yang tidak berurutan<br>
                • Gunakan numbering untuk kegiatan yang berurutan<br>
                • Gunakan <strong>Bold</strong> untuk penekanan informasi penting
            </p>
        </div>
    </div>
    
    <!-- Enhanced Textarea -->
    <textarea 
        id="{{ $id }}"
        wire:model="{{ $model }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        class="block w-full border-gray-300 dark:border-gray-600 rounded-b-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white sm:text-sm resize-y"
        style="min-height: 200px; font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; line-height: 1.6;"
    ></textarea>
    
    <!-- Character Counter -->
    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 text-right">
        <span id="{{ $id }}-counter">0</span> karakter
    </div>
</div>

<script>
// Enhanced Textarea Functions
function insertBulletList(textareaId) {
    const textarea = document.getElementById(textareaId);
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selectedText = textarea.value.substring(start, end);
    
    if (selectedText) {
        // Convert selected text to bullet list
        const lines = selectedText.split('\n');
        const bulletedLines = lines.map(line => line.trim() ? `• ${line.trim()}` : line).join('\n');
        textarea.value = textarea.value.substring(0, start) + bulletedLines + textarea.value.substring(end);
    } else {
        // Insert bullet point at cursor
        const bulletPoint = '• ';
        textarea.value = textarea.value.substring(0, start) + bulletPoint + textarea.value.substring(end);
        textarea.selectionStart = textarea.selectionEnd = start + bulletPoint.length;
    }
    
    // Trigger Livewire update
    textarea.dispatchEvent(new Event('input', { bubbles: true }));
    updateCounter(textareaId);
}

function insertNumberedList(textareaId) {
    const textarea = document.getElementById(textareaId);
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selectedText = textarea.value.substring(start, end);
    
    if (selectedText) {
        // Convert selected text to numbered list
        const lines = selectedText.split('\n').filter(line => line.trim());
        const numberedLines = lines.map((line, index) => `${index + 1}. ${line.trim()}`).join('\n');
        textarea.value = textarea.value.substring(0, start) + numberedLines + textarea.value.substring(end);
    } else {
        // Insert numbered point at cursor
        const numberedPoint = '1. ';
        textarea.value = textarea.value.substring(0, start) + numberedPoint + textarea.value.substring(end);
        textarea.selectionStart = textarea.selectionEnd = start + numberedPoint.length;
    }
    
    // Trigger Livewire update
    textarea.dispatchEvent(new Event('input', { bubbles: true }));
    updateCounter(textareaId);
}

function insertBold(textareaId) {
    const textarea = document.getElementById(textareaId);
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selectedText = textarea.value.substring(start, end);
    
    if (selectedText) {
        // Wrap selected text with ** for bold
        const boldText = `**${selectedText}**`;
        textarea.value = textarea.value.substring(0, start) + boldText + textarea.value.substring(end);
        textarea.selectionStart = start;
        textarea.selectionEnd = start + boldText.length;
    } else {
        // Insert bold placeholder
        const boldPlaceholder = '**teks tebal**';
        textarea.value = textarea.value.substring(0, start) + boldPlaceholder + textarea.value.substring(end);
        textarea.selectionStart = start + 2;
        textarea.selectionEnd = start + 10;
    }
    
    // Trigger Livewire update
    textarea.dispatchEvent(new Event('input', { bubbles: true }));
    updateCounter(textareaId);
}

function insertItalic(textareaId) {
    const textarea = document.getElementById(textareaId);
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selectedText = textarea.value.substring(start, end);
    
    if (selectedText) {
        // Wrap selected text with * for italic
        const italicText = `*${selectedText}*`;
        textarea.value = textarea.value.substring(0, start) + italicText + textarea.value.substring(end);
        textarea.selectionStart = start;
        textarea.selectionEnd = start + italicText.length;
    } else {
        // Insert italic placeholder
        const italicPlaceholder = '*teks miring*';
        textarea.value = textarea.value.substring(0, start) + italicPlaceholder + textarea.value.substring(end);
        textarea.selectionStart = start + 1;
        textarea.selectionEnd = start + 10;
    }
    
    // Trigger Livewire update
    textarea.dispatchEvent(new Event('input', { bubbles: true }));
    updateCounter(textareaId);
}

function clearFormatting(textareaId) {
    const textarea = document.getElementById(textareaId);
    const text = textarea.value;
    
    // Remove markdown formatting
    let cleanedText = text
        .replace(/\*\*(.*?)\*\*/g, '$1') // Remove bold
        .replace(/\*(.*?)\*/g, '$1')     // Remove italic
        .replace(/^•\s*/gm, '')          // Remove bullet points
        .replace(/^\d+\.\s*/gm, '')      // Remove numbered lists
        .replace(/\n\s*\n/g, '\n\n')     // Clean up extra spaces
        .trim();
    
    textarea.value = cleanedText;
    
    // Trigger Livewire update
    textarea.dispatchEvent(new Event('input', { bubbles: true }));
    updateCounter(textareaId);
}

function updateCounter(textareaId) {
    const textarea = document.getElementById(textareaId);
    const counter = document.getElementById(textareaId + '-counter');
    if (counter) {
        counter.textContent = textarea.value.length;
    }
}

// Initialize counter on page load
document.addEventListener('livewire:init', () => {
    const textarea = document.getElementById('{{ $id }}');
    if (textarea) {
        updateCounter('{{ $id }}');
        textarea.addEventListener('input', () => updateCounter('{{ $id }}'));
    }
});
</script>
