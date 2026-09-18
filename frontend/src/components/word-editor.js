import { el } from './dom.js';

export function createWordEditor({ placeholder = 'Nhập nội dung...', initialContent = '', minHeight = '140px', onChange = null }) {
  const contentArea = el('div', {
    contenteditable: 'true',
    style: `min-height: ${minHeight}; max-height: 380px; overflow-y: auto; padding: 0.85rem 1rem; background: #ffffff; font-family: 'Segoe UI', Arial, sans-serif; font-size: 13.5px; line-height: 1.6; color: #1e293b; outline: none;`,
  });

  if (initialContent) {
    contentArea.innerHTML = initialContent;
  }

  function exec(cmd, val = null) {
    contentArea.focus();
    document.execCommand(cmd, false, val);
    if (onChange) onChange(contentArea.innerHTML);
  }

  function toolBtn(label, cmd, title, isHtml = false) {
    const btn = el('button', {
      type: 'button',
      title,
      style: 'height: 26px; min-width: 26px; padding: 0 0.35rem; background: #ffffff; border: 1px solid #d1d5db; border-radius: 3px; font-size: 12px; font-weight: 700; color: #374151; cursor: pointer; display: inline-flex; align-items: center; justify-content: center;',
      onClick: (e) => {
        e.preventDefault();
        exec(cmd);
      },
    });
    if (isHtml) btn.innerHTML = label;
    else btn.textContent = label;
    btn.addEventListener('mouseenter', () => { btn.style.background = '#e5e7eb'; });
    btn.addEventListener('mouseleave', () => { btn.style.background = '#ffffff'; });
    return btn;
  }

  const formatSelect = el('select', {
    style: 'height: 26px; font-size: 11.5px; background: #ffffff; border: 1px solid #d1d5db; border-radius: 3px; color: #374151; cursor: pointer; padding: 0 0.25rem;',
    onChange: () => {
      exec('formatBlock', formatSelect.value);
    },
  }, [
    el('option', { value: '<p>' }, 'Đoạn văn (Normal)'),
    el('option', { value: '<h2>' }, 'Tiêu đề 1 (Heading 1)'),
    el('option', { value: '<h3>' }, 'Tiêu đề 2 (Heading 2)'),
  ]);

  const toolbar = el('div', {
    style: 'display: flex; flex-wrap: wrap; gap: 0.3rem; align-items: center; padding: 0.4rem 0.6rem; background: #f8fafc; border-bottom: 1px solid #cbd5e1; border-top-left-radius: 4px; border-top-right-radius: 4px;',
  }, [
    formatSelect,
    el('div', { style: 'width: 1px; height: 18px; background: #cbd5e1; margin: 0 0.15rem;' }),
    toolBtn('B', 'bold', 'In đậm (Ctrl+B)'),
    toolBtn('I', 'italic', 'In nghiêng (Ctrl+I)'),
    toolBtn('U', 'underline', 'Gạch chân (Ctrl+U)'),
    toolBtn('S', 'strikeThrough', 'Gạch ngang'),
    el('div', { style: 'width: 1px; height: 18px; background: #cbd5e1; margin: 0 0.15rem;' }),
    toolBtn('⇤', 'justifyLeft', 'Căn trái'),
    toolBtn('↔', 'justifyCenter', 'Căn giữa'),
    toolBtn('⇥', 'justifyRight', 'Căn phải'),
    toolBtn('≡', 'justifyFull', 'Căn đều 2 bên'),
    el('div', { style: 'width: 1px; height: 18px; background: #cbd5e1; margin: 0 0.15rem;' }),
    toolBtn('• List', 'insertUnorderedList', 'Danh sách dấu chấm'),
    toolBtn('1. List', 'insertOrderedList', 'Danh sách số'),
  ]);

  contentArea.addEventListener('input', () => {
    if (onChange) onChange(contentArea.innerHTML);
  });

  const wrapper = el('div', {
    class: 'word-editor-wrapper',
    style: 'border: 1px solid #cbd5e1; border-radius: 4px; background: #ffffff; box-shadow: 0 1px 2px rgba(0,0,0,0.04); overflow: hidden;',
  }, [toolbar, contentArea]);

  return {
    wrapper,
    getHtml: () => contentArea.innerHTML.trim(),
    getText: () => contentArea.innerText.trim(),
    setHtml: (html) => { contentArea.innerHTML = html; },
    focus: () => contentArea.focus(),
  };
}
