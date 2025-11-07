/**
 * Form Editors
 */

'use strict';

(function () {
  let snowEditor = null;

  // Snow Theme - Check if element exists first
  // --------------------------------------------------------------------
  const snowEditorElement = document.querySelector('#snow-editor');
  
  if (snowEditorElement) {
    const snowToolbar = [
      [{ font: [] }, { size: [] }],
      ['bold', 'italic', 'underline', 'strike'],
      [{ color: [] }, { background: [] }],
      [{ script: 'super' }, { script: 'sub' }],
      [{ header: '1' }, { header: '2' }, 'blockquote', 'code-block'],
      [{ list: 'ordered' }, { list: 'bullet' }, { indent: '-1' }, { indent: '+1' }],
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

    // Sync content to hidden input on editor change
    snowEditor.on('text-change', function() {
      syncEditorToHiddenInput();
    });

    // Initialize with existing content
    const existingContent = document.getElementById('deskripsi').value;
    if (existingContent) {
      snowEditor.root.innerHTML = existingContent;
    }
    
    console.log('Snow editor initialized');
  }

  // Function to sync editor content to hidden input
  function syncEditorToHiddenInput() {
    if (snowEditor) {
      const deskripsiInput = document.getElementById('deskripsi');
      const htmlContent = snowEditor.root.innerHTML;
      
      if (htmlContent === '<p><br></p>' || htmlContent.trim() === '' || htmlContent === '<p></p>') {
        deskripsiInput.value = '';
      } else {
        deskripsiInput.value = htmlContent;
      }
      
      console.log('Editor synced to hidden input:', deskripsiInput.value.length > 0 ? 'Has content' : 'Empty');
    }
  }

  // Handle Form Submission - Sync Quill content to hidden input
  // --------------------------------------------------------------------
  const form = document.querySelector('form');

  if (form) {
    form.addEventListener('submit', function (e) {
      console.log('Form submit triggered');
      
      // Final sync before submission
      syncEditorToHiddenInput();
      
      // Validation - check if deskripsi is empty
      const deskripsiInput = document.getElementById('deskripsi');
      if (!deskripsiInput.value || deskripsiInput.value.trim() === '') {
        e.preventDefault();
        alert('Deskripsi artikel harus diisi!');
        return false;
      }
      
      console.log('Form submitted successfully');
    });
    
    console.log('Form submit listener attached');
  }
})();