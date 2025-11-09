/**
 * Form Editors
 */

'use strict';

(function () {
  let snowEditor = null;

  // Snow Theme Editor
  const snowEditorElement = document.querySelector('#snow-editor');
  
  if (snowEditorElement) {
    const snowToolbar = [
      [{ font: [] }, { size: [] }],
      ['bold', 'italic', 'underline', 'strike'],
      [{ color: [] }, { background: [] }],
      [{ script: 'super' }, { script: 'sub' }],
      [{ header: '1' }, { header: '2' }, 'blockquote', 'code-block'],
      [{ list: 'ordered' }, { list: 'bullet' }, { indent: '-1' }, { indent: '+1' }],
      [{ align: [] }],
      ['link', 'image', 'video'],
      ['clean']
    ];

    snowEditor = new Quill('#snow-editor', {
      bounds: '#snow-editor',
      modules: {
        syntax: true,
        toolbar: snowToolbar
      },
      theme: 'snow',
      placeholder: 'Tulis deskripsi di sini...'
    });

    // Initialize with existing content
    const deskripsiInput = document.getElementById('deskripsi');
    const existingContent = deskripsiInput.value;
    
    if (existingContent && existingContent.trim() !== '') {
      snowEditor.root.innerHTML = existingContent;
    }

    // Sync content to hidden input on editor change
    snowEditor.on('text-change', function() {
      syncEditorToHiddenInput();
    });
  }

  // Function to sync editor content to hidden input
  function syncEditorToHiddenInput() {
    if (snowEditor) {
      const deskripsiInput = document.getElementById('deskripsi');
      const htmlContent = snowEditor.root.innerHTML;
      
      // Check if content is empty
      if (htmlContent === '<p><br></p>' || htmlContent.trim() === '' || htmlContent === '<p></p>') {
        deskripsiInput.value = '';
      } else {
        deskripsiInput.value = htmlContent;
      }
    }
  }

  // Handle Form Submission
  const form = document.querySelector('form');

  if (form) {
    form.addEventListener('submit', function (e) {
      console.log('→ Form submit triggered');
      
      // Final sync before submission
      syncEditorToHiddenInput();
      
      const deskripsiInput = document.getElementById('deskripsi');
      const content = deskripsiInput.value;
      
      console.log('Content length:', content.length);
      console.log('Content preview:', content.substring(0, 100));
      
      // Validation
      if (!content || content.trim() === '') {
        e.preventDefault();
        alert('Deskripsi artikel harus diisi!');
        return false;
      }
    });
  }
})();