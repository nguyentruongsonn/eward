import { el } from './dom.js';
import { getAuthToken, withAuthToken, downloadFile } from '../api/client.js';

let activeModal = null;

export async function openFilePreviewModal({ url, name }) {
  if (activeModal) {
    activeModal.remove();
    activeModal = null;
  }

  let objectUrl = null;

  const backdrop = el('div', {
    class: 'modal-backdrop',
    style: 'position: fixed; inset: 0; background: rgba(15, 23, 42, 0.7); z-index: 1060; display: flex; align-items: center; justify-content: center; padding: 1.5rem;',
  });

  const close = () => {
    window.removeEventListener('keydown', onKey);
    if (objectUrl) {
      URL.revokeObjectURL(objectUrl);
      objectUrl = null;
    }
    backdrop.remove();
    activeModal = null;
  };

  const onKey = (e) => { if (e.key === 'Escape') close(); };
  window.addEventListener('keydown', onKey);
  backdrop.addEventListener('click', close);

  const bodyContainer = el('div', {
    style: 'flex: 1; min-height: 420px; display: flex; align-items: center; justify-content: center; overflow: hidden; margin-bottom: 0.75rem; background: #f8fafc; border-radius: 4px; border: 1px solid #e2e8f0;',
  }, [
    el('div', { style: 'text-align: center; color: #64748b; font-size: 13px;' }, [
      el('div', { style: 'margin: 0 auto 0.75rem; width: 32px; height: 32px; border: 3px solid #cbd5e1; border-top-color: #004482; border-radius: 50%; animation: spin 0.8s linear infinite;' }),
      el('div', { style: 'font-weight: 600;' }, 'Đang tải tệp để xem trước...'),
    ]),
  ]);

  const openNewTabLink = el('a', {
    href: withAuthToken(url),
    target: '_blank',
    class: 'btn btn-secondary btn-sm',
    style: 'padding: 0.35rem 0.85rem; font-size: 12px; text-decoration: none;',
  }, 'Mở tab mới ↗');

  const downloadBtn = el('button', {
    type: 'button',
    class: 'btn btn-primary btn-sm',
    style: 'padding: 0.35rem 0.85rem; font-size: 12px; background: #004482; border: none; font-weight: 600;',
    onClick: () => downloadFile(url, name),
  }, 'Tải về ⤓');

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
    bodyContainer,
    el('div', { style: 'display: flex; justify-content: flex-end; gap: 0.5rem; border-top: 1px solid #f1f5f9; padding-top: 0.5rem;' }, [
      openNewTabLink,
      downloadBtn,
      el('button', { type: 'button', class: 'btn btn-secondary btn-sm', style: 'padding: 0.35rem 1rem; font-size: 12px; font-weight: 600;', onClick: close }, 'Đóng'),
    ]),
  ]);

  backdrop.append(card);
  document.body.append(backdrop);
  activeModal = backdrop;

  try {
    const token = getAuthToken();
    const headers = token ? { Authorization: `Bearer ${token}` } : {};
    const previewUrl = url ? `${url}${url.includes('?') ? '&' : '?'}disposition=inline` : url;
    const res = await fetch(previewUrl, { headers });

    if (!res.ok) {
      let errText = 'Không thể xem trước tệp tin.';
      try {
        const errJson = await res.json();
        errText = errJson.message || errText;
      } catch (_) {}
      bodyContainer.replaceChildren(el('div', {
        style: 'padding: 2rem; text-align: center; color: #b91c1c; font-size: 13.5px;',
      }, [
        el('div', { style: 'font-size: 24px; margin-bottom: 0.5rem;' }, '⚠️'),
        el('div', { style: 'font-weight: 700; margin-bottom: 0.3rem;' }, 'Không thể tải tệp để xem trước'),
        el('div', { style: 'color: #64748b; font-size: 12px; margin-bottom: 1rem;' }, errText),
        el('button', {
          type: 'button',
          class: 'btn btn-secondary btn-sm',
          onClick: () => downloadFile(url, name),
        }, 'Thử tải về máy'),
      ]));
      return;
    }

    const blob = await res.blob();
    const contentType = res.headers.get('content-type') || blob.type || '';
    const isImg = contentType.startsWith('image/') || /\.(png|jpe?g|webp|gif|svg)$/i.test(name || '');

    const finalBlob = isImg
      ? blob
      : new Blob([blob], { type: contentType || 'application/pdf' });
    objectUrl = URL.createObjectURL(finalBlob);

    openNewTabLink.href = objectUrl;

    const content = isImg
      ? el('div', { style: 'text-align: center; max-height: 70vh; overflow: auto; width: 100%;' }, [
          el('img', { src: objectUrl, alt: name, style: 'max-width: 100%; height: auto; border-radius: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);' }),
        ])
      : el('iframe', {
          src: objectUrl,
          title: name || 'Preview',
          style: 'width: 100%; height: 68vh; border: none; border-radius: 4px; background: #ffffff;',
        });

    bodyContainer.style.background = '#ffffff';
    bodyContainer.replaceChildren(content);
  } catch (err) {
    bodyContainer.replaceChildren(el('div', {
      style: 'padding: 2rem; text-align: center; color: #b91c1c; font-size: 13.5px;',
    }, [
      el('div', { style: 'font-size: 24px; margin-bottom: 0.5rem;' }, '⚠️'),
      el('div', { style: 'font-weight: 700; margin-bottom: 0.3rem;' }, 'Lỗi kết nối khi tải tệp'),
      el('div', { style: 'color: #64748b; font-size: 12px;' }, err.message || 'Vui lòng kiểm tra lại đường truyền.'),
    ]));
  }
}
