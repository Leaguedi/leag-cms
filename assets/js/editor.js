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

  function insertImage(textarea) {
    const url = prompt('Bild-URL einfügen, z.B. /uploads/media/bild.jpg');

    if (!url) {
      return;
    }

    const alt = prompt('Alt-Text für das Bild (optional)') || '';
    const html = '<img class="content-image" src="' + url + '" alt="' + alt.replace(/"/g, '&quot;') + '">';

    insertAtCursor(textarea, html, '');
  }

  function makeEditor(textarea) {
    if (textarea.dataset.editorReady === '1') {
      return;
    }

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
      ['Box', '<div class="info-box">', '</div>', 'Infobox']
    ];

    buttons.forEach(([label, before, after, title]) => {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.textContent = label;
      btn.title = title;

      btn.addEventListener('click', () => {
        insertAtCursor(textarea, before, after);
      });

      toolbar.appendChild(btn);
    });

    const imageBtn = document.createElement('button');
    imageBtn.type = 'button';
    imageBtn.textContent = 'Bild';
    imageBtn.title = 'Bild aus Medienmanager einfügen';
    imageBtn.addEventListener('click', () => insertImage(textarea));
    toolbar.appendChild(imageBtn);

    const mediaBtn = document.createElement('button');
    mediaBtn.type = 'button';
    mediaBtn.textContent = 'Medien';
    mediaBtn.title = 'Medienmanager öffnen';
    mediaBtn.addEventListener('click', () => {
      window.open('/admin/media.php', 'mediaManager', 'width=1100,height=750');
    });
    toolbar.appendChild(mediaBtn);

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

      if (!preview.hidden) {
        updatePreview();
      }
    });

    textarea.addEventListener('input', () => {
      if (!preview.hidden) {
        updatePreview();
      }
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('textarea.html-editor').forEach(makeEditor);
  });
})();