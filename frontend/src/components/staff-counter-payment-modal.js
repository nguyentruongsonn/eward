import { el } from './dom.js';
import { api } from '../api/client.js';

let activeModal = null;

export function openStaffCounterPaymentModal({ application, onSubmitted }) {
  if (activeModal) activeModal.remove();

  const backdrop = el('div', {
    style: 'position: fixed; inset: 0; z-index: 1060; display: flex; align-items: center; justify-content: center; padding: 1rem; background: rgba(15,23,42,0.62);',
  });
  const close = () => { backdrop.remove(); activeModal = null; };
  const error = el('div', { style: 'display: none; padding: 0.65rem 0.8rem; margin-bottom: 0.85rem; border-radius: 4px; background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; font-size: 12.5px;' });
  const amount = el('input', { class: 'input', type: 'number', min: '0', step: '1', value: String(Number(application.fee || 0)), style: 'width: 100%;' });
  const receipt = el('input', { class: 'input', type: 'text', maxlength: '100', placeholder: 'Ví dụ: BC-2026-0001', style: 'width: 100%;' });
  const note = el('textarea', { class: 'input', rows: 3, placeholder: 'Ghi chú thu tiền (nếu có)...', style: 'width: 100%; resize: vertical;' });
  const submit = el('button', { type: 'button', class: 'btn btn-primary btn-sm', style: 'background: #004482; color: #fff; font-weight: 700; padding: 0.5rem 1rem;' }, 'Xác nhận đã thu tiền');

  submit.addEventListener('click', async () => {
    error.style.display = 'none';
    if (!receipt.value.trim()) {
      error.textContent = 'Vui lòng nhập số biên lai hoặc mã giao dịch.';
      error.style.display = 'block';
      receipt.focus();
      return;
    }
    submit.disabled = true;
    submit.textContent = 'Đang lưu...';
    try {
      await api.post(`/admin/applications/${application.id}/payments/counter`, {
        amount: Number(amount.value),
        receipt_number: receipt.value.trim(),
        note: note.value.trim() || null,
      });
      close();
      onSubmitted?.();
    } catch (err) {
      error.textContent = err.message || 'Không thể xác nhận giao dịch.';
      error.style.display = 'block';
      submit.disabled = false;
      submit.textContent = 'Xác nhận đã thu tiền';
    }
  });

  const card = el('div', {
    class: 'card',
    style: 'width: 95%; max-width: 520px; padding: 1.5rem; background: #fff; border-radius: 6px; box-shadow: 0 16px 40px rgba(0,0,0,0.25);',
    onClick: (event) => event.stopPropagation(),
  }, [
    el('div', { style: 'display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.65rem;' }, [
      el('h3', { style: 'margin: 0; color: #004482; font-size: 15px; font-weight: 800;' }, 'XÁC NHẬN THU PHÍ TRỰC TIẾP'),
      el('button', { type: 'button', style: 'border: none; background: none; font-size: 20px; color: #64748b; cursor: pointer;', onClick: close }, '✕'),
    ]),
    el('p', { style: 'font-size: 12.5px; color: #475569; margin: 0 0 1rem;' }, `Hồ sơ ${application.id} · Số tiền phải thu: ${Number(application.fee || 0).toLocaleString('vi-VN')} đ`),
    error,
    el('label', { style: 'display: block; margin-bottom: 0.75rem; font-size: 12.5px; font-weight: 700; color: #334155;' }, ['Số tiền thực thu *', amount]),
    el('label', { style: 'display: block; margin-bottom: 0.75rem; font-size: 12.5px; font-weight: 700; color: #334155;' }, ['Số biên lai / mã giao dịch *', receipt]),
    el('label', { style: 'display: block; margin-bottom: 1rem; font-size: 12.5px; font-weight: 700; color: #334155;' }, ['Ghi chú', note]),
    el('div', { style: 'display: flex; justify-content: flex-end; gap: 0.6rem; border-top: 1px solid #f1f5f9; padding-top: 0.75rem;' }, [
      el('button', { type: 'button', class: 'btn btn-secondary btn-sm', style: 'border: 1px solid #cbd5e1; padding: 0.5rem 1rem;', onClick: close }, 'Hủy'),
      submit,
    ]),
  ]);

  backdrop.append(card);
  backdrop.addEventListener('click', close);
  document.addEventListener('keydown', function onKey(event) {
    if (event.key === 'Escape') {
      document.removeEventListener('keydown', onKey);
      close();
    }
  });
  document.body.append(backdrop);
  activeModal = backdrop;
}
