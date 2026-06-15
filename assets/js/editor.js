(function () {
  function insertAtCursor(textarea, before, after) {
    textarea.focus();
    const start = textarea.selectionStart || 0;
    const end = textarea.selectionEnd || 0;
    const selected = textarea.value.substring(start, end);
    const replacement = before + selected + after;
    textarea.setRangeText(replacement, start, end, 'end');
    textarea.dispatchEvent(new Event('input', { bubbles: true }));
  }

  function makeEditor(textarea) {
    if (textarea.dataset.editorReady === '1') return;
    textarea.dataset.editorReady = '1';

    const wrapper = document.createElement('div');
    wrapper.className = 'html-editor-wrap';
    const toolbar = document.createElement('div');
    toolbar.className = 'html-editor-toolbar';

    const buttons = [
      ['B', '<strong>', '</strong>', 'Fett'],
      ['I', '<em>', '</em>', 'Kursiv'],
      ['H2', '<h2>', '</h2>', 'Überschrift'],
      ['P', '<p>', '</p>', 'Absatz'],
      ['Liste', '<ul>\n<li>', '</li>\n</ul>', 'Liste'],
      ['Link', '<a href="https://">', '</a>', 'Link'],
      ['Bild', '<img src="/uploads/bild.jpg" alt="">', '', 'Bild'],
      ['Box', '<div class="info-box">', '</div>', 'Infobox']
    ];

    buttons.forEach(([label, before, after, title]) => {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.textContent = label;
      btn.title = title;
      btn.addEventListener('click', () => insertAtCursor(textarea, before, after));
      toolbar.appendChild(btn);
    });

    const previewBtn = document.createElement('button');
    previewBtn.type = 'button';
    previewBtn.textContent = 'Vorschau';
    toolbar.appendChild(previewBtn);

    const preview = document.createElement('div');
    preview.className = 'html-editor-preview';
    preview.hidden = true;

    textarea.parentNode.insertBefore(wrapper, textarea);
    wrapper.appendChild(toolbar);
    wrapper.appendChild(textarea);
    wrapper.appendChild(preview);
    textarea.classList.add('html-editor-textarea');

    function updatePreview() {
      preview.innerHTML = textarea.value;
    }

    previewBtn.addEventListener('click', () => {
      preview.hidden = !preview.hidden;
      if (!preview.hidden) updatePreview();
    });
    textarea.addEventListener('input', () => {
      if (!preview.hidden) updatePreview();
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('textarea.html-editor').forEach(makeEditor);
  });
})();
