import { el } from './dom.js';

let activeModal = null;

export function openFilePreviewModal({ url, name }) {
  if (activeModal) {
    activeModal.remove();
    activeModal = null;
  }

  const isImg = /\.(png|jpe?g|webp|gif)$/i.test(name || '') || (url && url.includes('image'));

  const backdrop = el('div', {
    class: 'modal-backdrop',
    style: 'position: fixed; inset: 0; background: rgba(15, 23, 42, 0.7); z-index: 1060; display: flex; align-items: center; justify-content: center; padding: 1.5rem;',
  });

  const onKey = (e) => { if (e.key === 'Escape') close(); };
  const close = () => {
    window.removeEventListener('keydown', onKey);
    backdrop.remove();
    activeModal = null;
  };
  window.addEventListener('keydown', onKey);
  backdrop.addEventListener('click', close);

  const content = isImg
    ? el('div', { style: 'text-align: center; max-height: 70vh; overflow: auto;' }, [
        el('img', { src: url, alt: name, style: 'max-width: 100%; height: auto; border-radius: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);' }),
      ])
    : el('iframe', {
        src: url,
        title: name || 'Preview',
        style: 'width: 100%; height: 68vh; border: 1px solid #cbd5e1; border-radius: 4px; background: #ffffff;',
      });

  const card = el('div', {
    class: 'card',
    style: 'width: 95%; max-width: 880px; background: #ffffff; border-radius: 8px; padding: 1.25rem 1.5rem; box-shadow: 0 20px 45px rgba(0,0,0,0.3); max-height: 90vh; display: flex; flex-direction: column;',
    onClick: (e) => e.stopPropagation(),
  }, [
    el('div', { style: 'display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.5rem;' }, [
      el('div', { style: 'display: flex; align-items: center; gap: 0.5rem; overflow: hidden;' }, [
        el('span', { style: 'font-size: 13.5px; font-weight: 800; color: #004482; white-space: nowrap;' }, 'XEM TRƯỚC:'),
        el('span', { style: 'font-size: 13px; font-weight: 600; color: #334155; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;' }, name || 'Tài liệu'),
      ]),
      el('button', { type: 'button', style: 'border: none; background: none; font-size: 20px; color: #64748b; cursor: pointer; padding: 0.2rem;', onClick: close }, '✕'),
    ]),
    el('div', { style: 'flex: 1; overflow: hidden; margin-bottom: 0.75rem;' }, [content]),
    el('div', { style: 'display: flex; justify-content: flex-end; gap: 0.5rem; border-top: 1px solid #f1f5f9; padding-top: 0.5rem;' }, [
      el('a', { href: url, target: '_blank', class: 'btn btn-secondary btn-sm', style: 'padding: 0.35rem 0.85rem; font-size: 12px; text-decoration: none;' }, 'Mở tab mới ↗'),
      el('button', { type: 'button', class: 'btn btn-secondary btn-sm', style: 'padding: 0.35rem 1rem; font-size: 12px; font-weight: 600;', onClick: close }, 'Đóng'),
    ]),
  ]);

  backdrop.append(card);
  document.body.append(backdrop);
  activeModal = backdrop;
}
